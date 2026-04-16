<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            FaqSeeder::class,
            PriceSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            CountrySeeder::class,
            SkillTypeSeeder::class,    // Will create skill types
            CustomerMessageSeeder::class,
            TechnicianMessageSeeder::class,
            ChemicalSeeder::class,     // Will create chemicals
            ChemicalUsedSeeder::class, // Will create chemical used items
        ]);
    }
}
