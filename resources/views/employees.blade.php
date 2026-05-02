<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Employees') }}
        </h2>
    </x-slot>

    <x-main-card>
        {{ __('Your employees!') }}
    </x-main-card>

</x-app-layout>
