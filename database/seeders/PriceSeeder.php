<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('prices')->insert([
            [
                'type' => 'admin',
                'label' => 'Daily',
                'description' => 'Pool Route Admin',
                'price_id' => 'price_1RlWUfH2j1ToP2ms1jPIJgSQ',
                'interval' => 'daily',
                'price' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'admin',
                'label' => 'Bill Monthly',
                'description' => 'Pool Route Admin',
                'price_id' => 'price_1RgpUuH2j1ToP2msYVRZMRN6',
                'interval' => 'monthly',
                'price' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'admin',
                'label' => 'Half Yearly',
                'description' => 'Pool Route Admin',
                'price_id' => 'price_1RgpVvH2j1ToP2msSRvwTJmk',
                'interval' => 'half-yearly',
                'price' => 57,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'admin',
                'label' => 'Bill Annually',
                'description' => 'Pool Route Admin',
                'price_id' => 'price_1RgpWoH2j1ToP2ms41ypX0qc',
                'interval' => 'yearly',
                'price' => 108,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'technician',
                'label' => 'Daily',
                'description' => 'Pool Route Technician',
                'price_id' => 'price_1RlWVVH2j1ToP2msnCUi2Q65',
                'interval' => 'daily',
                'price' => 20,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
               'type' => 'technician',
                'label' => 'Bill Monthly',
                'description' => 'Pool Route Technician',
                'price_id' => 'price_1RgpYCH2j1ToP2msfLncufBf',
                'interval' => 'monthly',
                'price' => 20,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'technician',
                'label' => 'Half Yearly',
                'description' => 'Pool Route Technician',
                'price_id' => 'price_1RgpaTH2j1ToP2msPMxku8lR',
                'interval' => 'half-yearly',
                'price' => 114,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'type' => 'technician',
                'label' => 'Bill Annually',
                'description' => 'Pool Route Technician',
                'price_id' => 'price_1RgpbGH2j1ToP2msFf5ekTLk',
                'interval' => 'yearly',
                'price' => 216,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
