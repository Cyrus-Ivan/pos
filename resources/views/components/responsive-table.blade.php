{{--
    USAGE:
        A responsive layout wrapper meant to house <x-responsive-table-row> components.
        It displays as a traditional native <table> with a sticky <thead> on desktop (md and up),
        and scales down automatically hiding the header out-of-the-box on mobile screens.

        Example from inventory.blade.php:
        @php
            $inventory_columns = [
                ['key' => 'sku', 'label' => 'SKU', 'class' => 'px-6 py-4 w-28 whitespace-nowrap'],
                ['key' => 'name', 'label' => 'Item Name', 'class' => 'px-6 py-4 w-[20rem] whitespace-nowrap'],
                // ...
            ];
        @endphp

        <x-responsive-table :columns="$inventory_columns">
            @foreach ($items as $item)
                <x-responsive-table-row>...</x-responsive-table-row>
            @endforeach
        </x-responsive-table>

    PROPS:
        - columns: Array of arrays holding 'key' (identifier), 'label' (visible text in <th>), and 'class' (column specific Tailwind sizing/alignment config).
        - Allows passing standard HTML attributes (like class) to the outermost container.

    FEATURES:
        - Automatically handles empty states and frontend search filtering. A MutationObserver
          watches the <tbody> for changes in child elements or style attributes (e.g. `display: none`).
          If all rows are hidden or the table is empty, a "No matching records found" message is shown.
--}}

@props(['columns' => []])

<div x-data="{ isMobile: window.innerWidth < 768 }" x-on:resize.window.debounce.150ms="isMobile = window.innerWidth < 768" x-cloak
    {{ $attributes->merge(['class' => 'w-full flex flex-col min-h-0']) }}>

    <div
        class="shadow w-full bg-white dark:bg-slate-800 md:rounded-lg flex flex-col min-h-0 overflow-y-auto overflow-x-auto">
        <table class="w-full table-fixed text-sm text-left text-slate-500 dark:text-slate-400">
            @isset($columns)
                <thead
                    class="hidden md:table-header-group sticky top-0 z-10 text-xs text-white uppercase dark:dark:border-slate-700 bg-indigo-600 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        @foreach ($columns as $column)
                            <th scope="col" id="{{ $column['key'] }}" class="{{ $column['class'] }}">
                                {{ $column['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endisset

            <tbody x-ref="tableBody" class="md:table-row-group divide-y divide-slate-200 dark:divide-slate-700">
                {{ $slot }}

            </tbody>
            <tr x-ref="noRecord" id="no-record-message" style="{{ $slot->isEmpty() ? '' : 'display: none;' }}"
                class="flex flex-col md:table-row m-3 bg-slate-100/50 dark:bg-slate-700/50 hover:bg-slate-200/50 dark:hover:bg-slate-600/50 transition-colors">
                <td colspan="{{ count($columns) }}" class="text-center py-9">No matching records found.
                </td>
            </tr>
        </table>
    </div>
</div>
