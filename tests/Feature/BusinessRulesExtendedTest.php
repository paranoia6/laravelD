<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Account;
use App\Models\Config;
use App\Models\Country;
use App\Models\Plan;
use App\Models\Support;
use App\Models\User;
use App\Services\AccountBlockService;
use App\Services\AccountCreationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\TestCase;

class BusinessRulesExtendedTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'Test Admin',
            'role' => UserRole::ADMIN,
            'is_active' => true,
            'balance' => 100000,
        ], $attributes));
    }

    private function makeSuperAdmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'Test Super Admin',
            'role' => UserRole::SUPER_ADMIN,
            'is_active' => true,
            'balance' => 0,
        ], $attributes));
    }

    private function makePlan(array $attributes = []): Plan
    {
        return Plan::create(array_merge([
            'name' => 'Test Plan',
            'duration_months' => 1,
            'price' => 1000,
            'type' => 'normal',
            'is_active' => true,
        ], $attributes));
    }

    private function makeSupport(
        User $admin,
        array $links = ['telegram' => 'https://t.me/test_support']
    ): Support {
        return Support::createWithLinks(
            $admin->id,
            'Test Support',
            $links
        );
    }

    public function test_admin_cannot_block_account_before_first_login(): void
    {
        Notification::fake();

        $admin = $this->makeAdmin();
        $plan = $this->makePlan();
        $support = $this->makeSupport($admin);

        $account = app(AccountCreationService::class)->create(
            $admin,
            'blocktest1',
            'password123',
            $plan,
            $support->id,
            1,
            1,
            false
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('اولین ورود موفق اکانت هنوز ثبت نشده است.');

        app(AccountBlockService::class)->block(
            $account,
            $admin
        );
    }

    public function test_account_block_under_72_hours_refunds_full_charged_amount(): void
    {
        Notification::fake();

        $admin = $this->makeAdmin([
            'balance' => 10000,
        ]);

        $plan = $this->makePlan([
            'price' => 2500,
        ]);

        $support = $this->makeSupport($admin);

        $account = app(AccountCreationService::class)->create(
            $admin,
            'refundtest1',
            'password123',
            $plan,
            $support->id,
            1,
            1,
            false
        );

        $this->assertSame(7500, (int) $admin->fresh()->balance);

        $account->update([
            'first_login_date' => now()->subHours(24),
        ]);

        $result = app(AccountBlockService::class)->block(
            $account->fresh(),
            $admin
        );

        $this->assertTrue($result['under_72_hours']);
        $this->assertSame(2500, $result['refund_amount']);
        $this->assertSame(2500, $result['refunded']);

        $this->assertSame(
            10000,
            (int) $admin->fresh()->balance
        );

        $blocked = $account->fresh();

        $this->assertSame(
            Account::STATUS_BLOCKED,
            $blocked->status
        );

        $this->assertSame(
            'blocked_under_72_hours',
            $blocked->block_reason
        );
    }

    public function test_admin_needs_confirmation_to_block_after_72_hours(): void
    {
        Notification::fake();

        $admin = $this->makeAdmin([
            'balance' => 10000,
        ]);

        $plan = $this->makePlan([
            'price' => 2500,
        ]);

        $support = $this->makeSupport($admin);

        $account = app(AccountCreationService::class)->create(
            $admin,
            'over72test1',
            'password123',
            $plan,
            $support->id,
            1,
            1,
            false
        );

        $account->update([
            'first_login_date' => now()->subHours(73),
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('بیش از ۷۲ ساعت');

        app(AccountBlockService::class)->block(
            $account->fresh(),
            $admin
        );
    }

    public function test_admin_can_block_after_72_hours_with_confirmation_without_refund(): void
    {
        Notification::fake();

        $admin = $this->makeAdmin([
            'balance' => 10000,
        ]);

        $plan = $this->makePlan([
            'price' => 2500,
        ]);

        $support = $this->makeSupport($admin);

        $account = app(AccountCreationService::class)->create(
            $admin,
            'confirm72test',
            'password123',
            $plan,
            $support->id,
            1,
            1,
            false
        );

        $account->update([
            'first_login_date' => now()->subHours(73),
        ]);

        $result = app(AccountBlockService::class)->block(
            $account->fresh(),
            $admin,
            true
        );

        $this->assertFalse($result['under_72_hours']);
        $this->assertSame(0, $result['refund_amount']);
        $this->assertSame(0, $result['refunded']);

        $this->assertSame(
            7500,
            (int) $admin->fresh()->balance
        );

        $this->assertSame(
            Account::STATUS_BLOCKED,
            $account->fresh()->status
        );
    }

    public function test_test_account_is_free_and_has_no_plan(): void
    {
        $admin = $this->makeAdmin([
            'balance' => 0,
        ]);

        $support = $this->makeSupport($admin);
        $plan = $this->makePlan();

        $account = app(AccountCreationService::class)->create(
            $admin,
            'testfree1',
            'password123',
            $plan,
            $support->id,
            1,
            1,
            true
        );

        $account->refresh();

        $this->assertTrue((bool) $account->is_test);
        $this->assertNull($account->plan_id);
        $this->assertSame(0, (int) $account->charged_amount);
        $this->assertSame(0, (int) $admin->fresh()->balance);
    }

    public function test_inactive_plan_cannot_be_used_for_real_account(): void
    {
        $admin = $this->makeAdmin();
        $support = $this->makeSupport($admin);

        $plan = $this->makePlan([
            'is_active' => false,
        ]);

        $this->expectException(RuntimeException::class);

        app(AccountCreationService::class)->create(
            $admin,
            'inactiveplan1',
            'password123',
            $plan,
            $support->id,
            1,
            1,
            false
        );
    }

    public function test_invalid_username_is_rejected(): void
    {
        $admin = $this->makeAdmin();
        $plan = $this->makePlan();
        $support = $this->makeSupport($admin);

        $this->expectException(RuntimeException::class);

        app(AccountCreationService::class)->create(
            $admin,
            'invalid-user',
            'password123',
            $plan,
            $support->id,
            1,
            1,
            false
        );
    }

    public function test_support_must_belong_to_admin(): void
    {
        $admin = $this->makeAdmin();
        $otherAdmin = $this->makeAdmin([
            'email' => 'other-admin@example.com',
        ]);

        $plan = $this->makePlan();
        $support = $this->makeSupport($otherAdmin);

        $this->expectException(RuntimeException::class);

        app(AccountCreationService::class)->create(
            $admin,
            'ownership1',
            'password123',
            $plan,
            $support->id,
            1,
            1,
            false
        );
    }

    public function test_support_without_links_cannot_be_used(): void
    {
        $admin = $this->makeAdmin();
        $plan = $this->makePlan();

        $support = $this->makeSupport(
            $admin,
            []
        );

        $this->assertFalse($support->fresh()->hasLinks());

        $this->expectException(RuntimeException::class);

        app(AccountCreationService::class)->create(
            $admin,
            'nolinktest1',
            'password123',
            $plan,
            $support->id,
            1,
            1,
            false
        );
    }

    public function test_country_belongs_to_config(): void
    {
        $country = Country::create([
            'name' => 'ایران',
            'code' => 'IR',
            'flag' => '🇮🇷',
        ]);

        $config = Config::create([
            'config' => 'test-config',
            'country_id' => $country->id,
            'internet_type' => 1,
            'account_type' => 1,
            'is_active' => true,
            'descriptions' => '',
        ]);

        $this->assertSame(
            $country->id,
            $config->fresh()->country->id
        );

        $this->assertSame(
            'ایران',
            $config->fresh()->country->name
        );
    }

    public function test_country_api_returns_country_information(): void
    {
        $country = Country::create([
            'name' => 'ایران',
            'code' => 'IR',
            'flag' => '🇮🇷',
        ]);

        Config::create([
            'config' => 'test-config-api',
            'country_id' => $country->id,
            'internet_type' => 1,
            'account_type' => 1,
            'is_active' => true,
            'descriptions' => '',
        ]);

        $response = $this->postJson('/api/v1/get-config');

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'name' => 'ایران',
            'code' => 'IR',
            'flag' => '🇮🇷',
        ]);
    }

    public function test_support_ownership_http_validation_rejects_other_admin_support(): void
    {
        $admin = $this->makeAdmin();
        $otherAdmin = $this->makeAdmin([
            'email' => 'http-other@example.com',
        ]);

        $plan = $this->makePlan();
        $support = $this->makeSupport($otherAdmin);

        $response = $this->actingAs($admin)
            ->post('/admin/accounts', [
                'device_type' => 1,
                'account_type' => 1,
                'plan_id' => $plan->id,
                'support_id' => $support->id,
                'username_mode' => 'random',
                'quantity' => 1,
                'is_test' => false,
            ]);

        $response->assertSessionHasErrors('support_id');
    }
}
