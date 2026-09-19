<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\AuditLog;
use App\Models\Plan;
use App\Models\User;
use App\Services\AccountBlockService;
use App\Services\AdminBlockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BlockingTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(array $extra = []): User
    {
        return User::create(array_merge([
            'name' => 'Test Admin',
            'email' => 'admin_' . uniqid() . '@test.local',
            'password' => 'password123',
            'role' => 'admin',
            'balance' => 100000,
            'is_active' => true,

            // Required users table fields
            'expired_type' => 0,
            'account_type' => 1,
            'device_type' => 0,
            'try_login' => 0,
            'is_test' => false,
            'is_other_device_allow' => false,
        ], $extra));
    }

    private function createSuperAdmin(array $extra = []): User
    {
        return User::create(array_merge([
            'name' => 'Test Super Admin',
            'email' => 'super_' . uniqid() . '@test.local',
            'password' => 'password123',
            'role' => 'super_admin',
            'balance' => 0,
            'is_active' => true,

            // Required users table fields
            'expired_type' => 0,
            'account_type' => 1,
            'device_type' => 0,
            'try_login' => 0,
            'is_test' => false,
            'is_other_device_allow' => false,
        ], $extra));
    }

    private function createPlan(): Plan
    {
        return Plan::create([
            'type' => 'normal',
            'duration_months' => 1,
            'price' => 500000,
            'is_active' => true,
        ]);
    }

    public function test_account_block_within_72_hours_refunds_exact_charged_amount(): void
    {
        Notification::fake();

        $admin = $this->createAdmin();
        $superAdmin = $this->createSuperAdmin();
        $plan = $this->createPlan();

        $account = Account::create([
            'admin_id' => $admin->id,
            'plan_id' => $plan->id,
            'username' => 'test_' . uniqid(),
            'password' => 'password',
            'charged_amount' => 375000,
            'activated_at' => now()->subHours(24),
            'expired_at' => now()->addMonth(),
            'status' => 'active',
        ]);

        $this->actingAs($superAdmin);

        $result = app(AccountBlockService::class)->block($account);

        $admin->refresh();
        $account->refresh();

        $this->assertSame(475000, $admin->balance);
        $this->assertSame(375000, $result['refunded']);
        $this->assertTrue($result['under_72_hours']);

        $this->assertSame('blocked', $account->status);
        $this->assertSame(
            'blocked_under_72_hours',
            $account->block_reason
        );

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'account_blocked',
            'subject_type' => Account::class,
            'subject_id' => $account->id,
        ]);

        Notification::assertSentTo(
            $admin,
            \App\Notifications\AccountBlockedNotification::class
        );
    }

    public function test_account_block_after_72_hours_has_no_refund(): void
    {
        Notification::fake();

        $admin = $this->createAdmin();
        $superAdmin = $this->createSuperAdmin();
        $plan = $this->createPlan();

        $account = Account::create([
            'admin_id' => $admin->id,
            'plan_id' => $plan->id,
            'username' => 'test_' . uniqid(),
            'password' => 'password',
            'charged_amount' => 375000,
            'activated_at' => now()->subHours(96),
            'expired_at' => now()->addMonth(),
            'status' => 'active',
        ]);

        $this->actingAs($superAdmin);

        $result = app(AccountBlockService::class)->block(
            $account,
            true
        );

        $admin->refresh();
        $account->refresh();

        $this->assertSame(100000, $admin->balance);
        $this->assertSame(0, $result['refunded']);
        $this->assertFalse($result['under_72_hours']);

        $this->assertSame('blocked', $account->status);
        $this->assertSame(
            'blocked_over_72_hours',
            $account->block_reason
        );

        Notification::assertSentTo(
            $admin,
            \App\Notifications\AccountBlockedNotification::class
        );
    }

    public function test_admin_block_with_accounts_then_unblock_only_reactivates_accounts_blocked_by_admin(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $admin = $this->createAdmin();
        $plan = $this->createPlan();

        $accountBlockedByAdmin = Account::create([
            'admin_id' => $admin->id,
            'plan_id' => $plan->id,
            'username' => 'adminblock_' . uniqid(),
            'password' => 'password',
            'charged_amount' => 500000,
            'activated_at' => now()->subDays(10),
            'expired_at' => now()->addMonth(),
            'status' => 'active',
        ]);

        $independentlyBlockedAccount = Account::create([
            'admin_id' => $admin->id,
            'plan_id' => $plan->id,
            'username' => 'independent_' . uniqid(),
            'password' => 'password',
            'charged_amount' => 500000,
            'activated_at' => now()->subDays(10),
            'expired_at' => now()->addMonth(),
            'status' => 'blocked',
            'blocked_at' => now()->subDay(),
            'block_reason' => 'blocked_over_72_hours',
        ]);

        app(AdminBlockService::class)->block(
            $admin,
            $superAdmin,
            true
        );

        $admin->refresh();
        $accountBlockedByAdmin->refresh();
        $independentlyBlockedAccount->refresh();

        $this->assertFalse($admin->is_active);

        $this->assertSame(
            'admin_and_accounts',
            $admin->admin_block_reason
        );

        $this->assertSame(
            'blocked',
            $accountBlockedByAdmin->status
        );

        $this->assertSame(
            'admin_blocked_with_accounts',
            $accountBlockedByAdmin->block_reason
        );

        $this->assertSame(
            'blocked',
            $independentlyBlockedAccount->status
        );

        app(AdminBlockService::class)->unblock(
            $admin,
            $superAdmin
        );

        $admin->refresh();
        $accountBlockedByAdmin->refresh();
        $independentlyBlockedAccount->refresh();

        $this->assertTrue($admin->is_active);
        $this->assertNull($admin->admin_block_reason);

        $this->assertSame(
            'active',
            $accountBlockedByAdmin->status
        );

        $this->assertNull(
            $accountBlockedByAdmin->block_reason
        );

        $this->assertSame(
            'blocked',
            $independentlyBlockedAccount->status
        );

        $this->assertSame(
            'blocked_over_72_hours',
            $independentlyBlockedAccount->block_reason
        );

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_blocked',
            'subject_type' => User::class,
            'subject_id' => $admin->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin_unblocked',
            'subject_type' => User::class,
            'subject_id' => $admin->id,
        ]);
    }
}
