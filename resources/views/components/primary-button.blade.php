{{--
    USAGE:
        <x-primary-button>
            Save Changes
        </x-primary-button>

    PROPS:
        (None) — Accepts all standard button attributes (e.g., type="submit", class)
--}}

<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-wide hover:bg-indigo-700 active:bg-indigo-800 dark:active:bg-indigo-600 active:scale-95 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-950 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
