@props(['buttonText' => 'Upgrade Now', 'warningText' => '', 'link' => ''])

<div class="limit-warning w-full bg-yellow-100 border border-yellow-200 rounded-[18px] p-6 flex flex-row gap-8 mt-6 max-[767px]:flex-col">
    <div class="flex flex-row items-start gap-3 flex-1 max-[767px]:flex-col">
        <!-- Warning Icon -->
        <div class="w-[42px] h-[42px]">
            <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="42" height="42" rx="21" fill="#DFB400"/>
                <g clip-path="url(#clip0_596_66378)">
                <path d="M21.1427 17.6191V21.143M21.1427 24.6668H21.1515M19.6363 13.0911L12.1746 25.5477C12.0207 25.8141 11.9393 26.1162 11.9385 26.4238C11.9376 26.7315 12.0173 27.034 12.1697 27.3013C12.322 27.5686 12.5417 27.7913 12.8069 27.9473C13.072 28.1033 13.3734 28.1872 13.681 28.1906H28.6044C28.912 28.1872 29.2134 28.1033 29.4785 27.9473C29.7437 27.7913 29.9633 27.5686 30.1157 27.3013C30.268 27.034 30.3477 26.7315 30.3469 26.4238C30.346 26.1162 30.2646 25.8141 30.1108 25.5477L22.6491 13.0911C22.4921 12.8321 22.2709 12.6181 22.0071 12.4695C21.7432 12.321 21.4455 12.2429 21.1427 12.2429C20.8399 12.2429 20.5422 12.321 20.2783 12.4695C20.0144 12.6181 19.7933 12.8321 19.6363 13.0911Z" stroke="white" stroke-width="1.7619" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                <clipPath id="clip0_596_66378">
                <rect width="21.1429" height="21.1429" fill="white" transform="translate(10.5713 10.5714)"/>
                </clipPath>
                </defs>
            </svg>
        </div>

        <!-- Warning Text -->
        <p class="text-black-0 text-sm leading-[1.3] font-normal self-center">
            {{ $warningText }}
        </p>
    </div>

    <!-- Upgrade Button -->
    <x-form.link :link="$link" class="btn w-12/12 max-w-[170px] flex justify-center items-center cursor-pointer">
        {{ $buttonText }}
    </x-form.link>
</div>
