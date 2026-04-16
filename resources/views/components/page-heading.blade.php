@props(['title' => '', 'description' => '', 'link' => ''])

<div class="flex flex-row items-center justify-between w-full  max-[767px]:flex-col">
    <div class="flex flex-col gap-4 w-full">
        <!-- Back Button -->
        <a href="{{ $link }}" class="back-btn">
            <div class="flex items-center gap-2 cursor-pointer">
                <div class="flex items-center justify-start">
                    
                    <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.5 11.5L1.50001 6.49999L6.5 1.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                    <span class="text-[#0C1421] text-xs leading-4 ml-2">{{ __('Back') }}</span>
                </div>
            </div>
        </a>

        <!-- Title and Subtitle -->
        <div class="flex flex-col gap-4">
            <h1 class="main-heading">{{ __($title) }}</h1>
            <p class="sub-heading !mt-[0]">{{ $description }}</p>
        </div>
    </div>

   
        {{ $slot }}
    
</div>
