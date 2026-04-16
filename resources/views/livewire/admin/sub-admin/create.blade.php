<div class="container mx-auto">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <div>
                    <a href="{{ route('admin.sub-admin') }}"  class="back-btn">
                        <p >
                        <x-icons name="back"/>
                            <span>Back</span>
                        </p>
                    </a>
                    <h1 class="text-lg font-medium main-heading mt-4">
                        {{ $subAdmin->id ? __('admin.sub-admin.edit_admin') : __('admin.sub-admin.add_admin') }}
                    </h1>
                    <p class="sub-heading">{{ __('Fill in the details to create an sub-admin account.')}}</p>
                </div>
            </div>
        </div>

        <div class="white-box">
            @if (session('error'))
                <x-alert type="error" :message="session('error')"/>
            @endif

            <div>
                @include('livewire.admin.sub-admin.form', ['subAdmin' => $subAdmin])
            </div>
        </div>
    </div>
</div>
