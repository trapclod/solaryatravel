<?php

namespace App\Livewire\Public;

use App\Models\Addon;
use App\Models\Tour;
use App\Models\TourDeparture;
use App\Services\BookingService;
use App\Services\PricingService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BookingForm extends Component
{
    public Tour $tour;
    public ?TourDeparture $departure = null;

    /** @var array<int,int> bracket_id => quantity */
    public array $bracketCounts = [];

    /** @var array<int> addon ids selezionati */
    public array $selectedAddons = [];

    public string $discountCode = '';
    public ?string $discountFeedback = null;
    public bool $discountValid = false;

    // Dati cliente
    public string $customer_first_name = '';
    public string $customer_last_name = '';
    public string $customer_email = '';
    public string $customer_phone = '';
    public string $special_requests = '';
    public bool $terms = false;

    public ?string $errorMessage = null;

    public function mount(Tour $tour, ?TourDeparture $departure = null): void
    {
        $this->tour = $tour;
        $this->departure = $departure;

        if (auth()->check()) {
            $u = auth()->user();
            $this->customer_first_name = $u->first_name ?? $u->name ?? '';
            $this->customer_last_name = $u->last_name ?? '';
            $this->customer_email = $u->email ?? '';
        }

        // Inizializza i counter delle fasce a 0
        foreach ($this->brackets as $b) {
            $this->bracketCounts[$b->id] = 0;
        }
    }

    /**
     * Fasce d'età applicabili alla data della partenza (via PricingService).
     */
    #[Computed]
    public function brackets(): Collection
    {
        if (!$this->departure) {
            return collect();
        }
        return app(PricingService::class)
            ->resolveBrackets($this->tour, $this->departure->departure_date);
    }

    #[Computed]
    public function addons(): Collection
    {
        return Addon::active()->ordered()->get();
    }

    #[Computed]
    public function pricing(): array
    {
        if (!$this->departure || empty($this->totalSelected)) {
            return [
                'base_price' => 0, 'addons_total' => 0, 'discount_amount' => 0,
                'discount_code_id' => null, 'subtotal' => 0, 'tax_rate' => 0,
                'tax_amount' => 0, 'total_amount' => 0, 'total_seats' => 0,
                'counting_seats' => 0, 'brackets' => [],
            ];
        }
        return app(PricingService::class)->calculate(
            $this->tour,
            $this->departure,
            array_filter($this->bracketCounts, fn($v) => (int) $v > 0),
            $this->selectedAddons,
            $this->discountValid ? $this->discountCode : null
        );
    }

    #[Computed]
    public function totalSelected(): int
    {
        return array_sum(array_map('intval', $this->bracketCounts));
    }

    public function increment(int $bracketId): void
    {
        $this->bracketCounts[$bracketId] = (int) ($this->bracketCounts[$bracketId] ?? 0) + 1;
    }

    public function decrement(int $bracketId): void
    {
        $current = (int) ($this->bracketCounts[$bracketId] ?? 0);
        $this->bracketCounts[$bracketId] = max(0, $current - 1);
    }

    public function toggleAddon(int $addonId): void
    {
        if (in_array($addonId, $this->selectedAddons, true)) {
            $this->selectedAddons = array_values(array_diff($this->selectedAddons, [$addonId]));
        } else {
            $this->selectedAddons[] = $addonId;
        }
    }

    public function applyDiscount(): void
    {
        $this->discountFeedback = null;
        $this->discountValid = false;
        if (trim($this->discountCode) === '') {
            return;
        }
        $code = \App\Models\DiscountCode::where('code', strtoupper($this->discountCode))
            ->where('is_active', true)
            ->first();
        if (!$code || !$code->isValid()) {
            $this->discountFeedback = 'Codice non valido o scaduto.';
            return;
        }
        $this->discountValid = true;
        $this->discountFeedback = 'Codice applicato correttamente.';
    }

    public function removeDiscount(): void
    {
        $this->discountCode = '';
        $this->discountFeedback = null;
        $this->discountValid = false;
    }

    public function submit(BookingService $bookingService)
    {
        $this->errorMessage = null;

        $rules = [
            'customer_first_name' => 'required|string|max:100',
            'customer_last_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'special_requests' => 'nullable|string|max:1000',
            'terms' => 'accepted',
        ];

        $this->validate($rules, [
            'terms.accepted' => 'Devi accettare i termini e condizioni.',
        ]);

        if (!$this->departure) {
            $this->errorMessage = 'Seleziona una data di partenza.';
            return null;
        }

        $bracketCounts = array_filter($this->bracketCounts, fn($v) => (int) $v > 0);
        if (empty($bracketCounts)) {
            $this->errorMessage = 'Seleziona almeno un partecipante.';
            return null;
        }

        try {
            $booking = $bookingService->create([
                'tour_id' => $this->tour->id,
                'tour_departure_id' => $this->departure->id,
                'bracket_counts' => $bracketCounts,
                'addons' => $this->selectedAddons,
                'discount_code' => $this->discountValid ? $this->discountCode : null,
                'customer_first_name' => $this->customer_first_name,
                'customer_last_name' => $this->customer_last_name,
                'customer_email' => $this->customer_email,
                'customer_phone' => $this->customer_phone,
                'special_requests' => $this->special_requests,
            ], 'website');

            return redirect()->route('payment.show', $booking->uuid);
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
            return null;
        }
    }

    public function render()
    {
        return view('livewire.public.booking-form');
    }
}
