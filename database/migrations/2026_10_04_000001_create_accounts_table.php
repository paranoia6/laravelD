<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('admin_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('plan_id')
                ->nullable()
                ->constrained('plans')
                ->restrictOnDelete();

            $table->unsignedTinyInteger('device_type')->default(1);

$table->unsignedBigInteger('supporter_id')->nullable();

$table->string('username')->unique();
$table->string('password');

$table->integer('expired_type')->default(1);
$table->integer('account_type')->default(1);

$table->string('device_model')->nullable();
$table->string('android_id')->nullable();

$table->boolean('is_test')->default(false);

$table->string('os_version', 10)->nullable();

$table->boolean('is_other_device_allow')->default(false);

$table->integer('try_login')->default(0);

$table->dateTime('last_seen')->nullable();

$table->string('manufacturer')->nullable();

$table->string('app_version_code', 50)->nullable();

$table->unsignedBigInteger('charged_amount')->nullable();

$table->dateTime('expired_at')->nullable();

$table->dateTime('first_login_date')->nullable();

$table->boolean('is_active')->default(true);

$table->string('status')->default('active');

$table->dateTime('blocked_at')->nullable();

$table->string('block_reason')->nullable();

            $table->timestamps();

            $table->index(['admin_id', 'status']);
            $table->index('supporter_id');
            $table->index('first_login_date');

            $table->foreign('supporter_id')
                ->references('id')
                ->on('supports')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
