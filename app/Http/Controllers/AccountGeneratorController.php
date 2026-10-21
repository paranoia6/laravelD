<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountGeneratorController extends Controller
{
    public function create()
    {
        return view('accounts.generator');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 401);

        $validated = $request->validate([
            'supporter_id' => [
                'nullable',
                'integer',
                'exists:supports,id',
            ],
            'username_prefix' => [
                'required',
                'string',
                'max:200',
            ],
            'count' => [
                'required',
                'integer',
                'min:1',
                'max:1000',
            ],
            'account_type' => [
                'required',
                'in:1,2',
            ],
        ]);

        $planId = Plan::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->value('id');

        if ($planId === null) {
            return back()
                ->withErrors([
                    'count' => 'برای ساخت اکانت ابتدا یک پلن فعال ثبت کنید.',
                ])
                ->withInput();
        }

        $accounts = [];
        $password = Hash::make('61490');

        $usedUsernames = [];

        for ($i = 1; $i <= $validated['count']; $i++) {
            do {
                $username = $validated['username_prefix'] . Str::lower(Str::random(8));
            } while (
                Account::where('username', $username)->exists()
                || isset($usedUsernames[$username])
            );

            $usedUsernames[$username] = true;

            $accounts[] = [
                'admin_id' => $user->id,
                'plan_id' => $planId,
                'device_type' => 1,
                'supporter_id' => $validated['supporter_id'],
                'username' => $username,
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
