{{--
    USAGE:
        <x-input-label for="email" :value="__('Email')" />
        -- or --
        <x-input-label for="email">Email</x-input-label>

    PROPS:
        value (optional) — The label text (falls back to slot if omitted)
--}}

@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700 dark:text-gray-300']) }}>
    {{ $value ?? $slot }}
</label>
