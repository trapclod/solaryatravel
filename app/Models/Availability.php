<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    use HasFactory;

    protected $table = 'availability';

    protected $fillable = [
        'catamaran_id',
        'date',
        'time_slot_id',
        'status',
        'seats_available',
        'seats_booked',
        'is_exclusive_booked',
        'block_reason',
        'custom_price',
    ];

    protected $casts = [
        'date' => 'date',
        'is_exclusive_booked' => 'boolean',
        'seats_available' => 'integer',
        'seats_booked' => 'integer',
        'custom_price' => 'decimal:2',
    ];

    public function catamaran(): BelongsTo
    {
        return $this->belongsTo(Catamaran::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForCatamaran($query, $catamaranId)
    {
        return $query->where('catamaran_id', $catamaranId);
    }

    public function scopeAvailable($query)
    {
        return $query->whereIn('status', ['available', 'partially_booked']);
    }

    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    // Helpers
    public function isAvailable(): bool
    {
        return in_array($this->status, ['available', 'partially_booked']);
    }

    public function isFullyBooked(): bool
    {
        return $this->status === 'fully_booked' || $this->is_exclusive_booked;
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function getRemainingSeats(): int
    {
        return max(0, $this->seats_available - $this->seats_booked);
    }

    public function canBookSeats(int $seats): bool
    {
        return $this->isAvailable() && !$this->is_exclusive_booked && $this->getRemainingSeats() >= $seats;
    }

    public function canBookExclusive(): bool
    {
        return $this->isAvailable() && $this->seats_booked === 0 && !$this->is_exclusive_booked;
    }

    public function getEffectivePrice(): ?float
    {
        return $this->custom_price;
    }
}
