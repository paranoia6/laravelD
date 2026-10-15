<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AuditLog;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AccountRenewalService
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    /**
     * تمدید اکانت
     *
     * قانون:
     * اگر تاریخ انقضای فعلی هنوز نگذشته باشد،
     * مدت جدید به expired_at فعلی اضافه می‌شود.
     *
     * اگر تاریخ انقضا گذشته باشد،
     * مدت جدید از همین الان شروع می‌شود.
     */
    public function renew(
        User $admin,
        Account $account,
        Plan $plan
    ): Account {
        return DB::transaction(function () use (
            $admin,
            $account,
            $plan
        ) {

            /*
             * Admin را Lock می‌کنیم تا موجودی همزمان
             * توسط عملیات دیگری تغییر نکند.
             */
            $lockedAdmin = User::query()
                ->whereKey($admin->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * بررسی دسترسی Admin
             */
            if (
                ! in_array(
                    $lockedAdmin->role?->value,
                    ['admin', 'super_admin'],
                    true
                )
            ) {
                throw new RuntimeException(
                    'دسترسی تمدید اکانت برای این کاربر وجود ندارد.'
                );
            }

            /*
             * Admin فعال باشد
             */
            if (! $lockedAdmin->is_active) {
                throw new RuntimeException(
                    'حساب Admin شما غیرفعال است.'
                );
            }

            /*
             * اکانت را Lock می‌کنیم.
             */
            $lockedAccount = Account::query()
                ->with(['admin', 'plan', 'support'])
                ->whereKey($account->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Admin فقط اکانت خودش را تمدید کند.
             */
            if (
                (int) $lockedAccount->admin_id
                !== (int) $lockedAdmin->id
            ) {
                throw new RuntimeException(
                    'شما فقط می‌توانید اکانت‌های خودتان را تمدید کنید.'
                );
            }

            /*
             * اکانت Block شده قابل تمدید نیست.
             */
            if (
                $lockedAccount->status
                !== Account::STATUS_ACTIVE
            ) {
                throw new RuntimeException(
                    'اکانت مسدود شده قابل تمدید نیست.'
                );
            }

            /*
             * اکانت باید اولین Login را انجام داده باشد.
             *
             * طبق قانون پروژه:
             * قبل از اولین Login، expired_at برابر null است
             * و زمان اعتبار هنوز شروع نشده است.
             */
            if (! $lockedAccount->first_login_date) {
                throw new RuntimeException(
                    'این اکانت هنوز اولین ورود را انجام نداده و قابل تمدید نیست.'
                );
            }

            if (! $lockedAccount->expired_at) {
                throw new RuntimeException(
                    'تاریخ انقضای اکانت مشخص نیست و امکان تمدید وجود ندارد.'
                );
            }

            /*
             * Plan را Lock می‌کنیم.
             */
            $lockedPlan = Plan::query()
                ->whereKey($plan->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * پلن فعال باشد.
             */
            if (! $lockedPlan->is_active) {
                throw new RuntimeException(
                    'این پلن در حال حاضر غیرفعال است.'
                );
            }

            /*
             * قیمت مشخص باشد.
             */
            if ($lockedPlan->price === null) {
                throw new RuntimeException(
                    'قیمت این پلن هنوز توسط Super Admin تعیین نشده است.'
                );
            }

            /*
             * نوع پلن تمدید باید با نوع اکانت یکی باشد.
             *
             * نوع اکانت فعلی از Plan فعلی مشخص می‌شود.
             */
            $currentAccountType = $lockedAccount->plan?->type;

            if (
                $currentAccountType
                && $lockedPlan->type !== $currentAccountType
            ) {
                throw new RuntimeException(
                    'نوع پلن تمدید باید با نوع اکانت فعلی یکسان باشد.'
                );
            }

            /*
             * مبلغ تمدید
             */
            $price = (int) $lockedPlan->price;

            /*
             * Super Admin محدودیت Wallet ندارد.
             *
             * Admin باید از Wallet خودش پرداخت کند.
             */
            if (
                $lockedAdmin->role?->value === 'admin'
            ) {

                if (
                    (int) $lockedAdmin->balance < $price
                ) {
                    throw new RuntimeException(
                        'موجودی شما برای تمدید این اکانت کافی نیست. '
                        . 'موجودی فعلی: '
                        . number_format(
                            (int) $lockedAdmin->balance
                        )
                        . ' تومان'
                    );
                }

                $this->walletService->debit(
                    $lockedAdmin,
                    $price,
                    $lockedAdmin,
                    'تمدید اکانت '
                    . $lockedAccount->username
                    . ' - '
                    . $lockedPlan->duration_months
                    . ' ماهه'
                );
            }

            /*
             * زمان فعلی
             */
            $now = now();

            /*
             * تاریخ انقضای فعلی
             */
            $currentExpiredAt = $lockedAccount
                ->expired_at
                ->copy();

            /*
             * قانون اصلی تمدید:
             *
             * اگر هنوز اعتبار دارد:
             *     expired_at قبلی + مدت جدید
             *
             * اگر منقضی شده:
             *     now + مدت جدید
             */
            if ($currentExpiredAt->isFuture()) {
                $newExpiredAt = $currentExpiredAt
                    ->copy()
                    ->addMonths(
                        (int) $lockedPlan->duration_months
                    );
            } else {
                $newExpiredAt = $now
                    ->copy()
                    ->addMonths(
                        (int) $lockedPlan->duration_months
                    );
            }

            /*
             * آپدیت تاریخ انقضا
             *
             * charged_amount دست نمی‌خورد،
             * چون مبلغ خرید اولیه اکانت است.
             */
            $lockedAccount->update([
                'expired_at' => $newExpiredAt,
            ]);

            /*
             * Audit Log
             */
            AuditLog::create([
                'user_id' => $lockedAdmin->id,
                'action' => 'account_renewed',
                'subject_type' => Account::class,
                'subject_id' => $lockedAccount->id,
                'description' =>
                    "اکانت {$lockedAccount->username} به مدت "
                    . $lockedPlan->duration_months
                    . " ماه تمدید شد.",
                'metadata' => [
                    'account_id' => $lockedAccount->id,
                    'username' => $lockedAccount->username,
                    'admin_id' => $lockedAccount->admin_id,
                    'actor_id' => $lockedAdmin->id,
                    'plan_id' => $lockedPlan->id,
                    'plan_type' => $lockedPlan->type,
                    'duration_months' => (int) $lockedPlan->duration_months,
                    'price' => $price,
                    'old_expired_at' => $currentExpiredAt->toDateTimeString(),
                    'new_expired_at' => $newExpiredAt->toDateTimeString(),
                    'renewed_from_existing_expiry' => $currentExpiredAt->isFuture(),
                ],
            ]);

            /*
             * Notification
             */
            try {
                $lockedAdmin->notify(
                    new SystemNotification(
                        'تمدید اکانت',
                        'اکانت '
                        . $lockedAccount->username
                        . ' به مدت '
                        . $lockedPlan->duration_months
                        . ' ماه تمدید شد. '
                        . 'تاریخ انقضا: '
                        . $newExpiredAt->format('Y/m/d H:i'),
                        route(
                            'admin.accounts.show',
                            $lockedAccount
                        )
                    )
                );
            } catch (\Throwable $e) {
                report($e);
            }

            /*
             * Reload
             */
            $lockedAccount->load([
                'admin',
                'plan',
                'support',
            ]);

            return $lockedAccount;
        });
    }
}
