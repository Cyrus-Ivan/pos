<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('Employees') }}
        </h2>
    </x-slot>

    <x-main-card>
        {{-- top of the card (e.g. search) --}}
        <div class="flex items-center justify-between pb-4 flex-shrink-0">
            {{-- search --}}
            <form method="GET" class="flex gap-2">
                <x-search-bar id="search-employee" />
            </form>

            {{-- Add Employee --}}
            <x-borderless-button x-data x-on:click="$dispatch('open-modal', { id: 'employee-form' })">
                <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                </svg>
                <span class="hidden md:block">Add Employee</span>
            </x-borderless-button>
        </div>

        @php
            $employee_columns = [
                ['key' => 'name', 'label' => 'Name', 'class' => 'px-6 py-4 w-48 whitespace-nowrap'],
                ['key' => 'email', 'label' => 'Email', 'class' => 'px-6 py-4 w-[20rem] whitespace-nowrap'],
                ['key' => 'role', 'label' => 'Role', 'class' => 'px-6 py-4 w-28 whitespace-nowrap'],
                ['key' => 'action', 'label' => '', 'class' => 'px-6 py-4 w-28 text-right'],
            ];
        @endphp

        <x-responsive-table :columns="$employee_columns">
            @foreach ($employees as $employee)
                <x-responsive-table-row>
                    <x-responsive-table-data
                        class="px-6 py-4 mb-2 md:m-0 w-full md:w-48 whitespace-nowrap md:table-cell text-slate-700 dark:text-white order-first md:order-none border-b md:border-none"
                        column-name="Name">
                        {{ $employee->name }}
                    </x-responsive-table-data>

                    <x-responsive-table-data
                        class="px-6 py-2 md:py-4 w-full md:w-[20rem] text-left whitespace-nowrap md:table-cell"
                        column-name="Email">
                        {{ $employee->email }}
                    </x-responsive-table-data>

                    <x-responsive-table-data
                        class="px-6 py-2 md:py-4 w-full md:w-28 text-left whitespace-nowrap md:table-cell"
                        column-name="Role">
                        {{ ucfirst($employee->role) }}
                    </x-responsive-table-data>

                    <x-responsive-table-data
                        class="px-6 py-2 md:py-4 w-full md:w-28 text-right whitespace-nowrap md:table-cell md:text-right">
                        <div class="flex justify-end gap-3">
                            <x-update-button x-data
                                x-on:click="$dispatch('open-modal', { id: 'employee-form', employee: {{ Js::from($employee) }} })" />
                            <x-delete-button x-data
                                x-on:click="$dispatch('open-modal', { id: 'employee-delete-form', employee: {{ Js::from($employee) }} })" />
                        </div>
                    </x-responsive-table-data>
                </x-responsive-table-row>
            @endforeach
        </x-responsive-table>

        <x-pagination :paginator="$employees" :per-page-options="[10, 25, 50, 100]" />
    </x-main-card>

</x-app-layout>
