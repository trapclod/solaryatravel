<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('booking_number', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('catamaran_id')->constrained()->cascadeOnDelete();
            $table->foreignId('time_slot_id')->constrained()->cascadeOnDelete();
            $table->date('booking_date');
            $table->enum('booking_type', ['seats', 'exclusive']);
            $table->unsignedInteger('seats')->default(1);
            $table->decimal('base_price', 10, 2);
            $table->decimal('addons_total', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->foreignId('discount_code_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->enum('status', [
                'pending', 'confirmed', 'checked_in', 'completed', 'cancelled', 'refunded', 'no_show'
            ])->default('pending');
            $table->string('customer_first_name');
            $table->string('customer_last_name');
            $table->string('customer_email');
            $table->string('customer_phone', 30)->nullable();
            $table->string('customer_country', 2)->nullable();
            $table->text('special_requests')->nullable();
            $table->string('qr_code', 100)->unique()->nullable();
            $table->timestamp('payment_deadline')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source', 50)->default('website');
            $table->string('external_reference')->nullable();
            $table->json('metadata')->nullable();
            $table->string('locale', 5)->default('it');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('booking_date');
            $table->index('status');
            $table->index(['catamaran_id', 'booking_date']);
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
