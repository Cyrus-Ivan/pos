{{--
    USAGE:
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" required autofocus />

    PROPS:
        disabled (optional) — boolean to disable the input field
--}}

@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'border-slate-300 dark:border-slate-700 bg-black/5 dark:bg-black/25 dark:text-slate-300  focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-inner']) }}>
