<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Seed the staff roles used by the custom authorization matrix.
     */
    public function run(): void
    {
        $now = now();

        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full access to every admin module, including users and settings.',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Manage most website content and operational modules.',
            ],
            [
                'name' => 'Center Manager',
                'slug' => 'center-manager',
                'description' => 'Manage assigned center information and center-related bookings.',
            ],
            [
                'name' => 'Receptionist',
                'slug' => 'receptionist',
                'description' => 'Manage booking requests and contact messages.',
            ],
            [
                'name' => 'Inspector',
                'slug' => 'inspector',
                'description' => 'View bookings and update inspection workflow statuses.',
            ],
            [
                'name' => 'Content Manager',
                'slug' => 'content-manager',
                'description' => 'Manage public content, services, pages, media, blog, and careers.',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                [
                    ...$role,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }
    }
}
