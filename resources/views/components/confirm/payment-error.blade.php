@props([
'title' => '',
'message' => '',
'onClick' => '',
'btnCancel' => __('admin.button.cancel'),
'btnConfirm' => __('admin.button.confirm'),
])

<div
  x-show="paymentError"
  x-cloak
  class="relative z-[999]" aria-labelledby="modal-payment-error" role="dialog" aria-modal="true">
  <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
  <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 payment-error-modal">
      <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">

        <div class="flex flex-col items-center justify-center gap-[20px]">
          <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10">
            <svg width="74" height="75" viewBox="0 0 74 75" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect y="0.5" width="74" height="74" rx="37" fill="#FFDBD9" />
              <path d="M36.9987 31.3335V37.5002M36.9987 43.6668H37.0141M52.4154 37.5002C52.4154 46.0146 45.5131 52.9168 36.9987 52.9168C28.4843 52.9168 21.582 46.0146 21.582 37.5002C21.582 28.9858 28.4843 22.0835 36.9987 22.0835C45.5131 22.0835 52.4154 28.9858 52.4154 37.5002Z" stroke="#B32318" stroke-width="2.64286" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </div>
          <div class="flex flex-col">
            <h3 class="flex items-center font-[700] text-[32px] text-[#1D242B] justify-center" id="modal-title"> {{ $title }} </h3>
            <div class="flex justify-center">
              <p class="flex items-center font-[400] text-[16px] text-[#4B5563] justify-center" id="payment-error">{{ $message }}</p>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-center gap-[24px] mt-[32px]">

          <button type="button"
            @click="paymentError=false"
            class="btn-box outlined">
            {{ $btnConfirm }}
          </button>

          <button type="button" @click="paymentError=false" class="btn-box btn" id="btn-cancel-payment-error"> {{ $btnCancel }}</button>
        </div>
      </div>
    </div>
  </div>
</div>
