{{--
    USAGE:
        <x-application-logo class="text-2xl font-bold" />
        
    PROPS:
        (None) - Accepts all standard HTML attributes via $attributes
--}}

@props([
    'hasName' => false,
    'isslateScale' => false,
])

<div class="flex items-center gap-3">
    {{-- We set the color logic here once on the SVG --}}
    <svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"
        class="shrink-0 {{ $isslateScale ? 'text-slate-950 dark:text-white' : 'text-[#6c63ff]' }}">

        {{-- fill="currentColor" makes the rect follow the text classes above --}}
        <rect width="36" height="36" x="2" y="2" rx="10" fill="currentColor" />

        <g transform="translate(10, 10)">
            {{-- For the inner dots, we likely want them to contrast. 
                 If the bg is dark, dots should be light (and vice versa). --}}
            @php
                $innerColor = $isslateScale ? 'fill-white dark:fill-slate-950' : 'fill-white';
            @endphp

            <rect width="8" height="8" x="0" y="0" rx="1.5" class="{{ $innerColor }}"
                fill-opacity="1.0" />
            <rect width="8" height="8" x="10" y="0" rx="1.5" class="{{ $innerColor }}"
                fill-opacity="0.3" />
            <rect width="8" height="8" x="0" y="10" rx="1.5" class="{{ $innerColor }}"
                fill-opacity="0.3" />
            <rect width="8" height="8" x="10" y="10" rx="1.5" class="{{ $innerColor }}"
                fill-opacity="1.0" />
        </g>
    </svg>

    @if ($hasName)
        <span class="font-bold text-4xl {{ $isslateScale ? 'text-slate-950 dark:text-white' : 'text-indigo-600' }}">
            {{ env('APP_NAME') }}
        </span>
    @endif
</div>
