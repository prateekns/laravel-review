@props(['target' => ''])
<div
    @if($target) wire:loading wire:target="{{ $target}}" @endif
    class="loader fixed top-0 left-0 opacity-50 inset-0 z-50 flex items-center justify-center bg-gray-50 bg-opacity-90 rounded-lg w-full h-full">
    <div class="flex absolute top-[50%] left-[50%] transform -translate-x-1/2 -translate-y-1/2">
        <div class="animate-spin rounded-full h-8 w-8 border-b-3 border-indigo-800"></div>
    </div>
</div>
