<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChemicalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('chemicals')->insert([
            [
                'name' => 'Free Chlorine(FAC)',
                'range' => '1-5 ppm',
                'ideal_target' => 3.0,
                'unit' => 'ppm',
                'type' => 1, // 1 = chemical
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'pH',
                'range' => '7.2 – 7.8',
                'ideal_target' => 7.5,
                'unit' => '',
                'type' => 1, // 1 = chemical
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Alkalinity(ALK)',
                'range' => '80-120 ppm',
                'ideal_target' => 100,
                'unit' => 'ppm',
                'type' => 1, // 1 = chemical
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Cyanuric Acid(CYA)',
                'range' => '30-50 ppm',
                'ideal_target' => 40,
                'unit' => 'ppm',
                'type' => 1, // 1 = chemical
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Calcium Hardness',
                'range' => '200-400 ppm',
                'ideal_target' => 300,
                'unit' => 'ppm',
                'type' => 1, // 1 = chemical
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Salt',
                'range' => '2800-3800 ppm',
                'ideal_target' => 3200,
                'unit' => 'ppm',
                'type' => 1, // 1 = chemical
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Additional chemicals with type=2 (additional items)
            [
                'name' => 'Phosphates',
                'range' => '',
                'ideal_target' => 0.00,
                'unit' => 'oz',
                'type' => 2, // 2 = additional items
                'remover_required' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Primary Algaecide',
                'range' => '',
                'ideal_target' => 0.00,
                'unit' => 'oz',
                'type' => 2, // 2 = additional items
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Algaecide 2',
                'range' => '',
                'ideal_target' => 0.00,
                'unit' => 'oz',
                'type' => 2, // 2 = additional items
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Stain Remover',
                'range' => '',
                'ideal_target' => 0.00,
                'unit' => 'oz',
                'type' => 2, // 2 = additional items
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Metal Remover',
                'range' => '',
                'ideal_target' => 0.00,
                'unit' => 'oz',
                'type' => 2, // 2 = additional items
                'remover_required' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Clarifier/filter aid',
                'range' => '',
                'ideal_target' => 0.00,
                'unit' => 'oz',
                'type' => 2, // 2 = additional items
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Flocculant',
                'range' => '',
                'ideal_target' => 0.00,
                'unit' => 'oz',
                'type' => 2, // 2 = additional items
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Bromine(BR)',
                'range' => '3-6 ppm',
                'ideal_target' => 0.00,
                'unit' => 'ppm',
                'type' => 2, // 1 = chemical
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'TDS Value',
                'range' => '',
                'ideal_target' => 0.00,
                'unit' => 'ppm',
                'type' => 2, // 1 = chemical
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Temp Value',
                'range' => '',
                'ideal_target' => 0.00,
                'unit' => '°F',
                'type' => 2, // 1 = chemical
                'remover_required' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
