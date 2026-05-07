<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'label',
        'start_date',
        'end_date',
        'weekdays',
        'times',
        'base_price',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'weekdays' => 'array',
        'times' => 'array',
        'base_price' => 'decimal:2',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function ageBrackets(): HasMany
    {
        return $this->hasMany(TourAgeBracket::class)->orderBy('sort_order');
    }
}
