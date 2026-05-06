<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourDeparture extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'departure_date',
        'start_time',
        'end_time',
        'status',
        'price_modifier',
        'capacity_override',
        'notes',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'price_modifier' => 'decimal:2',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Posti già venduti (somma seats di prenotazioni non cancellate
     * che occupano un posto vero — ageBracket.counts_as_seat = true,
     * o non specificato).
     */
    public function getSeatsBookedAttribute(): int
    {
        return (int) $this->bookings()
            ->whereNotIn('status', ['cancelled', 'refunded', 'no_show'])
            ->sum('seats');
    }

    public function getCapacityAttribute(): int
    {
        if (!is_null($this->capacity_override)) {
            return (int) $this->capacity_override;
        }
        return $this->tour?->total_capacity ?? 0;
    }

    public function getSeatsAvailableAttribute(): int
    {
        return max(0, $this->capacity - $this->seats_booked);
    }

    public function isSoldOut(): bool
    {
        return $this->seats_available <= 0 || $this->status !== 'scheduled';
    }
}
