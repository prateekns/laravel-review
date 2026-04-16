@props([
    'id' => 'image-preview-modal',
    'maxWidth' => '4xl'
])

<div
    x-data="{
        show: false,
        imageUrl: null,
        modalId: '{{ $id }}',
        init() {
            this.$watch('show', value => {
                if (value) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });

            window.addEventListener('open-modal', (event) => {
                if (event.detail.id === this.modalId) {
                    this.imageUrl = event.detail.url;
                    this.show = true;
                }
            });

            window.addEventListener('close-modal', (event) => {
                if (event.detail.id === this.modalId) {
                    this.show = false;
                    setTimeout(() => {
                        this.imageUrl = null;
                    }, 300);
                }
            });
        }
    }"
    x-on:keydown.escape.window="show = false"
    x-cloak
>
    <div
        x-show="show"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="show = false"
        id="wo-image-preview-modal"
    >
        <div class="bg-white p-4 rounded-lg shadow-lg max-w-[600px] max-[640px]:max-w-[100%] w-full mx-4">
            <div class="flex justify-end mb-2">
                <button
                    type="button"
                    @click="show = false"
                    class="text-gray-500 hover:text-gray-700"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="flex items-center justify-center">
                <img
                    :src="imageUrl"
                    alt="Preview"
                    class="max-h-[80vh] max-w-full object-contain"
                    @click.stop
                >
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('imagePreviewModal', () => ({
            show: false,
            imageUrl: null,
            modalId: '{{ $id }}',

            init() {
                this.$watch('show', value => {
                    if (value) {
                        document.body.style.overflow = 'hidden';
                    } else {
                        document.body.style.overflow = '';
                    }
                });

                window.addEventListener('open-modal', (event) => {
                    if (event.detail.id === this.modalId) {
                        this.open(event.detail.url);
                    }
                });

                window.addEventListener('close-modal', (event) => {
                    if (event.detail.id === this.modalId) {
                        this.close();
                    }
                });
            },

            open(url) {
                this.imageUrl = url;
                this.show = true;
            },

            close() {
                this.show = false;
                setTimeout(() => {
                    this.imageUrl = null;
                }, 300);
            }
        }));

        if (!window.openImagePreview) {
            window.openImagePreview = function(url, modalId = 'image-preview-modal') {
                window.dispatchEvent(new CustomEvent('open-modal', {
                    detail: {
                        id: modalId,
                        url: url
                    }
                }));
            };
        }

        if (!window.closeImagePreview) {
            window.closeImagePreview = function(modalId = 'image-preview-modal') {
                window.dispatchEvent(new CustomEvent('close-modal', {
                    detail: {
                        id: modalId
                    }
                }));
            };
        }
    });
</script>
@endpush
