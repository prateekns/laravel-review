@extends('layouts.business.app')

@section('content')
    <div class="container-fluid mx-auto">
        <div class="w-full">
            <!-- Header Section -->
            <x-page-heading title="{{ __('business.customers.import.title') }}"
                description="{{ __('business.customers.import.description') }}"
                link="{{ route('business.customers.index') }}" />
         
            @if (session()->has('notification'))
                <x-notification-alert type="{{ session('notification.type') }}"
                    message="{{ session('notification.message') }}" />
            @endif

            <div class="white-box rounded-[12px] shadow-sm">
                <div class="flex gap-[19px] items-start max-[640px]:flex-col">
                    <!-- Help Section -->
                    <div class="bg-[#EFF6FF] flex flex-col p-[20px] rounded-[8px] min-[641px]:max-w-[401px] w-full min-[641px]:min-h-[215px]">
                        <h2 class="text-[16px] text-blue font-[600] border-b-[1px] border-[#000000] pb-[20px] pl-[20px]">
                            {{ __('business.customers.import.need_help') }}</h2>
                        <div class="flex items-start px-[20px] pt-[16px] gap-[16px]">

                            <div class="flex flex-col">
                                <div class="flex gap-[10px] items-center">
                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect width="40" height="40" rx="6.66667" fill="white" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M26.3379 18.5628L24.7713 16.9962L21.1102 20.6517V8.89062H18.8879V20.6517L15.2268 16.9962L13.6602 18.5628L19.999 24.9073L26.3379 18.5628Z"
                                            fill="#0D44EA" />
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M26.6649 26.668V28.8902H13.3316V26.668H11.1094V28.8902C11.1094 29.4796 11.3435 30.0448 11.7602 30.4615C12.177 30.8783 12.7422 31.1124 13.3316 31.1124H26.6649C27.2543 31.1124 27.8195 30.8783 28.2363 30.4615C28.653 30.0448 28.8872 29.4796 28.8872 28.8902V26.668H26.6649Z"
                                            fill="#0D44EA" />
                                    </svg>

                                    <a href="{{ route('business.customers.import.sample') }}"
                                        class="text-[14px] text-blue font-[600] break-auto-phrase">
                                        {{ __('business.customers.import.download_sample') }}
                                    </a>
                                </div>

                                <p class="mt-[16px] text-[14px] font-[400] text-[#1D242B] break-auto-phrase">
                                    {{ __('business.customers.import.sample_description') }}
                                </p>

                                <p class="mt-[16px] text-[14px] text-blue font-[600] break-auto-phrase">{{__("Instructions for the fields in the CSV file:")}}</p>

                                <ul class="mt-[14px] text-[14px] font-[400] text-[#1D242B] break-auto-phrase">
                                    <li><strong>pool_type –</strong> {{ __("Must be R for Residential or C for Commercial.")}}</li>
                                    <li><strong>first_name –</strong> {{ __("Mandatory; only alphabets are allowed.")}}</li>
                                    <li><strong>last_name –</strong> {{ __("Mandatory; only alphabets are allowed.")}}</li>
                                    <li><strong>email_1 –</strong> {{ __("Mandatory; must be in a valid email format.")}}</li>
                                    <li><strong>phone_1 –</strong> {{ __("Must contain only numbers.")}}</li>
                                    <li><strong>address –</strong> {{ __("Mandatory.")}}</li>
                                    <li><strong>zip_code –</strong> {{ __("Mandatory.")}}</li>
                                    <li><strong>city –</strong> {{ __("Mandatory.")}}</li>
                                    <li><strong>state – </strong>{{ __("Mandatory.")}}</li>
                                </ul>

                            </div>
                        </div>
                    </div>

                    <!-- Import Form -->
                    <div x-data="importCustomers" class="w-full">
                        <form action="{{ route('business.customers.import.process') }}" method="POST"
                            enctype="multipart/form-data" class="space-y-6" id="import-form" @submit.prevent="submitForm"
                            x-ref="form">
                            @csrf

                            <div class="border-dashed border-[1px] border-blue rounded-[8px] flex flex-col items-center justify-center h-[215px] cursor-pointer"
                                :class="{ 'border-blue-500 bg-blue-50': isDragOver }" @dragover.prevent="isDragOver = true"
                                @dragleave.prevent="isDragOver = false" @drop.prevent="handleDrop($event)"
                                @click="selectFile">
                                <div class="text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg :class="selectedFile ? 'text-green-500' : 'text-gray-400'" width="36"
                                            height="36" viewBox="0 0 36 36" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect width="36" height="36" rx="18" fill="#EFF6FF" />
                                            <path
                                                d="M10 22.2422C8.79401 21.435 8 20.0602 8 18.5C8 16.1564 9.79151 14.2313 12.0797 14.0194C12.5478 11.1721 15.0202 9 18 9C20.9798 9 23.4522 11.1721 23.9203 14.0194C26.2085 14.2313 28 16.1564 28 18.5C28 20.0602 27.206 21.435 26 22.2422M14 22L18 18M18 18L22 22M18 18V27"
                                                stroke="#0D44EA" stroke-width="2.04545" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>

                                        <div class="text-[16px] font-[500] text-[#1D242B] mt-[10px] break-auto-phrase">
                                            <span >
                                                {{ __('business.customers.import.select_file') }}
                                            </span>
                                           
                                        </div>
                                        <p class="text-[14px] font-[400] text-[#1C1D1D] mt-[12px] break-auto-phrase">
                                            {{ __('business.customers.import.file_upload_instruction') }}</p>
                                    </div>
                                    <input type="file" name="csvFile" class="hidden" accept=".csv"
                                        @change="handleFileSelect" x-ref="fileUpload">
                                    <template x-if="selectedFile">
                                        <div class="mt-[12px] flex items-center gap-[4px] justify-center">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11.6673 1.89062V5.33274C11.6673 5.79945 11.6673 6.03281 11.7581 6.21107C11.838 6.36787 11.9655 6.49535 12.1223 6.57525C12.3006 6.66607 12.5339 6.66607 13.0007 6.66607H16.4428M13.334 10.8327H6.66732M13.334 14.166H6.66732M8.33398 7.49935H6.66732M11.6673 1.66602H7.33398C5.93385 1.66602 5.23379 1.66602 4.69901 1.9385C4.2286 2.17818 3.84615 2.56063 3.60647 3.03104C3.33398 3.56582 3.33398 4.26588 3.33398 5.66602V14.3327C3.33398 15.7328 3.33398 16.4329 3.60647 16.9677C3.84615 17.4381 4.2286 17.8205 4.69901 18.0602C5.23379 18.3327 5.93385 18.3327 7.33398 18.3327H12.6673C14.0674 18.3327 14.7675 18.3327 15.3023 18.0602C15.7727 17.8205 16.1552 17.4381 16.3948 16.9677C16.6673 16.4329 16.6673 15.7328 16.6673 14.3327V6.66602L11.6673 1.66602Z" stroke="#0D44EA" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                                            <template x-if="selectedFile">
                                                <span x-text="selectedFile.name" class="text-[14px] font-[400] text-[#0D44EA] underline"></span>
                                            </template>
                                                
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <template x-if="validationErrors.length > 0">
                                <div x-text="validationErrors[0]" class="error-message-box"></div>
                            </template>

                            @error('csvFile')
                                <div class="error-message-box">{{ $message }}</div>
                            @enderror

                            <div class="flex justify-center">
                                <button type="submit" class="btn-box transition-colors duration-200" :class="'btn-box btn'"
                                    :disabled="isUploading">
                                    <span
                                        x-text="isUploading ? 'Uploading...' : '{{ __('business.customers.import.upload') }}'"></span>
                                </button>
                            </div>
                        </form>

                        @if (session('success'))
                            <div class="mt-4 p-4 bg-green-50 text-green-700 rounded-md">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mt-4 p-4 bg-red-50 text-red-700 rounded-md">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
