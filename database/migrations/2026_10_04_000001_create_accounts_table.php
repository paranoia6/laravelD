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

            $table->unsignedBigInteger('charged_amount');

            $table->boolean('is_test')->default(false);

            $table->dateTime('first_login_date')->nullable();
            $table->dateTime('expired_at')->nullable();

            $table->unsignedTinyInteger('status')->default(1);

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
