<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RenewAccountRequest;
use App\Http\Requests\Admin\StoreAccountRequest;
use App\Models\Account;
use App\Models\Plan;
use App\Models\Support;
use App\Services\AccountBlockService;
use App\Services\AccountCreationService;
use App\Services\AccountRenewalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;
use Throwable;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $search = trim(
            (string) $request->input('search', '')
        );

        $query = Account::query()
            ->with([
                'admin',
                'plan',
                'support',
            ])
            ->latest('id');

        if ($user->role->value === 'admin') {
            $query->where(
                'admin_id',
                $user->id
            );
        }

        if ($search !== '') {
            $query->where(
                'username',
                'like',
                '%' . $search . '%'
            );
        }

        $accounts = $query
            ->paginate(20)
            ->withQueryString();

        return view(
            'admin.accounts.index',
            compact(
                'accounts',
                'search'
            )
        );
    }

    public function create(Request $request): View
    {
        $user = $request->user();

        $plans = Plan::query()
            ->where('is_active', true)
            ->where(function ($query) use ($user) {
                $query
                    ->where(function ($query) {
                        $query
                            ->where('type', 'normal')
                            ->where('duration_months', 1);
                    })
                    ->orWhere(function ($query) {
                        $query->where('type', 'normal');
                    })
                    ->orWhere(function ($query) {
                        $query->where('type', 'special');
                    });
            })
            ->where(function ($query) {
                $query
                    ->whereNotNull('price')
                    ->orWhere(function ($query) {
                        $query
                            ->where('type', 'normal')
                            ->where('duration_months', 1);
                    });
            })
            ->orderByRaw("
                CASE
                    WHEN type = 'normal' THEN 1
                    WHEN type = 'special' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('duration_months')
            ->get();

        $supports = Support::query()
            ->where(
                'user_id',
                $user->id
            )
            ->where(function ($query) {
                $query
                    ->where('is_active', true)
                    ->orWhereNull('is_active');
            })
            ->orderBy('id')
            ->get();

        return view(
            'admin.accounts.create',
            [
                'plans' => $plans,
                'supports' => $supports,
                'currentBalance' => (int) (
                    $user->balance ?? 0
                ),
                'isSuperAdmin' =>
                    $user->role->value === 'super_admin',
            ]
        );
    }

    public function store(
        StoreAccountRequest $request,
        AccountCreationService $creationService
    ): RedirectResponse {
        $validated = $request->validated();

        $user = $request->user();

        $isTest = (bool) (
            $validated['is_test'] ?? false
        );

        /*
         * برای Test، Request خودش plan_id را به
         * پلن عادی ۱ ماهه تبدیل کرده است.
         */
        $plan = Plan::query()
            ->where('is_active', true)
            ->when(
                $isTest,
                function ($query) {
                    $query
                        ->where('type', 'normal')
                        ->where('duration_months', 1);
                }
            )
            ->whereNotNull('id')
            ->when(
                ! $isTest,
                function ($query) use ($validated) {
                    $query
                        ->whereKey(
                            $validated['plan_id']
                        )
                        ->whereNotNull('price');
                }
            )
            ->when(
                $isTest,
                function ($query) use ($validated) {
                    $query->whereKey(
                        $validated['plan_id']
                    );
                }
            )
            ->first();

        if (! $plan) {
            return back()
                ->withErrors([
                    'plan_id' =>
                        $isTest
                            ? 'پلن عادی ۱ ماهه برای ساخت اکانت تست وجود ندارد.'
                            : 'پلن انتخاب‌شده معتبر یا فعال نیست.',
                ])
                ->withInput();
        }

        if (! $isTest) {
            $expectedType =
                (int) $validated['account_type'] === 2
                    ? 'special'
                    : 'normal';

            if ($plan->type !== $expectedType) {
                return back()
                    ->withErrors([
                        'plan_id' =>
                            'پلن انتخاب‌شده با نوع اکانت هماهنگ نیست.',
                    ])
                    ->withInput();
            }
        }

        $support = Support::query()
            ->whereKey(
                $validated['support_id']
            )
            ->where(
                'user_id',
                $user->id
            )
            ->where(function ($query) {
                $query
                    ->where('is_active', true)
                    ->orWhereNull('is_active');
            })
            ->first();

        if (! $support || ! $support->hasLinks()) {
            return back()
                ->withErrors([
                    'support_id' =>
                        'پشتیبانی انتخاب‌شده متعلق به حساب شما نیست، غیرفعال است یا اطلاعاتی برای آن ثبت نشده است.',
                ])
                ->withInput();
        }

        $quantity = (int) (
            $validated['quantity'] ?? 1
        );

        /*
         * Test رایگان است.
         */
        $totalPrice = $isTest
            ? 0
            : (int) $plan->price * $quantity;

        if (
            ! $isTest
            && $user->role->value === 'admin'
            && (int) $user->balance < $totalPrice
        ) {
            return back()
                ->withErrors([
                    'quantity' =>
                        'موجودی شما برای ساخت این تعداد اکانت کافی نیست. '
                        . 'موجودی فعلی: '
                        . number_format(
                            (int) $user->balance
                        )
                        . ' تومان',
                ])
                ->withInput();
        }

        $createdAccounts = [];

        try {
            DB::transaction(function () use (
                &$createdAccounts,
                $creationService,
                $user,
                $plan,
                $support,
                $validated,
                $quantity,
                $isTest
            ) {
                if ($isTest && $user->role->value === 'admin') {
                    $lockedAdmin = $user->newQuery()
                        ->whereKey($user->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $existingTestCount = Account::query()
                        ->where('admin_id', $lockedAdmin->id)
                        ->where('is_test', true)
                        ->count();

                    if ($existingTestCount + $quantity > 5) {
                        throw new RuntimeException(
                            'هر Admin فقط ۵ اکانت تست می‌تواند بسازد. سهمیه باقی‌مانده: '
                            . max(0, 5 - $existingTestCount)
                        );
                    }
                }

                $mode = $validated['username_mode'] ?? 'random';
                $prefix = trim(
                    (string) ($validated['username_prefix'] ?? '')
                );

                for ($i = 0; $i < $quantity; $i++) {
                    if ($mode === 'prefix') {
                        $username = $prefix;
                        $sequence = 2;

                        while (
                        Account::query()
                            ->where('username', $username)
                            ->exists()
                        ) {
                            $username = $prefix . $sequence;
                            $sequence++;
                        }
                    } else {
                        $username =
                            $creationService->generateUsername();
                    }

                    $password =
                        $creationService->generatePassword();

                    $account =
                        $creationService->create(
                            $user,
                            $username,
                            $password,
                            $plan,
                            (int) $support->id,
                            (int) $validated['device_type'],
                            $isTest
                                ? 1
                                : (int) $validated['account_type'],
                            $isTest
                        );

                    $createdAccounts[] = [
                        'id' => $account->id,
                        'username' => $username,
                        'password' => $password,
                        'plan_id' => $account->plan_id,
                        'duration_months' =>
                            (int) $account->plan->duration_months,
                        'plan_price' =>
                            (int) $account->charged_amount,
                        'support_id' =>
                            (int) $account->support_id,
                        'support' =>
                            $account->support?->name ?? '---',
                        'device_type' =>
                            (int) $account->device_type,
                        'account_type' =>
                            $isTest
                                ? 1
                                : (int) $validated['account_type'],
                        'charged_amount' =>
                            (int) $account->charged_amount,
                        'is_test' =>
                            (bool) $account->is_test,
                        'created_at' =>
                            $account->created_at?->toDateTimeString(),
                        'expired_at' =>
                            $account->expired_at?->toDateTimeString(),
                    ];
                }
            });

            return redirect()
                ->route('admin.accounts.result')
                ->with(
                    'created_accounts',
                    $createdAccounts
                );

        } catch (RuntimeException $e) {
            return back()
                ->withErrors([
                    'account' =>
                        $e->getMessage(),
                ])
                ->withInput();

        } catch (Throwable $e) {
            report($e);

            return back()
                ->withErrors([
                    'account' =>
                        'در ساخت اکانت خطایی رخ داد. لطفاً دوباره تلاش کنید.',
                ])
                ->withInput();
        }
    }

    public function result(
        Request $request
    ): View {
        $createdAccounts =
            $request->session()->get(
                'created_accounts',
                []
            );

        $request->session()->forget(
            'created_accounts'
        );

        return view(
            'admin.accounts.result',
            compact('createdAccounts')
        );
    }

    public function show(
        Request $request,
        Account $account
    ): View {
        $user = $request->user();

        if (
            $user->role->value === 'admin'
            && (int) $account->admin_id
            !== (int) $user->id
        ) {
            abort(403);
        }

        $account->load([
            'admin',
            'plan',
            'support',
        ]);

        $renewalPlans = Plan::query()
            ->where('is_active', true)
            ->whereNotNull('price')
            ->when(
                $account->plan?->type,
                function ($query, $type) {
                    $query->where(
                        'type',
                        $type
                    );
                }
            )
            ->orderBy('duration_months')
            ->get();

        return view(
            'admin.accounts.show',
            compact(
                'account',
                'renewalPlans'
            )
        );
    }

    public function renew(
        RenewAccountRequest $request,
        Account $account,
        AccountRenewalService $renewalService
    ): RedirectResponse {
        $user = $request->user();

        if (
            $user->role->value === 'admin'
            && (int) $account->admin_id
            !== (int) $user->id
        ) {
            abort(403);
        }

        $plan = Plan::query()
            ->whereKey(
                $request->integer('plan_id')
            )
            ->where('is_active', true)
            ->whereNotNull('price')
            ->first();

        if (! $plan) {
            return back()
                ->withErrors([
                    'plan_id' =>
                        'پلن تمدید معتبر نیست.',
                ]);
        }

        if (
            $account->plan?->type
            && $plan->type
            !== $account->plan->type
        ) {
            return back()
                ->withErrors([
                    'plan_id' =>
                        'نوع پلن تمدید باید با نوع اکانت فعلی یکسان باشد.',
                ]);
        }

        try {
            $renewedAccount =
                $renewalService->renew(
                    $user,
                    $account,
                    $plan
                );

            return redirect()
                ->route(
                    'admin.accounts.show',
                    $renewedAccount
                )
                ->with(
                    'success',
                    'اکانت '
                    . $renewedAccount->username
                    . ' با موفقیت به مدت '
                    . $plan->duration_months
                    . ' ماه تمدید شد. '
                    . 'تاریخ انقضای جدید: '
                    . jalali_date(
                        $renewedAccount->expired_at,
                        'Y/m/d H:i'
                    )
                );

        } catch (RuntimeException $e) {
            return back()
                ->withErrors([
                    'renew' =>
                        $e->getMessage(),
                ]);

        } catch (Throwable $e) {
            report($e);

            return back()
                ->withErrors([
                    'renew' =>
                        'در تمدید اکانت خطایی رخ داد. لطفاً دوباره تلاش کنید.',
                ]);
        }
    }

    public function block(
        Request $request,
        Account $account,
        AccountBlockService $blockService
    ): RedirectResponse {
        $user = $request->user();

        if (
            $user->role->value === 'admin'
            && (int) $account->admin_id
            !== (int) $user->id
        ) {
            abort(403);
        }

        if (
            $account->status
            !== Account::STATUS_ACTIVE
        ) {
            return back()
                ->withErrors([
                    'account' =>
                        'این اکانت در حال حاضر فعال نیست.',
                ]);
        }

        $confirmOver72Hours =
            $request->boolean(
                'confirm_over_72_hours'
            );

        try {
            $result =
                $blockService->block(
                    $account,
                    $user,
                    $confirmOver72Hours
                );

            $under72Hours =
                (bool) (
                    $result['under_72_hours']
                    ?? false
                );

            $refundAmount =
                (int) (
                    $result['refund_amount']
                    ?? $result['refunded']
                    ?? 0
                );

            if ($under72Hours) {
                if ($refundAmount > 0) {
                    return redirect()
                        ->route(
                            'admin.accounts'
                        )
                        ->with(
                            'success',
                            "اکانت {$account->username} مسدود شد و مبلغ "
                            . number_format(
                                $refundAmount
                            )
                            . " تومان به موجودی Admin برگشت داده شد."
                        );
                }

                return redirect()
                    ->route(
                        'admin.accounts'
                    )
                    ->with(
                        'success',
                        "اکانت {$account->username} مسدود شد."
                    );
            }

            return redirect()
                ->route(
                    'admin.accounts'
                )
                ->with(
                    'success',
                    "اکانت {$account->username} مسدود شد. "
                    . "به دلیل گذشت بیش از ۷۲ ساعت، مبلغی برگشت داده نشد."
                );

        } catch (RuntimeException $e) {
            return back()
                ->withErrors([
                    'account' =>
                        $e->getMessage(),
                ]);

        } catch (Throwable $e) {
            report($e);

            return back()
                ->withErrors([
                    'account' =>
                        'در مسدودسازی اکانت خطایی رخ داد. لطفاً دوباره تلاش کنید.',
                ]);
        }
    }
}
