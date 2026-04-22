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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm md:rounded-lg flex-1 flex flex-col min-h-0">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex-1 flex flex-col min-h-0">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
