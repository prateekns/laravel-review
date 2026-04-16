<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin1@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('P@ssword25'),
                'is_primary' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin2@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('P@ssword25'),
                'is_primary' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin3@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('P@ssword25'),
                'is_primary' => 1,
                'status' => 1,
            ],
        ]);
    }
}
