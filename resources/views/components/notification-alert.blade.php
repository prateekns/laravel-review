@props(['type' => 'success', 'message' => '', 'timestamp' => null])
<div x-cloak x-data="{
    show: @js(!empty($message) || !empty($timestamp)),
    type: @js($type),
    message: @js($message),
    timestamp: @js($timestamp),
    hideTimer: null,
    animationKey: 0,
    restartAnimation() {
        if (this.$refs && this.$refs.progress) {
            this.$refs.progress.classList.remove('animate-border-shrink');
            // force reflow
            void this.$refs.progress.offsetWidth;
            this.$refs.progress.classList.add('animate-border-shrink');
        }
    },
    showFor(duration = 5000) {
        if (this.hideTimer) { clearTimeout(this.hideTimer); }
        this.show = true;
        // restart animated border by changing key
        this.animationKey++;
        this.restartAnimation();
        this.hideTimer = setTimeout(() => { this.show = false; }, duration);
    }
}"
    x-on:notify.window="
    type = Array.isArray($event.detail) ? $event.detail[0].type : $event.detail.type;
    message = Array.isArray($event.detail) ? $event.detail[0].message : $event.detail.message;
    showFor();"
    x-on:showAlert.window="showFor()" x-on:show-alert.window="showFor()" x-init="if (message || timestamp) { showFor() }"
    x-effect="if (timestamp) { showFor() }" aria-live="assertive" class="message-toaster-box">
    <div x-show="show" class="toaster-wrapper">
        <div class="toaster-content">

            <!-- Animated shrinking border -->
            <div x-ref="progress" :key="animationKey" class="absolute bottom-0 left-0 h-0.5 animate-border-shrink"
                :class="type === 'error' ? 'bg-red-600' : 'bg-green-600'"></div>

            <div class="icon">
                <template x-if="type === 'success'">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_1220_6701)">
                            <rect width="24" height="24" fill="white" fill-opacity="0.01" />
                            <mask id="mask0_1220_6701" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                width="24" height="24">
                                <rect width="24" height="24" fill="#D9D9D9" />
                            </mask>
                            <g mask="url(#mask0_1220_6701)">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M12 22C9.34784 22 6.8043 20.9464 4.92893 19.0711C3.05357 17.1957 2 14.6522 2 12C2 9.34784 3.05357 6.8043 4.92893 4.92893C6.8043 3.05357 9.34784 2 12 2C14.6522 2 17.1957 3.05357 19.0711 4.92893C20.9464 6.8043 22 9.34784 22 12C22 14.6522 20.9464 17.1957 19.0711 19.0711C17.1957 20.9464 14.6522 22 12 22ZM13.705 8.295L11.015 13.4325L9.08625 11.695C8.9642 11.5852 8.82172 11.5005 8.66694 11.4457C8.51216 11.391 8.3481 11.3672 8.18415 11.3759C8.0202 11.3845 7.85955 11.4254 7.71139 11.4961C7.56322 11.5669 7.43044 11.6661 7.32063 11.7881C7.21081 11.9102 7.1261 12.0527 7.07135 12.2074C7.0166 12.3622 6.99287 12.5263 7.00152 12.6902C7.01016 12.8542 7.05102 13.0148 7.12175 13.163C7.19248 13.3112 7.2917 13.4439 7.41375 13.5538L10.5388 16.3663C10.6803 16.4938 10.8492 16.5872 11.0325 16.6395C11.2157 16.6917 11.4085 16.7014 11.596 16.6678C11.7836 16.6341 11.9609 16.558 12.1146 16.4453C12.2682 16.3326 12.3941 16.1863 12.4825 16.0175L15.92 9.455C16.0738 9.16127 16.1047 8.81847 16.0057 8.502C15.9068 8.18553 15.6862 7.92133 15.3925 7.7675C15.0988 7.61367 14.756 7.58283 14.4395 7.68176C14.123 7.78068 13.8588 8.00127 13.705 8.295Z"
                                    fill="#16A34A" />
                            </g>
                        </g>
                        <defs>
                            <clipPath id="clip0_1220_6701">
                                <rect width="24" height="24" fill="white" />
                            </clipPath>
                        </defs>
                    </svg>
                </template>

                <template x-if="type === 'error'">
                    <svg class="size-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </template>

                <template x-if="type === 'warning'">
                    <svg class="size-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </template>

                <template x-if="type === 'info'">
                    <svg class="size-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </template>
            </div>
            <div class="message">
                <p class="text" x-text="message"></p>
            </div>
            <div class="close-icon">
                <button type="button" @click="if (hideTimer) { clearTimeout(hideTimer); } show = false"
                    class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
                    <span class="sr-only">{{ __('Close') }}</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_1220_6705" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                            width="16" height="16">
                            <rect width="16" height="16" fill="#D9D9D9" />
                        </mask>
                        <g mask="url(#mask0_1220_6705)">
                            <path
                                d="M4.04853 2.35147C3.5799 1.88284 2.8201 1.88284 2.35147 2.35147C1.88284 2.8201 1.88284 3.5799 2.35147 4.04853L6.30294 8L2.35147 11.9515C1.88284 12.4201 1.88284 13.1799 2.35147 13.6485C2.8201 14.1172 3.5799 14.1172 4.04853 13.6485L8 9.69706L11.9515 13.6485C12.4201 14.1172 13.1799 14.1172 13.6485 13.6485C14.1172 13.1799 14.1172 12.4201 13.6485 11.9515L9.69706 8L13.6485 4.04853C14.1172 3.5799 14.1172 2.8201 13.6485 2.35147C13.1799 1.88284 12.4201 1.88284 11.9515 2.35147L8 6.30294L4.04853 2.35147Z"
                                fill="#1D242B" />
                        </g>
                    </svg>

                </button>
            </div>
        </div>
    </div>
</div>
