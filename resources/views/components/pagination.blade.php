@props(['paginator', 'perPageOptions' => [10, 25, 50, 100]])

<div class="flex items-center justify-between gap-4 pt-4 text-sm text-slate-700 dark:text-slate-400">
    <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-start *:">
        {{-- Results per page --}}
        <div class="flex items-center gap-2">
            <span class="whitespace-nowrap truncate text-slate-500 dark:text-slate-400">Rows per page:</span>
            <select
                class="w-24 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-1.5 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                onchange="window.location.href='{{ request()->fullUrlWithoutQuery(['page']) }}' + (window.location.href.includes('?') ? '&' : '?') + 'per_page=' + this.value">
                @foreach ($perPageOptions as $option)
                    <option value="{{ $option }}" {{ request('per_page', 50) == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Range + Total --}}
        <span class="hidden sm:inline">
            <span class="font-medium text-slate-900 dark:text-white">{{ $paginator->firstItem() ?? 0 }}</span> to
            <span class="font-medium text-slate-900 dark:text-white">{{ $paginator->lastItem() ?? 0 }}</span> of <span
                class="font-medium text-slate-900 dark:text-white">{{ number_format($paginator->total()) }}</span>
        </span>
    </div>

    {{-- Navigation --}}
    <nav class="flex items-center">
        <ul class="inline-flex items-center -space-x-px text-sm">
            {{-- First --}}
            <li>
                <{{ $paginator->onFirstPage() ? 'span' : 'a href=' . $paginator->url(1) }}
                    class="flex items-center justify-center px-3 py-2 border border-slate-300 rounded-l-lg dark:border-slate-700 
                           {{ $paginator->onFirstPage() ? 'text-slate-400 bg-white cursor-default dark:bg-slate-800 dark:text-slate-500' : 'text-slate-500 bg-white hover:bg-slate-100 hover:text-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white' }}">
                    <span class="sr-only">First</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 3L6 8l4 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M6 3L2 8l4 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    </{{ $paginator->onFirstPage() ? 'span' : 'a' }}>
            </li>

            {{-- Prev --}}
            <li>
                <{{ $paginator->onFirstPage() ? 'span' : 'a href=' . $paginator->previousPageUrl() }}
                    class="flex items-center justify-center px-3 py-2 border border-slate-300 dark:border-slate-700 
                           {{ $paginator->onFirstPage() ? 'text-slate-400 bg-white cursor-default dark:bg-slate-800 dark:text-slate-500' : 'text-slate-500 bg-white hover:bg-slate-100 hover:text-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white' }}">
                    <span class="sr-only">Previous</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 3L6 8l4 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    </{{ $paginator->onFirstPage() ? 'span' : 'a' }}>
            </li>

            {{-- Next --}}
            <li>
                <{{ !$paginator->hasMorePages() ? 'span' : 'a href=' . $paginator->nextPageUrl() }}
                    class="flex items-center justify-center px-3 py-2 border border-slate-300 dark:border-slate-700 
                           {{ !$paginator->hasMorePages() ? 'text-slate-400 bg-white cursor-default dark:bg-slate-800 dark:text-slate-500' : 'text-slate-500 bg-white hover:bg-slate-100 hover:text-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white' }}">
                    <span class="sr-only">Next</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 3l4 5-4 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    </{{ !$paginator->hasMorePages() ? 'span' : 'a' }}>
            </li>

            {{-- Last --}}
            <li>
                <{{ !$paginator->hasMorePages() ? 'span' : 'a href=' . $paginator->url($paginator->lastPage()) }}
                    class="flex items-center justify-center px-3 py-2 border border-slate-300 rounded-r-lg dark:border-slate-700 
                           {{ !$paginator->hasMorePages() ? 'text-slate-400 bg-white cursor-default dark:bg-slate-800 dark:text-slate-500' : 'text-slate-500 bg-white hover:bg-slate-100 hover:text-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white' }}">
                    <span class="sr-only">Last</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 3l4 5-4 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M10 3l4 5-4 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    </{{ !$paginator->hasMorePages() ? 'span' : 'a' }}>
            </li>
        </ul>
    </nav>
</div>
