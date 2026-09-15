<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('accounts', 'support_id')) {
                $table->foreignId('support_id')
                    ->nullable()
                    ->after('plan_id')
                    ->constrained('supports')
                    ->restrictOnDelete();
            }

            if (!Schema::hasColumn('accounts', 'device_type')) {
                $table->unsignedTinyInteger('device_type')
                    ->nullable()
                    ->after('support_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts', 'device_type')) {
                $table->dropColumn('device_type');
            }

            if (Schema::hasColumn('accounts', 'support_id')) {
                $table->dropForeign(['support_id']);
                $table->dropColumn('support_id');
            }
        });
    }
};
