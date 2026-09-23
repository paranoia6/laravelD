<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Plan;
use App\Models\Support;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class AccountCreationService
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    public function create(
        User $admin,
        string $username,
        string $password,
        Plan $plan,
        int $supportId,
        int $deviceType,
        int $accountType
    ): Account {
        return DB::transaction(
            function () use (
                $admin,
                $username,
                $password,
                $plan,
                $supportId,
                $deviceType,
                $accountType
            ) {

                /*
                 * قفل User برای جلوگیری از race condition موجودی
                 */
                $lockedAdmin = User::query()
                    ->whereKey($admin->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                 * Admin فعال باشد
                 */
                if (! $lockedAdmin->is_active) {
                    throw new RuntimeException(
                        'حساب Admin شما غیرفعال است.'
                    );
                }

                /*
                 * Role مجاز
                 */
                if (
                    ! in_array(
                        $lockedAdmin->role->value,
                        [
                            'admin',
                            'super_admin',
                        ],
                        true
                    )
                ) {
                    throw new RuntimeException(
                        'دسترسی ایجاد اکانت برای این کاربر وجود ندارد.'
                    );
                }

                /*
                 * پلن را lock می‌کنیم.
                 */
                $lockedPlan = Plan::query()
                    ->whereKey($plan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                 * پلن فعال
                 */
                if (! $lockedPlan->is_active) {
                    throw new RuntimeException(
                        'این پلن در حال حاضر غیرفعال است.'
                    );
                }

                /*
                 * قیمت مشخص شده
                 */
                if ($lockedPlan->price === null) {
                    throw new RuntimeException(
                        'قیمت این پلن هنوز توسط Super Admin تعیین نشده است.'
                    );
                }

                /*
                 * تطابق نوع پلن
                 */
                $expectedType =
                    $accountType === 2
                        ? 'special'
                        : 'normal';

                if ($lockedPlan->type !== $expectedType) {
                    throw new RuntimeException(
                        'نوع پلن با نوع اکانت انتخاب‌شده هماهنگ نیست.'
                    );
                }

                /*
                 * Support فقط متعلق به همان Admin/User باشد
                 * و حداقل یک لینک داشته باشد.
                 */
                $support = Support::query()
                    ->whereKey($supportId)
                    ->where('user_id', $lockedAdmin->id)
                    ->where('is_active', true)
                    ->first();

                if (! $support || ! $support->hasLinks()) {
                    throw new RuntimeException(
                        'پشتیبانی انتخاب‌شده ثبت نشده یا اطلاعات آن کامل نیست.'
                    );
                }

                /*
                 * Username
                 */
                $username = trim($username);

                if ($username === '') {
                    throw new RuntimeException(
                        'نام کاربری نمی‌تواند خالی باشد.'
                    );
                }

                /*
                 * فقط حروف انگلیسی و اعداد
                 */
                if (
                    ! preg_match(
                        '/^[A-Za-z0-9]+$/',
                        $username
                    )
                ) {
                    throw new RuntimeException(
                        'نام کاربری فقط باید شامل حروف انگلیسی و اعداد باشد.'
                    );
                }

                /*
                 * Unique
                 */
                if (
                    Account::query()
                        ->where(
                            'username',
                            $username
                        )
                        ->exists()
                ) {
                    throw new RuntimeException(
                        "نام کاربری {$username} قبلاً استفاده شده است."
                    );
                }

                /*
                 * قیمت واقعی
                 */
                $price =
                    (int) $lockedPlan->price;

                /*
                 * فقط Admin از Wallet پرداخت می‌کند.
                 *
                 * Super Admin unlimited است.
                 */
                if (
                    $lockedAdmin->role->value
                    === 'admin'
                ) {
                    if (
                        (int) $lockedAdmin->balance
                        < $price
                    ) {
                        throw new RuntimeException(
                            'موجودی شما برای ایجاد این اکانت کافی نیست. '
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
                        "خرید اکانت {$username} - پلن "
                        . $lockedPlan->duration_months
                        . ' ماهه'
                    );
                }

                /*
                 * زمان فعال‌سازی
                 */
                $activatedAt = now();

                /*
                 * تاریخ انقضا
                 */
                $expiredAt =
                    $activatedAt
                        ->copy()
                        ->addMonths(
                            (int) $lockedPlan->duration_months
                        );

                /*
                 * ایجاد Account
                 */
                $account = Account::create([
                    'admin_id' => $lockedAdmin->id,
                    'plan_id' => $plan->id,
                    'support_id' => $support->id,

                    'device_type' => $deviceType,

                    'username' => $username,
                    'password' => $password,

                    'charged_amount' => $price,

                    // زمان ساخته‌شدن اکانت؛ برای قانون 72 ساعت
                    'activated_at' => now(),

                    // اعتبار هنوز شروع نشده
                    'first_login_at' => null,
                    'expired_at' => null,

                    'status' => Account::STATUS_ACTIVE,
                ]);

                /*
                 * روابط برای صفحه نتیجه
                 */
                $account->load([
                    'plan',
                    'support',
                ]);

                return $account;
            }
        );
    }

    /**
     * ساخت Username تصادفی
     */
    public function generateUsername(): string
    {
        do {
            $username =
                'user'
                . Str::lower(
                    Str::random(8)
                );
        } while (
            Account::query()
                ->where(
                    'username',
                    $username
                )
                ->exists()
        );

        return $username;
    }

    /**
     * ساخت Password
     */
    public function generatePassword(): string
    {
        return Str::lower(
            Str::random(10)
        );
    }
}
