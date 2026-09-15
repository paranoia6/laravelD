<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignId('support_id')
                ->nullable()
                ->after('plan_id')
                ->constrained('supports')
                ->nullOnDelete();

            $table->index('support_id');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['support_id']);
            $table->dropColumn('support_id');
        });
    }
};
