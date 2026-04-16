<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Addon extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'price',
        'price_type',
        'max_quantity',
        'is_active',
        'requires_advance_booking',
        'advance_hours',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'requires_advance_booking' => 'boolean',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_addons')
            ->withPivot(['quantity', 'unit_price', 'total_price'])
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Helpers
    public function calculatePrice(int $quantity, int $persons = 1, int $days = 1): float
    {
        return match($this->price_type) {
            'per_booking' => $this->price * $quantity,
            'per_person' => $this->price * $quantity * $persons,
            'per_day' => $this->price * $quantity * $days,
            default => $this->price * $quantity,
        };
    }

    public function getPriceTypeLabel(): string
    {
        return match($this->price_type) {
            'per_booking' => 'per prenotazione',
            'per_person' => 'per persona',
            'per_day' => 'per giorno',
            default => '',
        };
    }
}
