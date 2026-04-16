<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business\CustomerMessage;

/**
 * Seeder for customer_messages table
 */
class CustomerMessageSeeder extends Seeder
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
            'Your pool technician is on the Way.',
            'Your pool technician is Heading your way, be there in 1 hour.',
            'Your pool technician says please put up the dogs.',
            'Your pool technician says please add water.',
            'Your pool technician is Heading your way, be there in 20 minutes.',
            'Your pool technician says please check your email.',
            'Your pool technician says please cut off water.',
            'Your pool technician says please open gate.'
        ];

        foreach ($messages as $msg) {
            CustomerMessage::create([
                'message'    => $msg,
                'status'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
