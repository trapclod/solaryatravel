<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_seats', function (Blueprint $table) {
            $table->string('tax_code', 32)->nullable()->after('guest_date_of_birth');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('participants_token', 64)->nullable()->unique()->after('qr_code');
            $table->timestamp('participants_details_requested_at')->nullable()->after('tickets_sent_at');
            $table->timestamp('participants_completed_at')->nullable()->after('participants_details_requested_at');
            $table->timestamp('reminder_48h_sent_at')->nullable()->after('participants_completed_at');
            $table->timestamp('reminder_24h_sent_at')->nullable()->after('reminder_48h_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('booking_seats', function (Blueprint $table) {
            $table->dropColumn('tax_code');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'participants_token',
                'participants_details_requested_at',
                'participants_completed_at',
                'reminder_48h_sent_at',
                'reminder_24h_sent_at',
            ]);
        });
    }
};
