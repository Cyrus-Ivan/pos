{{--
    USAGE:
        <x-modal id="my-modal" title="My Title">
            Your content here...
 
            <x-slot:footer>
                <x-borderless-button @click="$dispatch('close-modal', { id: 'my-modal' })">Cancel</x-borderless-button>
                <x-primary-button>Confirm</x-primary-button>
            </x-slot:footer>
        </x-modal>
 
    OPEN FROM ANYWHERE:
        <button x-data @click="$dispatch('open-modal', { id: 'my-modal' })">Open</button>
 
    CLOSE FROM ANYWHERE:
        <button x-data @click="$dispatch('close-modal', { id: 'my-modal' })">Close</button>
 
    PROPS:
        id          (required) — unique identifier, used to open/close the modal
        title       (optional) — heading text
        size        (optional) — sm | md | lg | xl  (default: md)
        dismissible (optional) — clicking backdrop or × closes modal (default: true)
        reset       (optional) — whether to clear out internal form inputs when closing (default: false)
--}}

@props(['id', 'title' => '', 'size' => 'md', 'dismissible' => true, 'reset' => false])

@php
    $sizeClass = match ($size) {
        'sm' => 'max-w-sm',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        default => 'max-w-lg',
    };
@endphp

<div x-data="{
    open: false,
    resetInputs() {
        @if ($reset) // Find all inputs, textareas, and selects within this modal and clear them
                $el.querySelectorAll('input, textarea, select').forEach(input => {
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        input.checked = false;
                    } else if (input.type !== 'submit' && input.type !== 'button') {
                        // Restore default value if available, else empty
                        input.value = input.defaultValue || '';
                    }
                }); @endif
    }
}" x-on:open-modal.window="$event.detail.id === '{{ $id }}' && (open = true)"
    x-on:close-modal.window="$event.detail.id === '{{ $id }}' && (open = false); resetInputs()"
    x-on:keyup.escape.window="if (open) { open = false; resetInputs(); }">
    {{-- Backdrop --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @if ($dismissible) x-on:click="open = false; resetInputs()" @endif
        class="fixed inset-0 z-40 bg-black/50" aria-hidden="true" x-cloak></div>

    {{-- Modal panel --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true"
        @if ($title) aria-labelledby="modal-title-{{ $id }}" @endif
        @if ($dismissible) x-on:click.self="open = false; resetInputs()" @endif x-cloak>
        <div
            class="relative w-full {{ $sizeClass }} bg-white dark:bg-slate-800 rounded-xl shadow-xl flex flex-col max-h-[90vh]">

            {{-- Header (only renders if title is provided) --}}
            @if ($title)
                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                    <h2 id="modal-title-{{ $id }}"
                        class="text-base font-semibold text-slate-800 dark:text-slate-200">
                        {{ $title }}
                    </h2>

                    @if ($dismissible)
                        <button type="button" x-on:click="open = false; resetInputs()"
                            class="p-1 rounded-md text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                            aria-label="Close modal">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="w-5 h-5">
                                <path
                                    d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                            </svg>
                        </button>
                    @endif
                </div>
            @endif

            {{-- Body (scrollable) --}}
            <div class="px-6 py-5 overflow-y-auto">
                {{ $slot }}
            </div>

            {{-- Footer (only renders if the footer slot is used) --}}
            @isset($footer)
                <div
                    class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 rounded-b-xl flex items-center justify-end gap-3 shrink-0">
                    {{ $footer }}
                </div>
            @endisset

        </div>
    </div>
</div>
