<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Singole partenze di un tour: l'admin definisce per ogni data
        // uno o più slot orari (start/end). La disponibilità è calcolata
        // dalla somma dei posti già prenotati sui catamarani assegnati.
        Schema::create('tour_departures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->date('departure_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['scheduled', 'cancelled', 'sold_out'])->default('scheduled');
            $table->decimal('price_modifier', 5, 2)->default(1.00); // moltiplicatore stagionale opzionale
            $table->unsignedInteger('capacity_override')->nullable(); // limita posti totali (se null usa capacità catamarani)
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['tour_id', 'departure_date', 'start_time'], 'uk_tour_departures');
            $table->index('departure_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_departures');
    }
};
