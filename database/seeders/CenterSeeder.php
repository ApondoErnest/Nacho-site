<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CenterSeeder extends Seeder
{
    /**
     * Seed verified center rows plus contacts, hours, and service assignments.
     */
    public function run(): void
    {
        $now = now();

        $centers = [
            [
                'slug' => 'nacho-yaounde',
                'name_en' => 'NOVETESCO Yaounde',
                'name_fr' => 'NOVETESCO Yaounde',
                'city_en' => 'Yaounde',
                'city_fr' => 'Yaounde',
                'region_en' => 'Centre',
                'region_fr' => 'Centre',
                'address_en' => 'Mendong Market, Yaounde',
                'address_fr' => 'Marche Mendong, Yaounde',
                'status' => 'active',
                'is_headquarters' => false,
                'booking_enabled' => true,
                'latitude' => 3.837496,
                'longitude' => 11.473015,
                'google_maps_url' => 'https://www.google.com/maps?q=3.837496,11.473015',
                'nearby_landmark' => 'Mendong market',
                'search_keywords' => 'Yaounde, Mendong, Mendong Market, Centre',
                'display_order' => 1,
                'featured_image' => 'images/homepage/yaounde.png',
                'contacts' => [
                    ['type' => 'email', 'value' => 'noblevehicletestingcompany@gmail.com', 'is_primary' => true],
                    ['type' => 'phone', 'value' => '(+237) 675117327', 'is_primary' => true],
                    ['type' => 'phone', 'value' => '(+237) 656901833', 'is_primary' => false],
                ],
                'hours' => $this->standardHours(),
            ],
            [
                'slug' => 'nacho-nkwen-bamenda',
                'name_en' => 'NOVETESCO Nkwen-Bamenda',
                'name_fr' => 'NOVETESCO Nkwen-Bamenda',
                'city_en' => 'Bamenda',
                'city_fr' => 'Bamenda',
                'region_en' => 'Northwest',
                'region_fr' => 'Nord-Ouest',
                'address_en' => 'NTEFINKI Quarter Mile 6, Nkwen',
                'address_fr' => 'Quartier NTEFINKI Mile 6, Nkwen',
                'status' => 'active',
                'is_headquarters' => false,
                'booking_enabled' => true,
                'latitude' => 6.000978,
                'longitude' => 10.206111,
                'google_maps_url' => 'https://www.google.com/maps?q=6.000978,10.206111',
                'nearby_landmark' => 'NTEFINKI Quarter mile 6 Nkwen',
                'search_keywords' => 'Bamenda, Nkwen, NTEFINKI, Northwest',
                'display_order' => 2,
                'featured_image' => 'images/homepage/nkwen-bamenda.png',
                'contacts' => [
                    ['type' => 'email', 'value' => 'noblevehicletestingcompany@gmail.com', 'is_primary' => true],
                    ['type' => 'phone', 'value' => '(+237) 674036182', 'is_primary' => true],
                    ['type' => 'phone', 'value' => '(+237) 696130530', 'is_primary' => false],
                ],
                'hours' => $this->standardHours(),
            ],
            [
                'slug' => 'nacho-mankon-bamenda',
                'name_en' => 'NOVETESCO Mankon-Bamenda / Headquarters',
                'name_fr' => 'NOVETESCO Mankon-Bamenda / Siège',
                'city_en' => 'Bamenda',
                'city_fr' => 'Bamenda',
                'region_en' => 'Northwest',
                'region_fr' => 'Nord-Ouest',
                'address_en' => 'Atuakum Mankon, Bamenda',
                'address_fr' => 'Atuakum Mankon, Bamenda',
                'postal_address' => 'P.O. Box 100 Mankon-Bamenda',
                'status' => 'active',
                'is_headquarters' => true,
                'booking_enabled' => true,
                'description_en' => 'This location serves as both an operational vehicle inspection center and NOVETESCO\'s principal administrative headquarters.',
                'description_fr' => 'Ce site sert a la fois de centre operationnel d inspection automobile et de siege administratif principal de NOVETESCO.',
                'latitude' => 5.9418158,
                'longitude' => 10.1493449,
                'google_maps_url' => 'https://www.google.com/maps?q=5.9418158,10.1493449',
                'nearby_landmark' => 'Atuakum Mankon',
                'search_keywords' => 'Bamenda, Atuakum, Mankon, Northwest, Headquarters',
                'display_order' => 3,
                'featured_image' => 'images/homepage/mankon-bamenda.png',
                'contacts' => [
                    ['type' => 'email', 'value' => 'noblevehicletestingcompany@gmail.com', 'is_primary' => true],
                    ['type' => 'phone', 'value' => '(+237) 33142037', 'is_primary' => true],
                    ['type' => 'phone', 'value' => '(+237) 675615478', 'is_primary' => false],
                ],
                'hours' => $this->standardHours(),
            ],
            [
                'slug' => 'nacho-douala',
                'name_en' => 'NOVETESCO Douala',
                'name_fr' => 'NOVETESCO Douala',
                'city_en' => 'Douala',
                'city_fr' => 'Douala',
                'region_en' => 'Littoral',
                'region_fr' => 'Littoral',
                'status' => 'construction',
                'booking_enabled' => false,
                'nearby_landmark' => null,
                'search_keywords' => 'Douala, Littoral',
                'target_date_text_en' => 'Before November 2026',
                'target_date_text_fr' => 'Avant novembre 2026',
                'display_order' => 4,
                'featured_image' => 'images/homepage/douala-coming-soon.png',
                'contacts' => [
                    ['type' => 'email', 'value' => 'noblevehicletestingcompany@gmail.com', 'is_primary' => true],
                ],
                'hours' => $this->standardHours(),
            ],
            [
                'slug' => 'nacho-kumba',
                'name_en' => 'NOVETESCO Kumba',
                'name_fr' => 'NOVETESCO Kumba',
                'city_en' => 'Kumba',
                'city_fr' => 'Kumba',
                'region_en' => 'Southwest',
                'region_fr' => 'Sud-Ouest',
                'status' => 'construction',
                'booking_enabled' => false,
                'nearby_landmark' => null,
                'search_keywords' => 'Kumba, Southwest',
                'target_date_text_en' => 'Before November 2026',
                'target_date_text_fr' => 'Avant novembre 2026',
                'display_order' => 5,
                'featured_image' => 'images/homepage/kumba-coming-soon.png',
                'contacts' => [
                    ['type' => 'email', 'value' => 'noblevehicletestingcompany@gmail.com', 'is_primary' => true],
                ],
                'hours' => $this->standardHours(),
            ],
        ];

        $bookableServiceIds = DB::table('services')
            ->whereIn('slug', ['periodic-inspection', 'light-vehicles', 'heavy-vehicles', 'counter-visit', 'pre-purchase'])
            ->pluck('id', 'slug');

        foreach ($centers as $center) {
            $contacts = $center['contacts'] ?? [];
            $hours = $center['hours'] ?? [];

            unset($center['contacts'], $center['hours']);

            DB::table('centers')->updateOrInsert(
                ['slug' => $center['slug']],
                [
                    'name_en' => $center['name_en'],
                    'name_fr' => $center['name_fr'],
                    'city_en' => $center['city_en'],
                    'city_fr' => $center['city_fr'],
                    'region_en' => $center['region_en'],
                    'region_fr' => $center['region_fr'],
                    'address_en' => $center['address_en'] ?? null,
                    'address_fr' => $center['address_fr'] ?? null,
                    'postal_address' => $center['postal_address'] ?? null,
                    'status' => $center['status'],
                    'is_headquarters' => $center['is_headquarters'] ?? false,
                    'booking_enabled' => $center['booking_enabled'],
                    'description_en' => $center['description_en'] ?? null,
                    'description_fr' => $center['description_fr'] ?? null,
                    'latitude' => $center['latitude'] ?? null,
                    'longitude' => $center['longitude'] ?? null,
                    'google_maps_url' => $center['google_maps_url'] ?? null,
                    'nearby_landmark' => $center['nearby_landmark'] ?? null,
                    'search_keywords' => $center['search_keywords'] ?? null,
                    'vehicle_categories_en' => null,
                    'vehicle_categories_fr' => null,
                    'featured_image' => $center['featured_image'] ?? null,
                    'target_opening_date' => null,
                    'target_date_text_en' => $center['target_date_text_en'] ?? null,
                    'target_date_text_fr' => $center['target_date_text_fr'] ?? null,
                    'expansion_phase' => null,
                    'expansion_updated_at' => null,
                    'display_order' => $center['display_order'],
                    'is_featured' => $center['is_headquarters'] ?? false,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ],
            );

            $centerId = DB::table('centers')->where('slug', $center['slug'])->value('id');

            foreach ($contacts as $index => $contact) {
                DB::table('center_contacts')->updateOrInsert(
                    [
                        'center_id' => $centerId,
                        'type' => $contact['type'],
                        'value' => $contact['value'],
                    ],
                    [
                        'label_en' => $contact['is_primary'] ? 'Primary' : 'Alternative',
                        'label_fr' => $contact['is_primary'] ? 'Principal' : 'Alternative',
                        'is_primary' => $contact['is_primary'],
                        'is_public' => true,
                        'display_order' => $index + 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                );
            }

            if ($contacts !== []) {
                DB::table('center_contacts')
                    ->where('center_id', $centerId)
                    ->whereNotIn('value', collect($contacts)->pluck('value'))
                    ->delete();
            }

            foreach ($hours as $hour) {
                DB::table('center_hours')->updateOrInsert(
                    [
                        'center_id' => $centerId,
                        'day_of_week' => $hour['day'],
                    ],
                    [
                        'opens_at' => $hour['opens_at'],
                        'closes_at' => $hour['closes_at'],
                        'is_closed' => false,
                        'special_note_en' => $hour['note_en'] ?? null,
                        'special_note_fr' => $hour['note_fr'] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                );
            }

            if ($center['status'] !== 'active') {
                continue;
            }

            foreach ($bookableServiceIds as $serviceId) {
                DB::table('center_service')->updateOrInsert(
                    [
                        'center_id' => $centerId,
                        'service_id' => $serviceId,
                    ],
                    [
                        'is_available' => true,
                        'booking_enabled' => true,
                        'note_en' => null,
                        'note_fr' => null,
                        'effective_date' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                );
            }
        }
    }

    /**
     * Shared weekday and Saturday schedule for every center.
     *
     * @return array<int, array<string, string>>
     */
    private function standardHours(): array
    {
        $weekdays = collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])
            ->map(fn (string $day): array => [
                'day' => $day,
                'opens_at' => '08:00',
                'closes_at' => '18:00',
                'note_en' => 'Weekdays',
                'note_fr' => 'Jours ouvrables',
            ]);

        return $weekdays->push([
            'day' => 'saturday',
            'opens_at' => '08:00',
            'closes_at' => '14:00',
            'note_en' => 'Saturday',
            'note_fr' => 'Samedi',
        ])->all();
    }
}
