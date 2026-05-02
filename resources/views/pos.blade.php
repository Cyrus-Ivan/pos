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
            <form method="POST" action="{{ route('pos.checkout') }}">
                @csrf
                <x-primary-button>Checkout Items > </x-primary-button>
            </form>
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
                        <button @disabled($item->stock === 0) x-data="{
                            id: {{ (int) $item->id }},
                            isSelected: {{ isset(session('cart')[$item->id]) ? 'true' : 'false' }}
                        }"
                            @click=" isSelected = !isSelected; axios.post( '{{ route('pos.toggle.item') }}', { id: Number(id) }); "
                            x-bind:class="isSelected ? 'ring ring-indigo-500' :
                                'border-transparent'"
                            class="item-btn group relative flex flex-col items-center bg-white dark:bg-slate-800 rounded shadow hover:bg-slate-50 dark:bg-hover-slate-900 active:scale-95
                               transition-all duration-150 p-3 text-left disabled:opacity-40 disabled:cursor-default">
                            {{-- Out of stock badge --}}
                            <p class="w-full text-xs font-semibold tracking-wide text-slate-800 dark:text-slate-100">
                                {{ $item->sku }}
                            </p>
                            @php
                                [$bg, $text] = match (true) {
                                    $item->stock == 0 => ['bg-red-100', 'text-red-500'],
                                    $item->stock < 5 => ['bg-orange-100', 'text-orange-500'],
                                    $item->stock < 10 => ['bg-yellow-100', 'text-yellow-500'],
                                    default => ['bg-green-100', 'text-green-600'],
                                };
                            @endphp

                            <span
                                class="absolute top-2 right-2 text-[10px] font-bold tracking-wide px-1.5 py-0.5 rounded-full {{ $bg }} {{ $text }}">
                                {{ $item->stock }} pcs left
                            </span>

                            {{-- Name --}}
                            <p
                                class="w-full text-sm font-bold text-slate-950 dark:text-white leading-tight mb-1 truncate text-ellipsis">
                                {{ $item->name }}
                            </p>

                            {{-- Price --}}
                            <p class="w-full text-xs font-bold text-indigo-600">
                                ₱{{ number_format($item->selling_price, 2) }}
                            </p>

                        </button>
                    @endforeach
                </div>
            @endif
        </div>
        <x-pagination :paginator="$items" :per-page-options="[10, 25, 50, 100]" />
    </x-main-card>
</x-app-layout>
