<?php

namespace App\Livewire\Public;

use App\Models\Catamaran;
use App\Models\TimeSlot;
use App\Models\Addon;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\DiscountCode;
use App\Enums\BookingStatus;
use App\Enums\BookingType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
#[Title('Prenota la tua escursione - Solarya Travel')]
class BookingWizard extends Component
{
    // Current step
    public int $step = 1;
    public int $totalSteps = 5;

    // Step 1: Catamaran Selection
    #[Url]
    public ?string $catamaran_slug = null;
    public ?Catamaran $selectedCatamaran = null;

    // Step 2: Date & Time Selection
    public ?string $date = null;
    public ?int $timeSlotId = null;
    public ?TimeSlot $selectedTimeSlot = null;
    public array $availableDates = [];
    public array $availableSlots = [];

    // Step 3: Booking Type & Guests
    public string $bookingType = 'seats'; // 'seats' or 'exclusive'
    public int $seats = 2;
    public int $availableSeats = 0;

    // Step 4: Addons
    public array $selectedAddons = [];

    // Step 5: Customer Info
    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public string $phone = '';
    public string $country = 'IT';
    public string $specialRequests = '';
    public bool $acceptTerms = false;
    public bool $acceptPrivacy = false;

    // Discount Code
    public string $discountCode = '';
    public ?DiscountCode $appliedDiscount = null;
    public string $discountError = '';

    // Pricing
    public float $basePrice = 0;
    public float $addonsTotal = 0;
    public float $discountAmount = 0;
    public float $taxAmount = 0;
    public float $totalAmount = 0;

    // UI State
    public bool $loading = false;
    public string $currentMonth = '';

    protected $listeners = [
        'dateSelected' => 'selectDate',
    ];

    protected function rules(): array
    {
        return [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'country' => 'required|string|size:2',
            'specialRequests' => 'nullable|string|max:1000',
            'acceptTerms' => 'accepted',
            'acceptPrivacy' => 'accepted',
        ];
    }

    protected $messages = [
        'firstName.required' => 'Il nome è obbligatorio',
        'lastName.required' => 'Il cognome è obbligatorio',
        'email.required' => 'L\'email è obbligatoria',
        'email.email' => 'Inserisci un indirizzo email valido',
        'acceptTerms.accepted' => 'Devi accettare i termini e condizioni',
        'acceptPrivacy.accepted' => 'Devi accettare la privacy policy',
    ];

    public function mount(?string $slug = null): void
    {
        $this->currentMonth = now()->format('Y-m');

        if ($slug) {
            $this->catamaran_slug = $slug;
            $this->selectedCatamaran = Catamaran::where('slug', $slug)
                ->where('is_active', true)
                ->first();

            if ($this->selectedCatamaran) {
                $this->step = 2;
                $this->loadAvailableDates();
            }
        }
    }

    public function selectCatamaran(int $id): void
    {
        $this->selectedCatamaran = Catamaran::findOrFail($id);
        $this->catamaran_slug = $this->selectedCatamaran->slug;
        $this->loadAvailableDates();
        $this->nextStep();
    }

    public function loadAvailableDates(): void
    {
        if (!$this->selectedCatamaran) return;

        $startDate = now()->addHours(config('booking.advance_hours', 24));
        $endDate = now()->addMonths(3);

        $this->availableDates = Availability::where('catamaran_id', $this->selectedCatamaran->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', '!=', 'blocked')
            ->where(function ($query) {
                $query->where('seats_available', '>', 0)
                    ->orWhere('is_exclusive_booked', false);
            })
            ->pluck('date')
            ->unique()
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
            ->toArray();
    }

    public function selectDate(string $date): void
    {
        $this->date = $date;
        $this->loadAvailableSlots();
        $this->timeSlotId = null;
        $this->selectedTimeSlot = null;
    }

    public function loadAvailableSlots(): void
    {
        if (!$this->selectedCatamaran || !$this->date) return;

        $this->availableSlots = [];

        $slots = TimeSlot::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($slots as $slot) {
            $availability = Availability::where('catamaran_id', $this->selectedCatamaran->id)
                ->where('date', $this->date)
                ->where('time_slot_id', $slot->id)
                ->first();

            if ($availability && $availability->status !== 'blocked') {
                $this->availableSlots[] = [
                    'slot' => $slot,
                    'availability' => $availability,
                    'seats_available' => $availability->seats_available - $availability->seats_booked,
                    'is_exclusive_available' => !$availability->is_exclusive_booked,
                ];
            }
        }
    }

    public function selectTimeSlot(int $slotId): void
    {
        $this->timeSlotId = $slotId;
        $this->selectedTimeSlot = TimeSlot::find($slotId);

        // Get available seats for this slot
        $availability = Availability::where('catamaran_id', $this->selectedCatamaran->id)
            ->where('date', $this->date)
            ->where('time_slot_id', $slotId)
            ->first();

        $this->availableSeats = $availability 
            ? $availability->seats_available - $availability->seats_booked 
            : 0;

        $this->calculatePricing();
    }

    public function setBookingType(string $type): void
    {
        $this->bookingType = $type;

        if ($type === 'exclusive') {
            $this->seats = $this->selectedCatamaran->capacity;
        }

        $this->calculatePricing();
    }

    public function updateSeats(int $seats): void
    {
        $this->seats = max(1, min($seats, $this->availableSeats));
        $this->calculatePricing();
    }

    public function toggleAddon(int $addonId): void
    {
        if (in_array($addonId, $this->selectedAddons)) {
            $this->selectedAddons = array_filter($this->selectedAddons, fn($id) => $id !== $addonId);
        } else {
            $this->selectedAddons[] = $addonId;
        }

        $this->calculatePricing();
    }

    public function applyDiscountCode(): void
    {
        $this->discountError = '';
        $this->appliedDiscount = null;
        $this->discountAmount = 0;

        if (empty($this->discountCode)) {
            return;
        }

        $discount = DiscountCode::where('code', strtoupper($this->discountCode))
            ->where('is_active', true)
            ->first();

        if (!$discount) {
            $this->discountError = 'Codice sconto non valido';
            return;
        }

        if (!$discount->isValid()) {
            $this->discountError = 'Codice sconto scaduto o non più utilizzabile';
            return;
        }

        if ($discount->min_amount && $this->basePrice < $discount->min_amount) {
            $this->discountError = "Importo minimo richiesto: €" . number_format($discount->min_amount, 2);
            return;
        }

        $this->appliedDiscount = $discount;
        $this->calculatePricing();
    }

    public function removeDiscount(): void
    {
        $this->appliedDiscount = null;
        $this->discountCode = '';
        $this->discountAmount = 0;
        $this->calculatePricing();
    }

    public function calculatePricing(): void
    {
        if (!$this->selectedCatamaran || !$this->selectedTimeSlot) {
            return;
        }

        $isFullDay = $this->selectedTimeSlot->slot_type === 'full_day';

        // Base price calculation
        if ($this->bookingType === 'exclusive') {
            $this->basePrice = $isFullDay
                ? $this->selectedCatamaran->exclusive_price_full_day
                : $this->selectedCatamaran->exclusive_price_half_day;
        } else {
            $pricePerPerson = $isFullDay
                ? $this->selectedCatamaran->price_per_person_full_day
                : $this->selectedCatamaran->price_per_person_half_day;
            $this->basePrice = $pricePerPerson * $this->seats;
        }

        // Apply price modifier from time slot
        $this->basePrice *= $this->selectedTimeSlot->price_modifier;

        // Calculate addons total
        $this->addonsTotal = 0;
        foreach ($this->selectedAddons as $addonId) {
            $addon = Addon::find($addonId);
            if ($addon) {
                $this->addonsTotal += $addon->calculatePrice($this->seats, $isFullDay ? 1 : 0.5);
            }
        }

        // Calculate discount
        $this->discountAmount = 0;
        if ($this->appliedDiscount) {
            $this->discountAmount = $this->appliedDiscount->calculateDiscount(
                $this->basePrice + $this->addonsTotal
            );
        }

        // Calculate tax
        $subtotal = $this->basePrice + $this->addonsTotal - $this->discountAmount;
        $taxRate = config('booking.tax_rate', 22) / 100;
        $this->taxAmount = $subtotal * $taxRate;

        // Total
        $this->totalAmount = $subtotal + $this->taxAmount;
    }

    public function nextStep(): void
    {
        if ($this->validateCurrentStep()) {
            $this->step = min($this->step + 1, $this->totalSteps);
        }
    }

    public function previousStep(): void
    {
        $this->step = max($this->step - 1, 1);
    }

    public function goToStep(int $step): void
    {
        // Only allow going back or to completed steps
        if ($step < $this->step) {
            $this->step = $step;
        }
    }

    protected function validateCurrentStep(): bool
    {
        return match ($this->step) {
            1 => $this->selectedCatamaran !== null,
            2 => $this->date !== null && $this->timeSlotId !== null,
            3 => $this->seats > 0 && $this->seats <= $this->availableSeats,
            4 => true, // Addons are optional
            5 => $this->validateCustomerInfo(),
            default => true,
        };
    }

    protected function validateCustomerInfo(): bool
    {
        $this->validate();
        return true;
    }

    public function createBooking(): void
    {
        if (!$this->validateCurrentStep()) {
            return;
        }

        $this->loading = true;

        try {
            DB::beginTransaction();

            // Create booking
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'catamaran_id' => $this->selectedCatamaran->id,
                'time_slot_id' => $this->timeSlotId,
                'booking_date' => $this->date,
                'booking_type' => $this->bookingType,
                'seats' => $this->seats,
                'base_price' => $this->basePrice,
                'addons_total' => $this->addonsTotal,
                'discount_amount' => $this->discountAmount,
                'discount_code_id' => $this->appliedDiscount?->id,
                'tax_amount' => $this->taxAmount,
                'total_amount' => $this->totalAmount,
                'status' => BookingStatus::PENDING,
                'customer_first_name' => $this->firstName,
                'customer_last_name' => $this->lastName,
                'customer_email' => $this->email,
                'customer_phone' => $this->phone,
                'customer_country' => $this->country,
                'special_requests' => $this->specialRequests,
                'payment_deadline' => now()->addMinutes(config('booking.payment_expiry_minutes', 30)),
                'source' => 'website',
                'locale' => app()->getLocale(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Attach addons
            foreach ($this->selectedAddons as $addonId) {
                $addon = Addon::find($addonId);
                if ($addon) {
                    $isFullDay = $this->selectedTimeSlot->slot_type === 'full_day';
                    $unitPrice = $addon->price;
                    $quantity = $addon->price_type === 'per_person' ? $this->seats : 1;
                    
                    $booking->addons()->attach($addonId, [
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $addon->calculatePrice($this->seats, $isFullDay ? 1 : 0.5),
                    ]);
                }
            }

            // Update availability
            $availability = Availability::where('catamaran_id', $this->selectedCatamaran->id)
                ->where('date', $this->date)
                ->where('time_slot_id', $this->timeSlotId)
                ->first();

            if ($availability) {
                if ($this->bookingType === 'exclusive') {
                    $availability->is_exclusive_booked = true;
                    $availability->status = 'fully_booked';
                } else {
                    $availability->seats_booked += $this->seats;
                    if ($availability->seats_booked >= $availability->seats_available) {
                        $availability->status = 'fully_booked';
                    } else {
                        $availability->status = 'partially_booked';
                    }
                }
                $availability->save();
            }

            // Increment discount usage
            if ($this->appliedDiscount) {
                $this->appliedDiscount->increment('times_used');
            }

            DB::commit();

            // Redirect to payment
            $this->redirect(route('payment.show', $booking));

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Si è verificato un errore durante la creazione della prenotazione. Riprova.');
            $this->loading = false;
        }
    }

    public function render()
    {
        $catamarans = Catamaran::where('is_active', true)
            ->orderBy('sort_order')
            ->with('images')
            ->get();

        $addons = Addon::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('livewire.public.booking-wizard', [
            'catamarans' => $catamarans,
            'addons' => $addons,
        ]);
    }
}
