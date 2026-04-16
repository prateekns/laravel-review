<?php

namespace App\Livewire\Business\Profile;

use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\View\View;
use App\Models\Shared\Country;
use App\Models\Shared\State;
use App\Models\Shared\City;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class Profile extends Component
{
    public const ALPHA_SPACES_REGEX = 'regex:/^[a-zA-Z\s]+$/';
    public const MIN_LENGTH_3 = 'min:3';
    public const MIN_LENGTH_1 = 'min:1';
    public const MIN_LENGTH_5 = 'min:5';
    public const MAX_LENGTH_12 = 'max:12';
    public const MAX_LENGTH_50 = 'max:50';
    public const MAX_LENGTH_200 = 'max:200';
    public const MAX_LENGTH_100 = 'max:100';
    public const ZIPCODE_REGEX = 'regex:/^[a-zA-Z0-9 .\-_]+$/';

    public $user;
    public $current_password; //NOSONAR
    public $password;
    public $confirm_password; //NOSONAR
    public $countries;
    public $states;
    public $cities;
    public $countryId;
    public $stateId;
    public $cityId;

    // Form fields
    public $first_name; //NOSONAR
    public $last_name; //NOSONAR
    public $business_name; //NOSONAR
    public $email;
    public $phone;
    public $website_url; //NOSONAR
    public $address;
    public $street;
    public $zipcode;
    public $isd_code; //NOSONAR
    public $logo;

    /**
     * Mount the component
     *
     * @return void
     */
    public function mount(): void
    {
        $this->user = Auth::guard('business')->user();
        $this->countries = Country::where('status', Country::ACTIVE)->pluck('name', 'id')->toArray();

        // Initialize form fields with user data
        $this->first_name = $this->user->first_name;
        $this->last_name = $this->user->last_name;
        $this->business_name = $this->user->business->name;
        $this->email = $this->user->email;
        $this->phone = $this->user->business->phone;
        $this->website_url = $this->user->business->website_url;
        $this->address = $this->user->business->address;
        $this->street = $this->user->business->street;
        $this->zipcode = $this->user->business->zipcode;

        // Load states if country is selected
        if ($this->user->business->country) {
            $this->countryId = $this->user->business->country_id;

            $this->states = State::where('status', State::ACTIVE)
                ->where('country_id', $this->user->business->country_id)
                ->pluck('name', 'id')
                ->toArray();

            $this->isd_code = $this->user->business->country->isd_code;
        }

        // Load cities if state is selected
        if ($this->user->business->state) {
            $this->stateId = $this->user->business->state_id;
            $this->cities = City::where('state_id', $this->user->business->state_id)->pluck('name', 'id')->toArray();
        }

        $this->cityId = $this->user->business->city_id;
    }


    /**
     * On country changed
     *
     * @param int $countryId
     * @return void
     */
    public function onCountryChanged($countryId): void
    {
        $this->countryId = $countryId;

        $this->states = State::where('status', State::ACTIVE)
            ->where('country_id', $countryId)
            ->pluck('name', 'id')
            ->toArray();

        $this->stateId = null;
        $this->cities = [];
        $this->cityId = null;
        $this->isd_code = Country::where('id', $countryId)->value('isd_code');
    }

    /**
     * On state changed
     *
     * @param int $stateId
     * @return void
     */
    public function onStateChanged($stateId): void
    {
        $this->stateId = $stateId;
        $this->cities = City::where('state_id', $stateId)->pluck('name', 'id')->toArray();
        $this->cityId = null;
    }


    /**
     * Validate the profile
     *
     * @return void
     */
    public function validateProfile(): void
    {
        $this->validate([
            'first_name' => ['required', self::ALPHA_SPACES_REGEX, self::MIN_LENGTH_1, self::MAX_LENGTH_50],
            'last_name' => ['required', self::ALPHA_SPACES_REGEX, self::MIN_LENGTH_1, self::MAX_LENGTH_50],
            'business_name' => ['required', self::ALPHA_SPACES_REGEX, self::MIN_LENGTH_1, self::MAX_LENGTH_50],
            'email' => ['required', 'email:dns', 'unique:business_users,email,' . $this->user->id],
            'phone' => ['required', 'regex:/^\+?[\d\s-]{10,}$/','digits:10'],
            'website_url' => ['nullable', 'min:5', 'max:100'],
            'address' => ['required', 'string', self::MIN_LENGTH_5, self::MAX_LENGTH_200],
            'street' => ['required', 'string', self::MIN_LENGTH_3, self::MAX_LENGTH_100],
            'zipcode' => ['required', 'string', self::MIN_LENGTH_3,self::MAX_LENGTH_12, self::ZIPCODE_REGEX],
            'countryId' => ['required', 'exists:countries,id'],
            'stateId' => ['required', 'exists:states,id'],
            'cityId' => ['required', 'exists:cities,id'],
        ], [
            'first_name.required' => __('common.auth.required'),
            'first_name.regex' => __('common.auth.alpha_only'),
            'first_name.min' => __('common.auth.required'),
            'first_name.max' => __('common.auth.max_50'),
            'last_name.required' => __('common.auth.required'),
            'last_name.regex' => __('common.auth.alpha_only'),
            'last_name.min' => __('common.auth.required'),
            'last_name.max' => __('common.auth.max_50'),
            'business_name.required' => __('common.auth.required'),
            'business_name.regex' => __('common.auth.alpha_only'),
            'business_name.min' => __('common.auth.required'),
            'business_name.max' => __('common.auth.max_50'),
            'email.required' => __('common.auth.required'),
            'email.email' => __('common.auth.invalid_email'),
            'email.unique' => __('common.auth.account_exists'),
            'phone.required' => __('common.auth.required'),
            'phone.regex' => __('common.auth.invalid_phone'),
            'phone.digits' => __('common.auth.invalid_phone'),
            'website_url.url' => __('common.auth.invalid_url'),
            'address.required' => __('common.auth.required'),
            'address.min' => __('common.auth.min_5_max_200'),
            'address.max' => __('common.auth.min_5_max_200'),
            'street.required' => __('common.auth.required'),
            'street.min' => __('common.auth.min_3_max_100'),
            'street.max' => __('common.auth.min_3_max_100'),
            'zipcode.required' => __('common.auth.required'),
            'zipcode.regex' => __('common.auth.zipcode_error'),
            'zipcode.min' => __('common.auth.zipcode_min_3_max_12'),
            'zipcode.max' => __('common.auth.zipcode_min_3_max_12'),
            'countryId.required' => __('common.auth.required'),
            'stateId.required' => __('common.auth.required'),
            'cityId.required' => __('common.auth.required'),
        ]);
    }

    /**
     * Update the user's profile
     *
     * @return void
     */
    public function updateProfile(): void
    {
        $this->validateProfile();

        try {
            // Update user data
            $this->user->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
            ]);

            // Update business data
            $this->user->business->update([
                'name' => $this->business_name,
                'isd_code' => $this->isd_code,
                'phone' => $this->phone,
                'website_url' => $this->website_url,
                'address' => $this->address,
                'street' => $this->street,
                'zipcode' => $this->zipcode,
                'country_id' => $this->countryId,
                'state_id' => $this->stateId,
                'city_id' => $this->cityId,
            ]);

            $this->dispatch('update-success', [
                'message' => __('business.message.profile_updated')
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update profile: ', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'business_id' => $this->user->business->id,
            ]);

            $this->dispatch('update-error', [
                'message' => __('business.message.profile_update_failed')
            ]);
        }
    }

    /**
     * Render the component
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.business.profile.profile');
    }
}
