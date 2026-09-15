<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $accountQuery = Account::query();

        if ($user->role->value === 'admin') {
            $accountQuery->where('admin_id', $user->id);
        }

        $today = now()->startOfDay();
        $tomorrow = now()->copy()->addDay()->startOfDay();

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->copy()->addMonth()->startOfMonth();

        $stats = [
            'total' => (clone $accountQuery)->count(),

            'activated' => (clone $accountQuery)
                ->whereNotNull('first_login_at')
                ->count(),

            'not_activated' => (clone $accountQuery)
                ->whereNull('first_login_at')
                ->count(),

            'today_created' => (clone $accountQuery)
                ->where('created_at', '>=', $today)
                ->where('created_at', '<', $tomorrow)
                ->count(),

            'month_created' => (clone $accountQuery)
                ->where('created_at', '>=', $monthStart)
                ->where('created_at', '<', $monthEnd)
                ->count(),

            'expiring' => (clone $accountQuery)
                ->where('status', Account::STATUS_ACTIVE)
                ->whereNotNull('first_login_at')
                ->whereNotNull('expired_at')
                ->whereBetween('expired_at', [
                    now(),
                    now()->copy()->addDays(7),
                ])
                ->count(),

            'expired' => (clone $accountQuery)
                ->whereNotNull('expired_at')
                ->where('expired_at', '<', now())
                ->count(),

            'blocked' => (clone $accountQuery)
                ->where('status', Account::STATUS_BLOCKED)
                ->count(),

            'blocked_under_72' => (clone $accountQuery)
                ->where('status', Account::STATUS_BLOCKED)
                ->whereNotNull('activated_at')
                ->whereNotNull('blocked_at')
                ->whereRaw(
                    'blocked_at <= DATE_ADD(activated_at, INTERVAL 72 HOUR)'
                )
                ->count(),

            'blocked_over_72' => (clone $accountQuery)
                ->where('status', Account::STATUS_BLOCKED)
                ->whereNotNull('activated_at')
                ->whereNotNull('blocked_at')
                ->whereRaw(
                    'blocked_at > DATE_ADD(activated_at, INTERVAL 72 HOUR)'
                )
                ->count(),
        ];

        $recentAccounts = (clone $accountQuery)
            ->with(['admin', 'plan', 'support'])
            ->latest('id')
            ->limit(50)
            ->get();

        $admins = collect();

        if ($user->role->value === 'super_admin') {

            $admins = User::query()
                ->where('role', 'admin')
                ->withCount([
                    'createdAccounts as total_accounts',

                    'createdAccounts as activated_accounts' => function ($query) {
                        $query->whereNotNull('first_login_at');
                    },

                    'createdAccounts as not_activated_accounts' => function ($query) {
                        $query->whereNull('first_login_at');
                    },

                    'createdAccounts as expiring_accounts' => function ($query) {
                        $query
                            ->where('status', Account::STATUS_ACTIVE)
                            ->whereNotNull('first_login_at')
                            ->whereNotNull('expired_at')
                            ->whereBetween('expired_at', [
                                now(),
                                now()->copy()->addDays(7),
                            ]);
                    },

                    'createdAccounts as blocked_accounts' => function ($query) {
                        $query->where(
                            'status',
                            Account::STATUS_BLOCKED
                        );
                    },
                ])
                ->orderBy('id')
                ->get();
        }

        return view('admin.reports.index', [
            'stats' => $stats,
            'recentAccounts' => $recentAccounts,
            'admins' => $admins,
        ]);
    }
}
