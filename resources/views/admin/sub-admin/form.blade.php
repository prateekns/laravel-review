<form
    action="{{ $subAdmin->id ? route('admin.sub-admins.update', $subAdmin) : route('admin.sub-admins.store') }}"
    method="POST"
    @submit.prevent="submitForm"
    x-ref="form">
    @csrf
    @method($subAdmin->id ? 'PUT' : 'POST')
    <div class="mb-4">
        <div>
            <div class="grid grid-cols-4 gap-4">
                <div class="mb-2 col-span-3">
                    <div class="flex items-center mb-2">
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name<span class="text-red-600">*</span></label>
                        <p x-show="errors.first_name" x-text="errors.first_name" class="ml-4 text-sm text-red-600"></p>
                    </div>
                    <input type="text" name="first_name" id="first_name" x-model="first_name" x-ref="first_name" value="{{ old('first_name', $subAdmin->first_name) }}" placeholder="John" class="block w-1/2 rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 @error('first_name') border-red-500 @enderror">
                </div>

                <div class="mb-2 col-span-3">
                    <div class="flex items-center mb-2">
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name<span class="text-red-600">*</span></label>
                        <p x-show="errors.last_name" x-text="errors.last_name" class="ml-4 text-sm text-red-600"></p>
                    </div>
                    <input type="text" name="last_name" id="last_name" x-model="last_name" x-ref="last_name" value="{{ old('last_name', $subAdmin->last_name) }}" placeholder="Doe" class="block w-1/2 rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 @error('last_name') border-red-500 @enderror">
                </div>

                <div class="mb-2 col-span-3">
                    <div class="flex items-center mb-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address<span class="text-red-600">*</span></label>
                        <p x-show="errors.email" x-text="errors.email" class="ml-4 text-sm text-red-600"></p>
                    </div>
                    <input type="email" name="email" id="email" x-model="email" x-ref="email" value="{{ old('email', $subAdmin->email) }}" placeholder="john.doe@example.com" class="block w-1/2 rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 @error('email') border-red-500 @enderror">
                </div>

                @if($subAdmin->id)
                    <div class="mb-2 col-span-2">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status<span class="text-red-600">*</span></label>
                        <input type="radio" name="status" x-model="status" id="status_active" value="1" class="mr-2" {{ old('status', $subAdmin->status ?? '1') == '1' ? 'checked' : '' }}>
                        <label for="status_active" class="mr-2 text-gray-500">Active</label>

                        <input type="radio" name="status" x-model="status" @change="checkStatusChange" id="status_inactive" value="0" class="mr-2" {{ old('status', $subAdmin->status ?? '1') == '0' ? 'checked' : '' }}>
                        <label for="status_inactive" class="mr-2 text-gray-500">Inactive</label>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="flex justify-start">
        @if($subAdmin->id)
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg mr-2">{{ __('Update') }}</button>
        @else
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg mr-2">Create Account</button>
        @endif
        <a href="{{ route('business.sub-admins.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Cancel</a>
    </div>

    @if($subAdmin->id)
        @include('components.status-modal' , [
            'title' => __('Confirm Deactivation'),
            'description' => __('Are you sure you want to deactivate this Sub-Admin? This will immediately revoke their access.'),
        ])
    @endif

</form>
