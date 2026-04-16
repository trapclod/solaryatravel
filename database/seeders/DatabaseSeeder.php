<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Catamaran;
use App\Models\TimeSlot;
use App\Models\Addon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedUsers();
        $this->seedTimeSlots();
        $this->seedCatamarans();
        $this->seedAddons();
        $this->seedSettings();
    }

    private function seedUsers(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin Solarya',
            'email' => 'admin@solaryatravel.com',
            'password' => Hash::make('Admin2024!'),
            'role' => 'super_admin',
            'locale' => 'it',
            'email_verified_at' => now(),
        ]);

        // Test customer
        User::create([
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@example.com',
            'password' => Hash::make('Customer2024!'),
            'role' => 'customer',
            'phone' => '+39 333 1234567',
            'locale' => 'it',
            'email_verified_at' => now(),
        ]);
    }

    private function seedTimeSlots(): void
    {
        $slots = [
            [
                'name' => 'Mattina',
                'slug' => 'morning',
                'start_time' => '09:00:00',
                'end_time' => '13:00:00',
                'slot_type' => 'half_day',
                'price_modifier' => 1.00,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pomeriggio',
                'slug' => 'afternoon',
                'start_time' => '14:30:00',
                'end_time' => '18:30:00',
                'slot_type' => 'half_day',
                'price_modifier' => 1.00,
                'sort_order' => 2,
            ],
            [
                'name' => 'Tramonto',
                'slug' => 'sunset',
                'start_time' => '18:00:00',
                'end_time' => '21:00:00',
                'slot_type' => 'half_day',
                'price_modifier' => 1.20,
                'sort_order' => 3,
            ],
            [
                'name' => 'Giornata Intera',
                'slug' => 'full-day',
                'start_time' => '09:00:00',
                'end_time' => '18:30:00',
                'slot_type' => 'full_day',
                'price_modifier' => 1.00,
                'sort_order' => 4,
            ],
        ];

        foreach ($slots as $slot) {
            TimeSlot::create($slot);
        }
    }

    private function seedCatamarans(): void
    {
        $catamarans = [
            [
                'uuid' => Str::uuid(),
                'name' => 'Solarya One',
                'slug' => 'solarya-one',
                'description' => 'Il nostro catamarano ammiraglia, perfetto per gruppi che cercano comfort e lusso. Dotato di ampi spazi esterni, zone relax e tutti i comfort per una giornata indimenticabile in mare.',
                'description_short' => 'Catamarano premium con 12 posti, perfetto per escursioni di gruppo di lusso.',
                'capacity' => 12,
                'length_meters' => 15.00,
                'features' => json_encode([
                    'Wi-Fi gratuito',
                    'Aria condizionata',
                    'Doccia esterna',
                    'Frigorifero',
                    'Attrezzatura snorkeling',
                    'Musica bluetooth',
                    'Area solarium',
                    'Tendalino ombreggiante',
                ]),
                'base_price_half_day' => 600.00,
                'base_price_full_day' => 1000.00,
                'exclusive_price_half_day' => 800.00,
                'exclusive_price_full_day' => 1400.00,
                'price_per_person_half_day' => 75.00,
                'price_per_person_full_day' => 120.00,
                'sort_order' => 1,
                'meta_title' => 'Solarya One - Escursioni in Catamarano Premium',
                'meta_description' => 'Prenota un\'escursione sul nostro catamarano premium Solarya One. 12 posti, massimo comfort per la tua giornata in mare.',
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Solarya Elegance',
                'slug' => 'solarya-elegance',
                'description' => 'Design elegante e raffinato per chi cerca un\'esperienza esclusiva. Ideale per coppie, piccoli gruppi e occasioni speciali come anniversari e proposte di matrimonio.',
                'description_short' => 'Catamarano elegante per 8 ospiti, ideale per occasioni speciali.',
                'capacity' => 8,
                'length_meters' => 12.00,
                'features' => json_encode([
                    'Wi-Fi gratuito',
                    'Aria condizionata',
                    'Mini bar',
                    'Sistema audio premium',
                    'Attrezzatura snorkeling',
                    'Lettini prendisole',
                    'Area relax coperta',
                ]),
                'base_price_half_day' => 500.00,
                'base_price_full_day' => 850.00,
                'exclusive_price_half_day' => 650.00,
                'exclusive_price_full_day' => 1100.00,
                'price_per_person_half_day' => 85.00,
                'price_per_person_full_day' => 140.00,
                'sort_order' => 2,
                'meta_title' => 'Solarya Elegance - Catamarano Esclusivo',
                'meta_description' => 'Il catamarano Solarya Elegance offre un\'esperienza esclusiva per 8 ospiti. Perfetto per occasioni speciali.',
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Solarya Family',
                'slug' => 'solarya-family',
                'description' => 'Il catamarano pensato per le famiglie. Spazioso, sicuro e con tutti i comfort per far divertire grandi e piccini. Area giochi dedicata e staff attento alle esigenze dei bambini.',
                'description_short' => 'Catamarano family-friendly per 14 ospiti, perfetto per famiglie.',
                'capacity' => 14,
                'length_meters' => 16.00,
                'features' => json_encode([
                    'Wi-Fi gratuito',
                    'Aria condizionata',
                    'Doccia esterna',
                    'Frigorifero grande',
                    'Attrezzatura snorkeling',
                    'Giochi acquatici',
                    'Reti di sicurezza',
                    'Area bambini',
                    'Bagno attrezzato',
                ]),
                'base_price_half_day' => 700.00,
                'base_price_full_day' => 1200.00,
                'exclusive_price_half_day' => 900.00,
                'exclusive_price_full_day' => 1600.00,
                'price_per_person_half_day' => 65.00,
                'price_per_person_full_day' => 110.00,
                'sort_order' => 3,
                'meta_title' => 'Solarya Family - Catamarano per Famiglie',
                'meta_description' => 'Solarya Family è il catamarano ideale per famiglie. 14 posti, giochi acquatici e massima sicurezza per i più piccoli.',
            ],
        ];

        foreach ($catamarans as $catamaran) {
            Catamaran::create($catamaran);
        }
    }

    private function seedAddons(): void
    {
        $addons = [
            [
                'uuid' => Str::uuid(),
                'name' => 'Aperitivo Premium',
                'slug' => 'aperitivo-premium',
                'description' => 'Aperitivo con prosecco, stuzzichini gourmet, tartine e selezione di formaggi e salumi locali.',
                'price' => 25.00,
                'price_type' => 'per_person',
                'requires_advance_booking' => true,
                'advance_hours' => 24,
                'sort_order' => 1,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Pranzo a Bordo',
                'slug' => 'pranzo-a-bordo',
                'description' => 'Pranzo completo con primo, secondo, contorno, dolce e bevande. Menù mediterraneo con ingredienti locali.',
                'price' => 45.00,
                'price_type' => 'per_person',
                'requires_advance_booking' => true,
                'advance_hours' => 48,
                'sort_order' => 2,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Open Bar',
                'slug' => 'open-bar',
                'description' => 'Bevande illimitate per tutta la durata dell\'escursione: cocktail, birra, vino, soft drink e acqua.',
                'price' => 35.00,
                'price_type' => 'per_person',
                'requires_advance_booking' => true,
                'advance_hours' => 24,
                'sort_order' => 3,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Fotografo Professionale',
                'slug' => 'fotografo-professionale',
                'description' => 'Fotografo professionista a bordo per immortalare i momenti più belli. Include 50+ foto in alta risoluzione.',
                'price' => 150.00,
                'price_type' => 'per_booking',
                'requires_advance_booking' => true,
                'advance_hours' => 72,
                'sort_order' => 4,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Attrezzatura Snorkeling Premium',
                'slug' => 'snorkeling-premium',
                'description' => 'Maschera professionale, pinne e muta (se necessario). Qualità superiore per un\'esperienza ottimale.',
                'price' => 15.00,
                'price_type' => 'per_person',
                'max_quantity' => 14,
                'sort_order' => 5,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Pacchetto Romantico',
                'slug' => 'pacchetto-romantico',
                'description' => 'Champagne, frutta fresca, cioccolatini, decorazioni floreali e musica personalizzata.',
                'price' => 120.00,
                'price_type' => 'per_booking',
                'requires_advance_booking' => true,
                'advance_hours' => 48,
                'sort_order' => 6,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Torta Personalizzata',
                'slug' => 'torta-personalizzata',
                'description' => 'Torta artigianale per compleanni, anniversari o occasioni speciali. Scrivi il messaggio in fase di prenotazione.',
                'price' => 60.00,
                'price_type' => 'per_booking',
                'requires_advance_booking' => true,
                'advance_hours' => 72,
                'sort_order' => 7,
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Transfer Hotel',
                'slug' => 'transfer-hotel',
                'description' => 'Servizio transfer da/per il tuo hotel. Prezzo a persona, andata e ritorno.',
                'price' => 20.00,
                'price_type' => 'per_person',
                'requires_advance_booking' => true,
                'advance_hours' => 24,
                'sort_order' => 8,
            ],
        ];

        foreach ($addons as $addon) {
            Addon::create($addon);
        }
    }

    private function seedSettings(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'Solarya Travel', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'site_tagline', 'value' => 'Escursioni in catamarano di lusso', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'contact_email', 'value' => 'info@solaryatravel.com', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'contact_phone', 'value' => '+39 123 456 7890', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'whatsapp_number', 'value' => '+39 123 456 7890', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            
            // Booking
            ['key' => 'booking_advance_hours', 'value' => '24', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'payment_expiry_minutes', 'value' => '30', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'tax_rate', 'value' => '22', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'currency', 'value' => 'EUR', 'type' => 'string', 'group' => 'booking', 'is_public' => true],
            ['key' => 'min_seats_booking', 'value' => '1', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'cancellation_hours', 'value' => '48', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'cancellation_refund_percent', 'value' => '80', 'type' => 'integer', 'group' => 'booking'],
            
            // Social
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/solaryatravel', 'type' => 'string', 'group' => 'social', 'is_public' => true],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/solaryatravel', 'type' => 'string', 'group' => 'social', 'is_public' => true],
            ['key' => 'tripadvisor_url', 'value' => '', 'type' => 'string', 'group' => 'social', 'is_public' => true],
            
            // Location
            ['key' => 'departure_port', 'value' => 'Porto Turistico di Salerno', 'type' => 'string', 'group' => 'location', 'is_public' => true],
            ['key' => 'departure_address', 'value' => 'Molo Manfredi, 84121 Salerno SA', 'type' => 'string', 'group' => 'location', 'is_public' => true],
            ['key' => 'departure_lat', 'value' => '40.6745', 'type' => 'string', 'group' => 'location', 'is_public' => true],
            ['key' => 'departure_lng', 'value' => '14.7631', 'type' => 'string', 'group' => 'location', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
