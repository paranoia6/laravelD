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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();

            $table->string('type');

            $table->string('title');

            $table->text('message');

            $table->string('button_text')->nullable();

            $table->text('button_url')->nullable();

            $table->unsignedInteger('max_build_number')->nullable();

            $table->unsignedInteger('priority')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamp('starts_at')->nullable();

            $table->timestamp('ends_at')->nullable();

            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
