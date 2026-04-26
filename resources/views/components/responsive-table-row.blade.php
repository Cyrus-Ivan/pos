@props(['item', 'columns', 'mainColumn'])

<tr
    :class="isMobile ?
        'block bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 rounded-xl shadow-sm p-5' :
        'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors'">
    <!-- Mobile Title Region -->
    <template x-if="isMobile">
        <td class="block border-b border-gray-100 dark:border-gray-700 pb-3 mb-3">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                {{ data_get($item, $mainColumn) }}
            </h3>
        </td>
    </template>

    <!-- Single foreach mapping columns to row data -->
    @foreach ($columns as $column)
        @php
            $isMain = $column['key'] === $mainColumn;
            $isFooterAction = in_array(strtolower($column['key']), ['action', 'actions']);
        @endphp

        <!--
          Visibility logic:
          - Desktop: Always show td
          - Mobile: Hide the main column (already in title)
        -->
        <td x-show="!isMobile || (isMobile && !{{ $isMain ? 'true' : 'false' }})"
            :class="isMobile ?
                '{{ $isFooterAction ? 'block pt-4 mt-4 border-t border-gray-100 dark:border-gray-700 w-full' : 'flex justify-between items-center py-2 gap-4' }}' :
                'table-cell px-6 py-4 whitespace-nowrap {{ $column['class'] ?? '' }}'">

            <!-- Mobile Label -->
            <template x-if="isMobile && !{{ $isFooterAction ? 'true' : 'false' }}">
                <span class="font-semibold text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider shrink-0">
                    {{ $column['label'] }}
                </span>
            </template>

            <!-- Value / Content -->
            <div
                :class="isMobile ?
                    '{{ $isFooterAction ? 'w-full flex justify-end items-center gap-3' : 'text-sm text-gray-900 dark:text-gray-100 text-right break-words' }}' :
                    'text-sm text-gray-900 dark:text-gray-100'">
                {{-- Allow passing custom blade slots for columns like <x-slot:col_actions> --}}
                @if (isset(${'col_' . $column['key']}))
                    {{ ${'col_' . $column['key']} }}
                @else
                    {!! data_get($item, $column['key']) !!}
                @endif
            </div>

        </td>
    @endforeach
</tr>
