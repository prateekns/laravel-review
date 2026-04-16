@props(['paginator'])

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between w-full mt-[10px] w-full">
        <div class="flex justify-between flex-1 sm:hidden">
            {{-- Previous Page Link (Mobile) --}}
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    {!! __('pagination.previous') !!}
                </button>
            @endif

            {{-- Next Page Link (Mobile) --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    {!! __('pagination.next') !!}
                </button>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            {{-- Pagination Elements (Desktop) --}}
            <div class="flex w-full justify-end">
                <span class="relative z-0 inline-flex gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center justify-center w-8 h-8 text-sm text-gray-500 cursor-default" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </span>
                        </span>
                    @else
                        <button wire:click="previousPage" rel="prev" class="relative inline-flex items-center justify-center w-8 h-8 text-sm text-gray-500 transition duration-150 ease-in-out hover:text-gray-400 focus:z-10 focus:outline-none cursor-pointer" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                    @endif

                    {{-- Generate page numbers --}}
                    @php
                        $start = max($paginator->currentPage() - 2, 1);
                        $end = min($start + 4, $paginator->lastPage());
                        $start = max(min($start, $paginator->lastPage() - 4), 1);
                    @endphp

                    {{-- First Page Link --}}
                    @if($start > 1)
                        <button wire:click="gotoPage(1)" class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md focus:z-10 focus:outline-none">
                            1
                        </button>
                        @if($start > 2)
                            <span class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-700">...</span>
                        @endif
                    @endif

                    {{-- Page Links --}}
                    @foreach(range($start, $end) as $page)
                        @if($page == $paginator->currentPage())
                            <span aria-current="page">
                                <span class="relative inline-flex items-center justify-center w-[28px] h-[28px] text-[14px] font-[400]  text-white bg-blue leading-[20px] rounded-[8px]">{{ $page }}</span>
                            </span>
                        @else
                            <button wire:click="gotoPage({{ $page }})" class="relative inline-flex items-center justify-center w-[28px] h-[28px] text-[14px] font-[400] leading-[20px] text-[#1C1C1C] hover:bg-[#5f87ff] cursor-pointer  rounded-[8px] focus:z-10 focus:outline-none">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach

                    {{-- Last Page Link --}}
                    @if($end < $paginator->lastPage())
                        @if($end < $paginator->lastPage() - 1)
                            <span class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-700">...</span>
                        @endif
                        <button wire:click="gotoPage({{ $paginator->lastPage() }})" class="relative inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md focus:z-10 focus:outline-none">
                            {{ $paginator->lastPage() }}
                        </button>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button wire:click="nextPage" rel="next" class="relative inline-flex items-center justify-center w-8 h-8 text-sm text-gray-500 transition duration-150 ease-in-out hover:text-gray-400 focus:z-10 focus:outline-none cursor-pointer" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center justify-center w-8 h-8 text-sm text-gray-500 cursor-default" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
