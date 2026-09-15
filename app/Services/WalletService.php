<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletService
{
    public function credit(
        User $user,
        int $amount,
        ?User $createdBy = null,
        ?string $description = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new RuntimeException('مبلغ شارژ باید بیشتر از صفر باشد.');
        }

        return DB::transaction(function () use (
            $user,
            $amount,
            $createdBy,
            $description
        ) {
            $target = User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($target->role?->value !== 'admin') {
                throw new RuntimeException(
                    'فقط کیف پول Admin قابل شارژ است.'
                );
            }

            $target->balance = (int) $target->balance + $amount;
            $target->save();

            $transaction = WalletTransaction::create([
                'user_id' => $target->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $target->balance,
                'description' => $description ?: 'شارژ کیف پول توسط Super Admin',
                'created_by' => $createdBy?->id,
            ]);

            $target->notify(new SystemNotification(
                'شارژ کیف پول',
                'مبلغ '.number_format($amount).' تومان به کیف پول شما اضافه شد.',
                route('admin.wallet')
            ));

            return $transaction;
        });
    }

    public function debit(
        User $user,
        int $amount,
        ?User $createdBy = null,
        ?string $description = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new RuntimeException('مبلغ کسر باید بیشتر از صفر باشد.');
        }

        return DB::transaction(function () use (
            $user,
            $amount,
            $createdBy,
            $description
        ) {
            $target = User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($target->role?->value !== 'admin') {
                throw new RuntimeException(
                    'فقط کیف پول Admin قابل کسر است.'
                );
            }

            $balance = (int) $target->balance;

            if ($balance < $amount) {
                throw new RuntimeException(
                    'موجودی کیف پول کافی نیست.'
                );
            }

            $target->balance = $balance - $amount;
            $target->save();

            $transaction = WalletTransaction::create([
                'user_id' => $target->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $target->balance,
                'description' => $description ?: 'کسر بابت ایجاد اکانت',
                'created_by' => $createdBy?->id,
            ]);

            $target->notify(new SystemNotification(
                'کسر از کیف پول',
                'مبلغ '.number_format($amount).' تومان از کیف پول شما کسر شد.',
                route('admin.wallet')
            ));

            return $transaction;
        });
    }

    public function refund(
        User $user,
        int $amount,
        ?User $createdBy = null,
        ?string $description = null
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new RuntimeException('مبلغ بازگشت باید بیشتر از صفر باشد.');
        }

        return DB::transaction(function () use (
            $user,
            $amount,
            $createdBy,
            $description
        ) {
            $target = User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($target->role?->value !== 'admin') {
                throw new RuntimeException(
                    'فقط کیف پول Admin قابل بازگشت است.'
                );
            }

            $target->balance = (int) $target->balance + $amount;
            $target->save();

            $transaction = WalletTransaction::create([
                'user_id' => $target->id,
                'type' => 'refund',
                'amount' => $amount,
                'balance_after' => $target->balance,
                'description' => $description ?: 'بازگشت مبلغ اکانت',
                'created_by' => $createdBy?->id,
            ]);

            $target->notify(new SystemNotification(
                'بازگشت مبلغ',
                'مبلغ '.number_format($amount).' تومان به کیف پول شما بازگردانده شد.',
                route('admin.wallet')
            ));

            return $transaction;
        });
    }

    public function adjustCredit(
        User $user,
        int $amount,
        ?User $createdBy = null,
        ?string $description = null
    ): WalletTransaction {
        if ($amount === 0) {
            throw new RuntimeException(
                'مبلغ اصلاح موجودی نمی‌تواند صفر باشد.'
            );
        }

        return DB::transaction(function () use (
            $user,
            $amount,
            $createdBy,
            $description
        ) {
            $target = User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($target->role?->value !== 'admin') {
                throw new RuntimeException(
                    'فقط موجودی Admin قابل اصلاح است.'
                );
            }

            $newBalance = (int) $target->balance + $amount;

            if ($newBalance < 0) {
                throw new RuntimeException(
                    'موجودی جدید نمی‌تواند منفی باشد.'
                );
            }

            $target->balance = $newBalance;
            $target->save();

            $transaction = WalletTransaction::create([
                'user_id' => $target->id,
                'type' => 'credit_adjustment',
                'amount' => abs($amount),
                'balance_after' => $target->balance,
                'description' => $description ?: 'اصلاح موجودی',
                'created_by' => $createdBy?->id,
            ]);

            $direction = $amount > 0 ? 'افزایش' : 'کاهش';

            $target->notify(new SystemNotification(
                'اصلاح موجودی',
                'موجودی کیف پول شما به میزان '
                .number_format(abs($amount))
                .' تومان '
                .$direction
                .' یافت.',
                route('admin.wallet')
            ));

            return $transaction;
        });
    }
}
