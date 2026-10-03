<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountGeneratorController extends Controller
{
    public function create()
    {
        return view('accounts.generator');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supporter_id' => ['nullable', 'integer'],
            'username_prefix' => ['required', 'string', 'max:200'],
            'count' => ['required', 'integer', 'min:1', 'max:1000'],
            'account_type' => ['required', 'in:1,2'],
        ]);

        $accounts = [];
        $password = Hash::make('61490');

        for ($i = 1; $i <= $validated['count']; $i++) {
            $accounts[] = [
                'admin_id' => 1,
                'plan_id' => 1,
                'device_type' => 1,
                'supporter_id' => $validated['supporter_id'],
                'username' => $validated['username_prefix'] . $i,
                'password' => $password,
                'expired_type' => 1,
                'account_type' => $validated['account_type'],
            ];
        }

        Account::insert($accounts);

        $createdAccounts = collect($accounts)->map(function ($account) {
            return [
                'username' => $account['username'],
                'password' => '61490',
            ];
        });

        return view('accounts.generator', [
            'createdAccounts' => $createdAccounts,
        ]);
    }
}
