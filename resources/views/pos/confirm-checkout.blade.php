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
                    setQuantity(val) {
                        const parsed = parseInt(val) || 0;
                        if (parsed <= 0) {
                            this.quantity = 1;
                        } else if (parsed > this.stock) {
                            this.quantity = this.stock;
                        } else {
                            this.quantity = parsed;
                        }
                    },
                    isAtMax() { return this.quantity >= this.stock; },
                    isAtMin() { return this.quantity <= 1; }
                }" x-ref="container"
                    class="relative bg-white dark:bg-slate-800/60 backdrop-blur-sm px-4 pt-4 pb-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200">

                    {{-- Delete Button --}}
                    <x-delete-button type="button" class="absolute top-3 right-3"
                        @click=" axios.post( '{{ route('pos.toggle.item') }}', { id: {{ (int) $item->id }} }).then($refs.container.remove()); " />


                    {{-- Item Name --}}
                    <p class=" font-semibold text-slate-800 dark:text-slate-100 pr-8 truncate">
                        {{ $item->name }}
                    </p>

                    <p class="pb-2 text-xs text-slate-900 dark:text-slate-50 ">Quantity left: {{ $item->stock }}
                    </p>

                    <div class="flex justify-between">
                        {{-- Quantity Controls --}}
                        <div class="flex items-center gap-1">
                            <button type="button"x-bind:disabled="isAtMin()" x-on:click="setQuantity(quantity - 1)"
                                x-bind:class="isAtMin() ? 'opacity-40' : 'hover:bg-slate-300 dark:hover:bg-slate-600'"
                                class="w-5 h-5 flex items-center justify-center rounded-md bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300  font-bold text-sm">
                                &minus;
                            </button>
                            <input type="number"
                                class="bg-transparent border-none w-16 p-1 text-sm text-center [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                x-model.number="quantity"
                                x-on:input="quantity = Math.min(stock, Math.max(1, parseInt($[event.target](http://event.target).value) || 1))"
                                min="1" max="stock" required />
                            <button type="button" x-bind:disabled="isAtMax()" x-on:click="setQuantity(quantity + 1)"
                                x-bind:class="isAtMax() ? 'opacity-40 ' : 'hover:bg-slate-300 dark:hover:bg-slate-600'"
                                class="w-5 h-5 flex items-center justify-center rounded-md bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm">
                                &plus;
                            </button>
                        </div>
                        {{-- Price Formula --}}
                        <p class="text-sm mt-1">
                            <span
                                class="text-slate-400 dark:text-slate-500">₱{{ number_format($item->selling_price, 2) }}
                                &times; <span x-text="quantity"></span> = </span>
                            <span class="font-bold text-slate-700 dark:text-slate-200">₱<span
                                    x-text="(price * quantity).toFixed(2)"></span></span>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

    </x-main-card>
</x-app-layout>
