<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\TourDeparture;
use App\Models\TourAgeBracket;
use App\Models\TourPeriod;
use App\Models\Addon;
use App\Models\DiscountCode;
use Illuminate\Support\Collection;

class PricingService
{
    /**
     * Calcola il prezzo totale di una prenotazione su un tour.
     *
     * @param  array<int,int>  $bracketCounts  mappa bracket_id => quantità
     * @param  array<int,int>  $addonIds       id degli addon selezionati
     */
    public function calculate(
        Tour $tour,
        TourDeparture $departure,
        array $bracketCounts,
        array $addonIds = [],
        ?string $discountCode = null
    ): array {
        $brackets = $this->resolveBrackets($tour, $departure->departure_date)
            ->whereIn('id', array_keys($bracketCounts))
            ->keyBy('id');

        // Calcolo posti totali e dettaglio fasce
        $bracketDetails = [];
        $basePrice = 0.0;
        $totalSeats = 0;
        $countingSeats = 0;

        foreach ($bracketCounts as $bracketId => $count) {
            $count = (int) $count;
            if ($count <= 0) {
                continue;
            }
            $bracket = $brackets->get($bracketId);
            if (!$bracket) {
                continue;
            }
            $unit = (float) $bracket->price * (float) $departure->price_modifier;
            $line = $unit * $count;
            $basePrice += $line;
            $totalSeats += $count;
            if ($bracket->counts_as_seat) {
                $countingSeats += $count;
            }
            $bracketDetails[] = [
                'bracket_id' => $bracket->id,
                'label' => $bracket->label,
                'unit_price' => round($unit, 2),
                'count' => $count,
                'line_total' => round($line, 2),
                'counts_as_seat' => $bracket->counts_as_seat,
            ];
        }

        $addonsTotal = $this->calculateAddonsTotal($addonIds, $countingSeats, $tour->duration_hours ?? 0);

        $discountAmount = 0.0;
        $discountCodeId = null;
        if ($discountCode) {
            $applied = $this->validateAndApplyDiscount($discountCode, $basePrice + $addonsTotal);
            if ($applied) {
                $discountAmount = $applied['amount'];
                $discountCodeId = $applied['id'];
            }
        }

        $subtotal = max(0, $basePrice + $addonsTotal - $discountAmount);
        $taxRate = (float) config('booking.tax_rate', 0) / 100;
        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount;

        return [
            'base_price' => round($basePrice, 2),
            'addons_total' => round($addonsTotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'discount_code_id' => $discountCodeId,
            'subtotal' => round($subtotal, 2),
            'tax_rate' => $taxRate * 100,
            'tax_amount' => round($taxAmount, 2),
            'total_amount' => round($totalAmount, 2),
            'total_seats' => $totalSeats,
            'counting_seats' => $countingSeats,
            'brackets' => $bracketDetails,
        ];
    }

    protected function calculateAddonsTotal(array $addonIds, int $seats, float $hours): float
    {
        $total = 0.0;
        foreach ($addonIds as $addonId) {
            $addon = Addon::find($addonId);
            if ($addon && $addon->is_active) {
                // Lasciamo che il modello Addon decida la formula; passiamo seats e hours/8 come "duration units"
                $total += (float) $addon->calculatePrice($seats, max(0.5, $hours / 8));
            }
        }
        return $total;
    }

    protected function validateAndApplyDiscount(string $code, float $amount): ?array
    {
        $discount = DiscountCode::where('code', strtoupper($code))
            ->where('is_active', true)
            ->first();

        if (!$discount || !$discount->isValid()) {
            return null;
        }

        $discountAmount = $discount->type === 'percentage'
            ? $amount * ((float) $discount->value / 100)
            : (float) $discount->value;

        return [
            'id' => $discount->id,
            'amount' => min($discountAmount, $amount),
        ];
    }

    /**
     * Periodo applicabile a una data (start_date <= data <= end_date).
     */
    public function resolvePeriod(Tour $tour, string|\DateTimeInterface|null $date): ?TourPeriod
    {
        if (!$date) {
            return null;
        }
        $d = is_string($date) ? $date : $date->format('Y-m-d');
        return TourPeriod::where('tour_id', $tour->id)
            ->whereDate('start_date', '<=', $d)
            ->whereDate('end_date', '>=', $d)
            ->orderBy('sort_order')
            ->orderBy('start_date')
            ->first();
    }

    /**
     * Brackets applicabili a una data: quelle del periodo che la copre;
     * fallback alle brackets "orfane" del tour (vecchio sistema senza periodi).
     */
    public function resolveBrackets(Tour $tour, string|\DateTimeInterface|null $date): Collection
    {
        $period = $this->resolvePeriod($tour, $date);
        if ($period) {
            return $period->ageBrackets()->orderBy('sort_order')->get();
        }
        return TourAgeBracket::where('tour_id', $tour->id)
            ->whereNull('tour_period_id')
            ->orderBy('sort_order')
            ->get();
    }
}
