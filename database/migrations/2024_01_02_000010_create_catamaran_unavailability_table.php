<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Periodi durante i quali un catamarano NON è disponibile
        // (manutenzione, riservato, fuori servizio).
        Schema::create('catamaran_unavailability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catamaran_id')->constrained()->cascadeOnDelete();
            $table->date('date_start');
            $table->date('date_end');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['catamaran_id', 'date_start', 'date_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catamaran_unavailability');
    }
};
