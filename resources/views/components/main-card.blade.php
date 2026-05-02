{{--
    USAGE:
        <x-main-card>
            Content goes here...
        </x-main-card>

    PROPS:
        (None) — Uses standard slot for content insertion
--}}

<div class="py-8 flex-1 flex flex-col min-h-0">
    <div class="max-w-7xl w-full mx-auto md:px-6 lg:px-8 flex-1 flex flex-col min-h-0">
        <div class="bg-slate-50 dark:bg-slate-900 overflow-hidden md:rounded-lg flex-1 flex flex-col min-h-0 shadow">
            <div class="p-6 text-slate-900 dark:text-slate-100 flex-1 flex flex-col min-h-0">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
