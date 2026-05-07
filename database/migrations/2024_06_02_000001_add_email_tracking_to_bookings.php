<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('payment_link_sent_at')->nullable()->after('confirmed_at');
            $table->timestamp('tickets_sent_at')->nullable()->after('payment_link_sent_at');
            $table->string('checkout_url', 1024)->nullable()->after('tickets_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_link_sent_at', 'tickets_sent_at', 'checkout_url']);
        });
    }
};
