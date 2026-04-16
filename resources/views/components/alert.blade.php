@props(['type' => 'success', 'message' => '', 'class'=>'', 'xShow'=>''])

<div class="{{$class}} mb-4 rounded-md bg-{{ $type == 'success' ? 'green' : 'red' }}-50 p-4 !m-[0]" @if($xShow) x-show="{{ $xShow }}" x-cloak @endif>
    <div class="flex">
        <div class="flex-shrink-0">
            @if($type === 'success')
                <x-icons name="success"/>
            @else
                <x-icons name="error"/>
            @endif
        </div>
        <div class="ml-3">
            @if($xShow)
                <p class="text-sm text-{{ $type == 'success' ? 'green' : 'red' }}-600 !m-[0]" x-text="{{ $message }}"></p>
            @else
                <p class="text-sm text-{{ $type == 'success' ? 'green' : 'red' }}-600 !m-[0]">{{ $message }}</p>
            @endif
        </div>
    </div>
</div>
