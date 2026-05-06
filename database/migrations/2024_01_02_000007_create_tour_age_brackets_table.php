<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_age_brackets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->string('label'); // es. "Adulti", "Bambini 3-11", "Infanti 0-2"
            $table->unsignedTinyInteger('min_age')->default(0);
            $table->unsignedTinyInteger('max_age')->nullable(); // null = nessun limite superiore
            $table->decimal('price', 10, 2);
            $table->boolean('counts_as_seat')->default(true); // se false (es. neonati in braccio) non occupa posto
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['tour_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_age_brackets');
    }
};
