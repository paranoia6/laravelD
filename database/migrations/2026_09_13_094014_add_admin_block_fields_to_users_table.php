<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('admin_blocked_at')
                ->nullable()
                ->after('is_active');

            $table->string('admin_block_reason')
                ->nullable()
                ->after('admin_blocked_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'admin_blocked_at',
                'admin_block_reason',
            ]);
        });
    }
};
