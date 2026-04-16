<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    /**
     * Get all available timezones.
     *
     * @return array
     */
    public static function listAll(): array
    {
        // Get all timezone identifiers
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);

        // Separate America timezones from others
        $americaTimezones = array_filter($timezones, fn ($tz) => str_starts_with($tz, 'America/'));
        $otherTimezones = array_filter($timezones, fn ($tz) => !str_starts_with($tz, 'America/'));

        // Sort both arrays
        sort($americaTimezones);
        sort($otherTimezones);

        // Merge America timezones first, followed by others
        return array_merge($americaTimezones, $otherTimezones);
    }

    /**
     * Get timezones grouped by region.
     *
     * @return array
     */
    public static function listByRegion(): array
    {
        $regions = [
            'Africa' => \DateTimeZone::AFRICA,
            'America' => \DateTimeZone::AMERICA,
            'Antarctica' => \DateTimeZone::ANTARCTICA,
            'Arctic' => \DateTimeZone::ARCTIC,
            'Asia' => \DateTimeZone::ASIA,
            'Atlantic' => \DateTimeZone::ATLANTIC,
            'Australia' => \DateTimeZone::AUSTRALIA,
            'Europe' => \DateTimeZone::EUROPE,
            'Indian' => \DateTimeZone::INDIAN,
            'Pacific' => \DateTimeZone::PACIFIC,
            'UTC' => \DateTimeZone::UTC,
        ];

        $groupedTimezones = [];
        foreach ($regions as $name => $region) {
            $groupedTimezones[$name] = \DateTimeZone::listIdentifiers($region);
        }

        return $groupedTimezones;
    }
}
