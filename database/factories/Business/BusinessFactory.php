<?php

namespace Database\Factories\Business;

use App\Models\Business\Business;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get location data or use defaults
        $locationData = $this->getLocationData();

        // Business types for more realistic business names
        $businessTypes = [
            'Technologies', 'Solutions', 'Innovations', 'Group', 'Consulting',
            'Partners', 'Ventures', 'Industries', 'Services', 'Enterprises',
        ];

        // Create a business name
        $businessName = $this->faker->city().' '.$this->faker->randomElement($businessTypes);

        // Email domain based on business name
        $emailDomain = 'yopmail.com';

        return [
            'name' => $businessName,
            'email' => $this->faker->userName().'@'.$emailDomain,
            'phone' => $this->faker->numerify('##########'),
            'address' => $this->faker->streetAddress(),
            'street' => $this->faker->streetName(),
            'zipcode' => $this->faker->postcode(),
            'country_id' => $locationData['country_id'],
            'state_id' => $locationData['state_id'],
            'city_id' => $locationData['city_id'],
            'status' => $this->faker->randomElement([0, 1]),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $attributes['created_at'];
            },
        ];
    }

    /**
     * Get location data with simple defaults.
     */
    protected function getLocationData(): array
    {
        // Default values
        $result = [
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
        ];

        // Try to get a random city
        $city = DB::table('cities')->inRandomOrder()->first();

        // If we found a city, use its ID and try to get related state and country
        if ($city) {
            $result['city_id'] = $city->id;

            // Try to get the state for this city
            $state = DB::table('states')->find($city->state_id);
            if ($state) {
                $result['state_id'] = $state->id;

                // Try to get the country for this state
                $country = DB::table('countries')->find($state->country_id);
                if ($country) {
                    $result['country_id'] = $country->id;
                }
            }
        }

        return $result;
    }
}
