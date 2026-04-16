<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\DurationType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid',
        'booking_number',
        'user_id',
        'catamaran_id',
        'booking_type',
        'duration_type',
        'start_date',
        'end_date',
        'time_slot_id',
        'seats_booked',
        'status',
        'base_amount',
        'addons_amount',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'currency',
        'discount_code_id',
        'qr_code',
        'qr_code_url',
        'checked_in_at',
        'checked_in_by',
        'customer_notes',
        'admin_notes',
        'ip_address',
        'user_agent',
        'source',
        'confirmed_at',
        'cancelled_at',
        'completed_at',
        'expires_at',
    ];

    protected $casts = [
        'booking_type' => BookingType::class,
        'duration_type' => DurationType::class,
        'status' => BookingStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'base_amount' => 'decimal:2',
        'addons_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'checked_in_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function catamaran(): BelongsTo
    {
        return $this->belongsTo(Catamaran::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function seats(): HasMany
    {
        return $this->hasMany(BookingSeat::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(BookingAddon::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function successfulPayment(): HasOne
    {
        return $this->hasOne(Payment::class)
            ->where('status', 'succeeded')
            ->latest();
    }

    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    public function checkedInByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', BookingStatus::PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', BookingStatus::CONFIRMED);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString())
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::PENDING]);
    }

    // Helpers
    public function isExclusive(): bool
    {
        return $this->booking_type === BookingType::EXCLUSIVE;
    }

    public function isPending(): bool
    {
        return $this->status === BookingStatus::PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === BookingStatus::CONFIRMED;
    }

    public function isCheckedIn(): bool
    {
        return $this->status === BookingStatus::CHECKED_IN;
    }

    public function canBeCheckedIn(): bool
    {
        return $this->isConfirmed() && $this->start_date->isToday();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [BookingStatus::PENDING, BookingStatus::CONFIRMED]);
    }

    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getFormattedDatesAttribute(): string
    {
        if ($this->start_date->equalTo($this->end_date)) {
            return $this->start_date->format('d/m/Y');
        }

        return $this->start_date->format('d/m/Y') . ' - ' . $this->end_date->format('d/m/Y');
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = static::generateBookingNumber();
            }
            if (empty($booking->qr_code)) {
                $booking->qr_code = static::generateQRCode();
            }
        });
    }

    public static function generateBookingNumber(): string
    {
        $year = now()->format('Y');
        $lastBooking = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastBooking 
            ? (int) substr($lastBooking->booking_number, -5) + 1 
            : 1;

        return sprintf('SLY-%s-%05d', $year, $sequence);
    }

    public static function generateQRCode(): string
    {
        do {
            $code = strtoupper(\Illuminate\Support\Str::random(12));
        } while (static::where('qr_code', $code)->exists());

        return $code;
    }
}
