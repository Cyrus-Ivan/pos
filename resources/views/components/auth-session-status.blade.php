{{--
    USAGE:
        <x-auth-session-status class="mb-4" :status="session('status')" />

    PROPS:
        status (required) — The status message to display (returns nothing if null)
--}}

@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600 dark:text-green-400']) }}>
        {{ $status }}
    </div>
@endif
