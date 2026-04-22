@props(['id' => null, 'item' => null, 'branches' => null])

<x-modal id="{{ $id }}" title="Add New Item" :reset="is_null($item)">
    <form>
        <div class="space-y-4">
            {{-- SKU --}}
            <div>
                <x-input-label for="sku" :value="__('SKU')" />
                <x-text-input id="sku" class="block mt-1 w-full" name="sku" required autofocus />
            </div>

            {{-- Item Name --}}
            <div>
                <x-input-label for="item-name" :value="__('Item Name')" />
                <x-text-input id="item-name" class="block mt-1 w-full" name="item-name" required />
            </div>

            {{-- Item Cost & Selling Price (Side by Side) --}}
            <div class="flex gap-4">
                <div class="flex-1">
                    <x-input-label for="item-cost" :value="__('Item Cost')" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400 sm:text-sm">₱</span>
                        </div>
                        <x-text-input id="item-cost" type="number" step="0.01" class="block w-full pl-8"
                            name="item-cost" required />
                    </div>
                </div>

                <div class="flex-1">
                    <x-input-label for="selling-price" :value="__('Selling Price')" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400 sm:text-sm">₱</span>
                        </div>
                        <x-text-input id="selling-price" type="number" step="0.01" class="block w-full pl-8"
                            name="selling-price" required />
                    </div>
                </div>
            </div>

            {{-- Branches Stock (Scrollable List) --}}
            <div>
                <x-input-label :value="__('Stocks per Branch')" class="mb-2" />

                <div
                    class="max-h-40 overflow-y-auto border border-gray-300 dark:border-gray-700 rounded-md p-3 space-y-3 bg-gray-50 dark:bg-gray-900/50">
                    @foreach ($branches as $branch)
                        <div class="flex items-center justify-between gap-4">
                            {{-- Left: Branch Name --}}
                            <div class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $branch->name }}
                            </div>

                            {{-- Right: Stock Input --}}
                            <div class="w-48">
                                <x-text-input type="number" min="0" class="block w-full text-left"
                                    name="stocks[{{ $branch->id }}]" value="0" required />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <x-slot:footer>
            <x-borderless-button
                @click="$dispatch('close-modal', { id: '{{ $id }}' })">Cancel</x-borderless-button>
            <x-primary-button>Confirm</x-primary-button>
        </x-slot:footer>
    </form>
</x-modal>
