<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The accounts table may already use the new column name.
        if (
            Schema::hasColumn('accounts', 'support_id') &&
            ! Schema::hasColumn('accounts', 'supporter_id')
        ) {
            Schema::table('accounts', function ($table) {
                $table->renameColumn('support_id', 'supporter_id');
            });
        }
    }

    public function down(): void
    {
        // Intentionally left empty: the previous migration already
        // creates supporter_id, so rolling back must not rename it.
    }
};
