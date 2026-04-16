<form
    action="{{ $subAdmin->id ? route('business.sub-admins.update', $subAdmin) : route('business.sub-admins.store') }}"
    method="POST"
    @submit.prevent="submitForm"
    x-ref="form">
    @csrf
    @method($subAdmin->id ? 'PUT' : 'POST')
    <div>
        <div>
            <div class="form-content">
                <div class="form-content-box">
                    <div class="flex items-center mb-[6px]">
                        <x-form.label for="first_name" required>{{ __('First Name') }}</x-form.label>
                        <p x-show="errors.first_name" x-text="errors.first_name" class="text-xxs red-600 ml-2 text-red-600"></p>
                        @error('first_name')<p class="text-xxs red-600">{{ $message }}</p>@enderror
                    </div>

                    <x-form.text
                        name="first_name"
                        x-ref="first_name"
                        placeholder="{{ __('Enter First Name')}}"
                        value="{{ old('first_name', $subAdmin->first_name) }}"
                        x-bind:class="{'outline-red-500': errors.first_name}"
                    />
                </div>

                <div class="form-content-box">
                    <div class="flex items-center mb-2">
                        <x-form.label for="last_name" required>{{ __('Last Name') }}</x-form.label>
                        <p x-show="errors.last_name" x-text="errors.last_name" class="text-xxs red-600 ml-2 text-red-600"></p>
                        @error('last_name')<p class="text-xxs red-600">{{ $message }}</p>@enderror
                    </div>

                    <x-form.text
                        name="last_name"
                        x-ref="last_name"
                        placeholder="{{ __('Enter Last Name')}}"
                        value="{{ old('last_name', $subAdmin->last_name) }}"
                        x-bind:class="{'outline-red-500': errors.last_name}"
                    />
                </div>

                <div class="form-content-box">
                    <div class="flex items-center mb-2">
                        <x-form.label for="email" required>{{ __('Email Address') }}</x-form.label>
                        <p x-show="errors.email" x-text="errors.email" class="text-xxs red-600 ml-2 text-red-600"></p>
                        @error('email')<p class="text-xxs red-600">{{ $message }}</p>@enderror
                    </div>

                    <x-form.text
                        name="email"
                        x-ref="email"
                        placeholder="{{ __('Enter Email Address')}}"
                        value="{{ old('email', $subAdmin->email) }}"
                        x-bind:class="{'outline-red-500': errors.email}"
                    />
                </div>

                @if($subAdmin->id)
                    <div class="w-12/12 block">
                        <label for="status" class="label-box">{{ __('Status') }}<span class="text-red-600">*</span></label>
                        <div class="flex items-center gap-[17px] mt-3">
                            <div class="custom-radio-box">
                                <input type="radio" name="status" x-model="status" id="status_active" value="1" class="mr-2" {{ old('status', $subAdmin->status ?? '1') == '1' ? 'checked' : '' }}>
                                <label for="status_active" class="mr-2 text-gray-500">{{ __('Active') }}</label>
                            </div>

                            <div class="custom-radio-box">
                                <input type="radio" name="status" x-model="status" @change="checkStatusChange" id="status_inactive" value="0" class="mr-2" {{ old('status', $subAdmin->status ?? '1') == '0' ? 'checked' : '' }}>
                                <label for="status_inactive" class="mr-2 text-gray-500">{{ __('Inactive') }}</label>
                            </div>
                        <div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="form-content-btns">
        @if($subAdmin->id)
            <button type="submit" class="btn">{{ __('Update') }}</button>
        @else
            <button type="submit" class="btn">Create Account</button>
        @endif
        <a href="{{ route('business.sub-admins.index') }}" class="btn-box outlined">Cancel</a>
    </div>

    @if($subAdmin->id)
        @include('components.confirm.confirm-modal' , [
            'title' => __('Confirm Deactivation'),
            'description' => __('Are you sure you want to deactivate this Sub-Admin? This will immediately revoke their access.'),
        ])
    @endif

</form>
