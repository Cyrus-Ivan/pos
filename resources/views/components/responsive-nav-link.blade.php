{{--
    USAGE:
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" view="web">
            {{ __('Dashboard') }}
        </x-responsive-nav-link>

    PROPS:
        active (optional) — boolean to indicate if the link is active
        view (required)   — 'web' or 'mobile' to determine the appropriate layout classes
--}}

@props(['active', 'view'])

@php
    if ($view === 'mobile') {
        $classes =
            $active ?? false
                ? 'tracking-wide block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-400 dark:border-indigo-600 text-start text-base font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-900 focus:border-indigo-700 dark:focus:border-indigo-300 transition duration-150 ease-in-out'
                : 'tracking-wide block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:text-slate-800 dark:focus:text-slate-200 focus:bg-slate-50 dark:focus:bg-slate-700 focus:border-slate-300 dark:focus:border-slate-600 transition duration-150 ease-in-out';
    } elseif ($view === 'web') {
        $classes =
            $active ?? false
                ? 'tracking-wide inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 dark:border-indigo-600 text-sm font-medium leading-5 text-slate-900 dark:text-slate-100 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
                : 'tracking-wide inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:border-slate-300 dark:hover:border-slate-700 focus:outline-none focus:text-slate-700 dark:focus:text-slate-300 focus:border-slate-300 dark:focus:border-slate-700 transition duration-150 ease-in-out';
    }
@endphp

@if ($view == 'mobile')
    {{-- mobile --}}
    <div class="pt-2 pb-3 space-y-1">
@endif
@if ($view == 'web')
    {{-- web --}}
    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
@endif

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
</div>
