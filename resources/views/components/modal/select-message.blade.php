@props([
'title' => __('Select message'),
'messages' => '',
'onClick' => '',
'btnCancel' => __('No, Cancel'),
'btnConfirm' => __('Yes, Proceed'),
])

<div
    x-show="showMessageModal"
  x-cloak
  class="relative z-[999]" aria-labelledby="modal-technician-message" role="dialog" aria-modal="true">
  <div class="fixed inset-0 bg-gray-600/75" aria-hidden="true"></div>
  <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0 payment-error-modal">
      <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">

        <div class="flex flex-col items-center justify-center gap-[20px]">
          <div class="mx-auto inline-flex">
            <svg width="74" height="74" viewBox="0 0 74 74" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="74" height="74" rx="37" fill="#EFF6FF"/>
<path fill-rule="evenodd" clip-rule="evenodd" d="M28.9866 22.1523H46.0943C49.9914 22.2861 53.046 25.5471 52.925 29.4447V39.4754C52.983 41.3578 52.2883 43.1856 50.9947 44.5542C49.7011 45.9229 47.9154 46.7195 46.0327 46.7677H31.3866C30.373 46.7887 29.4118 47.222 28.725 47.9677L25.0789 51.86C24.77 52.1969 24.3359 52.3917 23.8789 52.3985C23.4069 52.3865 22.9592 52.1863 22.6356 51.8424C22.312 51.4986 22.1392 51.0396 22.1558 50.5677V29.4447C22.0348 25.5471 25.0895 22.2861 28.9866 22.1523ZM46.0943 44.46C48.7173 44.3273 50.7393 42.0989 50.6173 39.4754V29.4447C50.7393 26.8211 48.7173 24.5928 46.0943 24.46H28.9866C26.3636 24.5928 24.3415 26.8211 24.4635 29.4447V49.137L26.9712 46.3985C28.118 45.1786 29.7124 44.4786 31.3866 44.46H46.0943Z" fill="#0D44EA"/>
<path d="M31.7712 32.537H39.4635C40.1008 32.537 40.6174 32.0204 40.6174 31.3831C40.6174 30.7459 40.1008 30.2293 39.4635 30.2293H31.7712C31.1339 30.2293 30.6174 30.7459 30.6174 31.3831C30.6174 32.0204 31.1339 32.537 31.7712 32.537Z" fill="#0D44EA"/>
<path d="M43.3097 36.3831H31.7712C31.1339 36.3831 30.6174 36.8997 30.6174 37.537C30.6174 38.1742 31.1339 38.6908 31.7712 38.6908H43.3097C43.9469 38.6908 44.4635 38.1742 44.4635 37.537C44.4635 36.8997 43.9469 36.3831 43.3097 36.3831Z" fill="#0D44EA"/>
</svg>

          </div>
          <div class="flex flex-col">
            <h3 class="flex items-center font-[700] text-[32px] text-[#1D242B] justify-center" id="modal-title"> {{ $title }} </h3>
            <div class="flex justify-center">
              <p class="flex items-center font-[400] text-[16px] text-[#4B5563] justify-center" id="payment-error">{{ __('Select the message you want to send to technician?') }}</p>
            </div>
          </div>
        </div>
        <x-loading :target="'sendMessage'" />

        <div class="mt-[32px]">
        <div class=" w-full">
            <select
                wire:model="selectedMessage"
                id="messageSelect"
                x-model="selectedMessage"
                class="form-select w-full"
                required
            >
                <option value="">{{ __('Select message template') }}</option>
                @foreach($messages as $message)
                    <option value="{{ $message->message }}">{{ $message->subject ?: $message->message }}</option>
                @endforeach
            </select>
            @error('selectedMessage')
                <p class="error-message-box">{{ $message }}</p>
            @enderror
        </div>
    </div>


        <div class="flex items-center justify-center gap-[24px] mt-[32px]">
          <button type="button" @click="cancelMessageModal" class="btn-box outlined " id="btn-cancel-payment-error"> {{ $btnCancel }}</button>
          <button type="button"
            wire:click="sendMessage"
            class="btn-box btn">
            {{ $btnConfirm }}
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- <x-modal>
    <x-slot name="title">
        <div class="gap-[16px] flex flex-col">
            <h1 class="main-heading">{{ __('Select Message') }}</h1>
            <p class="sub-heading">{{ __('Select the message you want to send to technician?') }}</p>
        </div>
    </x-slot>

    <div class="mt-4">
        <div class="form-group">
            <label for="message" class="form-label">{{ __('Select Message') }}</label>
            <select
                wire:model="selectedMessage"
                id="message"
                class="form-select"
                required
            >
                <option value="">{{ __('Select a message') }}</option>
                @foreach($messages as $message)
                    <option value="{{ $message->id }}">{{ $message->subject ?: Str::limit($message->message, 50) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-2">
            <button
                type="button"
                class="btn-box outlined"
                wire:click="closeMessageModal"
            >
                {{ __('Cancel') }}
            </button>
            <button
                type="button"
                class="btn-box"
                wire:click="sendMessage"
                wire:loading.attr="disabled"
            >
                {{ __('Send Message') }}
            </button>
        </div>
    </x-slot>
</x-modal> --}}
