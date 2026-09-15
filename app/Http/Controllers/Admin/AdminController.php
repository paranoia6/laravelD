<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Models\User;
use App\Services\AdminBlockService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * لیست Adminها
     */
    public function index(Request $request): View
    {
        $query = User::query()
            ->where('role', 'admin')
            ->withCount([
                'createdAccounts as total_accounts',

                'createdAccounts as active_accounts' => fn ($query) =>
                $query->where('status', 'active'),

                'createdAccounts as blocked_accounts' => fn ($query) =>
                $query->where('status', 'blocked'),

                'createdAccounts as expiring_accounts' => fn ($query) =>
                $query
                    ->where('status', 'active')
                    ->whereNotNull('expired_at')
                    ->whereBetween('expired_at', [
                        now(),
                        now()->copy()->addDays(7),
                    ]),

                'createdAccounts as today_accounts' => fn ($query) =>
                $query->whereDate('created_at', today()),

                'createdAccounts as month_accounts' => fn ($query) =>
                $query
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year),
            ]);

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%");
            });
        }

        $admins = $query
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.admins.index', compact('admins'));
    }


    /**
     * ساخت Admin
     */
    public function store(
        StoreAdminRequest $request
    ) {
        User::create([
            'email' =>
                $request
                    ->string('email')
                    ->toString(),

            'password' =>
                $request
                    ->string('password')
                    ->toString(),

            'role' =>
                'admin',

            'balance' =>
                0,

            'is_active' =>
                true,
        ]);


        return redirect()
            ->route('admin.admins')
            ->with(
                'success',
                'Admin با موفقیت ایجاد شد.'
            );
    }


    /**
     * شارژ Admin
     */
    public function credit(
        Request $request,
        User $user,
        WalletService $walletService
    ) {
        $validated = $request->validate([
            'amount' => [
                'required',
                'integer',
                'min:1',
            ],

            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);


        try {

            $walletService->credit(
                $user,
                (int) $validated['amount'],
                $request->user(),
                $validated['description']
                ?? null
            );


            return redirect()
                ->route('admin.admins')
                ->with(
                    'success',
                    'موجودی Admin با موفقیت شارژ شد.'
                );

        } catch (\Throwable $e) {

            return back()
                ->withErrors([
                    'amount' =>
                        $e->getMessage(),
                ])
                ->withInput();
        }
    }


    /**
     * اصلاح موجودی
     */
    public function adjustCredit(
        Request $request,
        User $user,
        WalletService $walletService
    ) {
        $validated = $request->validate([
            'amount' => [
                'required',
                'integer',
                'not_in:0',
            ],

            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);


        try {

            $walletService->adjustCredit(
                $user,
                (int) $validated['amount'],
                $validated['description']
                ?? null,
                $request->user()
            );


            return redirect()
                ->route('admin.admins')
                ->with(
                    'success',
                    'موجودی Admin با موفقیت اصلاح شد.'
                );

        } catch (\Throwable $e) {

            return back()
                ->withErrors([
                    'amount' =>
                        $e->getMessage(),
                ])
                ->withInput();
        }
    }


    /**
     * مسدودسازی Admin
     */
    public function block(
        Request $request,
        User $user,
        AdminBlockService $adminBlockService
    ) {
        $validated = $request->validate([
            'disable_accounts' => [
                'nullable',
                'boolean',
            ],
        ]);


        try {

            $result =
                $adminBlockService->block(
                    $user,
                    $request->user(),
                    (bool) (
                        $validated[
                        'disable_accounts'
                        ] ?? false
                    )
                );


            $accountsDisabled =
                (int) (
                    $result[
                    'accounts_disabled'
                    ] ?? 0
                );


            $message =
                $accountsDisabled > 0

                    ? "Admin مسدود شد و {$accountsDisabled} اکانت فعال او نیز مسدود شدند."

                    : 'Admin با موفقیت مسدود شد.';


            return redirect()
                ->route('admin.admins')
                ->with(
                    'success',
                    $message
                );

        } catch (\Throwable $e) {

            return back()
                ->withErrors([
                    'admin' =>
                        $e->getMessage(),
                ]);
        }
    }


    /**
     * رفع مسدودی Admin
     */
    public function unblock(
        Request $request,
        User $user,
        AdminBlockService $adminBlockService
    ) {
        try {

            $result =
                $adminBlockService->unblock(
                    $user,
                    $request->user()
                );


            $accountsReactivated =
                (int) (
                    $result[
                    'accounts_reactivated'
                    ] ?? 0
                );


            $message =
                $accountsReactivated > 0

                    ? "مسدودی Admin برداشته شد و {$accountsReactivated} اکانت نیز دوباره فعال شدند."

                    : 'مسدودی Admin با موفقیت برداشته شد.';


            return redirect()
                ->route('admin.admins')
                ->with(
                    'success',
                    $message
                );

        } catch (\Throwable $e) {

            return back()
                ->withErrors([
                    'admin' =>
                        $e->getMessage(),
                ]);
        }
    }


    /**
     * برای سازگاری با routeهای قدیمی
     */
    public function toggleStatus(
        User $user
    ) {
        return redirect()
            ->route('admin.admins')
            ->withErrors([
                'admin' =>
                    'برای تغییر وضعیت Admin فقط از مسدودسازی یا رفع مسدودی استفاده کنید.',
            ]);
    }
}
