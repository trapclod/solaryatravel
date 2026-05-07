<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourAgeBracket extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'tour_period_id',
        'label',
        'min_age',
        'max_age',
        'price',
        'counts_as_seat',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'counts_as_seat' => 'boolean',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(TourPeriod::class, 'tour_period_id');
    }

    public function getRangeLabelAttribute(): string
    {
        if ($this->min_age === 0 && is_null($this->max_age)) {
            return 'Tutte le età';
        }
        if (is_null($this->max_age)) {
            return $this->min_age . '+ anni';
        }
        return $this->min_age . '-' . $this->max_age . ' anni';
    }
}
