<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the first local super admin.
     */
    public function run(): void
    {
        $now = now();
        $email = env('SEED_ADMIN_EMAIL', 'admin@nacho.local');
        $roleId = DB::table('roles')->where('slug', 'super-admin')->value('id');

        $existingUser = DB::table('users')->where('email', $email)->first();

        if ($existingUser) {
            DB::table('users')
                ->where('id', $existingUser->id)
                ->update([
                    'name' => 'NACHO Super Admin',
                    'role_id' => $roleId,
                    'status' => 'active',
                    'email_verified_at' => $existingUser->email_verified_at ?? $now,
                    'updated_at' => $now,
                ]);

            return;
        }

        DB::table('users')->insert([
            'name' => 'NACHO Super Admin',
            'email' => $email,
            'email_verified_at' => $now,
            'password' => Hash::make(env('SEED_ADMIN_PASSWORD', 'NachoAdmin2026!')),
            'role_id' => $roleId,
            'phone' => null,
            'status' => 'active',
            'last_login_at' => null,
            'remember_token' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
