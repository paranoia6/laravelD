<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->unsignedTinyInteger('duration_months');
            $table->unsignedBigInteger('price')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type', 'duration_months']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
