<?php

namespace App\Services;

use App\Models\Catamaran;
use App\Models\TimeSlot;
use App\Models\Addon;
use App\Models\DiscountCode;

class PricingService
{
    /**
     * Calculate the total price for a booking.
     */
    public function calculate(
        Catamaran $catamaran,
        TimeSlot $timeSlot,
        string $bookingType,
        int $seats,
        array $addonIds = [],
        ?string $discountCode = null
    ): array {
        $isFullDay = $timeSlot->slot_type === 'full_day';

        // Calculate base price
        $basePrice = $this->calculateBasePrice($catamaran, $bookingType, $seats, $isFullDay);

        // Apply time slot modifier
        $basePrice *= $timeSlot->price_modifier;

        // Calculate addons total
        $addonsTotal = $this->calculateAddonsTotal($addonIds, $seats, $isFullDay);

        // Calculate discount
        $discountAmount = 0;
        $discountCodeId = null;
        $appliedDiscount = null;

        if ($discountCode) {
            $appliedDiscount = $this->validateAndApplyDiscount($discountCode, $basePrice + $addonsTotal);
            if ($appliedDiscount) {
                $discountAmount = $appliedDiscount['amount'];
                $discountCodeId = $appliedDiscount['id'];
            }
        }

        // Calculate subtotal
        $subtotal = $basePrice + $addonsTotal - $discountAmount;

        // Calculate tax
        $taxRate = config('booking.tax_rate', 22) / 100;
        $taxAmount = $subtotal * $taxRate;

        // Calculate total
        $totalAmount = $subtotal + $taxAmount;

        return [
            'base_price' => round($basePrice, 2),
            'addons_total' => round($addonsTotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'discount_code_id' => $discountCodeId,
            'subtotal' => round($subtotal, 2),
            'tax_rate' => config('booking.tax_rate', 22),
            'tax_amount' => round($taxAmount, 2),
            'total_amount' => round($totalAmount, 2),
        ];
    }

    /**
     * Calculate base price based on booking type.
     */
    protected function calculateBasePrice(
        Catamaran $catamaran,
        string $bookingType,
        int $seats,
        bool $isFullDay
    ): float {
        if ($bookingType === 'exclusive') {
            return $isFullDay
                ? $catamaran->exclusive_price_full_day
                : $catamaran->exclusive_price_half_day;
        }

        $pricePerPerson = $isFullDay
            ? $catamaran->price_per_person_full_day
            : $catamaran->price_per_person_half_day;

        return $pricePerPerson * $seats;
    }

    /**
     * Calculate total for selected addons.
     */
    protected function calculateAddonsTotal(array $addonIds, int $seats, bool $isFullDay): float
    {
        $total = 0;

        foreach ($addonIds as $addonId) {
            $addon = Addon::find($addonId);
            if ($addon && $addon->is_active) {
                $total += $addon->calculatePrice($seats, $isFullDay ? 1 : 0.5);
            }
        }

        return $total;
    }

    /**
     * Validate and apply discount code.
     */
    protected function validateAndApplyDiscount(string $code, float $amount): ?array
    {
        $discount = DiscountCode::where('code', strtoupper($code))
            ->where('is_active', true)
            ->first();

        if (!$discount || !$discount->isValid()) {
            return null;
        }

        if ($discount->min_amount && $amount < $discount->min_amount) {
            return null;
        }

        return [
            'id' => $discount->id,
            'code' => $discount->code,
            'amount' => $discount->calculateDiscount($amount),
        ];
    }

    /**
     * Get price breakdown for display.
     */
    public function getPriceBreakdown(
        Catamaran $catamaran,
        TimeSlot $timeSlot,
        string $bookingType,
        int $seats,
        array $addonIds = [],
        ?string $discountCode = null
    ): array {
        $pricing = $this->calculate(
            $catamaran,
            $timeSlot,
            $bookingType,
            $seats,
            $addonIds,
            $discountCode
        );

        $isFullDay = $timeSlot->slot_type === 'full_day';

        $breakdown = [];

        // Base price line
        if ($bookingType === 'exclusive') {
            $breakdown[] = [
                'label' => 'Escursione privata',
                'quantity' => 1,
                'unit_price' => $pricing['base_price'],
                'total' => $pricing['base_price'],
            ];
        } else {
            $pricePerPerson = $isFullDay
                ? $catamaran->price_per_person_full_day
                : $catamaran->price_per_person_half_day;

            $breakdown[] = [
                'label' => 'Escursione',
                'quantity' => $seats,
                'unit_price' => $pricePerPerson * $timeSlot->price_modifier,
                'total' => $pricing['base_price'],
            ];
        }

        // Addons
        foreach ($addonIds as $addonId) {
            $addon = Addon::find($addonId);
            if ($addon) {
                $quantity = $addon->price_type === 'per_person' ? $seats : 1;
                $addonTotal = $addon->calculatePrice($seats, $isFullDay ? 1 : 0.5);

                $breakdown[] = [
                    'label' => $addon->name,
                    'quantity' => $quantity,
                    'unit_price' => $addon->price,
                    'total' => $addonTotal,
                    'type' => 'addon',
                ];
            }
        }

        // Discount
        if ($pricing['discount_amount'] > 0) {
            $breakdown[] = [
                'label' => 'Sconto',
                'quantity' => 1,
                'unit_price' => -$pricing['discount_amount'],
                'total' => -$pricing['discount_amount'],
                'type' => 'discount',
            ];
        }

        return [
            'lines' => $breakdown,
            'subtotal' => $pricing['subtotal'],
            'tax_rate' => $pricing['tax_rate'],
            'tax_amount' => $pricing['tax_amount'],
            'total' => $pricing['total_amount'],
        ];
    }

    /**
     * Get available price range for a catamaran.
     */
    public function getPriceRange(Catamaran $catamaran): array
    {
        return [
            'half_day' => [
                'per_person' => $catamaran->price_per_person_half_day,
                'exclusive' => $catamaran->exclusive_price_half_day,
            ],
            'full_day' => [
                'per_person' => $catamaran->price_per_person_full_day,
                'exclusive' => $catamaran->exclusive_price_full_day,
            ],
            'min' => $catamaran->price_per_person_half_day,
            'max' => $catamaran->exclusive_price_full_day,
        ];
    }
}
