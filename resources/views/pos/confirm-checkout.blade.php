<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('POS') }}
        </h2>
    </x-slot>

    <x-main-card>
        <div class="flex items-center justify-between pb-5 flex-shrink-0">
            <x-secondary-button onclick="window.location.href='{{ route('pos') }}'">
                Back
            </x-secondary-button>

        </div>
        <div
            class="overflow-y-auto border border-slate-300 dark:border-slate-700 rounded-md p-3 space-y-3 bg-slate-100 dark:bg-slate-900 shadow-inner">
            @foreach ($selectedItems as $item)
                <div x-data="{
                    quantity: 1,
                    stock: {{ $item->stock }},
                    price: {{ $item->selling_price }},
                    warning: '',
                    setQuantity(val) {
                        const parsed = parseInt(val) || 0;
                        if (parsed <= 0) {
                            this.quantity = 1;
                            this.warning = 'Quantity cannot be 0.';
                        } else if (parsed > this.stock) {
                            this.quantity = this.stock;
                            this.warning = 'Quantity cannot exceed ' + this.stock + '.';
                        } else {
                            this.quantity = parsed;
                            this.warning = '';
                        }
                    }
                }"
                    class="relative bg-white dark:bg-slate-800/60 backdrop-blur-sm px-4 pt-4 pb-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200">

                    {{-- Delete Button --}}
                    <button type="button"
                        class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-rose-100 dark:bg-rose-500/20 hover:bg-rose-200 dark:hover:bg-rose-500/40 text-rose-500 dark:text-rose-400 transition-colors duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6" />
                            <path d="M19 6l-1 14H6L5 6" />
                            <path d="M10 11v6M14 11v6" />
                            <path d="M9 6V4h6v2" />
                        </svg>
                    </button>

                    {{-- Item Name --}}
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 pr-8 truncate">
                        {{ $item->name }}
                    </p>

                    {{-- Price Formula --}}
                    <p class="text-sm mt-1">
                        <span class="text-slate-400 dark:text-slate-500">₱{{ number_format($item->selling_price, 2) }}
                            &times; <span x-text="quantity"></span> = </span>
                        <span class="font-bold text-slate-700 dark:text-slate-200">₱<span
                                x-text="(price * quantity).toFixed(2)"></span></span>
                    </p>

                    {{-- Warning Message --}}
                    <p x-show="warning !== ''" x-text="warning" x-transition.opacity
                        class="text-xs text-rose-400 dark:text-rose-400 mt-1"></p>

                    {{-- Quantity Controls --}}
                    <div class="flex items-center gap-1">
                        <button type="button" x-on:click="setQuantity(quantity - 1)"
                            class="w-5 h-5 flex items-center justify-center rounded-md bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-600 font-bold text-sm">
                            &minus;
                        </button>
                        <input type="number"
                            class="bg-transparent border-none w-16 p-1 text-sm text-center [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                            x-model.number="quantity"
                            x-on:input="quantity = Math.min(stock, Math.max(1, parseInt($[event.target](http://event.target).value) || 1))"
                            min="1" max="stock" required />
                        <button type="button" x-on:click="setQuantity(quantity + 1)"
                            class="w-5 h-5 flex items-center justify-center rounded-md bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-600 font-bold text-sm">
                            &plus;
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

    </x-main-card>
</x-app-layout>
