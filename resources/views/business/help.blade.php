@extends('layouts.business.app')

@section('title', __('Help'))

@section('content')

@if (session('success'))
<x-notification-alert type="success" :message="session('success')" />
@endif

@if (session('error'))
<x-notification-alert type="error" :message="session('error')" />
@endif

<div class="flex justify-center items-start">
    <video src="{{ $video }}" controls class="w-full h-auto rounded-[18px] object-cover">
        <track kind="captions" src="{{ $video }}" srclang="en" label="English" />
    </video>
</div>


@if(!$user->business->subscribed())
<div class="flex flex-col rounded-[18px] bg-[#DBEAFE] text-center mt-[24px] p-[24px] items-start">
    <h2 class="text-[32px] font-[700] text-[#212529]">
        {{__('Get Started with Pay-As-You-Go')}}

    </h2>
    <p class="text-[20px] font-[400] text-[#1D242B] mt-[24px]">
        @if($user->business->onTrial())
        {{__('You are on a free trial. Upgrade anytime to continue uninterrupted access.')}}
        @else
        {{__('Your trial has ended. Please purchase a plan to continue using all features.')}}
        @endif
    </p>
    <a href="{{ route('account.index') }}" class="btn-box btn mt-[24px] max-[767px]:w-full justify-center">
        {{ $user->business->onTrial() ? __('Upgrade Now') : __('Purchase Plan')}}
    </a>
</div>
@endif

    <div  class="mt-[24px] mb-[24px]  !py-[24px] !px-[24px] white-box" x-data="{ activeAccordion: 1 }">
        <h1 class="text-[24px] font-[600] text-[#000000] mb-[24px]">{{ __('Your Guide to Key Modules') }}</h1>
        
        <div class="space-y-4">
            @foreach($faqs as $index => $faq)
            <div class="border-b-[#4B5563] border-b-[1px] overflow-hidden last:border-b-0 mb-0" :class="{'bg-[#EFF6FF]': activeAccordion === {{ $index + 1 }}, 'bg-[#FFFFFF]': activeAccordion !== {{ $index + 1 }}}">
                <button @click="activeAccordion = activeAccordion === {{ $index + 1 }} ? null : {{ $index + 1 }}"
                        class="w-full flex items-start text-left justify-between p-6 focus:outline-none cursor-pointer">
                    <div class="flex items-start text-left">
                        <span class="text-[32px] font-[700] text-[#767676] mr-4 max-[640]:text-[22px] max-[640]:mt-[2px]">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h2 class="text-[26px] font-[700] text-[#000000] mt-[2px] max-[640]:text-[18px]">{{ $faq->getTranslatedQuestion($locale) }}</h2>
                    </div>

                    <div x-show="activeAccordion !== {{ $index + 1 }}">
                        <svg class="w-6 h-6 transform transition-transform duration-200" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="14" cy="14" r="13.5525" stroke="black" stroke-width="0.895035" />
                            <path d="M10.0908 14.6582V13.3352H18.6798V14.6582H10.0908ZM13.7028 9.49219H15.0678V18.4802H13.7028V9.49219Z" fill="black" />
                        </svg>

                    </div>
                    <div x-show="activeAccordion === {{ $index + 1 }}" style="display: none;">
                        <a href="{{ $faq->link }}">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="14" cy="14" r="14" fill="#0D44EA" />
                                <path d="M10.0591 16.852C9.81438 17.0967 9.81439 17.4934 10.0591 17.7381C10.3037 17.9827 10.7004 17.9827 10.9451 17.7381L10.0591 16.852ZM17.9244 10.499C17.9244 10.153 17.6439 9.87249 17.2978 9.8725L11.6591 9.8726C11.3131 9.87261 11.0326 10.1531 11.0326 10.4991C11.0326 10.8452 11.3131 11.1257 11.6591 11.1257L16.6713 11.1256L16.6714 16.1378C16.6714 16.4838 16.9519 16.7643 17.298 16.7643C17.644 16.7643 17.9245 16.4837 17.9245 16.1377L17.9244 10.499ZM10.5021 17.295L10.9451 17.7381L17.7409 10.942L17.2979 10.499L16.8548 10.056L10.0591 16.852L10.5021 17.295Z" fill="white" />
                            </svg>
                        </a>
                    </div>
                </button>

                <div x-show="activeAccordion === {{ $index + 1 }}"  class="pr-[48px] pl-[58px] pb-6">
                    <p class="text-[18px] font-[400] text-[#000000] max-[640px]:text-[16px]">{!! $faq->getTranslatedAnswer($locale) !!}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection
