<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business\Technician\TechnicianMessage;

/**
 * Seeder for customer_messages table
 */
class TechnicianMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        /**
         * Preset messages for customer_messages table
         *
         * @var string[]
         */
        $messages = [
            'Contact the office when you have a chance.',
            'Make sure to send and save your tickets at the stop when completed.',
            'Make sure to use your field notes/customer notes and item sold boxes.',
            'Clean your cells this week.',
            'Weather emergency, head back to the Shop/office!',
            'Stay in the field, weather will pass.',
            'Excellent work!',
            'Take your vehicle in for service today.',
            'Tap the Refresh icon on your dashboard to get the latest jobs.'
        ];

        foreach ($messages as $msg) {
            TechnicianMessage::create([
                'message'    => $msg,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
