<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Seed public inspection services.
     */
    public function run(): void
    {
        $now = now();

        $services = [
            [
                'slug' => 'periodic-inspection',
                'title_en' => 'Periodic Vehicle Technical Inspection',
                'title_fr' => 'Controle technique periodique du vehicule',
                'short_description_en' => 'Official periodic inspection for compliance and safer roads.',
                'short_description_fr' => 'Controle technique periodique officiel pour la conformite et la securite.',
                'icon' => 'clipboard-check',
                'display_order' => 1,
            ],
            [
                'slug' => 'light-vehicles',
                'title_en' => 'Light Vehicle Inspection',
                'title_fr' => 'Inspection vehicules legers',
                'short_description_en' => 'Professional checks for cars, SUVs, pickups, and light utility vehicles.',
                'short_description_fr' => 'Controles professionnels pour voitures, SUV, pick-up et utilitaires legers.',
                'icon' => 'car-front',
                'display_order' => 2,
            ],
            [
                'slug' => 'heavy-vehicles',
                'title_en' => 'Heavy Vehicle Inspection',
                'title_fr' => 'Inspection des vehicules lourds',
                'short_description_en' => 'Stricter checks for trucks, buses, and special heavy vehicles.',
                'short_description_fr' => 'Controles renforces pour camions, bus et vehicules lourds speciaux.',
                'icon' => 'truck',
                'display_order' => 3,
            ],
            [
                'slug' => 'counter-visit',
                'title_en' => 'Counter-Visit / Re-inspection',
                'title_fr' => 'Contre-visite / Re-inspection',
                'short_description_en' => 'Re-inspection after repairs following a suspended result.',
                'short_description_fr' => 'Re-inspection apres reparation suite a un resultat suspendu.',
                'icon' => 'refresh-cw',
                'display_order' => 4,
            ],
            [
                'slug' => 'pre-purchase',
                'title_en' => 'Pre-Purchase Vehicle Inspection',
                'title_fr' => 'Inspection avant achat',
                'short_description_en' => 'Independent assessment before you buy a used vehicle.',
                'short_description_fr' => 'Evaluation independante avant achat d un vehicule d occasion.',
                'icon' => 'scan-search',
                'display_order' => 5,
            ],
            [
                'slug' => 'road-safety',
                'title_en' => 'Road Safety Advisory',
                'title_fr' => 'Conseils en securite routiere',
                'short_description_en' => 'Guidance to prepare your vehicle and reduce inspection risk.',
                'short_description_fr' => 'Conseils pour preparer votre vehicule et reduire les risques.',
                'icon' => 'shield-check',
                'display_order' => 6,
            ],
        ];

        foreach ($services as $service) {
            DB::table('services')->updateOrInsert(
                ['slug' => $service['slug']],
                [
                    ...$service,
                    'full_description_en' => null,
                    'full_description_fr' => null,
                    'featured_image' => null,
                    'is_active' => true,
                    'seo_title_en' => null,
                    'seo_title_fr' => null,
                    'meta_description_en' => null,
                    'meta_description_fr' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
