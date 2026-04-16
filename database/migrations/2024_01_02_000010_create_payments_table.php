<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('gateway', 50);
            $table->string('gateway_payment_id')->nullable();
            $table->string('gateway_payment_intent')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->enum('status', [
                'pending', 'processing', 'succeeded', 'failed', 'cancelled', 'refunded', 'partially_refunded'
            ])->default('pending');
            $table->string('payment_method_type', 50)->nullable();
            $table->string('last_four', 4)->nullable();
            $table->string('card_brand', 20)->nullable();
            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->string('failure_code')->nullable();
            $table->text('failure_message')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index('gateway_payment_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
