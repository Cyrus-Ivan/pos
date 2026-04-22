{{--
    USAGE:
        <x-borderless-button>
            Click Me
        </x-borderless-button>

    PROPS:
        (None) — Accepts all standard button attributes (e.g., type, class, wire:click)
--}}

<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'flex items-center justify-center bg-transparent border border-transparent font-medium rounded-lg text-sm px-4 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600 transition-colors duration-150 ease-in-out']) }}>
    {{ $slot }}
</button>
