<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('POS') }}
        </h2>
    </x-slot>

    <x-main-card>
        <div class="flex items-center justify-between pb-5 flex-shrink-0">
            <form method="GET">
                <x-search-bar />
            </form>
            <x-primary-button onclick="window.location.href='{{ route('pos.confirm.checkout') }}'">
                Checkout Items &gt;
            </x-primary-button>

        </div>
        <hr class="mt-0 mb-3 border-slate-200 dark:border-slate-700">
        {{-- Item Grid --}}
        <div class="flex-1 overflow-y-auto">
            @if ($items->isEmpty())
                <div class="flex flex-col items-center justify-center h-48 text-slate-400">
                    <svg class="w-10 h-10 mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h13M7 13L5.4 5M10 21a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm9 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                    </svg>
                    <p class="text-sm">No items found</p>
                </div>
            @else
                <div class="p-1 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    @foreach ($items as $item)
                        <x-pos.select-items-item :item="$item" />
                    @endforeach
                </div>
            @endif
        </div>
        <x-pagination :paginator="$items" :per-page-options="[10, 25, 50, 100]" />
    </x-main-card>
</x-app-layout>
