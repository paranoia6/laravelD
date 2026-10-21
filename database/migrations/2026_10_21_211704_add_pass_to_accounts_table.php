<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('accounts', 'pass')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->string('pass')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('accounts', 'pass')) {
            Schema::table('accounts', function (Blueprint $table) {
                $table->dropColumn('pass');
            });
        }
    }
};
