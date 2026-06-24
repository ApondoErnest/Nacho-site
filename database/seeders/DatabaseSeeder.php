<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ServiceSeeder::class,
            TariffSeeder::class,
            CenterSeeder::class,
            CareerDepartmentSeeder::class,
            BlogCategorySeeder::class,
            PageSeeder::class,
            SiteSettingSeeder::class,
        ]);
    }
}
