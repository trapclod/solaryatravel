<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_seats', function (Blueprint $table) {
            $table->string('qr_code', 32)->nullable()->unique()->after('is_primary');
            $table->timestamp('boarded_at')->nullable()->after('qr_code');
            $table->foreignId('boarded_by')->nullable()->after('boarded_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('booking_seats', function (Blueprint $table) {
            $table->dropForeign(['boarded_by']);
            $table->dropColumn(['qr_code', 'boarded_at', 'boarded_by']);
        });
    }
};
