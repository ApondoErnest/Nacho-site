<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TariffSeeder extends Seeder
{
    /**
     * Seed the initial Master Pricing Console rows.
     */
    public function run(): void
    {
        $now = now();

        $tariffs = [
            [
                'category_code' => 'A',
                'category_slug' => 'category-a-taxi',
                'name_en' => 'Taxi / Driving School',
                'name_fr' => 'Taxi / Auto-ecole',
                'description_en' => 'For taxis, driving school cars, and frequent public-service inspection.',
                'description_fr' => 'Pour taxis, vehicules d auto-ecole et controle frequent de service public.',
                'price_fcfa' => 4900,
                'validity_value' => 3,
                'validity_unit' => 'months',
                'vehicle_icon' => 'car-taxi-front',
                'display_order' => 1,
            ],
            [
                'category_code' => 'B',
                'category_slug' => 'category-b-private',
                'name_en' => 'Private Vehicle',
                'name_fr' => 'Vehicule particulier',
                'description_en' => 'For privately used passenger cars and standard personal vehicles.',
                'description_fr' => 'Pour voitures particulieres et vehicules personnels standards.',
                'price_fcfa' => 17900,
                'validity_value' => 12,
                'validity_unit' => 'months',
                'vehicle_icon' => 'car-front',
                'display_order' => 2,
            ],
            [
                'category_code' => 'B1',
                'category_slug' => 'category-b1-pickup',
                'name_en' => 'Pickup / Light utility vehicle',
                'name_fr' => 'Pick-up / Vehicule utilitaire leger',
                'description_en' => 'For pickups and light utility vehicles up to 3.5 tonnes.',
                'description_fr' => 'Pour pick-up et vehicules utilitaires legers jusqu a 3,5 tonnes.',
                'price_fcfa' => 15500,
                'validity_value' => 6,
                'validity_unit' => 'months',
                'maximum_weight_kg' => 3500,
                'vehicle_icon' => 'truck',
                'display_order' => 3,
            ],
            [
                'category_code' => 'C',
                'category_slug' => 'category-c-minibus',
                'name_en' => 'Minibus',
                'name_fr' => 'Mini-bus',
                'description_en' => 'For minibuses and passenger transport vehicles under 3.5 tonnes.',
                'description_fr' => 'Pour mini-bus et transport de personnes de moins de 3,5 tonnes.',
                'price_fcfa' => 15500,
                'validity_value' => 3,
                'validity_unit' => 'months',
                'maximum_weight_kg' => 3500,
                'vehicle_icon' => 'bus-front',
                'display_order' => 4,
            ],
            [
                'category_code' => 'C',
                'category_slug' => 'category-c-coaster',
                'name_en' => 'Large bus / Coaster',
                'name_fr' => 'Grand bus / Coaster',
                'description_en' => 'For larger passenger buses, coasters, and high-capacity transport vehicles.',
                'description_fr' => 'Pour grands bus, coasters et vehicules de transport grande capacite.',
                'price_fcfa' => 19080,
                'validity_value' => 3,
                'validity_unit' => 'months',
                'vehicle_icon' => 'bus',
                'display_order' => 5,
            ],
            [
                'category_code' => 'D',
                'category_slug' => 'category-d-heavy-utility',
                'name_en' => 'Trucks / Semi-trailers / Heavy utility vehicles',
                'name_fr' => 'Camions / Semi-remorques / Vehicules utilitaires lourds',
                'description_en' => 'For trucks, tractors, semi-trailers, and heavy utility vehicles.',
                'description_fr' => 'Pour camions, tracteurs, semi-remorques et utilitaires lourds.',
                'price_fcfa' => 26235,
                'validity_value' => 6,
                'validity_unit' => 'months',
                'vehicle_icon' => 'tractor',
                'display_order' => 6,
            ],
            [
                'category_code' => 'D',
                'category_slug' => 'category-d-other-engins',
                'name_en' => 'Other machinery',
                'name_fr' => 'Autres engins',
                'description_en' => 'For special machinery and other equipment requiring technical inspection.',
                'description_fr' => 'Pour engins speciaux et autres equipements soumis au controle technique.',
                'price_fcfa' => 41750,
                'validity_value' => 6,
                'validity_unit' => 'months',
                'vehicle_icon' => 'construction',
                'display_order' => 7,
            ],
        ];

        foreach ($tariffs as $tariff) {
            DB::table('tariffs')->updateOrInsert(
                ['category_slug' => $tariff['category_slug']],
                [
                    ...$tariff,
                    'minimum_weight_kg' => $tariff['minimum_weight_kg'] ?? null,
                    'maximum_weight_kg' => $tariff['maximum_weight_kg'] ?? null,
                    'effective_date' => null,
                    'expiry_date' => null,
                    'regulatory_reference' => null,
                    'last_verified_at' => null,
                    'is_active' => true,
                    'is_bookable' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
