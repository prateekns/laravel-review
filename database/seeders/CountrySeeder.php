<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'United States', 'code' => 'US','isd_code' => '+1'],
            ['name' => 'Canada', 'code' => 'CA','isd_code' => '+1'],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'name' => $country['name'],
                'code' => $country['code'],
                'isd_code' => $country['isd_code'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
