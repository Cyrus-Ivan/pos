@props(['item' => []])
<div x-data="{
    quantity: 1,
    stock: {{ $item->stock }},
    price: {{ $item->selling_price }},
    discount: 0,
    init() {
        this.$watch('quantity', () => this.sync());
        this.$watch('discount', () => this.sync());
    },
    sync() {
        this.$dispatch('update-item', { id: {{ $item->id }}, quantity: this.quantity, discount: this.discount || 0 });
    },
    setQuantity(val) {
        const parsed = parseInt(val) || 1;
        if (parsed < 1) {
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

    <div class="flex justify-between">
        <div>
            {{-- Item Name --}}
            <p class="font-bold text-lg text-slate-800 dark:text-white truncate">
                {{ $item->name }}
            </p>
            <div class="pb-2">
                @if ($item->stock < 5)
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-400">
                        Quantity left: {{ $item->stock }}
                    </span>
                @elseif ($item->stock < 10)
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-400">
                        Quantity left: {{ $item->stock }}
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-400">
                        Quantity left: {{ $item->stock }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Quantity Controls --}}


        <ul class="inline-flex items-center -space-x-px text-sm">
            <li>
                <button type="button" x-show="!isAtMin()" x-on:click="setQuantity(quantity - 1)"
                    class="flex items-center justify-center px-3 py-2 border border-slate-300 rounded-l-lg dark:border-slate-700 bg-white hover:bg-slate-100 hover:text-red-600 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-red-400 h-[34px]">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 8h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
                <x-delete-button x-cloak x-show="isAtMin()"
                    x-on:click="axios.post('{{ route('pos.toggle.item') }}', { id: {{ $item->id }} }).then(() => { $dispatch('remove-item', { id: {{ $item->id }} }); $refs.container.remove(); })"
                    class="flex items-center justify-center px-3 border border-slate-300 rounded-l-lg dark:border-slate-700 bg-white hover:bg-red-50 hover:text-red-700 dark:bg-slate-800 dark:text-red-400 dark:hover:bg-slate-700 dark:hover:text-red-300 h-[34px] !py-0" />
            </li>
            <li>
                <input type="number"
                    class="border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 w-16 p-1 text-sm text-center h-[34px] focus:ring-0 focus:border-slate-300 dark:focus:border-slate-700 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                    x-model.number="quantity" x-on:change="setQuantity($event.target.value)" min="1"
                    :max="stock" required />
            </li>
            <li>
                <button type="button" x-bind:disabled="isAtMax()" x-on:click="setQuantity(quantity + 1)"
                    x-bind:class="isAtMax() ? 'text-slate-400 cursor-default' :
                        'text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white'"
                    class="flex items-center justify-center px-3 py-2 border border-slate-300 rounded-r-lg dark:border-slate-700 bg-white dark:bg-slate-800 h-[34px]">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 4v8M4 8h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </li>
        </ul>
    </div>

    <hr class="my-3 border-slate-200 dark:border-slate-700">

    <div class="space-y-2">
        <div class="w-full flex items-center justify-between text-slate-600 dark:text-slate-400">
            <p class="text-xs">Unit price × qty</p>
            <p class="text-sm">
                ₱{{ number_format($item->selling_price, 2) }} &times; <span x-text="quantity"></span> =
                <span class="font-medium text-slate-800 dark:text-slate-200">₱<span
                        x-text="subtotal().toFixed(2)"></span></span>
            </p>
        </div>

        <div class="w-full flex items-center justify-between text-slate-600 dark:text-slate-400">
            <p class="text-xs flex items-center gap-1">
                Discount
            </p>
            <div class="flex items-center text-sm">
                <span class="mr-1">&minus; ₱</span>
                <input type="number" x-model.number="discount"
                    x-on:change="discount = Math.min(subtotal(), Math.max(0, parseFloat($event.target.value) || 0))"
                    min="0" :max="subtotal()" placeholder="0.00"
                    class="w-16 bg-transparent border-0 border-b border-dashed border-slate-300 dark:border-slate-600 focus:border-solid focus:ring-0 focus:border-indigo-500 text-sm text-right p-0 font-medium text-slate-800 dark:text-slate-200 transition-colors placeholder:text-slate-300 dark:placeholder:text-slate-600 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
            </div>
        </div>
    </div>

    {{-- Price Formula --}}
    <div class="w-full pt-3 mt-3 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center">
        <p class="font-semibold text-slate-800 dark:text-white">Total</p>
        <div>
            {{-- Final price --}}
            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                ₱<span x-text="finalPrice().toFixed(2)"></span>
            </span>
        </div>
    </div>
</div>
