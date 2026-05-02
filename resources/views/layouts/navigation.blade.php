<nav x-data="{ open: false }" class="bg-slate-50 dark:bg-slate-900 border-b border-slate-100 dark:border-slate-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('pos') }}" class="flex items-center justify-center">
                        <x-application-logo
                            class="h-9 w-auto flex items-center fill-current text-slate-800 dark:text-slate-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <x-nav-links view='web' class="hidden md:flex" />
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden md:flex md:items-center md:ms-6">
                <!-- Current Branch Display -->
                <div class=" md:items-center text-sm text-slate-500 dark:text-slate-400 font-medium">
                    <span class="tracking-wide mr-4 px-3 py-1 bg-white dark:bg-slate-800 shadow rounded-md">
                        {{ \App\Models\Branch::find(env('BRANCH_ID'))->name }}
                    </span>
                </div>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class=" tracking-wide inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Theme Toggle -->
                        <x-dropdown-link type="button"
                            onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light')">
                            <div class="flex items-center justify-between">
                                <span class="block dark:hidden">{{ __('Dark Mode') }}</span>
                                <span class="hidden dark:block">{{ __('Light Mode') }}</span>

                                <!-- Moon icon for light mode -->
                                <svg class="w-4 h-4 mr-2 block dark:hidden" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>
                                <!-- Sun icon for dark mode -->
                                <svg class="w-4 h-4 mr-2 hidden dark:block" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>

                            </div>
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('profile.edit')">
                            <div class="flex items-center justify-between">

                                <span>{{ __('Profile') }}</span>
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" id="logoutFormDesktop">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <div class="flex items-center justify-between">
                                    <span>{{ __('Log Out') }}</span>
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                </div>
                            </x-dropdown-link>
                        </form>


                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center md:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md hover:text-slate-900 dark:hover:text-slate-50 text-slate-500 text-slate-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Hamburger) -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden md:hidden">

        <!-- Navigation Links (Hamburger) -->
        <x-nav-links view='mobile' />

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-slate-200 dark:border-slate-600">
            <div class="px-4">
                <div class="font-medium text-base text-slate-800 dark:text-slate-200">{{ Auth::user()->name }}</div>
                <div class="font-medium tracking-wide text-sm text-slate-500">{{ Auth::user()->email }}</div>
                <!-- Current Branch Display -->
                <div class="tracking-wide font-medium text-sm text-slate-500">
                    {{ \App\Models\Branch::find(env('BRANCH_ID'))->name }}
                </div>
            </div>

            <div class="mt-3 text-slate-500 dark:text-slate-400 space-y-1">
                <!-- Theme Toggle Mobile -->
                <button type="button"
                    onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light')"
                    class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 focus:outline-none focus:text-slate-800 dark:focus:text-slate-200 focus:bg-slate-50 dark:focus:bg-slate-700 focus:border-slate-300 dark:focus:border-slate-600 transition duration-150 ease-in-out">
                    <div class="flex items-center">
                        <!-- Moon icon for light mode -->
                        <svg class="w-5 h-5 mr-3 block dark:hidden" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <!-- Sun icon for dark mode -->
                        <svg class="w-5 h-5 mr-3 hidden dark:block" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <span class="block dark:hidden">{{ __('Dark Mode') }}</span>
                        <span class="hidden dark:block">{{ __('Light Mode') }}</span>
                    </div>
                </button>

                <x-responsive-nav-link view="mobile" :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-slate-500" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ __('Profile') }}</span>
                    </div>
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" id="logoutFormMobile">
                    @csrf
                    <x-responsive-nav-link view='mobile' :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-slate-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span>{{ __('Log Out') }}</span>
                        </div>
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
