@props([
    'skillTypes' => [],
    'selectedSkills' => [],
    'name' => '',
    'old_value' => '',
    'placeholder' => 'Select a option',
])

@php
    $selectedSkillIds = collect($selectedSkills)->pluck('id')->toArray();
@endphp

<select id="select" x-cloak style="display: none;">
    @foreach ($skillTypes as $skillType)
        <option value="{{ $skillType->id }}" {{ in_array($skillType->id, $selectedSkillIds) ? 'selected' : '' }}>{{ $skillType->skill_type }}</option>
    @endforeach
</select>

<div x-data="multiSelect()" x-init="loadOptions()">
    <input name="old_data" x-ref="old_value" type="hidden" value="{{ $old_value }}">
    <div class="inline-block relative w-full">
        <div x-on:click="open">
            <div class="multiselect-box">
                <div class="flex flex-auto flex-wrap">
                    <p class="text-[16px] text-[#767676] font-[400]">{{ __('Select Skill Type') }}</p>
                    <div x-show="selected.length == 0" class="w-1/2">
                    </div>
                </div>
                <div class="text-gray-300 w-8 pl-2 pr-1 flex items-center">
                    <button type="button" x-show="isOpen() === true" x-on:click="open"
                        class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none">
                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                            <path
                                d="M2.582,13.891c-0.272,0.268-0.709,0.268-0.979,0s-0.271-0.701,0-0.969l7.908-7.83
                                    c0.27-0.268,0.707-0.268,0.979,0l7.908,7.83c0.27,0.268,0.27,0.701,0,0.969c-0.271,0.268-0.709,0.268-0.978,0L10,6.75L2.582,13.891z" />
                        </svg>
                    </button>
                    <button type="button" x-show="isOpen() === false" @click="close"
                        class="cursor-pointer w-6 h-6 text-gray-600 outline-none focus:outline-none">

                        <svg version="1.1" class="fill-current h-4 w-4" viewBox="0 0 20 20">
                            <path d="M17.418,6.109c0.272-0.268,0.709-0.268,0.979,0s0.271,0.701,0,0.969l-7.908,7.83
                                    c-0.27,0.268-0.707,0.268-0.979,0l-7.908-7.83c-0.27-0.268-0.27-0.701,0-0.969c0.271-0.268,0.709-0.268,0.979,0L10,13.25
                                    L17.418,6.109z" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="selected-options-multiple-box">
                <template x-for="(option,index) in selected" :key="options[option].value">
                    <div class="options">
                        <div class="text-[12px] text-blue font-[400]" x-text="options[option].text"></div>
                        <div class="flex flex-auto flex-row-reverse cursor-pointer">
                            <div x-on:click.stop="remove(index,option)">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect width="20" height="20" rx="10" fill="white" />
                                    <path
                                        d="M13.4424 7.375L10.8594 10L13.4424 12.625L12.582 13.5L9.99805 10.874L7.41602 13.5L6.55469 12.625L9.1377 10L6.55469 7.375L7.41602 6.5L9.99805 9.125L12.582 6.5L13.4424 7.375Z"
                                        fill="#0D44EA" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <div class="w-full px-4">
            <div x-show.transition.origin.top="isOpen()" class="multiselect-dropdown-menu-box" x-on:click.away="close">
                <div class="multiselect-dropdown-menu">
                    <template x-for="(option,index) in options" :key="index" class="overflow-auto">
                        <div class="multiselect-items" @click="select(index,$event)">
                            <div class="flex w-full items-center p-2 pl-2 border-transparent border-l-2 relative">
                                <div class="w-full items-center flex justify-between">
                                    <div class="mx-2 leading-6 cursor-pointer" x-text="option.text" :class="{ 'text-[#0d44ea]': option.selected }"></div>
                                    <div x-show="option.selected">
                                        <svg class="svg-icon" viewBox="0 0 20 20">
                                            <path fill="none" d="M7.197,16.963H7.195c-0.204,0-0.399-0.083-0.544-0.227l-6.039-6.082c-0.3-0.302-0.297-0.788,0.003-1.087
                                                    C0.919,9.266,1.404,9.269,1.702,9.57l5.495,5.536L18.221,4.083c0.301-0.301,0.787-0.301,1.087,0c0.301,0.3,0.301,0.787,0,1.087
                                                    L7.741,16.738C7.596,16.882,7.401,16.963,7.197,16.963z" stroke="#0d44ea"
                                                        stroke-width="1"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
