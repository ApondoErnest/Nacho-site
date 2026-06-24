<?php

namespace Database\Factories;

use App\Enums\CareerPostStatus;
use App\Models\CareerDepartment;
use App\Models\CareerPost;
use App\Models\Center;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CareerPost>
 */
class CareerPostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->jobTitle();

        return [
            'reference' => 'NCH-CAR-'.now()->format('Y').'-'.fake()->unique()->numerify('###'),
            'title_en' => $title,
            'title_fr' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'department_id' => CareerDepartment::factory(),
            'center_id' => Center::factory(),
            'employment_type' => 'full-time',
            'summary_en' => fake()->sentence(),
            'summary_fr' => fake()->sentence(),
            'description_en' => fake()->paragraph(),
            'description_fr' => fake()->paragraph(),
            'responsibilities_en' => fake()->paragraph(),
            'responsibilities_fr' => fake()->paragraph(),
            'requirements_en' => fake()->paragraph(),
            'requirements_fr' => fake()->paragraph(),
            'preferred_requirements_en' => null,
            'preferred_requirements_fr' => null,
            'skills_en' => fake()->sentence(),
            'skills_fr' => fake()->sentence(),
            'application_documents_en' => 'CV and cover letter',
            'application_documents_fr' => 'CV et lettre de motivation',
            'application_email' => 'careers@nacho.local',
            'application_subject' => 'Application - {title} - {reference}',
            'application_instructions_en' => fake()->sentence(),
            'application_instructions_fr' => fake()->sentence(),
            'vacancies_count' => 1,
            'published_at' => now(),
            'closes_at' => today()->addMonth(),
            'status' => CareerPostStatus::PUBLISHED->value,
            'allow_email_application' => true,
            'display_order' => fake()->numberBetween(1, 50),
            'created_by' => User::factory(),
            'seo_title_en' => null,
            'seo_title_fr' => null,
            'meta_description_en' => null,
            'meta_description_fr' => null,
        ];
    }
}
