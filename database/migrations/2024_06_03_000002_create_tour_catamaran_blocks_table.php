<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Blocchi per catamarano relativi a un tour: in queste finestre temporali
        // il catamarano specificato non può essere utilizzato per quel tour
        // (es. manutenzione, charter privato, indisponibilità).
        Schema::create('tour_catamaran_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catamaran_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['tour_id', 'catamaran_id', 'start_date', 'end_date'], 'tcb_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_catamaran_blocks');
    }
};
