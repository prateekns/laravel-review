<?php

namespace App\Actions\Business;

use App\Actions\UploadImage;
use App\Exceptions\UploadException;
use App\Models\Business\Business;
use App\Models\Business\BusinessUser;
use App\Models\Shared\Setting;
use Exception;
use App\Exceptions\OnBoardingException;
use Illuminate\Support\Facades\DB;
use App\Models\Shared\Country;

class BusinessOnboarding
{
    public function __construct(private UploadImage $uploadImage)
    {
    }

    /**
     * Handle the complete business onboarding process.
     *
     * @throws Exception
     */
    public function handle(BusinessUser $businessUser, array $data): Business
    {
        try {
            return DB::transaction(function () use ($businessUser, $data) {
                // Get business instance first
                $business = $businessUser->business;
                $isd_code = Country::find($data['country'])->isd_code;

                // Handle logo upload using reusable action with correct parameters
                $logoPath = $this->uploadImage->handle($data['logo'], Business::IMAGE_PATH);

                // Update business user details (simple inline operation)
                $businessUser->update([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                ]);

                // Get trial period (simple inline operation)
                $settings = Setting::first();
                $trialPeriod = $settings?->trial_period ?? config('app.trial_period');

                // Update business details (many fields, but straightforward)
                $businessData = [
                    'name' => $data['business_name'],
                    'isd_code' => $isd_code ?? config('app.isd_code'),
                    'phone' => $data['phone_number'],
                    'website_url' => $data['website_url'],
                    'address' => $data['address'],
                    'street' => $data['street'],
                    'country_id' => $data['country'],
                    'state_id' => $data['state'],
                    'city_id' => $data['city'],
                    'zipcode' => $data['zipcode'],
                    'timezone' => $data['timezone'],
                    'onboarding_completed' => Business::ONBOARDING_COMPLETED,
                    'status' => Business::STATUS_ACTIVE,
                    'trial_ends_at' => now()->addDays($trialPeriod)->endOfDay(),
                    'trial_end_at' => now()->addDays($trialPeriod)->endOfDay(),
                ];

                if ($logoPath) {
                    $businessData['logo'] = $logoPath;
                }

                $business->update($businessData);
                return $business->fresh();
            });

        } catch (UploadException $e) {
            $errorMessage = $e->getMessage();
        } catch (Exception $e) {
            $errorMessage = trans('business.message.onboarding_error');
        }

        throw new OnBoardingException($errorMessage);
    }
}
