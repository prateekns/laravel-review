@props([
'title' => __('Select message'),
'messages' => '',
'onClick' => '',
'btnConfirm' => __('Submit'),
])

<div x-show="showFeedbackModal" x-cloak class="relative z-[999]" aria-labelledby="modal-technician-message" role="dialog" aria-modal="true">
  <div class="fixed inset-0 bg-gray-600/75" aria-hidden="true"></div>
  <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 payment-error-modal">
      <div class="relative transform overflow-hidden rounded-[12px] bg-white text-left shadow-sm transition-all p-[24px] min-[767px]:min-w-[709px] min-w-[100%]">
<button type="button" class="absolute top-[16px] right-[16px] cursor-pointer" @click="showFeedbackModal = false; feedback = ''">
                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M6.4 19.5L5 18.1L10.6 12.5L5 6.9L6.4 5.5L12 11.1L17.6 5.5L19 6.9L13.4 12.5L19 18.1L17.6 19.5L12 13.9L6.4 19.5Z" fill="#1D1B20"/>
</svg>        </button>
        <div class="flex flex-col items-center justify-center gap-[20px]">
                
           

            <h3 class="inline-flex items-center font-[700] text-[32px] text-[#1D242B] leading-[32px] justify-center" id="modal-title"> {{ __('Share Your Feedback Here') }} </h3>
          
        </div>
        <x-loading :target="'sendFeedback'" />
        <form wire:submit="sendFeedback" class="w-full inline-flex flex-col mt-[23px]">
        <div class="inline-flex flex-col mx-auto w-full max-w-[470px]">
            <div class="form-group w-full">
                <div class="flex items-center mb-2">
                        <label for="feedback" class="label-box">{{ __('Add feedback') }}<span class="text-[#212529]">*</span></label>
                        @error('message') <span class="ml-2 text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                <textarea name="feedback" rows="4" wire:model="feedback" class="input-box textarea manage-input-box mt-[6px] text-box !w-full {{ $errors->has('message') ? 'outline-red-500' : '' }}" placeholder="Enter your feedback"></textarea>
                  <small class="inline-flex text-[12px] font-[500] text-[#353535] mt-[4px] float-right">(Max. 200 characters)</small>
                @error('feedback')
                    <p class="error-message-box">{{ $message }}</p>
                @enderror
            </div>
        </div>


        <div class="flex items-center justify-center gap-[24px] mt-[23px]">
          <button type="button"
            wire:click="sendFeedback"
            class="btn-box btn">
            {{ $btnConfirm }}
          </button>
        </div>

</form>
      </div>
    </div>
  </div>
</div>
