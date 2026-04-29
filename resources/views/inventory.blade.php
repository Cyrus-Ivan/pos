<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Inventory') }}
        </h2>
    </x-slot>

    <x-main-card>
        {{-- top of the card (e.g. search) --}}
        <div class="flex items-center justify-between pb-4 flex-shrink-0">
            {{-- search & inventory dropdown --}}
            <form method="GET" class="flex gap-2">
                <x-search-bar id="search-item" />
                <select name="branch" id="inventory_id" onchange="this.form.submit()"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-36 p-2 pr-8 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </form>


            {{-- Add an Item --}}
            <x-borderless-button x-data x-on:click="$dispatch('open-modal', { id: 'item-form' })">
                <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                </svg>
                <span class="hidden md:block">Add Item</span>
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
                        {{ $item->stock }}
                    </x-responsive-table-data>

                    <x-responsive-table-data
                        class="px-6 py-2 md:py-4 w-full md:w-28 text-right whitespace-nowrap md:table-cell md:text-right">

                        <div class="flex justify-end gap-3">
                            <x-update-button x-data
                                x-on:click="$dispatch('open-modal', { id: 'item-form', item: {{ Js::from($item) }} })" />
                            <x-delete-button x-data
                                x-on:click="$dispatch('open-modal', { id: 'item-delete-form', item: {{ Js::from($item) }} })" />
                        </div>
                    </x-responsive-table-data>
                </x-responsive-table-row>
            @endforeach
        </x-responsive-table>
        <x-pagination :paginator="$items" :per-page-options="[10, 25, 50, 100]" />
    </x-main-card>

    <x-inventory.item-form :branches="$branches" id="item-form" />
    <x-inventory.item-delete-form id="item-delete-form" />

</x-app-layout>
