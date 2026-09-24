<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AuditLog;
use App\Models\User;
use App\Notifications\AccountBlockedNotification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AccountBlockService
{
    public function __construct(
        private readonly WalletService $walletService
    ) {
    }

    /**
     * مسدودسازی اکانت
     *
     * قوانین:
     * - فقط اکانت active قابل Block است.
     * - first_login_date باید وجود داشته باشد.
     * - اگر <= 72 ساعت از اولین ورود موفق گذشته باشد،
     *   charged_amount کامل Refund می‌شود.
     * - اگر > 72 ساعت گذشته باشد، نیاز به confirmation دارد.
     * - بیشتر از 72 ساعت هیچ Refund انجام نمی‌شود.
     * - Audit Log ثبت می‌شود.
     * - Admin مربوطه Notification دریافت می‌کند.
     */
    public function block(
        Account $account,
        User|bool|null $actorOrConfirm = null,
        bool $confirmOver72Hours = false
    ): array {
        /*
         * Backward compatibility:
         *
         * block($account)
         * block($account, true)
         *
         * حالت جدید:
         * block($account, $actor, true/false)
         */
        if (is_bool($actorOrConfirm)) {
            $confirmOver72Hours = $actorOrConfirm;

            $actor = auth()->user();

            if (! $actor instanceof User) {
                $actor = $account->admin;
            }
        } else {
            $actor = $actorOrConfirm ?? auth()->user();

            if (! $actor instanceof User) {
                $actor = $account->admin;
            }
        }

        return DB::transaction(function () use (
            $account,
            $actor,
            $confirmOver72Hours
        ) {
            /*
             * Lock کردن Account
             */
            $account = Account::query()
                ->with([
                    'admin',
                    'plan',
                    'support',
                ])
                ->lockForUpdate()
                ->findOrFail($account->id);

            /*
             * Admin مالک اکانت
             */
            $admin = $account->admin;

            if (! $admin instanceof User) {
                throw new RuntimeException(
                    'Admin مالک این اکانت پیدا نشد.'
                );
            }

            /*
             * فقط Active
             */
            if ($account->status !== Account::STATUS_ACTIVE) {
                throw new RuntimeException(
                    'این اکانت در حال حاضر فعال نیست.'
                );
            }

            /*
             * first_login_date الزامی است.
             */
            if (! $account->first_login_date) {
                throw new RuntimeException(
                    'اولین ورود موفق اکانت هنوز ثبت نشده است.'
                );
            }

            $now = now();

            /*
             * محاسبه زمان واقعی از اولین ورود موفق
             */
            $hoursPassed = $account->first_login_date->diffInHours($now);

            $under72Hours = $hoursPassed <= 72;

            /*
             * بیشتر از 72 ساعت:
             * بدون تأیید اجازه Block نداریم.
             */
            if (
                ! $under72Hours
                && ! $confirmOver72Hours
            ) {
                throw new RuntimeException(
                    'بیش از ۷۲ ساعت از اولین ورود موفق این اکانت گذشته است. برای مسدودسازی باید تأیید کنید.'
                );
            }

            $refundAmount = 0;

            /*
             * زیر 72 ساعت:
             * دقیقاً charged_amount برگشت می‌خورد.
             */
            if ($under72Hours) {
                $refundAmount = (int) $account->charged_amount;

                if ($refundAmount > 0) {
                    $this->walletService->refund(
                        $admin,
                        $refundAmount,
                        $actor instanceof User ? $actor : null,
                        'بازگشت مبلغ اکانت به دلیل مسدود شدن در ۷۲ ساعت اول'
                    );
                }
            }

            /*
             * Block
             */
            $account->update([
                'status' => Account::STATUS_BLOCKED,
                'blocked_at' => $now,
                'block_reason' => $under72Hours
                    ? 'blocked_under_72_hours'
                    : 'blocked_over_72_hours',
            ]);

            /*
             * Audit Log
             */
            AuditLog::create([
                'user_id' => $actor?->id,

                'action' => 'account_blocked',

                'subject_type' => Account::class,

                'subject_id' => $account->id,

                'description' => $under72Hours
                    ? "اکانت {$account->username} زیر ۷۲ ساعت مسدود شد و مبلغ {$refundAmount} بازگردانده شد."
                    : "اکانت {$account->username} پس از گذشت بیش از ۷۲ ساعت مسدود شد و مبلغی بازگردانده نشد.",

                'metadata' => [
                    'account_id' => $account->id,

                    'username' => $account->username,

                    'admin_id' => $account->admin_id,

                    'actor_id' => $actor?->id,

                    'under_72_hours' => $under72Hours,

                    'hours_passed' => $hoursPassed,

                    'refund_amount' => $refundAmount,

                    'refunded' => $refundAmount,

                    'charged_amount' => (int) $account->charged_amount,

                    'block_reason' => $account->block_reason,
                ],
            ]);

            /*
             * Notification نباید باعث Fail شدن Block شود.
             */
            try {
                $admin->notify(
                    new AccountBlockedNotification(
                        $account,
                        $under72Hours,
                        $refundAmount
                    )
                );
            } catch (\Throwable $e) {
                report($e);
            }

            /*
             * هر دو نام را برمی‌گردانیم:
             *
             * refund_amount -> کد جدید
             * refunded      -> تست‌های قبلی
             */
            return [
                'account' => $account,

                'under_72_hours' => $under72Hours,

                'refund_amount' => $refundAmount,

                'refunded' => $refundAmount,

                'hours_passed' => $hoursPassed,
            ];
        });
    }
}
