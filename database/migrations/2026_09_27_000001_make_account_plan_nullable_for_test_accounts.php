<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignId('plan_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        if (
            \App\Models\Account::query()
                ->whereNull('plan_id')
                ->exists()
        ) {
            throw new RuntimeException(
                'امکان rollback وجود ندارد چون اکانت تست بدون plan_id وجود دارد.'
            );
        }

        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignId('plan_id')
                ->nullable(false)
                ->change();
        });
    }
};
