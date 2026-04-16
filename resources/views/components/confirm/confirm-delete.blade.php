@props([
'message' => '',
'btnCancel' => __('admin.button.cancel'),
'btnConfirm' => __('admin.button.confirm'),
])

<div
  x-show="confirmDelete"
  x-cloak
  class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <!--
    Background backdrop, show/hide based on modal state.
    Entering: "ease-out duration-300"
      From: "opacity-0"
      To: "opacity-100"
    Leaving: "ease-in duration-200"
      From: "opacity-100"
      To: "opacity-0"
  -->
  <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>
  <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
      <!--
        Modal panel, show/hide based on modal state.
        Entering: "ease-out duration-300"
          From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          To: "opacity-100 translate-y-0 sm:scale-100"
        Leaving: "ease-in duration-200"
          From: "opacity-100 translate-y-0 sm:scale-100"
          To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      -->
      <div class="relative transform overflow-hidden rounded-[12px] bg-white p-[32px] text-center shadow-sm transition-all  sm:w-full sm:max-w-lg ">

        <div class="flex items-center justify-center flex-col">
          <div class="mx-auto flex  shrink-0 items-center justify-center rounded-full">
            <svg width="74" height="74" viewBox="0 0 74 74" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect width="74" height="74" rx="37" fill="#EFF6FF" />
              <path d="M36.9999 30.8342V37.0008M36.9999 43.1675H37.0154M34.3637 22.91L21.3058 44.7092C21.0365 45.1754 20.8941 45.704 20.8926 46.2424C20.8911 46.7808 21.0306 47.3102 21.2972 47.7779C21.5638 48.2456 21.9482 48.6354 22.4122 48.9084C22.8763 49.1815 23.4037 49.3283 23.942 49.3342H50.0579C50.5962 49.3283 51.1236 49.1815 51.5876 48.9084C52.0517 48.6354 52.4361 48.2456 52.7027 47.7779C52.9693 47.3102 53.1088 46.7808 53.1073 46.2424C53.1058 45.704 52.9633 45.1754 52.6941 44.7092L39.6362 22.91C39.3614 22.4569 38.9744 22.0823 38.5126 21.8223C38.0508 21.5624 37.5299 21.4258 36.9999 21.4258C36.47 21.4258 35.949 21.5624 35.4873 21.8223C35.0255 22.0823 34.6385 22.4569 34.3637 22.91Z" stroke="#0D44EA" stroke-width="3.08333" stroke-linecap="round" stroke-linejoin="round" />
            </svg>


          </div>
          <div class="flex flex-col gap-[8px] mt-[20px]">
            <h3 class="text-[32px] font-[700] leading-[42px] text-[#4C4C4C]" id="modal-title"> {{ __('admin.alert.confirm_deletion') }} </h3>
            <div class="flex">
              <p class="text-[16px] font-[400] leading-[21px] text-[#5A5A5A]"> {{ $message }}</p>
            </div>
          </div>
        </div>
        <div class="mt-[32px] flex max-[767px]:flex-col gap-[24px] items-center justify-center">
          <button type="button" x-on:click="$dispatch('cancelled')" class="btn-box outlined max-[767px]:w-full"> {{ $btnCancel }}</button>
          <button
            type="button"
            x-on:click="$dispatch('confirmed')"
            class="btn-box btn max-[767px]:w-full">
            {{ $btnConfirm }}
            <span wire:loading wire:target="delete"></span>
          </button>


        </div>
      </div>
    </div>
  </div>
</div>
