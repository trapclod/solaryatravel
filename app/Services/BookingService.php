<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Catamaran;
use App\Models\DiscountCode;
use App\Models\Tour;
use App\Models\TourAgeBracket;
use App\Models\TourDeparture;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected PricingService $pricingService
    ) {}

    /**
     * Crea una prenotazione su un tour, distribuendo automaticamente i posti
     * tra i catamarani disponibili (ottimizzazione: gruppo unito quando possibile).
     *
     * @param  array  $data  [
     *   'tour_id', 'tour_departure_id', 'bracket_counts' => [id => count],
     *   'customer_first_name', 'customer_last_name', 'customer_email', ...,
     *   'addons' => [], 'discount_code' => null
     * ]
     */
    public function create(array $data, string $source = 'website'): Booking
    {
        return DB::transaction(function () use ($data, $source) {
            /** @var Tour $tour */
            $tour = Tour::findOrFail($data['tour_id']);
            /** @var TourDeparture $departure */
            $departure = TourDeparture::where('tour_id', $tour->id)
                ->lockForUpdate()
                ->findOrFail($data['tour_departure_id']);

            if ($departure->status !== 'scheduled') {
                throw new \Exception('Questa partenza non è disponibile.');
            }

            $bracketCounts = $data['bracket_counts'] ?? [];
            if (empty(array_filter($bracketCounts))) {
                throw new \Exception('Devi selezionare almeno un partecipante.');
            }

            // Pricing
            $pricing = $this->pricingService->calculate(
                $tour,
                $departure,
                $bracketCounts,
                $data['addons'] ?? [],
                $data['discount_code'] ?? null
            );

            $countingSeats = $pricing['counting_seats'];
            if ($countingSeats <= 0) {
                throw new \Exception('Numero posti non valido.');
            }

            // Auto-distribuzione
            $assignment = $this->distributeSeats($tour, $departure, $countingSeats);
            if ($assignment === null) {
                throw new \Exception('Posti insufficienti per questa partenza.');
            }

            $booking = Booking::create([
                'user_id' => $data['user_id'] ?? auth()->id(),
                'tour_id' => $tour->id,
                'tour_departure_id' => $departure->id,
                'booking_date' => $departure->departure_date,
                'seats' => $countingSeats,
                'base_price' => $pricing['base_price'],
                'addons_total' => $pricing['addons_total'],
                'discount_amount' => $pricing['discount_amount'],
                'discount_code_id' => $pricing['discount_code_id'],
                'tax_amount' => $pricing['tax_amount'],
                'total_amount' => $pricing['total_amount'],
                'currency' => 'EUR',
                'status' => BookingStatus::PENDING,
                'customer_first_name' => $data['customer_first_name'],
                'customer_last_name' => $data['customer_last_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_country' => $data['customer_country'] ?? 'IT',
                'special_requests' => $data['special_requests'] ?? null,
                'payment_deadline' => now()->addMinutes(config('booking.payment_expiry_minutes', 30)),
                'source' => $source,
                'locale' => app()->getLocale(),
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'metadata' => [
                    'pricing' => $pricing,
                    'distribution' => $assignment,
                ],
            ]);

            // Crea booking_seats per ogni partecipante (counting + non-counting)
            $this->createSeats($booking, $bracketCounts, $assignment, $data['guests'] ?? []);

            // Addons
            if (!empty($data['addons'])) {
                foreach ($data['addons'] as $addonId) {
                    $addon = \App\Models\Addon::find($addonId);
                    if ($addon) {
                        $totalPrice = (float) $addon->calculatePrice($countingSeats, max(0.5, ($tour->duration_hours ?? 0) / 8));
                        $booking->addons()->attach($addonId, [
                            'quantity' => $addon->price_type === 'per_person' ? $countingSeats : 1,
                            'unit_price' => $addon->price,
                            'total_price' => $totalPrice,
                        ]);
                    }
                }
            }

            // Aggiorna utilizzo discount code
            if ($pricing['discount_code_id']) {
                DiscountCode::find($pricing['discount_code_id'])?->increment('times_used');
            }

            return $booking->fresh(['seatRecords.catamaran', 'tour', 'departure']);
        });
    }

    /**
     * Annulla una prenotazione (libera i posti).
     */
    public function cancel(Booking $booking, ?string $reason = null): bool
    {
        if (!$booking->canBeCancelled()) {
            throw new \Exception('Questa prenotazione non può essere annullata.');
        }

        return DB::transaction(function () use ($booking, $reason) {
            $booking->update([
                'status' => BookingStatus::CANCELLED,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            // I seat records restano per audit ma la booking essendo cancelled
            // non conta più nelle disponibilità (vedi scope active e seatsBookedOnDeparture).

            if ($booking->discount_code_id) {
                DiscountCode::find($booking->discount_code_id)?->decrement('times_used');
            }

            return true;
        });
    }

    /**
     * Verifica disponibilità di una partenza per N posti.
     *
     * @return array{available:bool, message?:string, distribution?:array}
     */
    public function checkAvailability(TourDeparture $departure, int $seats): array
    {
        $departure->loadMissing('tour');
        $tour = $departure->tour;

        // Vincoli temporali
        $date = Carbon::parse($departure->departure_date);
        if ($date->lt(now()->startOfDay())) {
            return ['available' => false, 'message' => 'La data selezionata è nel passato.'];
        }

        $minAdvanceHours = (int) config('booking.advance_hours', 0);
        if ($minAdvanceHours > 0 && $date->diffInHours(now()) < $minAdvanceHours) {
            return ['available' => false, 'message' => "Serve prenotare con almeno {$minAdvanceHours} ore di anticipo."];
        }

        if ($departure->status !== 'scheduled') {
            return ['available' => false, 'message' => 'Partenza non disponibile.'];
        }

        if ($seats < ($tour->min_capacity ?? 1)) {
            return ['available' => false, 'message' => "Numero minimo partecipanti: {$tour->min_capacity}."];
        }
        if ($tour->max_capacity && $seats > $tour->max_capacity) {
            return ['available' => false, 'message' => "Numero massimo partecipanti: {$tour->max_capacity}."];
        }

        $assignment = $this->distributeSeats($tour, $departure, $seats);
        if ($assignment === null) {
            return ['available' => false, 'message' => 'Posti insufficienti per questa partenza.'];
        }

        return ['available' => true, 'distribution' => $assignment];
    }

    /**
     * Auto-distribuzione posti tra catamarani con ottimizzazione "gruppo unito".
     *
     * Strategia:
     *  1. Recupera catamarani operativi del tour disponibili nella data.
     *  2. Calcola posti liberi su ciascuno (capacity - già prenotati).
     *  3. Se UN catamarano basta da solo → assegna tutto lì (gruppo unito).
     *  4. Altrimenti riempi i catamarani in ordine decrescente di posti liberi
     *     (minimizza il numero di catamarani coinvolti).
     *
     * @return array<int, array{catamaran_id:int, seats:int}>|null
     */
    public function distributeSeats(Tour $tour, TourDeparture $departure, int $seats): ?array
    {
        $catamarans = $tour->operatingCatamarans();

        $candidates = [];
        foreach ($catamarans as $cat) {
            // Salta catamarani non disponibili nella data (manutenzione, blocchi)
            if (!$cat->isAvailableOn($departure->departure_date)) {
                continue;
            }
            $booked = $cat->seatsBookedOnDeparture($departure->id);
            $free = max(0, $cat->capacity - $booked);
            if ($free <= 0) {
                continue;
            }
            $candidates[] = [
                'catamaran_id' => $cat->id,
                'free' => $free,
                'priority' => $cat->pivot->priority ?? $cat->sort_order ?? 0,
            ];
        }

        // Eventuale capacity_override sulla partenza
        if (!is_null($departure->capacity_override)) {
            $totalFree = array_sum(array_column($candidates, 'free'));
            $alreadyBooked = $departure->seats_booked;
            $allowedRemaining = max(0, $departure->capacity_override - $alreadyBooked);
            // Se l'override è più restrittivo, scaliamo proporzionalmente
            if ($allowedRemaining < $totalFree) {
                // Riduci la disponibilità totale a $allowedRemaining preservando l'ordine
                $remaining = $allowedRemaining;
                foreach ($candidates as &$c) {
                    if ($remaining <= 0) {
                        $c['free'] = 0;
                    } else {
                        $c['free'] = min($c['free'], $remaining);
                        $remaining -= $c['free'];
                    }
                }
                unset($c);
            }
        }

        $totalFree = array_sum(array_column($candidates, 'free'));
        if ($totalFree < $seats) {
            return null;
        }

        // 1) Tentativo "gruppo unito": catamarano singolo che ospita tutti
        // Preferisci il catamarano con MENO posti liberi che riesca a contenere
        // tutto il gruppo (best-fit), così lasciamo intatti i grandi.
        $singleFit = collect($candidates)
            ->filter(fn ($c) => $c['free'] >= $seats)
            ->sortBy('free')
            ->first();
        if ($singleFit) {
            return [['catamaran_id' => $singleFit['catamaran_id'], 'seats' => $seats]];
        }

        // 2) Split: riempi i catamarani con più posti liberi prima
        $sorted = collect($candidates)->sortByDesc('free')->values();
        $remaining = $seats;
        $assignment = [];
        foreach ($sorted as $c) {
            if ($remaining <= 0) {
                break;
            }
            $take = min($remaining, $c['free']);
            if ($take > 0) {
                $assignment[] = ['catamaran_id' => $c['catamaran_id'], 'seats' => $take];
                $remaining -= $take;
            }
        }

        return $remaining === 0 ? $assignment : null;
    }

    /**
     * Crea i record booking_seats applicando la distribuzione e le fasce d'età.
     *
     * @param  array<int,int>  $bracketCounts
     * @param  array<int, array{catamaran_id:int, seats:int}>  $distribution
     * @param  array  $guests  dati ospiti opzionali in ordine
     */
    protected function createSeats(Booking $booking, array $bracketCounts, array $distribution, array $guests = []): void
    {
        // Espandi la distribuzione in una lista di catamaran_id (uno per posto contante)
        $catamaranQueue = [];
        foreach ($distribution as $slot) {
            for ($i = 0; $i < $slot['seats']; $i++) {
                $catamaranQueue[] = $slot['catamaran_id'];
            }
        }

        // Espandi i bracket in una lista di bracket_id (uno per partecipante totale)
        // Mantieni l'ordine: bracket "counts_as_seat=true" prima per matchare la queue
        $brackets = TourAgeBracket::where('tour_id', $booking->tour_id)
            ->whereIn('id', array_keys($bracketCounts))
            ->get()
            ->keyBy('id');

        $countingList = [];
        $nonCountingList = [];
        foreach ($bracketCounts as $bracketId => $count) {
            $bracket = $brackets->get($bracketId);
            if (!$bracket || $count <= 0) {
                continue;
            }
            for ($i = 0; $i < $count; $i++) {
                $entry = ['bracket' => $bracket];
                if ($bracket->counts_as_seat) {
                    $countingList[] = $entry;
                } else {
                    $nonCountingList[] = $entry;
                }
            }
        }

        $seatNumber = 1;

        // Posti contanti → assegnati a catamarano dalla queue
        foreach ($countingList as $idx => $entry) {
            $bracket = $entry['bracket'];
            $guest = $guests[$idx] ?? [];
            BookingSeat::create([
                'booking_id' => $booking->id,
                'seat_number' => $seatNumber++,
                'catamaran_id' => $catamaranQueue[$idx] ?? null,
                'tour_age_bracket_id' => $bracket->id,
                'price_paid' => (float) $bracket->price * (float) $booking->departure->price_modifier,
                'guest_first_name' => $guest['first_name'] ?? null,
                'guest_last_name' => $guest['last_name'] ?? null,
                'guest_date_of_birth' => $guest['date_of_birth'] ?? null,
                'is_primary' => $idx === 0,
            ]);
        }

        // Posti non contanti (es. neonati) → seguono il catamarano del primo posto contante
        $defaultCatamaran = $catamaranQueue[0] ?? null;
        foreach ($nonCountingList as $entry) {
            $bracket = $entry['bracket'];
            BookingSeat::create([
                'booking_id' => $booking->id,
                'seat_number' => $seatNumber++,
                'catamaran_id' => $defaultCatamaran,
                'tour_age_bracket_id' => $bracket->id,
                'price_paid' => (float) $bracket->price * (float) $booking->departure->price_modifier,
                'is_primary' => false,
            ]);
        }
    }

    /**
     * Sposta un singolo posto da un catamarano all'altro.
     * Solleva eccezione se il catamarano destinazione è pieno.
     */
    public function moveSeat(BookingSeat $seat, int $newCatamaranId): BookingSeat
    {
        return DB::transaction(function () use ($seat, $newCatamaranId) {
            $booking = $seat->booking;
            $departure = $booking->departure;
            $catamaran = Catamaran::findOrFail($newCatamaranId);

            $booked = $catamaran->seatsBookedOnDeparture($departure->id);
            // Esclude il seat corrente se già su questo catamarano
            if ($seat->catamaran_id === $catamaran->id) {
                return $seat;
            }
            if ($booked >= $catamaran->capacity) {
                throw new \Exception("Catamarano {$catamaran->name} pieno per questa partenza.");
            }

            if (!$catamaran->isAvailableOn($departure->departure_date)) {
                throw new \Exception("Catamarano {$catamaran->name} non disponibile nella data.");
            }

            $seat->catamaran_id = $catamaran->id;
            $seat->save();
            return $seat->fresh('catamaran');
        });
    }
}
