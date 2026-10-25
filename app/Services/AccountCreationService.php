<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Plan;
use App\Models\Support;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class AccountCreationService
{
    private const NORMAL_ADMIN_TEST_LIMIT = 3;
    private const TEST_WINDOW_DAYS = 30;

    public function __construct(
        protected WalletService $walletService
    )
    {
    }

    public function create(
        User   $admin,
        string $username,
        string $password,
        ?Plan  $plan,
        int    $supportId,
        int    $deviceType,
        int    $accountType,
        bool   $isTest = false,
         $encode = null
    ): Account
    {
        return DB::transaction(function () use (
            $admin,
            $username,
            $password,
            $plan,
            $supportId,
            $deviceType,
            $accountType,
            $isTest,
            $encode
        ) {
            $lockedAdmin = User::query()
                ->whereKey($admin->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$lockedAdmin->is_active) {
                throw new RuntimeException(
                    'حساب Admin شما غیرفعال است.'
                );
            }

            if (!in_array(
                $lockedAdmin->roleValue(),
                ['admin', 'super_admin'],
                true
            )) {
                throw new RuntimeException(
                    'دسترسی ایجاد اکانت برای این کاربر وجود ندارد.'
                );
            }

            /*
             * اکانت تست:
             * Admin عادی: حداکثر ۳ عدد در هر ۳۰ روز شناور.
             * Super Admin: بدون محدودیت.
             */
            if (
                $isTest
                && $lockedAdmin->roleValue() === 'admin'
            ) {
                $windowStart = now()->subDays(
                    self::TEST_WINDOW_DAYS
                );

                $testAccountsCount = Account::query()
                    ->where('admin_id', $lockedAdmin->id)
                    ->where('is_test', true)
                    ->where('created_at', '>=', $windowStart)
                    ->count();

                if (
                    $testAccountsCount
                    >= self::NORMAL_ADMIN_TEST_LIMIT
                ) {
                    throw new RuntimeException(
                        'سهمیه اکانت تست شما در ۳۰ روز اخیر تکمیل شده است. هر Admin در هر ۳۰ روز فقط ۳ اکانت تست می‌تواند بسازد.'
                    );
                }
            }

            /*
             * Test هیچ ارتباطی با Plan ندارد.
             */
            if (!$isTest) {
                if (!$plan) {
                    throw new RuntimeException(
                        'پلن اکانت انتخاب نشده است.'
                    );
                }

                $lockedPlan = Plan::query()
                    ->whereKey($plan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!$lockedPlan->is_active) {
                    throw new RuntimeException(
                        'این پلن در حال حاضر غیرفعال است.'
                    );
                }

                if ($lockedPlan->price === null) {
                    throw new RuntimeException(
                        'قیمت این پلن هنوز توسط Super Admin تعیین نشده است.'
                    );
                }

                $expectedType = $accountType === 2
                    ? 'special'
                    : 'normal';

                if ($lockedPlan->type !== $expectedType) {
                    throw new RuntimeException(
                        'نوع پلن با نوع اکانت انتخاب‌شده هماهنگ نیست.'
                    );
                }
            } else {
                $lockedPlan = Plan::query()
                    ->where('is_active', true)
                    ->where('type', 'special')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->first();

                if (!$lockedPlan) {
                    throw new RuntimeException(
                        'برای ساخت اکانت تست، پلن ویژه فعال وجود ندارد.'
                    );
                }

                $accountType = 2;
            }

            $support = Support::query()
                ->whereKey($supportId)
                ->where('user_id', $lockedAdmin->id)
                ->where('is_active', true)
                ->first();

            if (!$support || !$support->hasLinks()) {
                throw new RuntimeException(
                    'پشتیبانی انتخاب‌شده ثبت نشده یا اطلاعات آن کامل نیست.'
                );
            }

            $username = trim($username);

            if ($username === '') {
                throw new RuntimeException(
                    'نام کاربری نمی‌تواند خالی باشد.'
                );
            }

            if (!preg_match('/^[A-Za-z0-9]+$/', $username)) {
                throw new RuntimeException(
                    'نام کاربری فقط باید شامل حروف انگلیسی و اعداد باشد.'
                );
            }

            if (
                Account::query()
                    ->where('username', $username)
                    ->exists()
            ) {
                throw new RuntimeException(
                    "نام کاربری {$username} قبلاً استفاده شده است."
                );
            }

            /*
             * Test کاملاً رایگان است.
             */
            $price = $isTest
                ? 0
                : (int)$lockedPlan->price;

            if (
                !$isTest
                && $lockedAdmin->roleValue() === 'admin'
            ) {
                if (
                    (int)$lockedAdmin->balance < $price
                ) {
                    throw new RuntimeException(
                        'موجودی شما برای ایجاد این اکانت کافی نیست. موجودی فعلی: '
                        . number_format((int)$lockedAdmin->balance)
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

            $account = Account::create([
                'admin_id' => $lockedAdmin->id,

                /*
                 * برای Test نیز پلن ویژه فعال ذخیره می‌شود؛
                 * هزینه تست همچنان صفر است.
                 */
                'plan_id' => $lockedPlan->id,

                'supporter_id' => $support->id,
                'device_type' => $deviceType,
                'account_type' => $accountType,

                'username' => $username,
                'password' => Hash::make($password),
                'pass' => $password,
                'expired_type' => $isTest ? 1 : $lockedPlan->duration_months,


                'charged_amount' => $price,
                'is_test' => $isTest,

                /*
                 * اعتبار Test و معمولی هر دو از اولین ورود
                 * شروع می‌شود.
                 */
                'first_login_date' => null,
                'expired_at' => null,

                'status' => Account::STATUS_ACTIVE,
                'encode' => $encode
            ]);

            $account->load([
                'plan',
                'support',
            ]);

            return $account;
        });
    }

    public function generateUsername(): string
{
    do {
        $username = Str::lower(Str::random(10));
    } while (
        Account::query()
            ->where('username', $username)
            ->exists()
    );

    return $username;
}

    public function generatePassword(): string
    {
        return Str::lower(
            Str::random(10)
        );
    }
}
