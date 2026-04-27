<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Inventory') }}
        </h2>
    </x-slot>


    <x-main-card>
        {{-- top of the card (e.g. search) --}}
        <div class="flex items-center justify-between pb-4 flex-shrink-0">
            {{-- search --}}
            <x-search-bar id="search-item" />

            {{-- Add an Item --}}
            <x-borderless-button x-data x-on:click="$dispatch('open-modal', { id: 'add-new-item' })">
                <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                </svg>
                Add Item
            </x-borderless-button>

        </div>

        @php
            $inventory_columns = [
                ['key' => 'sku', 'label' => 'SKU', 'class' => 'px-6 py-4 w-28 whitespace-nowrap'],
                ['key' => 'name', 'label' => 'Item Name', 'class' => 'px-6 py-4 w-[20rem] whitespace-nowrap'],
                ['key' => 'cost', 'label' => 'Cost', 'class' => 'px-6 py-4 w-28 text-right whitespace-nowrap'],
                [
                    'key' => 'selling_price',
                    'label' => 'Price',
                    'class' => 'px-6 py-4 w-28 text-right whitespace-nowrap',
                ],
                ['key' => 'stock', 'label' => 'Stock', 'class' => 'px-6 py-4 w-28 text-right whitespace-nowrap'],
                ['key' => 'action', 'label' => '', 'class' => 'px-6 py-4 w-28 text-right'],
            ];
        @endphp

        <x-responsive-table :columns="$inventory_columns">
            @foreach ($items as $item)
                <x-responsive-table-row>
                    <x-responsive-table-data
                        class="px-6 py-2 md:py-4 w-full md:w-28 text-left whitespace-nowrap md:table-cell md:text-left"
                        column-name="SKU">
                        {{ $item->sku }}
                    </x-responsive-table-data>

                    <x-responsive-table-data
                        class="px-6 py-4 mb-2 md:m-0 w-full md:w-[20rem] whitespace-nowrap md:table-cell text-gray-700 dark:text-white order-first md:order-none border-b md:border-none">
                        {{ $item->name }}
                    </x-responsive-table-data>

                    <x-responsive-table-data
                        class="px-6 py-2 md:py-4 w-full md:w-28 text-left whitespace-nowrap md:table-cell md:text-right"
                        column-name="Cost">
                        ₱{{ $item->cost }}
                    </x-responsive-table-data>

                    <x-responsive-table-data
                        class="px-6 py-2 md:py-4 w-full md:w-28 text-left whitespace-nowrap md:table-cell md:text-right"
                        column-name="Price">
                        ₱{{ $item->selling_price }}
                    </x-responsive-table-data>

                    <x-responsive-table-data
                        class="px-6 py-2 md:py-4 w-full md:w-28 text-left whitespace-nowrap md:table-cell md:text-right"
                        column-name="Stock">
                        {{ $item->V5 }}
                    </x-responsive-table-data>

                    <x-responsive-table-data
                        class="px-6 py-2 md:py-4 w-full md:w-28 text-right whitespace-nowrap md:table-cell md:text-right">
                        <x-update-delete-buttons :object="$item" />
                    </x-responsive-table-data>
                </x-responsive-table-row>
            @endforeach
        </x-responsive-table>
    </x-main-card>

    <x-inventory.item-form :branches="$branches" id="add-new-item" />


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-item');
            const tableRows = document.querySelectorAll('tbody tr');
            const noRecordFoundRow = document.getElementById('no-record-message');

            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                let visibleCount = 0;

                tableRows.forEach(row => {
                    if (row.id === 'no-record-message') return;

                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 2) {
                        const skuText = cells[0].textContent.toLowerCase();
                        const itemNameText = cells[1].textContent.toLowerCase();

                        if (skuText.includes(searchTerm) || itemNameText.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });

                if (noRecordFoundRow) {
                    noRecordFoundRow.style.display = visibleCount === 0 ? 'table-row' : 'none';
                }
            });
        });
    </script>
</x-app-layout>
