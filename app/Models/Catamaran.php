<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Catamaran extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'description_short',
        'capacity',
        'length_meters',
        'features',
        'base_price_half_day',
        'base_price_full_day',
        'exclusive_price_half_day',
        'exclusive_price_full_day',
        'price_per_person_half_day',
        'price_per_person_full_day',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'base_price_half_day' => 'decimal:2',
        'base_price_full_day' => 'decimal:2',
        'exclusive_price_half_day' => 'decimal:2',
        'exclusive_price_full_day' => 'decimal:2',
        'price_per_person_half_day' => 'decimal:2',
        'price_per_person_full_day' => 'decimal:2',
        'length_meters' => 'decimal:2',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Relationships
    public function images(): HasMany
    {
        return $this->hasMany(CatamaranImage::class)->orderBy('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function availability(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getPrimaryImageAttribute(): ?CatamaranImage
    {
        return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        return $this->primaryImage?->image_path;
    }

    // Helpers
    public function getPriceForDuration(string $durationType, bool $isExclusive = false): float
    {
        if ($isExclusive) {
            return $durationType === 'half_day' 
                ? $this->exclusive_price_half_day 
                : $this->exclusive_price_full_day;
        }

        return $durationType === 'half_day' 
            ? $this->base_price_half_day 
            : $this->base_price_full_day;
    }

    public function getPricePerPerson(string $durationType): float
    {
        return $durationType === 'half_day' 
            ? $this->price_per_person_half_day 
            : $this->price_per_person_full_day;
    }
}
