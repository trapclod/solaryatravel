<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catamaran_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('time_slot_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['available', 'partially_booked', 'fully_booked', 'blocked'])->default('available');
            $table->unsignedInteger('seats_available');
            $table->unsignedInteger('seats_booked')->default(0);
            $table->boolean('is_exclusive_booked')->default(false);
            $table->string('block_reason')->nullable();
            $table->decimal('custom_price', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['catamaran_id', 'date', 'time_slot_id'], 'uk_availability');
            $table->index('date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability');
    }
};
