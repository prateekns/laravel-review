
<form action="{{ $technician->id ? route('business.technicians.update', $technician ) : route('business.technicians.store') }}" method="POST" x-ref="form" enctype="multipart/form-data" @submit.prevent="submitForm()">
    @csrf
    @method($technician->id ? 'PUT' : 'POST')
    <div>
        <div>
            <div class="form-content">
                    <div class="form-content-box">
                        <div class="flex items-center mb-2">
                            <x-form.label for="first_name" required>{{ __('First Name') }}</x-form.label>
                        </div>
                            <x-form.text
                                name="first_name"
                                x-model="first_name"
                                x-ref="first_name"
                                value="{{ old('first_name', $technician->first_name) }}"
                                placeholder="{{ __('Enter First Name')}}"
                                x-bind:class="{'error-message-border': errors.first_name}"
                            />
                            <div x-show="errors.first_name" x-text="errors.first_name" class="error-message-box first_name"></div>
                            @error('first_name')<div class="error-message-box first_name">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-content-box">
                        <div class="flex items-center mb-2">
                            <x-form.label for="last_name" required>{{ __('Last Name') }}</x-form.label>
                            
                        </div>
                            <x-form.text
                                name="last_name"
                                x-model="last_name"
                                x-ref="last_name"
                                value="{{ old('last_name', $technician->last_name) }}"
                                placeholder="{{ __('Enter Last Name')}}"
                                x-bind:class="{'error-message-border': errors.last_name}"
                            />
                            <div x-show="errors.last_name" x-text="errors.last_name" class="error-message-box last_name"></div>
                            @error('last_name')<div class="error-message-box last_name">{{ $message }}</div>@enderror
                    </div>

                <div class="form-content-box">
                    <div class="flex items-center mb-2">
                        <x-form.label for="email" required>{{ __('Email Address') }}</x-form.label>
                        
                    </div>
                        <x-form.text
                                name="email"
                                x-model="email"
                                x-ref="email"
                                value="{{ old('email', $technician->email) }}"
                                placeholder="{{ __('Enter Email Address')}}"
                                x-bind:class="{'error-message-border': errors.email}"
                                :readonly="$technician->id ? true : false"
                            />
                            <p x-show="errors.email" x-text="errors.email" class="error-message-box email"></p>
                            @error('email')
                                <p class="error-message-box duplicate-email">{{ $message }}</p>
                            @enderror
                </div>

                <div class="form-content-box">
                    <div class="flex items-center mb-2">
                        <x-form.label for="phone" required>{{ __('Phone Number') }}</x-form.label>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="code-inputs">
                        <x-form.text
                            name="isd_code"
                            value="{{ old('isd_code', $isd_code ?? $technician->isd_code) }}"
                            readonly
                        />
                        </div>
                        <x-form.input-number
                            name="phone"
                            x-ref="phone"
                            x-model="phone"
                            value="{{ old('phone', $technician->phone) }}"
                            placeholder="{{ __('Enter Phone Number') }}"
                            x-bind:class="{'error-message-border': errors.phone}"
                        />
                    </div>
                        <div x-show="errors.phone" x-text="errors.phone" class="error-message-box phone"></div>
                        @error('phone')<div class="error-message-box phone">{{ $message }}</div>@enderror
                </div>

                <div class="form-content-box">
                    <div class="flex items-center mb-2">
                    <x-form.label for="skill_type" required>{{ __('Skill Type') }}</x-form.label>
                    
                    </div>
                   
                    @include('components.multi-select', [
                        'skillTypes' => $skills,
                        'name' => 'skill_type',
                        'old_value' => old('skill_type', $selectedSkills),
                        'placeholder' => 'Skill Type'
                    ])
                    <div x-show="errors.skill_type" x-text="errors.skill_type" class="error-message-box"></div>
                    @error('skill_type')<div class="error-message-box">{{ $message }}</div>@enderror
                </div>

                <input name="skill_type" type="hidden" x-ref="skill_type" value="{{ old('skill_type', $selectedSkills) }}">

                <!-- Working Days -->
                <div class="form-content-box">
                    <div class="flex flex-col gap-2.5">
                        <x-form.label for="working_days">{{ __('Setup working days*') }}</x-form.label>
        
                        <div class="flex flex-row flex-wrap gap-6">
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                <div class="flex items-center gap-2">
                                    <div class="relative flex items-center justify-center w-6 h-6">
                                        <input
                                            type="checkbox"
                                            id="{{$day}}"
                                            name="working_days[]"
                                            value="{{$day}}"
                                            {{ $technician->id ? ($technician->working_days[$day] ?? false) ? 'checked' : '' : 'checked' }}
                                            @change="handleWorkingDaysChange()"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                        />
                                    </div>
                                    <x-form.label for="{{$day}}">
                                        {{ucfirst($day)}}
                                    </x-form.label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div x-show="errors.working_days" x-text="errors.working_days" class="error-message-box working-days"></div>
                    @error('working_days')<div class="error-message-box working-days">{{ $message }}</div>@enderror
                </div>
                <!-- Working Days -->

                <!-- Status -->
                @if($technician->id)
                    <div class="w-12/12 block">
                        <x-form.label for="status" required>{{__('Status')}}</x-form.label>
                        <div class="flex items-center gap-[17px] mt-3">
                            <div class="custom-radio-box">
                                <input type="radio"
                                    name="status"
                                    x-model.number="status"
                                    value="1"
                                    class="mr-2"
                                    id="status_active"
                                    @change="handleStatusChange()">
                                <label for="status_active" class="mr-2 text-gray-500">{{ __('Active')}}</label>
                            </div>
                            <div class="custom-radio-box">
                                <input type="radio"
                                    name="status"
                                    x-model.number="status"
                                    value="0"
                                    class="mr-2"
                                    id="status_inactive"
                                    @change="handleStatusChange()">
                                <label for="status_inactive" class="mr-2 text-gray-500">{{ __('Inactive')}}</label>
                            </div>
                        </div>
                        <input type="hidden" name="status" x-ref="status" value="{{ old('status', $technician->status ? 1 : 0) }}">
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Working Days -->

    <div class="flex justify-start mt-[40px] gap-[24px] max-[767px]:flex-col">
        @if($technician->id)
            <button type="submit" class="btn-box btn" x-bind:disabled="isSubmitting">
                {{__('Update')}}
            </button>
        @else
            <input type="hidden" name="action" x-ref="saveInput">
            <button type="submit" name="action" value="save" class="btn-box btn" x-bind:disabled="isSubmitting">
                {{__('Add Technician')}}
            </button>
        @endif
        <a href="{{ route('business.technicians.index') }}" class="btn-box outlined">{{__('Cancel')}}</a>
    </div>

    <!-- Status Change Confirmation Modal -->
    @include('components.confirm.confirm-modal', [
        'title' => __('Confirm Status Change'),
        'description' => __('business.message.technician_status_change_confirm'),
        'btnCancel' => __('Cancel'),
        'btnConfirm' => __('Confirm'),
        'modalType' => 'status'
    ])

    @if($technician->id)
    <!-- Update Confirmation Modal (only for update) -->
    @include('components.confirm.update-confirm-modal', [
        'title' => __('Confirm Technician Update'),
        'description' => __('business.message.technician_update_confirm'),
        'btnCancel' => __('Cancel'),
        'btnConfirm' => __('Confirm')
    ])
    @endif
</form>
