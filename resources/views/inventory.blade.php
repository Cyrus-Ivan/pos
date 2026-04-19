<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Inventory') }}
        </h2>
    </x-slot>

    <div class="py-8 flex-1 flex flex-col min-h-0">
        <div class="max-w-7xl w-full mx-auto md:px-6 lg:px-8 flex-1 flex flex-col min-h-0">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm md:rounded-lg flex-1 flex flex-col min-h-0">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex-1 flex flex-col min-h-0">
                    <div class="flex items-center justify-between pb-4 flex-shrink-0">
                        {{-- search --}}
                        <div class="sm:w-auto">
                            <form class="flex items-center" onsubmit="event.preventDefault();">
                                <label for="simple-search" class="sr-only">Search</label>
                                <div class="relative w-auto">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                            fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        id="simple-search"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full sm:w-64 pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Search">
                                </div>
                            </form>
                        </div>

                        {{-- add new --}}
                        <div
                            class="md:w-auto flex flex-col space-y-2 md:space-y-0 items-stretch justify-end md:space-x-3 flex-shrink-0">
                            <button type="button" id="createProductModalButton" data-modal-target="createProductModal"
                                data-modal-toggle="createProductModal"
                                class="flex items-center justify-center text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                                <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                </svg>
                                Add Item
                            </button>
                        </div>
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
                                            @if ($current_branch)
                                                <option value="{{ $current_branch->id }}"
                                                    class="dark:bg-gray-800 text-gray-900 dark:text-gray-100" selected>
                                                    {{ $current_branch->name }}
                                                </option>
                                            @endif
                                            @foreach ($branches as $branch)
                                                @if (!$current_branch || $branch->id !== $current_branch->id)
                                                    <option value="{{ $branch->id }}"
                                                        class="dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                                        {{ $branch->name }}
                                                    </option>
                                                @endif
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
                                        <td
                                            class="px-4  py-3 font-medium text-gray-900  dark:text-white whitespace-nowrap">
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
                                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor"
                                                    viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%"
                                            class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            No items securely found in this branch!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('simple-search');
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
