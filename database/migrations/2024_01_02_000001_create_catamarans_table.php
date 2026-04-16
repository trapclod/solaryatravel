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
        Schema::create('catamarans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('description_short', 500)->nullable();
            $table->unsignedInteger('capacity')->default(12);
            $table->decimal('length_meters', 5, 2)->nullable();
            $table->json('features')->nullable();
            $table->decimal('base_price_half_day', 10, 2);
            $table->decimal('base_price_full_day', 10, 2);
            $table->decimal('exclusive_price_half_day', 10, 2);
            $table->decimal('exclusive_price_full_day', 10, 2);
            $table->decimal('price_per_person_half_day', 10, 2);
            $table->decimal('price_per_person_full_day', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catamarans');
    }
};
