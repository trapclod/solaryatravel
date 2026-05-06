<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingSeat extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'seat_number',
        'catamaran_id',
        'tour_age_bracket_id',
        'price_paid',
        'guest_first_name',
        'guest_last_name',
        'guest_date_of_birth',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'price_paid' => 'decimal:2',
        'guest_date_of_birth' => 'date',
        'created_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function catamaran(): BelongsTo
    {
        return $this->belongsTo(Catamaran::class);
    }

    public function ageBracket(): BelongsTo
    {
        return $this->belongsTo(TourAgeBracket::class, 'tour_age_bracket_id');
    }

    public function getGuestFullNameAttribute(): string
    {
        return trim(($this->guest_first_name ?? '') . ' ' . ($this->guest_last_name ?? ''));
    }
}
