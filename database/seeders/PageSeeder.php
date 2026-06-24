<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSeeder extends Seeder
{
    /**
     * Seed editable legal page placeholders.
     */
    public function run(): void
    {
        $now = now();

        $pages = [
            [
                'slug' => 'privacy-policy',
                'title_en' => 'Privacy Policy',
                'title_fr' => 'Politique de confidentialite',
            ],
            [
                'slug' => 'terms-and-conditions',
                'title_en' => 'Terms and Conditions',
                'title_fr' => 'Conditions generales',
            ],
            [
                'slug' => 'cookie-policy',
                'title_en' => 'Cookie Policy',
                'title_fr' => 'Politique cookies',
            ],
            [
                'slug' => 'legal-notice',
                'title_en' => 'Legal Notice',
                'title_fr' => 'Mentions legales',
            ],
        ];

        foreach ($pages as $page) {
            DB::table('pages')->updateOrInsert(
                ['slug' => $page['slug']],
                [
                    ...$page,
                    'content_en' => 'Content to be validated by NACHO.',
                    'content_fr' => 'Contenu a valider par NACHO.',
                    'status' => 'published',
                    'seo_title_en' => $page['title_en'],
                    'seo_title_fr' => $page['title_fr'],
                    'meta_description_en' => null,
                    'meta_description_fr' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ],
            );
        }
    }
}
