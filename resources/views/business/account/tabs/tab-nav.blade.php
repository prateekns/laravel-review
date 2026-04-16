<nav class="tabs" aria-label="Tabs">
    <a href="{{ route('account.profile') }}" class="w-full group relative min-w-0 flex-1 overflow-hidden  px-4 py-[10px] text-center focus:z-10 ">
        <button id="profile-tab"
            class="cursor-pointer {{ $activeTab === 'profile' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
            <span>{{ __('Profile') }}</span>
            <span aria-hidden="true"
                class="absolute inset-x-0 bottom-0 h-0.5 {{ $activeTab === 'profile' ? 'bg-blue' : 'bg-transparent' }}"></span>
        </button>
    </a>

    <a href="{{ route('account.index') }}" class="w-full group relative min-w-0 flex-1 overflow-hidden  px-4 py-[10px] text-center focus:z-10">
    <button id="pricing-tab"
        class="cursor-pointer {{ $activeTab === 'index' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
        <span>{{ __('Pricing') }}</span>
        <span aria-hidden="true"
            class="absolute inset-x-0 bottom-0 h-0.5 {{ $activeTab === 'index' ? 'bg-blue' : 'bg-transparent' }}"></span>
    </button>
    </a>

    <a href="{{ route('account.my-plan') }}" class="w-full group relative min-w-0 flex-1 overflow-hidden  px-4 py-[10px] text-center focus:z-10">
        <button id="my-plan-tab"
            class="cursor-pointer {{ $activeTab === 'my-plan' ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
            <span>{{ __('My Plan') }}</span>
            <span aria-hidden="true"
                class="absolute inset-x-0 bottom-0 h-0.5 {{ $activeTab === 'my-plan' ? 'bg-blue' : 'bg-transparent' }}"></span>
        </button>
    </a>
</nav>
