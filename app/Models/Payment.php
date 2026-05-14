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
        'gateway_payment_intent',
        'amount',
        'currency',
        'status',
        'payment_method_type',
        'last_four',
        'card_brand',
        'gateway_response',
        'failure_code',
        'failure_message',
        'refunded_amount',
        'refunded_at',
        'paid_at',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'gateway_response' => 'array',
        'amount' => 'decimal:2',
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
        if (!$this->card_brand || !$this->last_four) {
            return null;
        }

        return ucfirst($this->card_brand) . ' •••• ' . $this->last_four;
    }
}
