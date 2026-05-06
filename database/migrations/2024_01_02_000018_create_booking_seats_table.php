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
            // Catamarano assegnato a questo posto (auto-distribuzione, riassegnabile da admin)
            $table->foreignId('catamaran_id')->nullable()->constrained()->nullOnDelete();
            // Fascia d'età a cui è stato venduto (determina il prezzo)
            $table->foreignId('tour_age_bracket_id')->nullable()->constrained('tour_age_brackets')->nullOnDelete();
            $table->decimal('price_paid', 10, 2)->default(0);
            $table->string('guest_first_name')->nullable();
            $table->string('guest_last_name')->nullable();
            $table->date('guest_date_of_birth')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['booking_id', 'seat_number']);
            $table->index('catamaran_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_seats');
    }
};
