<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('slot_type', ['half_day', 'full_day']);
            $table->decimal('price_modifier', 5, 2)->default(1.00);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('slot_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
