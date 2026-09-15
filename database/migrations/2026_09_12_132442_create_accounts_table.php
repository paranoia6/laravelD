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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('admin_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('plan_id')
                ->constrained('plans')
                ->restrictOnDelete();

            $table->string('username')->unique();
            $table->string('password');

            $table->unsignedBigInteger('charged_amount');

            // زمان واقعی فعال‌شدن حساب؛ برای قانون 72 ساعت
            $table->dateTime('activated_at')->nullable();

            $table->dateTime('expired_at')->nullable();

            $table->string('status')->default('active');

            $table->dateTime('blocked_at')->nullable();
            $table->string('block_reason')->nullable();

            $table->timestamps();

            $table->index(['admin_id', 'status']);
            $table->index('activated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
