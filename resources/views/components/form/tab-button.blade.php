@props(['name', 'activeTab', 'reload'])

<button
  @click="activeTab = '{{ $name }}'"
  @if($activeTab != $name ) wire:click="switchTab('{{ $name }}')" @endif
    :class="{
        'text-gray-900': activeTab === '{{ $name }}',
        'text-gray-500 hover:text-gray-700': activeTab !== '{{ $name }}'
    }"
    class="group relative min-w-0 flex-1 overflow-hidden rounded-l-lg bg-white px-4 py-4 text-center text-sm font-medium hover:bg-gray-50 focus:z-10 cursor-pointer"
>
    <span>{{ $slot }}</span>
    <span
        aria-hidden="true"
        :class="{
            'bg-indigo-500': activeTab === '{{ $name }}',
            'bg-transparent': activeTab !== '{{ $name }}'
        }"
        class="absolute inset-x-0 bottom-0 h-0.5">
    </span>
</button>
