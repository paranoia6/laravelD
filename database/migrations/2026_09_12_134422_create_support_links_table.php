<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('support_id')
                ->constrained('supports')
                ->cascadeOnDelete();

            $table->string('title')->nullable();
            $table->string('link');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['support_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_links');
    }
};
