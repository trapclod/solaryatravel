<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    /**
     * Display settings page.
     */
    public function index(): View
    {
        $settings = $this->getSettings();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:100',
            'site_email' => 'required|email|max:100',
            'site_phone' => 'nullable|string|max:30',
            'site_address' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:100',
            'vat_number' => 'nullable|string|max:50',
            'currency' => 'required|string|size:3',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'booking_advance_days' => 'required|integer|min:0|max:365',
            'cancellation_hours' => 'required|integer|min:0|max:168',
            'default_seats' => 'required|integer|min:1|max:50',
            'payment_deadline_minutes' => 'required|integer|min:5|max:1440',
            'stripe_public_key' => 'nullable|string|max:255',
            'stripe_secret_key' => 'nullable|string|max:255',
            'stripe_webhook_secret' => 'nullable|string|max:255',
            'smtp_host' => 'nullable|string|max:100',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:100',
            'smtp_password' => 'nullable|string|max:100',
            'smtp_encryption' => 'nullable|string|in:tls,ssl',
            'mail_from_name' => 'nullable|string|max:100',
            'mail_from_address' => 'nullable|email|max:100',
            'enable_notifications' => 'boolean',
            'maintenance_mode' => 'boolean',
        ]);

        // Convert checkboxes
        $validated['enable_notifications'] = $request->boolean('enable_notifications');
        $validated['maintenance_mode'] = $request->boolean('maintenance_mode');

        $this->saveSettings($validated);

        // Clear cache
        Cache::forget('app_settings');

        return back()->with('success', 'Impostazioni aggiornate con successo.');
    }

    /**
     * Display time slots management.
     */
    public function timeSlots(): View
    {
        $timeSlots = TimeSlot::orderBy('sort_order')->get();

        return view('admin.settings.timeslots', compact('timeSlots'));
    }

    /**
     * Update time slots.
     */
    public function updateTimeSlots(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'slots' => 'required|array',
            'slots.*.id' => 'nullable|exists:time_slots,id',
            'slots.*.name' => 'required|string|max:100',
            'slots.*.start_time' => 'required|date_format:H:i',
            'slots.*.end_time' => 'required|date_format:H:i|after:slots.*.start_time',
            'slots.*.slot_type' => 'required|in:half_day,full_day',
            'slots.*.price_modifier' => 'required|numeric|min:0|max:10',
            'slots.*.is_active' => 'boolean',
            'slots.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['slots'] as $slotData) {
            $isActive = isset($slotData['is_active']) ? (bool) $slotData['is_active'] : false;
            
            if (!empty($slotData['id'])) {
                // Update existing
                TimeSlot::where('id', $slotData['id'])->update([
                    'name' => $slotData['name'],
                    'start_time' => $slotData['start_time'],
                    'end_time' => $slotData['end_time'],
                    'slot_type' => $slotData['slot_type'],
                    'price_modifier' => $slotData['price_modifier'],
                    'is_active' => $isActive,
                    'sort_order' => $slotData['sort_order'],
                ]);
            } else {
                // Create new
                TimeSlot::create([
                    'name' => $slotData['name'],
                    'slug' => Str::slug($slotData['name']),
                    'start_time' => $slotData['start_time'],
                    'end_time' => $slotData['end_time'],
                    'slot_type' => $slotData['slot_type'],
                    'price_modifier' => $slotData['price_modifier'],
                    'is_active' => $isActive,
                    'sort_order' => $slotData['sort_order'],
                ]);
            }
        }

        return back()->with('success', 'Fasce orarie aggiornate con successo.');
    }

    /**
     * Get settings from storage.
     */
    private function getSettings(): array
    {
        return Cache::remember('app_settings', 3600, function () {
            $path = storage_path('app/settings.json');
            
            if (file_exists($path)) {
                return json_decode(file_get_contents($path), true) ?? $this->getDefaultSettings();
            }

            return $this->getDefaultSettings();
        });
    }

    /**
     * Save settings to storage.
     */
    private function saveSettings(array $settings): void
    {
        $path = storage_path('app/settings.json');
        file_put_contents($path, json_encode($settings, JSON_PRETTY_PRINT));
    }

    /**
     * Get default settings.
     */
    private function getDefaultSettings(): array
    {
        return [
            'site_name' => 'Solarya Travel',
            'site_email' => 'info@solaryatravel.com',
            'site_phone' => '',
            'site_address' => '',
            'company_name' => 'Solarya Travel S.r.l.',
            'vat_number' => '',
            'currency' => 'EUR',
            'tax_rate' => 22,
            'booking_advance_days' => 30,
            'cancellation_hours' => 24,
            'default_seats' => 1,
            'payment_deadline_minutes' => 30,
            'stripe_public_key' => config('services.stripe.key', ''),
            'stripe_secret_key' => '',
            'stripe_webhook_secret' => '',
            'smtp_host' => config('mail.mailers.smtp.host', ''),
            'smtp_port' => config('mail.mailers.smtp.port', 587),
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',
            'mail_from_name' => config('mail.from.name', 'Solarya Travel'),
            'mail_from_address' => config('mail.from.address', ''),
            'enable_notifications' => true,
            'maintenance_mode' => false,
        ];
    }
}
