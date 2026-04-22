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
            <x-borderless-button x-data @click="$dispatch('open-modal', { id: 'add-new-item' })">
                <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                </svg>
                Add Item
            </x-borderless-button>

        </div>

        {{-- table --}}
        <div class="flex-1 overflow-auto rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead
                    class="w-full text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th scope="col" class="px-4 py-3 w-40">SKU</th>
                        <th scope="col" class="px-4 py-3 w-90">Item Name</th>
                        <th scope="col" class="px-4 py-3 w-28 text-center">Item Cost</th>
                        <th scope="col" class="px-4 py-3 w-40 text-center">Selling Price</th>

                        <th scope="col" class="px-4 py-3 w-40">
                            <select onchange="window.location.href = '?branch_id=' + this.value"
                                class="border-0 bg-transparent p-0 pr-6 text-xs font-bold uppercase text-gray-700 hover:text-gray-900 focus:ring-0 dark:text-gray-400 dark:hover:text-gray-300 cursor-pointer w-full">
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ $branch->id == request('branch_id', env('BRANCH_ID')) ? 'selected' : '' }}
                                        class="dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </th>

                        <th scope="col" class="px-4 py-3">
                            <span class="sr-only">Actions</span>
                        </th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr
                            class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-4 py-3   ">
                                {{ $item->sku }}
                            </td>
                            <td class="px-4  py-3 font-medium text-gray-900  dark:text-white whitespace-nowrap">
                                {{ $item->item_name }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                ₱{{ number_format($item->cost, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                ₱{{ number_format($item->selling_price, 2) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center">
                                    <span
                                        class="px-2 py-1 font-semibold rounded-md {{ $item->current_stock > 10 ? ' text-green-800 dark:text-green-300' : ($item->current_stock > 0 ? ' text-yellow-800 dark:text-yellow-300' : ' text-red-800 dark:text-red-300') }}">
                                        {{ $item->current_stock }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 flex items-center justify-end">
                                <button
                                    class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100"
                                    type="button">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No item was found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-main-card>

    <x-inventory.item-form :branches="$branches" id="add-new-item" />


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-item');
            const tableRows = document.querySelectorAll('tbody tr');

            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();

                tableRows.forEach(row => {
                    // Skip the empty list message row if it exists
                    if (row.querySelector('td[colspan="100%"]')) return;

                    const rowText = row.textContent.toLowerCase();
                    if (rowText.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

            });
        });
    </script>
</x-app-layout>
