<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('email');
            $table->boolean('is_test')->default(false);

            $table->timestamp('email_verified_at')->nullable();

            // قدیمی
            $table->string('pass')->nullable();

            // Laravel Auth
            $table->string('password');

            $table->unsignedInteger('device_type')->default(1);
            $table->integer('expired_type');

            $table->dateTime('expired_at')->nullable();
            $table->dateTime('first_login_date')->nullable();

            $table->boolean('is_active')->default(true);

            $table->dateTime('suspend_at')->nullable();

            $table->integer('account_type')->default(1);

            $table->unsignedBigInteger('supporter_id')->nullable();

            $table->string('device_model')->nullable();
            $table->string('android_id')->nullable();

            $table->string('os_version', 10)->nullable();

            $table->boolean('is_other_device_allow')->default(false);

            $table->integer('try_login')->default(0);

            $table->dateTime('last_seen')->nullable();

            $table->string('manufacturer')->nullable();

            $table->string('app_version_code', 50)->nullable();

            $table->integer('seller_id')->default(1);

            $table->rememberToken();

            $table->softDeletes();

            $table->timestamps();

        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
