<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'start_time',
        'end_time',
        'slot_type',
        'price_modifier',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'price_modifier' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function availability(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHalfDay($query)
    {
        return $query->where('slot_type', 'half_day');
    }

    public function scopeFullDay($query)
    {
        return $query->where('slot_type', 'full_day');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    public function isHalfDay(): bool
    {
        return $this->slot_type === 'half_day';
    }

    public function isFullDay(): bool
    {
        return $this->slot_type === 'full_day';
    }
}
