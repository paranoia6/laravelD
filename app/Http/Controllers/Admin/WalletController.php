<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        if (! in_array($user->role->value, ['admin', 'super_admin'], true)) {
            abort(403);
        }

        if ($user->role->value === 'admin') {

            $transactions = WalletTransaction::query()
                ->where('user_id', $user->id)
                ->with('creator')
                ->latest('id')
                ->paginate(30);

            return view('admin.wallet.index', [
                'balance' => (int) $user->balance,
                'transactions' => $transactions,
                'admins' => collect(),
            ]);
        }

        $admins = User::query()
            ->where('role', 'admin')
            ->orderBy('email')
            ->get([
                'id',
                'email',
                'balance',
                'is_active',
            ]);

        $transactions = WalletTransaction::query()
            ->with([
                'user',
                'creator',
            ])
            ->latest('id')
            ->paginate(50);

        return view('admin.wallet.index', [
            'balance' => null,
            'transactions' => $transactions,
            'admins' => $admins,
        ]);
    }
}
