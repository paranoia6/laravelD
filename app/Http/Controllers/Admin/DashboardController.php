<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Accounts Query
        |--------------------------------------------------------------------------
        */

        $accountQuery = Account::query();

        if ($user->role->value === 'admin') {
            $accountQuery->where('admin_id', $user->id);
        }

        /*
        |--------------------------------------------------------------------------
        | Dashboard Statistics
        |--------------------------------------------------------------------------
        */

        $stats = [

            // کل اکانت‌های ساخته‌شده
            'total_accounts' => (clone $accountQuery)
                ->count(),

            // فعال‌شده = اولین لاگین انجام شده
            'active_accounts' => (clone $accountQuery)
                ->whereNotNull('first_login_date')
                ->count(),

            // نام جدید برای استفاده‌های بعدی
            'activated_accounts' => (clone $accountQuery)
                ->whereNotNull('first_login_date')
                ->count(),

            // هنوز اولین لاگین انجام نشده
            'not_activated_accounts' => (clone $accountQuery)
                ->whereNull('first_login_date')
                ->count(),

            // ساخته‌شده امروز
            'today_accounts' => (clone $accountQuery)
                ->whereDate('created_at', today())
                ->count(),

            // ساخته‌شده این ماه
            'month_accounts' => (clone $accountQuery)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            // در حال انقضا = حداکثر 7 روز مانده
            'expiring_accounts' => (clone $accountQuery)
                ->where('status', Account::STATUS_ACTIVE)
                ->whereNotNull('first_login_date')
                ->whereNotNull('expired_at')
                ->whereBetween('expired_at', [
                    now(),
                    now()->copy()->addDays(7),
                ])
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Blocked Under 72 Hours
            |--------------------------------------------------------------------------
            */

            'blocked_under_72_hours' => (clone $accountQuery)
                ->where('status', Account::STATUS_BLOCKED)
                ->whereNotNull('first_login_date')
                ->whereNotNull('blocked_at')
                ->whereRaw(
                    'blocked_at <= DATE_ADD(first_login_date, INTERVAL 72 HOUR)'
                )
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Blocked Over 72 Hours
            |--------------------------------------------------------------------------
            */

            'blocked_over_72_hours' => (clone $accountQuery)
                ->where('status', Account::STATUS_BLOCKED)
                ->whereNotNull('first_login_date')
                ->whereNotNull('blocked_at')
                ->whereRaw(
                    'blocked_at > DATE_ADD(first_login_date, INTERVAL 72 HOUR)'
                )
                ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Total Sales - Super Admin Only
        |--------------------------------------------------------------------------
        |
        | فروش بر اساس مبلغ شارژ واقعی پنل Adminها محاسبه می‌شود.
        | هر WalletTransaction از نوع credit یعنی مبلغی که به پنل
        | یک Admin شارژ شده و به عنوان فروش ثبت می‌شود.
        |
        */

        if ($user->role->value === 'super_admin') {
            $stats['totalSales'] = (int) WalletTransaction::query()
                ->where('type', 'credit')
                ->whereHas('user', function ($query) {
                    $query->where('role', 'admin');
                })
                ->sum('amount');
        }

        /*
        |--------------------------------------------------------------------------
        | Recent Accounts
        |--------------------------------------------------------------------------
        */

        $recentAccounts = (clone $accountQuery)
            ->with([
                'admin',
                'plan',
                'support',
            ])
            ->latest('id')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Super Admin Statistics
        |--------------------------------------------------------------------------
        */

        $adminStats = collect();

        if ($user->role->value === 'super_admin') {

            $admins = User::query()
                ->where('role', 'admin')
                ->withCount([

                    // کل
                    'createdAccounts as total_accounts',

                    // فعال‌شده = اولین لاگین
                    'createdAccounts as activated_accounts' => function ($query) {
                        $query->whereNotNull('first_login_date');
                    },

                    // فعال‌نشده
                    'createdAccounts as not_activated_accounts' => function ($query) {
                        $query->whereNull('first_login_date');
                    },

                    // کل مسدودها
                    'createdAccounts as blocked_accounts' => function ($query) {
                        $query->where(
                            'status',
                            Account::STATUS_BLOCKED
                        );
                    },

                    // مسدودی زیر 72 ساعت
                    'createdAccounts as blocked_under_72h' => function ($query) {
                        $query
                            ->where(
                                'status',
                                Account::STATUS_BLOCKED
                            )
                            ->whereNotNull('first_login_date')
                            ->whereNotNull('blocked_at')
                            ->whereRaw(
                                'blocked_at <= DATE_ADD(first_login_date, INTERVAL 72 HOUR)'
                            );
                    },

                    // مسدودی بیشتر از 72 ساعت
                    'createdAccounts as blocked_over_72h' => function ($query) {
                        $query
                            ->where(
                                'status',
                                Account::STATUS_BLOCKED
                            )
                            ->whereNotNull('first_login_date')
                            ->whereNotNull('blocked_at')
                            ->whereRaw(
                                'blocked_at > DATE_ADD(first_login_date, INTERVAL 72 HOUR)'
                            );
                    },

                    // امروز
                    'createdAccounts as today_accounts' => function ($query) {
                        $query->whereDate(
                            'created_at',
                            today()
                        );
                    },

                    // این ماه
                    'createdAccounts as month_accounts' => function ($query) {
                        $query
                            ->whereMonth(
                                'created_at',
                                now()->month
                            )
                            ->whereYear(
                                'created_at',
                                now()->year
                            );
                    },

                    // در حال انقضا
                    'createdAccounts as expiring_accounts' => function ($query) {
                        $query
                            ->where(
                                'status',
                                Account::STATUS_ACTIVE
                            )
                            ->whereNotNull('first_login_date')
                            ->whereNotNull('expired_at')
                            ->whereBetween('expired_at', [
                                now(),
                                now()->copy()->addDays(7),
                            ]);
                    },
                ])
                ->orderBy('id')
                ->get();

            $adminStats = $admins;

            $stats['total_admins'] = $admins->count();

            $stats['active_admins'] = $admins
                ->where('is_active', true)
                ->count();

            $stats['inactive_admins'] = $admins
                ->where('is_active', false)
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentAccounts' => $recentAccounts,
            'adminStats' => $adminStats,
        ]);
    }
}
