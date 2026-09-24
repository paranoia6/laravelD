<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropIndex(['first_login_at']);
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->renameColumn(
                'first_login_at',
                'first_login_date'
            );
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->index('first_login_date');

            $table->dropIndex(['activated_at']);
            $table->dropColumn('activated_at');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropIndex(['first_login_date']);
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->renameColumn(
                'first_login_date',
                'first_login_at'
            );
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->index('first_login_at');

            $table->dateTime('activated_at')
                ->nullable()
                ->after('charged_amount');

            $table->index('activated_at');
        });
    }
};
