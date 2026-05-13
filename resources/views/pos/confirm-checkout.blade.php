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

        <div class="flex flex-col md:flex-row gap-4 h-full min-h-0" x-data="{
        
            paymentType: 'cash',
            items: {
                @foreach ($selectedItems as $item)
                '{{ $item->id }}': { price: {{ $item->selling_price }}, quantity: 1, discount: 0 }, @endforeach
            },
            get itemCount() { return Object.keys(this.items).length; },
            get totalItemQuantity() { return Object.values(this.items).reduce((sum, item) => sum + item.quantity, 0); },
            get subtotal() { return Object.values(this.items).reduce((sum, item) => sum + (item.price * item.quantity), 0); },
            get totalDiscount() { return Object.values(this.items).reduce((sum, item) => sum + item.discount, 0); },
            get grandTotal() { return Math.max(0, this.subtotal - this.totalDiscount); },
        
            async submitCheckout() {
                try {
                    const response = await axios.post('{{ route('pos.checkout') }}', {
                        items: this.items,
                        payment_type: this.paymentType,
                        subtotal: this.subtotal,
                        total_discount: this.totalDiscount,
                        grand_total: this.grandTotal,
                    });
        
                    // Re-route on successful checkout
                    window.location.href = response.data.redirect_url;
                } catch (error) {
                    console.error('Failed to checkout:', error.response?.data?.message || error.message);
                    alert(error.response?.data?.message || 'Checkout Error');
                }
            }
        }"
            @update-item.window="if(items[$event.detail.id]) { items[$event.detail.id].quantity = $event.detail.quantity; items[$event.detail.id].discount = $event.detail.discount; }"
            @remove-item.window="delete items[$event.detail.id]">
            <div
                class="flex-1 min-h-0 flex flex-col border border-slate-300 dark:border-slate-700 rounded-md overflow-hidden">
                <div class="px-3 py-2 border-b border-slate-300 dark:border-slate-700 flex-shrink-0">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 tracking-wide">
                        Selected Items
                        <span class="ml-1 text-xs font-normal text-slate-500 dark:text-slate-400">(<span
                                x-text="itemCount">{{ count($selectedItems) }}</span>)</span>
                    </p>
                </div>
                {{-- Existing Items List --}}
                <div class="flex-1 overflow-y-auto p-3 space-y-3  shadow-inner">
                    @foreach ($selectedItems as $item)
                        <x-pos.confirm-checkout-item :item="$item" />
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
                        <span>Branch</span>
                        <span
                            class="font-medium text-slate-800 dark:text-slate-200">{{ \App\Models\Branch::find(env('BRANCH_ID'))?->name ?? 'Main Branch' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Date</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ now()->format('Y-m-d') }}</span>
                    </div>

                    <div class="flex justify-between ">
                        <span>Cashier</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ auth()->user()->name }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Total Quantity</span>
                        <span x-text="totalItemQuantity">{{ count($selectedItems) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>₱<span
                                x-text="subtotal.toFixed(2)">{{ number_format($selectedItems->sum('selling_price'), 2) }}</span></span>
                    </div>
                    {{-- Optional: discount row --}}
                    <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                        <span>Discount</span>
                        <span>&minus; ₱<span x-text="totalDiscount.toFixed(2)">0.00</span></span>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-slate-200 dark:border-slate-700"></div>

                {{-- Total --}}
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">Total</span>
                    <span class="text-xl font-bold text-slate-900 dark:text-white">
                        ₱<span
                            x-text="grandTotal.toFixed(2)">{{ number_format($selectedItems->sum('selling_price'), 2) }}</span>
                    </span>
                </div>

                {{-- Payment method (optional) --}}
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400">Payment Method</label>
                    <select x-model="paymentType"
                        class="w-full text-sm rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-slate-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="cash">Cash</option>
                        <option value="online">Online</option>
                    </select>
                </div>

                {{-- Complete Purchase Button --}}
                <x-primary-button type="button" @click="submitCheckout()">
                    Complete Purchase
                </x-primary-button>
            </div>

        </div>

    </x-main-card>
</x-app-layout>
