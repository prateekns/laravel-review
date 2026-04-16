@extends('layouts.business.app')

@section('title', 'Business Dashboard')

@section('content')

<div class="container-fluid mx-auto">
    <div class="payement-successful-box flex items-center justify-center flex-col gap-[16px]">
        <div class="icon">
            <svg width="88" height="88" viewBox="0 0 88 88" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="44" cy="44" r="42.5" stroke="#16A34A" stroke-width="3" />
                <mask id="mask0_344_40400" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="16" y="16" width="56" height="56">
                    <rect x="16.5" y="16.5" width="55" height="55" fill="#D9D9D9" stroke="#16A34A" />
                </mask>
                <g mask="url(#mask0_344_40400)">
                    <path d="M63.5371 27.8223C64.2331 27.8223 64.7542 28.0424 65.1562 28.459C65.5601 28.8775 65.7793 29.4282 65.7793 30.168C65.7793 30.9102 65.5599 31.4617 65.1562 31.8799L38.459 59.542C38.2238 59.7856 37.9863 59.9402 37.749 60.0273L37.7461 60.0283C37.4819 60.1268 37.1929 60.1787 36.875 60.1787C36.5573 60.1787 36.2689 60.1268 36.0049 60.0283L36.002 60.0273L35.8232 59.9482C35.6456 59.8567 35.4682 59.7245 35.292 59.542L22.8135 46.6123C22.415 46.1993 22.2055 45.6557 22.2246 44.9189C22.246 44.174 22.4769 43.6151 22.8857 43.1914C23.2913 42.7712 23.8027 42.5548 24.4697 42.5547C25.1368 42.5547 25.6482 42.7713 26.0537 43.1914L36.4277 53.9404L36.875 54.4043L37.3232 53.9404L61.916 28.459C62.3177 28.0428 62.839 27.8223 63.5371 27.8223Z" fill="#16A34A" stroke="#16A34A" stroke-width="1.24364" />
                </g>
            </svg>

        </div>
        <div class="success-message-box flex items-center justify-center mx-auto flex-col">
            <h1 class="text-[32px] font-[700] text-[#212529] break-auto-phrase">{{ __('Payment Successful!') }}</h1>
            <p class="text-[16px] font-[400] text-[#4B5563] break-auto-phrase">{{ __('Thank you, your payment has been processed successfully') }}</p>
        </div>
    </div>
    <div class="w-full rounded-[12px] bg-white shadow-sm mt-[28px] flex flex-col p-[24px] gap-[40px]">
        <div class="flex items-center justify-center flex-col border-b-[1px] border-[#E5E7EB]">
            <h2 class="text-[20px] font-[600] text-[#000000] text-center break-auto-phrase">{{ __('Payment Details') }}</h2>
            <p class="text-[16px] font-[400] text-[#374151] pb-[12px] break-auto-phrase">{{ \Carbon\Carbon::parse($order?->updated_at)->format('M d, Y') }}</p>
        </div>
        <div class="grid grid-cols-2 border-b-[1px] border-[#E5E7EB]">
            <h2 class=" text-[20px] font-[400] text-[#000000] pb-[40px break-auto-phrase">{{ __('Total Amount:') }}</h1>
                <p class="text-[20px] font-[600] text-blue pb-[40px] text-right break-auto-phrase"> {{ $amount }}</p>

        </div>
        <div class="flex">
            <h2 class="text-[16px] font-[400] text-[#1D242B] break-auto-phrase">{{ __('Payment Information') }}</h2>
        </div>
        <div class="grid grid-cols-2">
            <p class="text-[20px] font-[400] text-[#1D242B] pb-[40px] flex items-center gap-[8px]"><x-icons :name="$business->pm_type" />{{ $business->pm_type }}</p>
            <p class="text-[20px] font-[400] text-blue pb-[40px] text-right flex items-center gap-[8px] justify-end whitespace-nowrap"><span>Credit **{{ $business->pm_last_four }}</span>

                <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.66667 7.3335V4.66683C3.66667 3.78277 4.01786 2.93493 4.64298 2.30981C5.2681 1.68469 6.11595 1.3335 7 1.3335C7.88406 1.3335 8.7319 1.68469 9.35702 2.30981C9.98214 2.93493 10.3333 3.78277 10.3333 4.66683V7.3335M2.33333 7.3335H11.6667C12.403 7.3335 13 7.93045 13 8.66683V13.3335C13 14.0699 12.403 14.6668 11.6667 14.6668H2.33333C1.59695 14.6668 1 14.0699 1 13.3335V8.66683C1 7.93045 1.59695 7.3335 2.33333 7.3335Z" stroke="#0D44EA" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

            </p>
        </div>

        <div class="flex items-center justify-center">
            <x-form.link :link="route('dashboard')" class="btn  justify-center">{{ __('Proceed to Dashboard')}}</x-form.link>
        </div>

    </div>
</div>
@endsection
