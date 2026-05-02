{{--
    USAGE:
        <x-borderless-button>
            Click Me
        </x-borderless-button>

    PROPS:
        (None) — Accepts all standard button attributes (e.g., type, class, wire:click)
--}}

<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'flex items-center justify-center bg-transparent border border-transparent font-medium rounded-lg text-sm px-4 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-slate-600 transition-colors duration-150 ease-in-out']) }}>
    {{ $slot }}
</button>
