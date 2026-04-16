<div x-data="{
    showConfirm: false,
    status: @entangle('status'),
}" @close-confirm.window="showConfirm = false;" @close-cancel.window="showConfirm = false;status=true">
    <form wire:submit="{{ $subAdmin->id ? 'updateAdmin' : 'addAdmin ' }}"
        method="POST">
        @csrf
        @method($subAdmin->id ? 'PUT' : 'POST')
        <div class="mb-4">
            <div >
                <div class="grid grid-cols-5 gap-4">
                    <div class="mb-2 col-span-4">
                        <div class="flex items-center mb-2">
                            <label for="first_name" class="block text-sm font-medium text-gray-700">{{ __('admin.sub-admin.name') }}<span class="text-red-600">*</span></label>
                            @error('name') <span class="ml-2 text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <input type="text" name="first_name" wire:model="name" placeholder="John" class="block w-1/2 rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-1 focus:-outline-offset-1 focus:outline-indigo-600 sm:text-sm/6 {{ $errors->has('name') ? 'outline-red-500' : '' }}">
                    </div>

                    <div class="mb-2 col-span-4">
                        <div class="flex items-center mb-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">{{ __('admin.sub-admin.email') }}<span class="text-red-600">*</span></label>
                            @error('email') <span class="ml-2 text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <input type="text" name="email" wire:model="email" placeholder="john.doe@example.com" class="block w-1/2 rounded-md bg-{{ $subAdmin->id ? 'gray-100' : 'white  focus:outline-1 focus:-outline-offset-1 focus:outline-indigo-600' }} px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 sm:text-sm/6 {{$errors->has('email') ? 'outline-red-500' : '' }}" {{ $subAdmin->id ? 'readonly' : ''}}>
                    </div>

                    @if(!$subAdmin->id)
                    <div class="mb-2 col-span-4">
                        <div class="flex items-center mb-2">
                            <label for="password" class="block text-sm font-medium text-gray-700">{{ __('admin.sub-admin.password') }}<span class="text-red-600">*</span></label>
                            @error('password') <span class="ml-2 text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <input type="password" name="password" wire:model="password" class="block w-1/2 rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-1 focus:-outline-offset-1 focus:outline-indigo-600 sm:text-sm/6 {{$errors->has('password') ? 'outline-red-500' : '' }}">
                    </div>

                    <div class="mb-2 col-span-4">
                        <div class="flex items-center mb-2">
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">{{ __('admin.sub-admin.confirm_password') }}<span class="text-red-600">*</span></label>
                            @error('confirm_password') <span class="ml-2 text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <input type="password" name="confirm_password" wire:model="confirm_password" class="block w-1/2 rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-1 focus:-outline-offset-1 focus:outline-indigo-600 sm:text-sm/6 {{$errors->has('confirm_password') ? 'outline-red-500' : '' }}">
                    </div>

                    @endif

                    @if($subAdmin->id)
                        <div class="mb-2 col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.status')}}<span class="text-red-600">*</span></label>
                            <input type="radio" name="status" wire:model="status" id="status_active" value="1" class="mr-2">
                            <label for="status_active" class="mr-2 text-gray-500">{{ __('admin.active')}}</label>

                            <input @change="showConfirm=true"type="radio" name="status" id="status_inactive" wire:model="status" value="0" class="mr-2">
                            <label for="status_inactive" class="mr-2 text-gray-500">{{ __('admin.inactive')}}</label>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex justify-start">
            <button type="submit" class="cursor-pointer bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg mr-2">
                {{ __('admin.button.submit') }}
                <span wire:loading wire:target="addAdmin,updateAdmin" class="ml-2 animate-spin rounded-full h-4 w-4 border-b-3 border-white-800"></span>
            </button>
        
            <a href="{{ route('admin.sub-admin') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">{{ __('admin.button.cancel')}}</a>
        </div>

        @if($subAdmin->id)
            @include('components.status-modal' , [
                'title' => __('admin.alert.title_deactivate'),
                'description' => __('admin.alert.deactivate_admin'),
            ])
        @endif

    </form>
</div>
