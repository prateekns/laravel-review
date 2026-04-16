@if($customerAttachments && ($customerAttachments['customerImage1'] || $customerAttachments['customerImage2']))
    <p class="text-[16px] font-[600] text-[#1D242B] mt-[12px] mb-[12px]">
        {{ __('Customer Attachments')}}
    </p>
    <div class="flex gap-2">
        @if($customerAttachments['customerImage1Thumb'])
            <img
                x-data
                @click="$dispatch('open-modal', {
                        id: 'image-preview-modal',
                        url: '{{ $customerAttachments['customerImage1'] }}'
                    })" width="48" src="{{ $customerAttachments['customerImage1'] }}" alt="Work order completion attached for customer">
        @endif
        
        @if($customerAttachments['customerImage2Thumb'])
            <img
                x-data
                @click="$dispatch('open-modal', {
                        id: 'image-preview-modal',
                        url: '{{ $customerAttachments['customerImage2'] }}'
                    })" width="48" src="{{ $customerAttachments['customerImage2Thumb'] }}" alt="Work order completion attached for customer">
        @endif
    </div>
@endif
