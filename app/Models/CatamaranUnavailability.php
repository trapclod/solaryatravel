<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatamaranUnavailability extends Model
{
    use HasFactory;

    protected $table = 'catamaran_unavailability';

    protected $fillable = [
        'catamaran_id',
        'date_start',
        'date_end',
        'reason',
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
    ];

    public function catamaran(): BelongsTo
    {
        return $this->belongsTo(Catamaran::class);
    }

    public function scopeOverlapping($query, $date)
    {
        return $query->where('date_start', '<=', $date)->where('date_end', '>=', $date);
    }
}
