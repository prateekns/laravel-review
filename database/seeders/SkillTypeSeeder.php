<?php

namespace Database\Seeders;

use App\Models\Business\Technician\SkillType;
use App\Models\User;
use Illuminate\Database\Seeder;

class SkillTypeSeeder extends Seeder
{
    /**
     * Skill types
     */
    protected $skillTypes = [
        'Maintenance',
        'Service',
        'Construction',
        'Utility Tech',
        'Manager',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get an admin user if available
        $createdBy = 1;

        $this->command->info('Seeding skill types...');

        foreach ($this->skillTypes as $skillType) {
            SkillType::create([
                'skill_type' => $skillType,
                'created_by' => $createdBy,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('Skill types seeded successfully!');
    }
}
