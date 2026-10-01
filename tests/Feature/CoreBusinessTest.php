<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Plan;
use App\Models\Support;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\AccountCreationService;
use App\Services\AccountRenewalService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class CoreBusinessTest extends TestCase
{
    use RefreshDatabase;

    protected function makeSuperAdmin(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
            'balance' => 0,
            'expired_type' => 1,
        ], $attributes));
    }

    protected function makeAdmin(array $attributes = []): User
    {
        static $counter = 0;
        $counter++;

        return User::create(array_merge([
            'name' => 'Admin ' . $counter,
            'email' => 'admin' . $counter . '@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'balance' => 1_000_000,
            'expired_type' => 1,
        ], $attributes));
    }

    protected function makePlan(array $attributes = []): Plan
    {
        static $counter = 0;
        $counter++;

        return Plan::create(array_merge([
            'name' => 'Plan ' . $counter,
            'duration_months' => 1,
            'price' => 100_000,
            'is_active' => true,
            'type' => 'normal',
        ], $attributes));
    }

    protected function makeSupport(User $user): Support
    {
        return Support::create([
            'user_id' => $user->id,
            'type' => 'telegram',
            'title' => 'Test Support',
            'is_active' => true,
            'meta_data' => [
                'links' => [
                    'telegram' => 'https://t.me/test',
                ],
            ],
        ]);
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login(): void
    {
        $admin = $this->makeAdmin([
            'email' => 'login@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($admin);
    }

    public function test_invalid_login_is_rejected(): void
    {
        $this->makeAdmin([
            'email' => 'login@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => 'login@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_wallet_credit_updates_balance_and_creates_transaction(): void
    {
        $admin = $this->makeAdmin([
            'balance' => 100_000,
        ]);

        $actor = $this->makeSuperAdmin();

        app(WalletService::class)->credit(
            $admin,
            50_000,
            $actor,
            'شارژ تست'
        );

        $this->assertSame(150_000, (int) $admin->fresh()->balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $admin->id,
            'type' => 'credit',
            'amount' => 50_000,
            'balance_after' => 150_000,
            'created_by' => $actor->id,
            'description' => 'شارژ تست',
        ]);
    }

    public function test_wallet_debit_updates_balance_and_creates_transaction(): void
    {
        $admin = $this->makeAdmin([
            'balance' => 200_000,
        ]);

        $actor = $this->makeSuperAdmin();

        app(WalletService::class)->debit(
            $admin,
            75_000,
            $actor,
            'کسر تست'
        );

        $this->assertSame(125_000, (int) $admin->fresh()->balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $admin->id,
            'type' => 'debit',
            'amount' => 75_000,
            'balance_after' => 125_000,
            'created_by' => $actor->id,
            'description' => 'کسر تست',
        ]);
    }

    public function test_wallet_debit_rejects_insufficient_balance(): void
    {
        $admin = $this->makeAdmin([
            'balance' => 10_000,
        ]);

        $this->expectException(RuntimeException::class);

        app(WalletService::class)->debit(
            $admin,
            20_000
        );
    }

    public function test_wallet_adjust_credit_uses_correct_created_by_and_description(): void
    {
        $admin = $this->makeAdmin([
            'balance' => 500_000,
        ]);

        $actor = $this->makeSuperAdmin();

        app(WalletService::class)->adjustCredit(
            $admin,
            100_000,
            $actor,
            'اصلاح موجودی تست'
        );

        $this->assertSame(600_000, (int) $admin->fresh()->balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $admin->id,
            'type' => 'credit_adjustment',
            'amount' => 100_000,
            'balance_after' => 600_000,
            'created_by' => $actor->id,
            'description' => 'اصلاح موجودی تست',
        ]);
    }

    public function test_account_creation_charges_admin_wallet(): void
    {
        $admin = $this->makeAdmin([
            'balance' => 500_000,
        ]);

        $plan = $this->makePlan([
            'price' => 100_000,
            'duration_months' => 1,
        ]);

        $support = $this->makeSupport($admin);

        $account = app(AccountCreationService::class)->create(
            $admin,
            'testuser1',
            'secret123',
            $plan,
            $support->id,
            1,
            1,
            false
        );

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'admin_id' => $admin->id,
            'plan_id' => $plan->id,
            'support_id' => $support->id,
            'device_type' => 1,
            'username' => 'testuser1',
            'charged_amount' => 100_000,
            'is_test' => false,
            'status' => Account::STATUS_ACTIVE,
        ]);

        $this->assertSame(400_000, (int) $admin->fresh()->balance);
    }

    public function test_account_creation_rejects_inactive_admin(): void
    {
        $admin = $this->makeAdmin([
            'is_active' => false,
        ]);

        $plan = $this->makePlan();
        $support = $this->makeSupport($admin);

        $this->expectException(RuntimeException::class);

        app(AccountCreationService::class)->create(
            $admin,
            'inactive-admin-account',
            'secret123',
            $plan,
            $support->id,
            1,
            1
        );
    }

    public function test_account_creation_rejects_insufficient_balance(): void
    {
        $admin = $this->makeAdmin([
            'balance' => 10_000,
        ]);

        $plan = $this->makePlan([
            'price' => 100_000,
        ]);

        $support = $this->makeSupport($admin);

        $this->expectException(RuntimeException::class);

        app(AccountCreationService::class)->create(
            $admin,
            'no-balance-account',
            'secret123',
            $plan,
            $support->id,
            1,
            1
        );
    }

    public function test_account_creation_rejects_duplicate_username(): void
    {
        $admin = $this->makeAdmin();

        $plan = $this->makePlan();
        $support = $this->makeSupport($admin);

        Account::create([
            'admin_id' => $admin->id,
            'plan_id' => $plan->id,
            'support_id' => $support->id,
            'device_type' => 1,
            'username' => 'duplicate-user',
            'password' => 'secret',
            'charged_amount' => 0,
            'is_test' => false,
            'first_login_date' => null,
            'expired_at' => null,
            'status' => Account::STATUS_ACTIVE,
        ]);

        $this->expectException(RuntimeException::class);

        app(AccountCreationService::class)->create(
            $admin,
            'duplicate-user',
            'secret123',
            $plan,
            $support->id,
            1,
            1
        );
    }

    public function test_account_creation_generates_username_and_password(): void
    {
        $admin = $this->makeAdmin();
        $service = app(AccountCreationService::class);

        $username = $service->generateUsername();
        $password = $service->generatePassword();

        $this->assertMatchesRegularExpression('/^user[0-9]+$/', $username);
        $this->assertMatchesRegularExpression('/^[a-z0-9]{10}$/', $password);
    }

    public function test_admin_only_sees_own_accounts(): void
    {
        $admin1 = $this->makeAdmin();
        $admin2 = $this->makeAdmin();

        $plan = $this->makePlan();

        $account1 = Account::create([
            'admin_id' => $admin1->id,
            'plan_id' => $plan->id,
            'device_type' => 1,
            'username' => 'admin-one-account',
            'password' => 'secret',
            'charged_amount' => 100_000,
            'is_test' => false,
            'status' => Account::STATUS_ACTIVE,
        ]);

        Account::create([
            'admin_id' => $admin2->id,
            'plan_id' => $plan->id,
            'device_type' => 1,
            'username' => 'admin-two-account',
            'password' => 'secret',
            'charged_amount' => 100_000,
            'is_test' => false,
            'status' => Account::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin1)
            ->get(route('admin.accounts'))
            ->assertOk()
            ->assertSee($account1->username)
            ->assertDontSee('admin-two-account');
    }

    public function test_super_admin_can_see_all_accounts(): void
    {
        $superAdmin = $this->makeSuperAdmin();
        $admin = $this->makeAdmin();
        $plan = $this->makePlan();

        Account::create([
            'admin_id' => $admin->id,
            'plan_id' => $plan->id,
            'device_type' => 1,
            'username' => 'visible-to-super',
            'password' => 'secret',
            'charged_amount' => 100_000,
            'is_test' => false,
            'status' => Account::STATUS_ACTIVE,
        ]);

        $this->actingAs($superAdmin)
            ->get(route('admin.accounts'))
            ->assertOk()
            ->assertSee('visible-to-super');
    }

    public function test_admin_cannot_view_another_admin_account(): void
    {
        $admin1 = $this->makeAdmin();
        $admin2 = $this->makeAdmin();
        $plan = $this->makePlan();

        $account = Account::create([
            'admin_id' => $admin2->id,
            'plan_id' => $plan->id,
            'device_type' => 1,
            'username' => 'private-account',
            'password' => 'secret',
            'charged_amount' => 100_000,
            'is_test' => false,
            'status' => Account::STATUS_ACTIVE,
        ]);

        $this->actingAs($admin1)
            ->get(route('admin.accounts.show', $account))
            ->assertForbidden();
    }

    public function test_account_creation_http_flow_works(): void
    {
        $admin = $this->makeAdmin([
            'balance' => 500_000,
        ]);

        $plan = $this->makePlan([
            'price' => 100_000,
        ]);

        $support = $this->makeSupport($admin);

        $response = $this->actingAs($admin)
            ->post(route('admin.accounts.store'), [
                'device_type' => 1,
                'account_type' => 1,
                'plan_id' => $plan->id,
                'support_id' => $support->id,
                'username_mode' => 'random',
                'quantity' => 1,
                'is_test' => false,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('accounts', [
            'admin_id' => $admin->id,
            'plan_id' => $plan->id,
            'support_id' => $support->id,
            'device_type' => 1,
            'charged_amount' => 100_000,
            'is_test' => false,
        ]);
    }

    public function test_account_creation_validation_rejects_invalid_device_type(): void
    {
        $admin = $this->makeAdmin();
        $plan = $this->makePlan();
        $support = $this->makeSupport($admin);

        $response = $this->actingAs($admin)
            ->post(route('admin.accounts.store'), [
                'device_type' => 99,
                'account_type' => 1,
                'plan_id' => $plan->id,
                'support_id' => $support->id,
                'quantity' => 1,
            ]);

        $response->assertSessionHasErrors('device_type');
    }

    public function test_account_renewal_charges_wallet(): void
    {
        $admin = $this->makeAdmin([
            'balance' => 500_000,
        ]);

        $oldPlan = $this->makePlan([
            'price' => 100_000,
            'duration_months' => 1,
        ]);

        $newPlan = $this->makePlan([
            'price' => 150_000,
            'duration_months' => 3,
        ]);

        $account = Account::create([
            'admin_id' => $admin->id,
            'plan_id' => $oldPlan->id,
            'device_type' => 1,
            'username' => 'renewable-account',
            'password' => 'secret',
            'charged_amount' => 100_000,
            'is_test' => false,
            'first_login_date' => now()->subDays(5),
            'expired_at' => now()->addDays(5),
            'status' => Account::STATUS_ACTIVE,
        ]);

        app(AccountRenewalService::class)->renew(
            $admin,
            $account,
            $newPlan
        );

        $this->assertSame(350_000, (int) $admin->fresh()->balance);
        $renewedAccount = $account->fresh();

        $this->assertSame(
            $oldPlan->id,
            (int) $renewedAccount->plan_id
        );

        $this->assertTrue(
            $renewedAccount->expired_at->greaterThan(
                now()->addDays(5)
            )
        );
    }

    public function test_wallet_transaction_description_is_string(): void
    {
        $admin = $this->makeAdmin([
            'balance' => 100_000,
        ]);

        $actor = $this->makeSuperAdmin();

        app(WalletService::class)->adjustCredit(
            $admin,
            25_000,
            $actor,
            'توضیح تست'
        );

        $transaction = WalletTransaction::query()
            ->where('user_id', $admin->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertIsString($transaction->description);
        $this->assertSame('توضیح تست', $transaction->description);
    }
}
