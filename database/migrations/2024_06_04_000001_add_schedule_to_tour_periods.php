<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_periods', function (Blueprint $table) {
            // Giorni della settimana operativi: array ISO [1=Lun..7=Dom]
            $table->json('weekdays')->nullable()->after('end_date');
            // Orari di partenza giornalieri: array di stringhe "HH:MM"
            $table->json('times')->nullable()->after('weekdays');
        });

        // Default per record esistenti: tutti i giorni, un orario alle 10:00
        DB::table('tour_periods')->whereNull('weekdays')->update([
            'weekdays' => json_encode([1, 2, 3, 4, 5, 6, 7]),
            'times' => json_encode(['10:00']),
        ]);
    }

    public function down(): void
    {
        Schema::table('tour_periods', function (Blueprint $table) {
            $table->dropColumn(['weekdays', 'times']);
        });
    }
};
