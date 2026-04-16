<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Catamaran;
use App\Models\TimeSlot;
use App\Models\Availability;
use App\Models\DiscountCode;
use App\Enums\BookingStatus;
use App\Enums\BookingType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingService
{
    public function __construct(
        protected PricingService $pricingService
    ) {}

    /**
     * Create a new booking.
     */
    public function create(array $data, string $source = 'website'): Booking
    {
        return DB::transaction(function () use ($data, $source) {
            $catamaran = Catamaran::findOrFail($data['catamaran_id']);
            $timeSlot = TimeSlot::findOrFail($data['time_slot_id']);
            $bookingDate = Carbon::parse($data['booking_date']);
            
            // Check availability
            $availability = $this->checkAvailability(
                $catamaran->id,
                $bookingDate,
                $timeSlot->id,
                $data['booking_type'],
                $data['seats'] ?? 1
            );

            if (!$availability['available']) {
                throw new \Exception($availability['message']);
            }

            // Calculate pricing
            $pricing = $this->pricingService->calculate(
                $catamaran,
                $timeSlot,
                $data['booking_type'],
                $data['seats'] ?? 1,
                $data['addons'] ?? [],
                $data['discount_code'] ?? null
            );

            // Generate booking number
            $bookingNumber = $this->generateBookingNumber();

            // Create booking
            $booking = Booking::create([
                'booking_number' => $bookingNumber,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'catamaran_id' => $catamaran->id,
                'time_slot_id' => $timeSlot->id,
                'booking_date' => $bookingDate,
                'booking_type' => $data['booking_type'],
                'seats' => $data['seats'] ?? $catamaran->capacity,
                'base_price' => $pricing['base_price'],
                'addons_total' => $pricing['addons_total'],
                'discount_amount' => $pricing['discount_amount'],
                'discount_code_id' => $pricing['discount_code_id'],
                'tax_amount' => $pricing['tax_amount'],
                'total_amount' => $pricing['total_amount'],
                'status' => $data['status'] ?? BookingStatus::PENDING,
                'customer_first_name' => $data['customer_first_name'],
                'customer_last_name' => $data['customer_last_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_country' => $data['customer_country'] ?? 'IT',
                'special_requests' => $data['special_requests'] ?? null,
                'admin_notes' => $data['admin_notes'] ?? null,
                'payment_deadline' => now()->addMinutes(config('booking.payment_expiry_minutes', 30)),
                'source' => $source,
                'locale' => app()->getLocale(),
                'verification_code' => strtoupper(Str::random(16)),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Attach addons
            if (!empty($data['addons'])) {
                foreach ($data['addons'] as $addonId) {
                    $addon = \App\Models\Addon::find($addonId);
                    if ($addon) {
                        $isFullDay = $timeSlot->slot_type === 'full_day';
                        $quantity = $addon->price_type === 'per_person' ? $booking->seats : 1;
                        $unitPrice = $addon->price;
                        $totalPrice = $addon->calculatePrice($booking->seats, $isFullDay ? 1 : 0.5);
                        
                        $booking->addons()->attach($addonId, [
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'total_price' => $totalPrice,
                        ]);
                    }
                }
            }

            // Update availability
            $this->updateAvailability(
                $catamaran->id,
                $bookingDate,
                $timeSlot->id,
                $data['booking_type'],
                $data['seats'] ?? 1
            );

            // Increment discount usage if applied
            if ($pricing['discount_code_id']) {
                DiscountCode::find($pricing['discount_code_id'])?->increment('times_used');
            }

            return $booking;
        });
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Booking $booking, ?string $reason = null): bool
    {
        if (!$booking->canBeCancelled()) {
            throw new \Exception('Questa prenotazione non può essere annullata.');
        }

        return DB::transaction(function () use ($booking, $reason) {
            // Restore availability
            $this->restoreAvailability($booking);

            // Update booking status
            $booking->update([
                'status' => BookingStatus::CANCELLED,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            // Handle refund if paid
            if ($booking->isPaid()) {
                // TODO: Process refund through payment service
            }

            // Restore discount code usage
            if ($booking->discount_code_id) {
                DiscountCode::find($booking->discount_code_id)?->decrement('times_used');
            }

            return true;
        });
    }

    /**
     * Check availability for a booking.
     */
    public function checkAvailability(
        int $catamaranId,
        Carbon $date,
        int $timeSlotId,
        string $bookingType,
        int $seats = 1
    ): array {
        // Check if date is in the past
        if ($date->lt(now()->startOfDay())) {
            return ['available' => false, 'message' => 'La data selezionata è nel passato.'];
        }

        // Check advance booking hours
        $minAdvanceHours = config('booking.advance_hours', 24);
        if ($date->diffInHours(now()) < $minAdvanceHours) {
            return [
                'available' => false,
                'message' => "È necessario prenotare con almeno {$minAdvanceHours} ore di anticipo."
            ];
        }

        // Check if date is too far in the future
        $maxAdvanceDays = config('booking.max_advance_days', 90);
        if ($date->diffInDays(now()) > $maxAdvanceDays) {
            return [
                'available' => false,
                'message' => "Non è possibile prenotare con più di {$maxAdvanceDays} giorni di anticipo."
            ];
        }

        // Get availability record
        $availability = Availability::where('catamaran_id', $catamaranId)
            ->where('date', $date->toDateString())
            ->where('time_slot_id', $timeSlotId)
            ->first();

        if (!$availability || $availability->status === 'blocked') {
            return ['available' => false, 'message' => 'Questo slot non è disponibile.'];
        }

        if ($availability->status === 'fully_booked') {
            return ['available' => false, 'message' => 'Questo slot è già al completo.'];
        }

        if ($bookingType === 'exclusive') {
            if ($availability->is_exclusive_booked) {
                return ['available' => false, 'message' => 'Questo slot è già prenotato in esclusiva.'];
            }
            if ($availability->seats_booked > 0) {
                return ['available' => false, 'message' => 'Non è possibile prenotare in esclusiva, ci sono già altri ospiti.'];
            }
        } else {
            $availableSeats = $availability->seats_available - $availability->seats_booked;
            if ($seats > $availableSeats) {
                return [
                    'available' => false,
                    'message' => "Solo {$availableSeats} posti disponibili per questo slot."
                ];
            }
        }

        return [
            'available' => true,
            'seats_available' => $availability->seats_available - $availability->seats_booked,
            'is_exclusive_available' => !$availability->is_exclusive_booked,
        ];
    }

    /**
     * Update availability after booking.
     */
    protected function updateAvailability(
        int $catamaranId,
        Carbon $date,
        int $timeSlotId,
        string $bookingType,
        int $seats
    ): void {
        $availability = Availability::where('catamaran_id', $catamaranId)
            ->where('date', $date->toDateString())
            ->where('time_slot_id', $timeSlotId)
            ->firstOrFail();

        if ($bookingType === 'exclusive') {
            $availability->update([
                'is_exclusive_booked' => true,
                'status' => 'fully_booked',
            ]);
        } else {
            $newSeatsBooked = $availability->seats_booked + $seats;
            $newStatus = $newSeatsBooked >= $availability->seats_available 
                ? 'fully_booked' 
                : 'partially_booked';

            $availability->update([
                'seats_booked' => $newSeatsBooked,
                'status' => $newStatus,
            ]);
        }
    }

    /**
     * Restore availability after cancellation.
     */
    protected function restoreAvailability(Booking $booking): void
    {
        $availability = Availability::where('catamaran_id', $booking->catamaran_id)
            ->where('date', $booking->booking_date->toDateString())
            ->where('time_slot_id', $booking->time_slot_id)
            ->first();

        if (!$availability) return;

        if ($booking->isExclusive()) {
            $availability->update([
                'is_exclusive_booked' => false,
                'status' => 'available',
            ]);
        } else {
            $newSeatsBooked = max(0, $availability->seats_booked - $booking->seats);
            $newStatus = $newSeatsBooked > 0 ? 'partially_booked' : 'available';

            $availability->update([
                'seats_booked' => $newSeatsBooked,
                'status' => $newStatus,
            ]);
        }
    }

    /**
     * Generate a unique booking number.
     */
    protected function generateBookingNumber(): string
    {
        do {
            $number = strtoupper(sprintf(
                '%s-%s-%s',
                Str::random(4),
                Str::random(4),
                Str::random(4)
            ));
        } while (Booking::where('booking_number', $number)->exists());

        return $number;
    }

    /**
     * Send booking confirmation email.
     */
    public function sendConfirmation(Booking $booking): void
    {
        // TODO: Implement email sending
        // Mail::to($booking->customer_email)->send(new BookingConfirmation($booking));
    }

    /**
     * Send booking reminder.
     */
    public function sendReminder(Booking $booking): void
    {
        // TODO: Implement reminder email
    }
}
