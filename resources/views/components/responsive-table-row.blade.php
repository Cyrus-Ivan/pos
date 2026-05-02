{{--
    USAGE:
        Represents a table row element (<tr>) that adapts to Flexbox layout on mobile.
        This provides the row-level looping container wrapping your <x-responsive-table-data> elements so they
        display as a stacked card-like element on mobile and revert to native inline table cells on desktop.

        Example from inventory.blade.php:
        <x-responsive-table-row>
            <x-responsive-table-data column-name="SKU">
                {{ $item->sku }}
            </x-responsive-table-data>
            ...
        </x-responsive-table-row>

    PROPS:
        - Accepts standard HTML attributes (e.g., class, id). Merges explicitly with a predefined set of styling
          so you only need to pass extra overrides or Javascript directives if needed.
--}}

<tr
    {{ $attributes->merge(['class' => 'flex flex-col md:table-row mb-3 md:m-3 bg-slate-100/50 dark:bg-slate-700/50 hover:bg-slate-200/50 dark:hover:bg-slate-600/50 transition-colors']) }}>
    {{ $slot }}
</tr>
