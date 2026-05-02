{{--
    USAGE:
        <x-search-bar id="table-search" />

    PROPS:
        id (optional) — The ID to place on the input element
--}}

@props(['id' => null])

<div class="sm:w-auto">
    <div class="flex items-center">
        <label for="{{ $id ?? 'simple-search' }}" class="sr-only">Search</label>
        <div class="relative w-auto flex">
            <input type="text" name="search" value="{{ request('search') }}" id="{{ $id }}" autocomplete="off"
                class="shadow bg-slate-100 border border-slate-300 text-slate-900 text-sm rounded-none rounded-l-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:w-64 p-2 dark:bg-slate-700 dark:border-slate-600 dark:placeholder-slate-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                placeholder="Search">
            <button type="submit"
                class="p-2 text-sm font-medium text-white bg-indigo-600 rounded-r-lg border border-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800">
                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span class="sr-only">Search</span>
            </button>
        </div>
    </div>
</div>
