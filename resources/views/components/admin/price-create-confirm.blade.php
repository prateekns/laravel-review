@props([
  'model' => '',
  'message' => '',
  'btnCancel' => __('admin.button.cancel'),
  'btnConfirm' => __('admin.button.confirm'),
  ])

<div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
      <!--
        Modal panel, show/hide based on modal state.
        Entering: "ease-out duration-300"
          From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          To: "opacity-100 translate-y-0 sm:scale-100"
        Leaving: "ease-in duration-200"
          From: "opacity-100 translate-y-0 sm:scale-100"
          To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      -->
      <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-[800px] sm:p-6">
        <div class="absolute top-0 right-0 hidden pt-4 pr-4 sm:block">
          <button type="button" @click="confirmCreatePrice = false" class="cursor-pointer rounded-md bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
            <span class="sr-only">Close</span>
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
          </button>
        </div>
        <div class="sm:flex sm:items-start">
        <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:size-10">

            <svg class="size-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"></path>
            </svg>
         
          </div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-base font-semibold text-gray-900" id="modal-title">
            {{ __('How Pricing Updates Will Work') }}</h3>
            <div class="mt-4">
              <p class="text-sm text-gray-500">

              <ul>
                <li style="list-style:disc">
                <span class="text-base font-semibold text-gray-900">{{__('New Customers')}}:</span> {{__('Anyone signing up after the price change will automatically pay the new price.')}}
                </li>
                <li class="text-base font-semibold text-gray-900" style="list-style:disc">{{__('Existing Customers:')}}</li>
                <ul style="padding-left:25px">
                <li style="list-style:circle">{{__('Current users will remain on their old price unless they make a change.')}} </li>
                <li style="list-style:circle">{{__('On renewal, they’ll move to the new price. ')}}</li>
                <li style="list-style:circle"> {{__('On upgrade, they’ll move to the new price. ')}}</li>
                <li style="list-style:circle"> {{__('On downgrade, they’ll move to the new price. ')}}</li>
                <ul>
                </ul>
              </p>

            </div>
            <p class="mt-2">{{__('Are you sure you want to update the subscription prices?')}}</p>
            <p>{{__('Click Confirm to continue the Price update.')}}</p>
          </div>
        </div>
        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
 
        <button
                type="button"
                wire:click="save"
                class="inline-flex w-full cursor-pointer justify-center rounded-md bg-green-600 hover:bg-green-500 px-3 py-2 text-sm font-semibold text-white shadow-xs sm:ml-3 sm:w-auto">
                {{ $btnConfirm }}
                <span wire:loading wire:target="save" class="ml-2 animate-spin rounded-full h-4 w-4 border-b-3 border-white-800"></span>
              </button>
          <button type="button" @click="confirmCreatePrice = false" class="mt-3 cursor-pointer inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto"> {{ $btnCancel }}</button>
        </div>
      </div>
    </div>
  </div>
</div>
