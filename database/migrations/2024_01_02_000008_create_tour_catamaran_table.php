<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot: limita un tour a un sottoinsieme di catamarani.
        // Se un tour non ha record qui, vengono usati TUTTI i catamarani attivi.
        Schema::create('tour_catamaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catamaran_id')->constrained()->cascadeOnDelete();
            $table->integer('priority')->default(0); // ordine preferenziale di assegnazione

            $table->unique(['tour_id', 'catamaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_catamaran');
    }
};
