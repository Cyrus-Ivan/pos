{{--
    resources/views/components/toast.blade.php

    PLACE ONCE in your layout file (e.g. app.blade.php), just before </body>:
        <x-toast />

    TRIGGER FROM ALPINE (any blade view):
        <button x-data @click="$dispatch('toast', { message: 'Saved!', type: 'success' })">Save</button>

    TRIGGER FROM BLADE (after a form submit):
        @if (session('success'))
            <x-toast-flash type="success" :message="session('success')" />
        @endif
        @if (session('error'))
            <x-toast-flash type="error" :message="session('error')" />
        @endif

    TYPES: success | error | warning | info

    TRIGGER FROM CONTROLLER:
        return back()->with('success', 'Item saved!');
        return back()->with('error', 'Something went wrong.');
        return back()->with('warning', 'Stock is running low.');
        return back()->with('info', 'Changes are pending approval.');
--}}

<div x-data="toastManager()" x-on:toast.window="add($event.detail)"
    class="fixed bottom-5 right-5 z-[999] flex flex-col gap-3 items-end pointer-events-none" aria-live="polite"
    aria-atomic="false">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="relative overflow-hidden pointer-events-auto flex items-start gap-3 w-80 rounded-xl px-4 py-3 shadow-lg border text-sm"
            :class="styles(toast.type)" role="alert">
            {{-- Icon --}}
            <span class="mt-0.5 shrink-0" x-html="icon(toast.type)"></span>

            {{-- Message --}}
            <p class="flex-1 font-medium leading-snug" x-text="toast.message"></p>

            {{-- Dismiss button --}}
            <button @click="dismiss(toast.id)" class="shrink-0 opacity-50 hover:opacity-100 transition-opacity mt-0.5"
                aria-label="Dismiss">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path
                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                </svg>
            </button>

            {{-- Progress bar --}}
            <div class="absolute bottom-0 left-0 h-0.5 rounded-b-xl transition-all duration-75 opacity-40"
                :class="progressColor(toast.type)" :style="'width:' + toast.progress + '%'"></div>
        </div>
    </template>
</div>

{{-- Flash bridge: fires the Alpine toast event from PHP session flashes --}}
<div x-data>
    @foreach (['success', 'error', 'warning', 'info'] as $type)
        @if (session($type))
            <span x-init="$nextTick(() => $dispatch('toast', { message: @js(session($type)), type: '{{ $type }}' }))"></span>
        @endif
    @endforeach

    {{-- Validation Error from $validated in controllers --}}
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <span x-init="$nextTick(() => $dispatch('toast', { message: @js($error), type: 'error' }))"></span>
        @endforeach
    @endif
</div>

@once
    <script>
        function toastManager() {
            return {
                toasts: [],
                _id: 0,

                add({
                    message,
                    type = 'info',
                    duration = 6000
                }) {
                    const id = ++this._id;
                    const interval = 200;
                    const steps = duration / interval;
                    let step = 0;

                    this.toasts.push({
                        id,
                        message,
                        type,
                        visible: true,
                        progress: 100
                    });

                    const timer = setInterval(() => {
                        step++;
                        const toast = this.toasts.find(t => t.id === id);
                        if (!toast) return clearInterval(timer);

                        toast.progress = 100 - (step / steps) * 100;

                        if (step >= steps) {
                            clearInterval(timer);
                            this.dismiss(id);
                        }
                    }, interval);
                },

                dismiss(id) {
                    const toast = this.toasts.find(t => t.id === id);
                    if (toast) toast.visible = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 300);
                },

                styles(type) {
                    return {
                        success: 'bg-green-50  border-green-200 text-green-800  dark:bg-green-900/40  dark:border-green-700 dark:text-green-300',
                        error: 'bg-red-50    border-red-200   text-red-800    dark:bg-red-900/40    dark:border-red-700   dark:text-red-300',
                        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/40 dark:border-yellow-700 dark:text-yellow-300',
                        info: 'bg-blue-50   border-blue-200  text-blue-800   dark:bg-blue-900/40   dark:border-blue-700  dark:text-blue-300',
                    } [type] ?? 'bg-slate-50 border-slate-200 text-slate-800';
                },

                progressColor(type) {
                    return {
                        success: 'bg-green-500',
                        error: 'bg-red-500',
                        warning: 'bg-yellow-500',
                        info: 'bg-blue-500',
                    } [type] ?? 'bg-slate-400';
                },

                icon(type) {
                    const icons = {
                        success: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-green-500"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>`,
                        error: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-red-500"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>`,
                        warning: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-yellow-500"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>`,
                        info: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-blue-500"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 0-2 0 1 1 0 0 0 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd"/></svg>`,
                    };
                    return icons[type] ?? icons.info;
                },
            };
        }
    </script>
@endonce
