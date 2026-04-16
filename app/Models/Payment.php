<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'uuid',
        'booking_id',
        'gateway',
        'gateway_payment_id',
        'gateway_transaction_id',
        'gateway_customer_id',
        'amount',
        'currency',
        'fee_amount',
        'net_amount',
        'status',
        'card_brand',
        'card_last_four',
        'gateway_response',
        'failure_reason',
        'refund_reason',
        'refunded_amount',
        'refunded_at',
        'idempotency_key',
        'paid_at',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'gateway_response' => 'array',
        'amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // Scopes
    public function scopeSucceeded($query)
    {
        return $query->where('status', PaymentStatus::SUCCEEDED);
    }

    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::PENDING);
    }

    public function scopeForGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    // Helpers
    public function isSucceeded(): bool
    {
        return $this->status === PaymentStatus::SUCCEEDED;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::FAILED;
    }

    public function canBeRefunded(): bool
    {
        return $this->isSucceeded() && $this->refunded_amount < $this->amount;
    }

    public function getRemainingRefundableAmount(): float
    {
        return $this->amount - $this->refunded_amount;
    }

    public function isStripe(): bool
    {
        return $this->gateway === 'stripe';
    }

    public function isPayPal(): bool
    {
        return $this->gateway === 'paypal';
    }

    public function getCardDisplayAttribute(): ?string
    {
        if (!$this->card_brand || !$this->card_last_four) {
            return null;
        }

        return ucfirst($this->card_brand) . ' •••• ' . $this->card_last_four;
    }
}
