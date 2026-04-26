@props(['columns', 'emptyMessage' => 'No matching records found.'])

<div x-data="{ isMobile: window.innerWidth < 768 }" @resize.window.debounce.150ms="isMobile = window.innerWidth < 768" x-init="isMobile = window.innerWidth < 768"
    x-cloak {{ $attributes->merge(['class' => 'w-full']) }}>
    <div
        class="w-full overflow-x-auto overflow-y-auto max-h-[70vh] bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400" :class="isMobile ? 'block' : 'table'">
            <!-- Desktop Header -->
            <thead x-show="!isMobile"
                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    @foreach ($columns as $column)
                        <th scope="col" class="px-6 py-4 {{ $column['class'] ?? '' }}">
                            {{ $column['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <!-- Table Body / Card Container -->
            <tbody
                :class="isMobile ? 'block space-y-4 p-4 bg-gray-50/50 dark:bg-gray-900/50' :
                    'table-row-group divide-y divide-gray-200 dark:divide-gray-700'">
                @if ($slot->isNotEmpty())
                    {{ $slot }}
                @else
                    <tr :class="isMobile ? 'block bg-white dark:bg-gray-800 rounded-lg p-6 text-center' : 'table-row'">
                        <td colspan="{{ count($columns) }}"
                            class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
