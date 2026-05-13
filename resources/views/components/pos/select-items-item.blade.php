@props(['item' => []])
<button @disabled($item->stock === 0) x-data="{
    id: {{ (int) $item->id }},
    isSelected: {{ isset(session('selectedItems')[$item->id]) ? 'true' : 'false' }}
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
    <p class="w-full text-sm font-bold text-slate-950 dark:text-white leading-tight mb-1 truncate text-ellipsis">
        {{ $item->name }}
    </p>

    {{-- Price --}}
    <p class="w-full text-xs font-bold text-indigo-600">
        ₱{{ number_format($item->selling_price, 2) }}
    </p>

</button>
