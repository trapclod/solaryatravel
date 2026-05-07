<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->string('label')->nullable(); // es. "Alta stagione"
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('base_price', 10, 2)->default(0); // prezzo adulto base per persona
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['tour_id', 'start_date']);
        });

        // Aggiunge tour_period_id alle fasce d'età esistenti
        Schema::table('tour_age_brackets', function (Blueprint $table) {
            $table->foreignId('tour_period_id')->nullable()->after('tour_id')
                ->constrained('tour_periods')->nullOnDelete();
        });

        // Migra dati: per ogni tour con fasce esistenti, crea un periodo default
        // coprendo la stagione del tour (o l'anno corrente come fallback).
        $tours = DB::table('tours')->get(['id', 'season_start', 'season_end']);
        foreach ($tours as $t) {
            $hasBrackets = DB::table('tour_age_brackets')->where('tour_id', $t->id)->exists();
            if (!$hasBrackets) {
                continue;
            }
            $start = $t->season_start ?? date('Y-01-01');
            $end = $t->season_end ?? date('Y-12-31');

            // Prezzo base = minimo prezzo tra le fasce esistenti del tour
            $basePrice = (float) DB::table('tour_age_brackets')
                ->where('tour_id', $t->id)
                ->where('price', '>', 0)
                ->min('price') ?? 0;

            $periodId = DB::table('tour_periods')->insertGetId([
                'tour_id' => $t->id,
                'label' => 'Stagione',
                'start_date' => $start,
                'end_date' => $end,
                'base_price' => $basePrice,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('tour_age_brackets')
                ->where('tour_id', $t->id)
                ->update(['tour_period_id' => $periodId]);
        }
    }

    public function down(): void
    {
        Schema::table('tour_age_brackets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tour_period_id');
        });
        Schema::dropIfExists('tour_periods');
    }
};
