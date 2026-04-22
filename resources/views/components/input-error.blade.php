{{--
    USAGE:
        <x-input-error :messages="$errors->get('email')" class="mt-2" />

    PROPS:
        messages (required) — The array of error messages to display
--}}

@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' =>  ' mb-2 p-3 text-sm text-red-700 bg-red-100 rounded-md dark:bg-red-200 dark:text-red-800']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
