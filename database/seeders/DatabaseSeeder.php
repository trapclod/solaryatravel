<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Catamaran;
use App\Models\Tour;
use App\Models\TourAgeBracket;
use App\Models\TourDeparture;
use App\Models\TourImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedCatamarans();
        $this->seedTours();
        $this->seedAddons();
        $this->seedSettings();
    }

    private function seedUsers(): void
    {
        User::create([
            'name' => 'Admin Solarya',
            'email' => 'admin@solaryatravel.com',
            'password' => Hash::make('Admin2024!'),
            'role' => 'super_admin',
            'locale' => 'it',
            'email_verified_at' => now(),
        ]);

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

    private function seedCatamarans(): void
    {
        $catamarans = [
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Solarya One',
                'slug' => 'solarya-one',
                'description' => 'Il nostro catamarano ammiraglia, perfetto per gruppi che cercano comfort e lusso.',
                'description_short' => 'Catamarano premium con 12 posti, perfetto per escursioni di gruppo di lusso.',
                'capacity' => 12,
                'length_meters' => 15.00,
                'features' => ['Wi-Fi', 'Aria condizionata', 'Doccia esterna', 'Frigorifero', 'Snorkeling', 'Bluetooth', 'Solarium'],
                'sort_order' => 1,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Solarya Elegance',
                'slug' => 'solarya-elegance',
                'description' => 'Design elegante e raffinato per chi cerca un\'esperienza esclusiva.',
                'description_short' => 'Catamarano elegante per 8 ospiti, ideale per occasioni speciali.',
                'capacity' => 8,
                'length_meters' => 12.00,
                'features' => ['Wi-Fi', 'Aria condizionata', 'Mini bar', 'Audio premium', 'Snorkeling'],
                'sort_order' => 2,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Solarya Family',
                'slug' => 'solarya-family',
                'description' => 'Il catamarano pensato per le famiglie. Spazioso e sicuro.',
                'description_short' => 'Catamarano family-friendly per 14 ospiti.',
                'capacity' => 14,
                'length_meters' => 16.00,
                'features' => ['Wi-Fi', 'Aria condizionata', 'Snorkeling', 'Giochi acquatici', 'Reti di sicurezza', 'Area bambini'],
                'sort_order' => 3,
            ],
        ];
        foreach ($catamarans as $c) {
            Catamaran::create($c);
        }
    }

    private function seedTours(): void
    {
        $tours = [
            [
                'name' => 'Tour delle Calette al Tramonto',
                'slug' => 'tour-calette-tramonto',
                'description' => 'Una crociera serale alla scoperta delle calette più suggestive, con aperitivo a bordo durante il tramonto.',
                'description_short' => 'Crociera al tramonto tra calette nascoste e aperitivo a bordo.',
                'itinerary' => "Partenza dal porto\nPrima sosta in caletta per nuoto\nNavigazione lungo costa\nAperitivo al tramonto\nRientro al porto",
                'duration_hours' => 3,
                'departure_point' => 'Marina di Salivoli',
                'included' => ['Skipper', 'Carburante', 'Snorkeling', 'Aperitivo a bordo'],
                'excluded' => ['Trasferimento dal hotel', 'Pranzo'],
                'min_capacity' => 1,
                'max_capacity' => 34,
                'is_active' => true,
                'sort_order' => 1,
                'brackets' => [
                    ['label' => 'Adulto', 'min_age' => 12, 'max_age' => null, 'price' => 65, 'counts_as_seat' => true],
                    ['label' => 'Bambino', 'min_age' => 3, 'max_age' => 11, 'price' => 35, 'counts_as_seat' => true],
                    ['label' => 'Infante', 'min_age' => 0, 'max_age' => 2, 'price' => 0, 'counts_as_seat' => false],
                ],
                'departures' => [
                    ['start' => '17:30', 'end' => '20:30'],
                ],
            ],
            [
                'name' => 'Giornata in Catamarano - Costa e Snorkeling',
                'slug' => 'giornata-catamarano-snorkeling',
                'description' => 'Un\'intera giornata di mare per scoprire la costa, fare snorkeling nelle migliori location e gustare un pranzo a bordo.',
                'description_short' => 'Giornata intera con pranzo, snorkeling e relax in mare aperto.',
                'itinerary' => "09:00 partenza\nNuoto e snorkeling\nPranzo a bordo\nSeconda location di balneazione\n17:00 rientro",
                'duration_hours' => 8,
                'departure_point' => 'Marina di Salivoli',
                'included' => ['Skipper', 'Carburante', 'Snorkeling', 'Pranzo leggero', 'Acqua e soft drink'],
                'excluded' => ['Bevande alcoliche', 'Mance'],
                'min_capacity' => 1,
                'max_capacity' => 34,
                'is_active' => true,
                'sort_order' => 2,
                'brackets' => [
                    ['label' => 'Adulto', 'min_age' => 12, 'max_age' => null, 'price' => 120, 'counts_as_seat' => true],
                    ['label' => 'Bambino', 'min_age' => 3, 'max_age' => 11, 'price' => 70, 'counts_as_seat' => true],
                    ['label' => 'Infante', 'min_age' => 0, 'max_age' => 2, 'price' => 0, 'counts_as_seat' => false],
                ],
                'departures' => [
                    ['start' => '09:00', 'end' => '17:00'],
                ],
            ],
            [
                'name' => 'Mini-Crociera Mattutina',
                'slug' => 'mini-crociera-mattutina',
                'description' => 'Una mini-crociera mattutina di 2 ore, perfetta per chi vuole una breve esperienza in mare.',
                'description_short' => 'Mini-crociera di 2 ore al mattino.',
                'duration_hours' => 2,
                'departure_point' => 'Marina di Salivoli',
                'included' => ['Skipper', 'Carburante'],
                'excluded' => ['Pranzo', 'Snorkeling'],
                'min_capacity' => 1,
                'max_capacity' => 34,
                'is_active' => true,
                'sort_order' => 3,
                'brackets' => [
                    ['label' => 'Adulto', 'min_age' => 12, 'max_age' => null, 'price' => 40, 'counts_as_seat' => true],
                    ['label' => 'Bambino', 'min_age' => 0, 'max_age' => 11, 'price' => 20, 'counts_as_seat' => true],
                ],
                'departures' => [
                    ['start' => '10:00', 'end' => '12:00'],
                    ['start' => '12:30', 'end' => '14:30'],
                ],
            ],
        ];

        foreach ($tours as $t) {
            $brackets = $t['brackets'];
            $departures = $t['departures'];
            unset($t['brackets'], $t['departures']);

            $tour = Tour::create(array_merge($t, ['uuid' => (string) Str::uuid()]));

            foreach ($brackets as $i => $b) {
                TourAgeBracket::create(array_merge($b, [
                    'tour_id' => $tour->id,
                    'sort_order' => $i,
                ]));
            }

            // Crea partenze per i prossimi 30 giorni
            for ($d = 0; $d < 30; $d++) {
                $date = now()->addDays($d)->toDateString();
                foreach ($departures as $dep) {
                    TourDeparture::create([
                        'tour_id' => $tour->id,
                        'departure_date' => $date,
                        'start_time' => $dep['start'],
                        'end_time' => $dep['end'],
                        'status' => 'scheduled',
                        'price_modifier' => 1,
                    ]);
                }
            }
        }
    }

    private function seedAddons(): void
    {
        $addons = [
            ['name' => 'Aperitivo Premium', 'slug' => 'aperitivo-premium', 'description' => 'Aperitivo con prosecco e stuzzichini gourmet.', 'price' => 25.00, 'price_type' => 'per_person', 'sort_order' => 1],
            ['name' => 'Pranzo a Bordo', 'slug' => 'pranzo-a-bordo', 'description' => 'Pranzo completo mediterraneo.', 'price' => 45.00, 'price_type' => 'per_person', 'sort_order' => 2],
            ['name' => 'Open Bar', 'slug' => 'open-bar', 'description' => 'Bevande illimitate.', 'price' => 35.00, 'price_type' => 'per_person', 'sort_order' => 3],
            ['name' => 'Fotografo Professionale', 'slug' => 'fotografo-professionale', 'description' => 'Foto in alta risoluzione.', 'price' => 150.00, 'price_type' => 'per_booking', 'sort_order' => 4],
            ['name' => 'Snorkeling Premium', 'slug' => 'snorkeling-premium', 'description' => 'Maschera, pinne, muta.', 'price' => 15.00, 'price_type' => 'per_person', 'sort_order' => 5],
            ['name' => 'Pacchetto Romantico', 'slug' => 'pacchetto-romantico', 'description' => 'Champagne, fiori, musica.', 'price' => 120.00, 'price_type' => 'per_booking', 'sort_order' => 6],
            ['name' => 'Transfer Hotel', 'slug' => 'transfer-hotel', 'description' => 'Andata e ritorno.', 'price' => 20.00, 'price_type' => 'per_person', 'sort_order' => 7],
        ];
        foreach ($addons as $a) {
            Addon::create(array_merge($a, ['uuid' => (string) Str::uuid()]));
        }
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Solarya Travel', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'site_tagline', 'value' => 'Escursioni in catamarano di lusso', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'contact_email', 'value' => 'info@solaryatravel.com', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'contact_phone', 'value' => '+39 123 456 7890', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'whatsapp_number', 'value' => '+39 123 456 7890', 'type' => 'string', 'group' => 'general', 'is_public' => true],
            ['key' => 'booking_advance_hours', 'value' => '24', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'payment_expiry_minutes', 'value' => '30', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'tax_rate', 'value' => '22', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'currency', 'value' => 'EUR', 'type' => 'string', 'group' => 'booking', 'is_public' => true],
            ['key' => 'min_seats_booking', 'value' => '1', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'cancellation_hours', 'value' => '48', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'cancellation_refund_percent', 'value' => '80', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'departure_port', 'value' => 'Porto Turistico di Salerno', 'type' => 'string', 'group' => 'location', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
