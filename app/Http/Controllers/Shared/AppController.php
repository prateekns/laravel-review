<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Shared\City;
use App\Models\Shared\State;

class AppController extends Controller
{
    public function getStates($countryId)
    {
        $states = State::where('country_id', $countryId)->where('status', State::ACTIVE)->get();

        return response()->json($states);
    }

    public function getCities($stateId)
    {
        $cities = City::where('state_id', $stateId)->get();

        return response()->json($cities);
    }
}
