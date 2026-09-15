<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AdminBlockService
{
    public function block(
        User $admin,
        User $superAdmin,
        bool $disableAccounts = false
    ): array {
        return DB::transaction(function () use (
            $admin,
            $superAdmin,
            $disableAccounts
        ) {
            $admin = User::query()
                ->whereKey($admin->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($admin->role->value !== 'admin') {
                throw new RuntimeException(
                    'فقط Admin قابل مسدودسازی است.'
                );
            }

            if (!$admin->is_active) {
                throw new RuntimeException(
                    'این Admin قبلاً غیرفعال شده است.'
                );
            }

            $blockedAccountIds = [];

            if ($disableAccounts) {
                $accounts = Account::query()
                    ->where('admin_id', $admin->id)
                    ->where('status', 'active')
                    ->lockForUpdate()
                    ->get();

                foreach ($accounts as $account) {
                    $account->update([
                        'status' => 'blocked',
                        'blocked_at' => now(),
                        'block_reason' => 'admin_blocked_with_accounts',
                    ]);

                    $blockedAccountIds[] = $account->id;
                }
            }

            $admin->update([
                'is_active' => false,
                'admin_blocked_at' => now(),
                'admin_block_reason' => $disableAccounts
                    ? 'admin_and_accounts'
                    : 'admin_only',
            ]);

            AuditLog::create([
                'user_id' => $superAdmin->id,
                'action' => 'admin_blocked',
                'subject_type' => User::class,
                'subject_id' => $admin->id,
                'description' => $disableAccounts
                    ? 'Admin و اکانت‌های فعال او مسدود شدند.'
                    : 'فقط Admin مسدود شد.',
                'metadata' => [
                    'admin_id' => $admin->id,
                    'admin_email' => $admin->email,
                    'mode' => $disableAccounts
                        ? 'admin_and_accounts'
                        : 'admin_only',
                    'blocked_account_ids' => $blockedAccountIds,
                    'blocked_accounts_count' => count($blockedAccountIds),
                ],
            ]);

            return [
                'accounts_disabled' => count($blockedAccountIds),
            ];
        });
    }

    public function unblock(
        User $admin,
        User $superAdmin
    ): array {
        return DB::transaction(function () use (
            $admin,
            $superAdmin
        ) {
            $admin = User::query()
                ->whereKey($admin->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($admin->role->value !== 'admin') {
                throw new RuntimeException(
                    'این کاربر Admin نیست.'
                );
            }

            if ($admin->is_active) {
                throw new RuntimeException(
                    'این Admin فعال است.'
                );
            }

            $accountsReactivated = 0;

            if (
                $admin->admin_block_reason ===
                'admin_and_accounts'
            ) {
                $lastLog = AuditLog::query()
                    ->where('action', 'admin_blocked')
                    ->where('subject_type', User::class)
                    ->where('subject_id', $admin->id)
                    ->latest('id')
                    ->first();

                $accountIds = $lastLog?->metadata[
                'blocked_account_ids'
                ] ?? [];

                if (!empty($accountIds)) {
                    $accounts = Account::query()
                        ->whereIn('id', $accountIds)
                        ->where('admin_id', $admin->id)
                        ->where('status', 'blocked')
                        ->where(
                            'block_reason',
                            'admin_blocked_with_accounts'
                        )
                        ->lockForUpdate()
                        ->get();

                    foreach ($accounts as $account) {
                        $account->update([
                            'status' => 'active',
                            'blocked_at' => null,
                            'block_reason' => null,
                        ]);

                        $accountsReactivated++;
                    }
                }
            }

            $admin->update([
                'is_active' => true,
                'admin_blocked_at' => null,
                'admin_block_reason' => null,
            ]);

            AuditLog::create([
                'user_id' => $superAdmin->id,
                'action' => 'admin_unblocked',
                'subject_type' => User::class,
                'subject_id' => $admin->id,
                'description' => 'مسدودی Admin برداشته شد.',
                'metadata' => [
                    'admin_id' => $admin->id,
                    'admin_email' => $admin->email,
                    'accounts_reactivated' => $accountsReactivated,
                ],
            ]);

            return [
                'accounts_reactivated' => $accountsReactivated,
            ];
        });
    }
}
