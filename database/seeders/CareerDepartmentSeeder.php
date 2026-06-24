<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CareerDepartmentSeeder extends Seeder
{
    /**
     * Seed career-family departments for the careers page.
     */
    public function run(): void
    {
        $now = now();

        $departments = [
            [
                'slug' => 'technical-inspection',
                'name_en' => 'Technical Inspection',
                'name_fr' => 'Controle technique',
                'description_en' => 'Inspection, diagnostics, and technical vehicle safety roles.',
                'description_fr' => 'Inspection, diagnostic et securite technique des vehicules.',
                'icon' => 'clipboard-check',
                'display_order' => 1,
            ],
            [
                'slug' => 'center-operations',
                'name_en' => 'Center Operations',
                'name_fr' => 'Operations des centres',
                'description_en' => 'Reception, customer flow, and inspection center coordination.',
                'description_fr' => 'Accueil, parcours client et coordination des centres.',
                'icon' => 'building-2',
                'display_order' => 2,
            ],
            [
                'slug' => 'quality-safety-admin',
                'name_en' => 'Quality, Safety and Administration',
                'name_fr' => 'Qualite, securite et administration',
                'description_en' => 'Quality systems, safety oversight, finance, and administration.',
                'description_fr' => 'Qualite, securite, finances et administration.',
                'icon' => 'shield-check',
                'display_order' => 3,
            ],
            [
                'slug' => 'digital-support',
                'name_en' => 'Digital and Technical Support',
                'name_fr' => 'Support numerique et technique',
                'description_en' => 'Digital tools, reporting support, and technical systems assistance.',
                'description_fr' => 'Outils numeriques, reporting et assistance systemes.',
                'icon' => 'monitor-cog',
                'display_order' => 4,
            ],
        ];

        foreach ($departments as $department) {
            DB::table('career_departments')->updateOrInsert(
                ['slug' => $department['slug']],
                [
                    ...$department,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
