<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogCategorySeeder extends Seeder
{
    /**
     * Seed starter blog categories.
     */
    public function run(): void
    {
        $now = now();

        $categories = [
            [
                'slug' => 'road-safety',
                'name_en' => 'Road Safety',
                'name_fr' => 'Securite routiere',
                'description_en' => 'Vehicle safety, preparation, and responsible driving education.',
                'description_fr' => 'Securite automobile, preparation et conduite responsable.',
            ],
            [
                'slug' => 'inspection',
                'name_en' => 'Technical Inspection',
                'name_fr' => 'Controle technique',
                'description_en' => 'Inspection process guidance and vehicle compliance education.',
                'description_fr' => 'Conseils sur le controle technique et la conformite automobile.',
            ],
        ];

        foreach ($categories as $category) {
            DB::table('blog_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                [
                    ...$category,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
