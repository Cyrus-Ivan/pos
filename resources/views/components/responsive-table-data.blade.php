{{--
    USAGE:
        A responsive wrapper for table data (<td>) that provides an inline label purely for mobile views.
        On small screens it acts as a stacked div element (often flexbox), showing the `column-name` label
        beside the value to inform the user what the field is. On larger screens, the `column-name` hides 
        automatically and it behaves strictly as a native table cell width based on the header alignment.

        Example from inventory.blade.php:
        <x-responsive-table-data
            class="px-6 py-2 md:py-4 w-full md:w-28 text-left whitespace-nowrap md:table-cell md:text-left"
            column-name="SKU">
            {{ $item->sku }}
        </x-responsive-table-data>

    PROPS:
        - columnName: Formatted human-readable label to display beside the value *only* in mobile (md:hidden) view.
            (e.g., 'SKU', 'Item Name', 'Cost'). This simulates the hidden header column values in the stacked list view.
        - Attributes directly merge with the <td> element for tailwind resizing, alignment, grouping spacing.
--}}

@props(['columnName' => ''])

<td {{ $attributes }}>
    @if ($columnName)
        <span class="md:hidden">{{ $columnName }}:</span>
    @endif
    <span class="font-bold">{{ $slot }}</span>
</td>
