<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shared\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'currency' => 'USD',
            'training_video' => null,
            'trial_period' => 15,
            'trial_admin' => 1,
            'trial_technician' => 2,
            'admin_product_id' => 'prod_Sc3cyC96RQo9g6',
            'technician_product_id' => 'prod_Sc3f2jYl0OA8f7',
            'discount_half_yearly' => 5,
            'discount_yearly' => 10,
            'status' => 1,
            'admin_email' => 'admin@example.com',
            'website' => 'https://pool-route.com',
            'phone' => '877.807.3750'
        ]);
    }
}
