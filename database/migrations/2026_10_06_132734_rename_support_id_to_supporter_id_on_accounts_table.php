<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign('accounts_support_id_foreign');
            $table->dropIndex('accounts_support_id_index');
            $table->renameColumn('support_id', 'supporter_id');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->index('supporter_id');
            $table->foreign('supporter_id')
                ->references('id')
                ->on('supports')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['supporter_id']);
            $table->dropIndex(['supporter_id']);
            $table->renameColumn('supporter_id', 'support_id');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->index('support_id');
            $table->foreign('support_id')
                ->references('id')
                ->on('supports')
                ->nullOnDelete();
        });
    }
};
