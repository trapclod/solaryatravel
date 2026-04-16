<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('seat_number');
            $table->string('guest_first_name');
            $table->string('guest_last_name');
            $table->date('guest_date_of_birth')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['booking_id', 'seat_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_seats');
    }
};
