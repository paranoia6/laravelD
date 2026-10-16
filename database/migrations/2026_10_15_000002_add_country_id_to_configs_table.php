<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('configs', 'country_id')) {
            Schema::table('configs', function (Blueprint $table) {
                $table->unsignedInteger('country_id')->nullable();
                $table->foreign('country_id')
                    ->references('id')
                    ->on('countries')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('configs', 'country_id')) {
            Schema::table('configs', function (Blueprint $table) {
                $table->dropForeign(['country_id']);
                $table->dropColumn('country_id');
            });
        }
    }
};
