<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Plan;
use App\Models\Support;
use App\Models\User;
use App\Services\AccountCreationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class AccountCreationServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(int $balance = 100000): User
    {
        return User::create([
            'email' => 'admin@example.com',
            'name' => 'Test Admin',
            'role' => 'admin',
            'balance' => $balance,
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
    }

    private function makeSupport(User $admin): Support
    {
        return Support::createWithLinks(
            $admin->id,
            'Test Support',
            ['telegram' => 'https://t.me/test_support']
        );
    }

    private function makeSpecialPlan(bool $active = true): Plan
    {
        return Plan::create([
            'type' => 'special',
            'duration_months' => 1,
            'price' => 109000,
            'is_active' => $active,
        ]);
    }

    public function test_test_account_uses_active_special_plan_and_is_free(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport($admin);
        $plan = $this->makeSpecialPlan();

        $account = app(AccountCreationService::class)->create(
            admin: $admin,
            username: 'testaccount123',
            password: 'secret123',
            plan: null,
            supportId: $support->id,
            deviceType: 1,
            accountType: 1,
            isTest: true,
        );

        $this->assertInstanceOf(Account::class, $account);
        $this->assertSame($plan->id, $account->plan_id);
        $this->assertTrue($account->is_test);
        $this->assertSame(2, $account->account_type);
        $this->assertSame(0, (int) $account->charged_amount);
        $this->assertNull($account->expired_at);
        $this->assertNull($account->first_login_date);

        $this->assertSame(
            100000,
            (int) $admin->fresh()->balance
        );

        $this->assertSame(1, Account::count());
    }

    public function test_test_account_fails_when_no_active_special_plan_exists(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport($admin);
        $this->makeSpecialPlan(active: false);

        try {
            app(AccountCreationService::class)->create(
                admin: $admin,
                username: 'testaccount456',
                password: 'secret123',
                plan: null,
                supportId: $support->id,
                deviceType: 1,
                accountType: 1,
                isTest: true,
            );

            $this->fail('Expected a RuntimeException was not thrown.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'برای ساخت اکانت تست، پلن ویژه فعال وجود ندارد.',
                $exception->getMessage()
            );
        }

        $this->assertSame(0, Account::count());
        $this->assertSame(
            100000,
            (int) $admin->fresh()->balance
        );
    }
}
