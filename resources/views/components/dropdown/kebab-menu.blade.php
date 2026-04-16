@props([
    'align' => 'right',
    'width' => 'max-content',
    'contentClasses' => 'py-2 bg-white',
    'show' => true,
])

<div class="relative calender-menu float-right" x-data="{
    open: false,
    menuId: $id('menu'),
    init() {
        window.addEventListener('close-all-menus', (e) => {
            if (e.detail.except !== this.menuId) {
                this.open = false;
            }
        });
    }
}" @click.away="open = false"
    @keydown.escape.window="open = false" x-show="{{ $show }}" x-cloak>

    <button class="p-1 cursor-pointer" x-ref="trigger"
        @click.stop="
            window.dispatchEvent(new CustomEvent('close-all-menus', { detail: { except: menuId } }));
            open = !open
        "
        type="button">
        <svg width="2" height="8" viewBox="0 0 2 8" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M0.99967 7.28956C0.82017 7.28956 0.66742 7.22565 0.54142 7.09781C0.415337 6.96998 0.352295 6.81635 0.352295 6.63694C0.352295 6.45744 0.416212 6.30465 0.544045 6.17856C0.671878 6.05256 0.825503 5.98956 1.00492 5.98956C1.18442 5.98956 1.33717 6.05348 1.46317 6.18131C1.58925 6.30906 1.65229 6.46269 1.65229 6.64219C1.65229 6.82169 1.58838 6.97444 1.46054 7.10044C1.33271 7.22652 1.17909 7.28956 0.99967 7.28956ZM0.99967 4.70494C0.82017 4.70494 0.66742 4.64102 0.54142 4.51319C0.415337 4.38535 0.352295 4.23173 0.352295 4.05231C0.352295 3.87281 0.416212 3.72006 0.544045 3.59406C0.671878 3.46798 0.825503 3.40494 1.00492 3.40494C1.18442 3.40494 1.33717 3.46885 1.46317 3.59669C1.58925 3.72452 1.65229 3.87815 1.65229 4.05756C1.65229 4.23706 1.58838 4.38981 1.46054 4.51581C1.33271 4.6419 1.17909 4.70494 0.99967 4.70494ZM0.99967 2.12031C0.82017 2.12031 0.66742 2.0564 0.54142 1.92856C0.415337 1.80081 0.352295 1.64719 0.352295 1.46769C0.352295 1.28819 0.416212 1.13544 0.544045 1.00944C0.671878 0.883354 0.825503 0.820312 1.00492 0.820312C1.18442 0.820312 1.33717 0.884229 1.46317 1.01206C1.58925 1.1399 1.65229 1.29352 1.65229 1.47294C1.65229 1.65244 1.58838 1.80523 1.46054 1.93131C1.33271 2.05731 1.17909 2.12031 0.99967 2.12031Z"
                fill="#1C1B1F" />
        </svg>
    </button>

    <template x-teleport="body">
        <div x-show="open" x-anchor.bottom-end.offset.4="$refs.trigger"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="z-50 {{ $contentClasses }} calendar-menuBox shadow-lg rounded-md bg-white"
            style="width: {{ $width }}; display: none;">

            @if (isset($title))
                <div class="px-0">
                    <p class="font-[600] text-[16px] leading-[21px] text-[#212529] mb-[4px] px-2">
                        {{ $title }}
                    </p>
                    <div class="h-[1px] w-full bg-[#1C1C1C33] mb-[6px]"></div>
                </div>
            @endif

            <div class="px-1">
                {{ $slot }}
            </div>
        </div>
    </template>
</div>
