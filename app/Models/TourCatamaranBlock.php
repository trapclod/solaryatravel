<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourCatamaranBlock extends Model
{
    protected $table = 'tour_catamaran_blocks';

    protected $fillable = [
        'tour_id',
        'catamaran_id',
        'start_date',
        'end_date',
        'reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function catamaran(): BelongsTo
    {
        return $this->belongsTo(Catamaran::class);
    }
}
