<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\AccountBlockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\TestCase;

class AccountBlockServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->travelTo(now()->setDate(2026, 10, 15)->setTime(12, 0));
    }

    protected function tearDown(): void
    {
        $this->travelBack();

        parent::tearDown();
    }

    private function makeUser(string $role = 'admin', int $balance = 100): User
    {
        $id = DB::table('users')->insertGetId([
            'email' => uniqid('test-', true).'@example.com',
            'role' => $role,
            'balance' => $balance,
            'password' => bcrypt('password'),
            'expired_type' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($id);
    }

    private function makeAccount(
        User $admin,
        mixed $firstLoginDate,
        int $chargedAmount = 500
    ): Account {
        return Account::create([
            'admin_id' => $admin->id,
            'username' => uniqid('account-'),
            'password' => bcrypt('password'),
            'first_login_date' => $firstLoginDate,
            'charged_amount' => $chargedAmount,
            'status' => Account::STATUS_ACTIVE,
        ]);
    }

    public function test_account_without_first_login_cannot_be_blocked_by_admin_or_super_admin(): void
    {
        foreach (['admin', 'super_admin'] as $role) {
            $admin = $this->makeUser('admin');
            $actor = $role === 'admin' ? $admin : $this->makeUser('super_admin');
            $account = $this->makeAccount($admin, null);

            try {
                app(AccountBlockService::class)->block($account, $actor, true);
                $this->fail("Blocking without first login should fail for {$role}.");
            } catch (RuntimeException $exception) {
                $this->assertStringContainsString(
                    'اولین ورود',
                    $exception->getMessage()
                );
            }

            $this->assertSame(Account::STATUS_ACTIVE, $account->fresh()->status);
            $this->assertSame(100, (int) $admin->fresh()->balance);
        }
    }

    public function test_account_blocked_at_exactly_72_hours_refunds_full_charge(): void
    {
        $admin = $this->makeUser('admin', 100);
        $account = $this->makeAccount($admin, now()->copy()->subHours(72), 500);

        $result = app(AccountBlockService::class)->block($account, $admin);

        $this->assertSame(Account::STATUS_BLOCKED, $result['account']->status);
        $this->assertTrue($result['under_72_hours']);
        $this->assertSame(500, $result['refund_amount']);
        $this->assertSame(600, (int) $admin->fresh()->balance);

        $this->assertSame(
            1,
            WalletTransaction::query()
                ->where('user_id', $admin->id)
                ->where('type', 'refund')
                ->count()
        );
    }

    public function test_admin_must_confirm_blocking_after_72_hours_and_no_refund_is_given(): void
    {
        $admin = $this->makeUser('admin', 100);
        $account = $this->makeAccount($admin, now()->copy()->subHours(73), 500);

        try {
            app(AccountBlockService::class)->block($account, $admin);
            $this->fail('Blocking after 72 hours should require confirmation.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('تأیید', $exception->getMessage());
        }

        $this->assertSame(Account::STATUS_ACTIVE, $account->fresh()->status);

        $result = app(AccountBlockService::class)->block($account, $admin, true);

        $this->assertSame(Account::STATUS_BLOCKED, $result['account']->status);
        $this->assertFalse($result['under_72_hours']);
        $this->assertSame(0, $result['refund_amount']);
        $this->assertSame(100, (int) $admin->fresh()->balance);
        $this->assertSame(0, WalletTransaction::query()->where('type', 'refund')->count());
    }

    public function test_super_admin_can_block_after_72_hours_without_confirmation_and_without_refund(): void
    {
        $admin = $this->makeUser('admin', 100);
        $superAdmin = $this->makeUser('super_admin');
        $account = $this->makeAccount($admin, now()->copy()->subHours(73), 500);

        $result = app(AccountBlockService::class)->block($account, $superAdmin);

        $this->assertSame(Account::STATUS_BLOCKED, $result['account']->status);
        $this->assertFalse($result['under_72_hours']);
        $this->assertSame(0, $result['refund_amount']);
        $this->assertSame(100, (int) $admin->fresh()->balance);
        $this->assertSame(0, WalletTransaction::query()->where('type', 'refund')->count());
    }

    public function test_already_blocked_account_cannot_trigger_a_second_refund(): void
    {
        $admin = $this->makeUser('admin', 100);
        $account = $this->makeAccount($admin, now()->copy()->subHours(24), 500);

        app(AccountBlockService::class)->block($account, $admin);

        $this->assertSame(600, (int) $admin->fresh()->balance);

        try {
            app(AccountBlockService::class)->block($account->fresh(), $admin);
            $this->fail('An already blocked account should not be blocked again.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('فعال نیست', $exception->getMessage());
        }

        $this->assertSame(600, (int) $admin->fresh()->balance);
        $this->assertSame(
            1,
            WalletTransaction::query()
                ->where('user_id', $admin->id)
                ->where('type', 'refund')
                ->count()
        );
    }
}
