{{--
    USAGE:
        <x-input-error :messages="$errors->get('email')" class="mt-2" />

    PROPS:
        messages (required) — The array of error messages to display
--}}

@props(['messages'])
@if ($messages)
    <ul {{ $attributes->merge(['class' => 'mb-2 pt-1 p-1 text-sm text-red-700 dark:text-red-300']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
