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
        {{-- Wrapper: stacks vertically on mobile, side-by-side on md+ --}}

        <div class="flex flex-col md:flex-row gap-4 h-full min-h-0">
            <div
                class="flex-1 min-h-0 flex flex-col border border-slate-300 dark:border-slate-700 rounded-md overflow-hidden">

                <div
                    class="px-3 py-2 bg-slate-200 dark:bg-slate-800 border-b border-slate-300 dark:border-slate-700 flex-shrink-0">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 tracking-wide">
                        Selected Items
                        <span
                            class="ml-1 text-xs font-normal text-slate-500 dark:text-slate-400">({{ count($selectedItems) }})</span>
                    </p>
                </div>
                {{-- Existing Items List --}}
                <div class="flex-1 overflow-y-auto rounded-md p-3 space-y-3 bg-slate-100 dark:bg-slate-900 shadow-inner">

                    @foreach ($selectedItems as $item)
                        <div x-data="{
                            quantity: 1,
                            stock: {{ $item->stock }},
                            price: {{ $item->selling_price }},
                            discount: 0,
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
                            finalPrice() { return Math.max(0, this.subtotal() - this.discount); },
                            subtotal() { return this.price * this.quantity; },
                            isAtMax() { return this.quantity >= this.stock; },
                            isAtMin() { return this.quantity <= 1; }
                        }" x-ref="container"
                            class="relative bg-white dark:bg-slate-800/60 backdrop-blur-sm px-4 pt-4 pb-3 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700/50 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-200">

                            {{-- Delete Button --}}
                            <x-delete-button type="button" class="absolute top-3 right-3"
                                @click="axios.post('{{ route('pos.toggle.item') }}', { id: {{ (int) $item->id }} }).then($refs.container.remove());" />

                            {{-- Item Name --}}
                            <p class="font-semibold text-slate-800 dark:text-slate-100 pr-8 truncate">
                                {{ $item->name }}
                            </p>

                            <p class="pb-2 text-xs text-slate-900 dark:text-slate-50">Quantity left: {{ $item->stock }}
                            </p>

                            <div class="flex justify-between">
                                {{-- Quantity Controls --}}
                                <div class="flex items-center gap-1">
                                    <button type="button" x-bind:disabled="isAtMin()"
                                        x-on:click="setQuantity(quantity - 1)"
                                        x-bind:class="isAtMin() ? 'opacity-40' : 'hover:bg-slate-300 dark:hover:bg-slate-600'"
                                        class="w-5 h-5 flex items-center justify-center rounded-md bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm">
                                        &minus;
                                    </button>
                                    <input type="number"
                                        class="bg-transparent border-none w-16 p-1 text-sm text-center [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                        x-model.number="quantity"
                                        x-on:input="quantity = Math.min(stock, Math.max(1, parseInt($event.target.value) || 1))"
                                        min="1" max="stock" required />
                                    <button type="button" x-bind:disabled="isAtMax()"
                                        x-on:click="setQuantity(quantity + 1)"
                                        x-bind:class="isAtMax() ? 'opacity-40' : 'hover:bg-slate-300 dark:hover:bg-slate-600'"
                                        class="w-5 h-5 flex items-center justify-center rounded-md bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-sm">
                                        &plus;
                                    </button>
                                </div>
                                {{-- Price Formula --}}
                                <div class="flex flex-wrap items-center gap-x-1 gap-y-1 mt-2 text-sm">
                                    {{-- ₱price × qty = subtotal --}}
                                    <span class="text-slate-400 dark:text-slate-500">
                                        ₱{{ number_format($item->selling_price, 2) }} &times; <span
                                            x-text="quantity"></span> =
                                    </span>
                                    <span class="text-slate-600 dark:text-slate-300 font-medium">
                                        ₱<span x-text="subtotal().toFixed(2)"></span>
                                    </span>

                                    {{-- Discount --}}
                                    <span class="text-slate-400 dark:text-slate-500">&minus;</span>
                                    <input type="number" x-model.number="discount"
                                        x-on:input="discount = Math.min(subtotal(), Math.max(0, parseFloat($event.target.value) || 0))"
                                        min="0" :max="subtotal()" placeholder="0.00"
                                        class="w-20 text-sm text-center rounded-md border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100 px-2 py-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />

                                    {{-- Final price --}}
                                    <span class="text-slate-400 dark:text-slate-500">=</span>
                                    <span class="font-bold text-slate-700 dark:text-slate-200">
                                        ₱<span x-text="finalPrice().toFixed(2)"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Order Summary Panel --}}
            <div
                class="md:w-72 w-full md:h-full md:overflow-y-auto flex-shrink-0 flex flex-col gap-3 bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded p-5 shadow-sm">
                <h2
                    class="text-base font-bold text-slate-800 dark:text-slate-100 border-b border-slate-200 dark:border-slate-700 pb-2">
                    Order Summary
                </h2>

                {{-- Line items summary --}}
                <div class="space-y-1 text-sm text-slate-600 dark:text-slate-400">
                    <div class="flex justify-between">
                        <span>Items</span>
                        <span>{{ count($selectedItems) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>₱{{ number_format($selectedItems->sum('selling_price'), 2) }}</span>
                    </div>
                    {{-- Optional: discount row --}}
                    <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                        <span>Discount</span>
                        <span>— ₱0.00</span>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-slate-200 dark:border-slate-700"></div>

                {{-- Total --}}
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Total</span>
                    <span class="text-xl font-bold text-slate-900 dark:text-white">
                        ₱{{ number_format($selectedItems->sum('selling_price'), 2) }}
                    </span>
                </div>

                {{-- Payment method (optional) --}}
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400">Payment Method</label>
                    <select
                        class="w-full text-sm rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Cash</option>
                        <option>Card</option>
                        <option>GCash</option>
                    </select>
                </div>

                {{-- Complete Purchase Button --}}
                <button type="submit"
                    class="w-full mt-1 bg-blue-600 hover:bg-blue-700 active:scale-95 transition-all duration-150 text-white font-semibold text-sm py-3 rounded-xl shadow-md">
                    Complete Purchase
                </button>

                {{-- Cancel / Clear --}}
                <button type="button"
                    class="w-full text-xs text-slate-400 dark:text-slate-500 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                    Cancel Order
                </button>
            </div>

        </div>

    </x-main-card>
</x-app-layout>
