<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChemicalUsedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('chemical_used')->insert([
            [
                'name' => 'Free Chlorine(FAC) -ppm',
                'unit' => 'ppm',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'pH',
                'unit' => '',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Alkalinity(ALK) -ppm',
                'unit' => 'ppm',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Cyanuric Acid(CYA) -ppm',
                'unit' => 'ppm',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Calcium Hardness -ppm',
                'unit' => 'ppm',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Salt -ppm',
                'unit' => 'ppm',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Phosphates (oz)',
                'unit' => 'oz',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Remover Added (oz)',
                'unit' => 'oz',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Primary Algaecide (oz)',
                'unit' => 'oz',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Algaecide 2 (oz)',
                'unit' => 'oz',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Stain Remover (oz)',
                'unit' => 'oz',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Metal Remover (oz)',
                'unit' => 'oz',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Clarifier/filter aid (oz)',
                'unit' => 'oz',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Flocculant (oz)',
                'unit' => 'oz',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Bromine(BR)-ppm',
                'unit' => 'ppm',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'TDS Value (default 1500ppm)',
                'unit' => 'ppm',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Temp Value (°F)',
                'unit' => '°F',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
