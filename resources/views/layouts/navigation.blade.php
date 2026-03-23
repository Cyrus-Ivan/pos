<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('pos') }}" class="flex items-center justify-center">
                        <x-application-logo
                            class="h-9 w-auto flex items-center fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <x-nav-links view='web' />
            </div>



            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Current Branch Display -->
                <div class="hidden sm:flex sm:items-center text-sm text-gray-500 dark:text-gray-400 font-medium">
                    @if (isset($currentBranch))
                        <span class="mr-4 px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-md">
                            {{ $currentBranch->name }}
                        </span>
                    @endif
                </div>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
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
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" id="logoutFormDesktop">
                            @csrf
                            <input type="hidden" name="latitude" class="logout_latitude">
                            <input type="hidden" name="longitude" class="logout_longitude">
                            <x-dropdown-link href="#"
                                onclick="event.preventDefault(); handleLogout('logoutFormDesktop');">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
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
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">

        <!-- Navigation Links (Hamburger) -->
        <x-nav-links view='mobile' />

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                <!-- Current Branch Display -->
                @if (isset($currentBranch))
                    <div class="font-medium text-sm text-gray-500">
                        {{ $currentBranch->name }}
                    </div>
                @endif

            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link view="mobile" :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" id="logoutFormMobile">
                    @csrf
                    <input type="hidden" name="latitude" class="logout_latitude">
                    <input type="hidden" name="longitude" class="logout_longitude">
                    <button type="button" onclick="handleLogout('logoutFormMobile')"
                        class="w-full text-left px-4 py-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    let latestCoords = {
        latitude: '',
        longitude: ''
    };

    function updateCoords() {
        navigator.geolocation.getCurrentPosition(
            position => {
                latestCoords.latitude = position.coords.latitude;
                latestCoords.longitude = position.coords.longitude;
                document.querySelectorAll('.logout_latitude').forEach(input => input.value = latestCoords.latitude);
                document.querySelectorAll('.logout_longitude').forEach(input => input.value = latestCoords
                    .longitude);
            },
            error => {
                // Optionally handle errors silently or log them
            }, {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );

        console.log(latestCoords);
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateCoords();
        setInterval(updateCoords, 5 * 60 * 1000); // every 5 minutes
    });

    function handleLogout(formId) {
        const form = document.getElementById(formId);
        const button = form.querySelector('button') || form.querySelector('a');
        const latInput = form.querySelector('input[name="latitude"]');
        const lngInput = form.querySelector('input[name="longitude"]');

        // Disable the button to prevent multiple clicks
        if (button) {
            button.style.pointerEvents = 'none';
            button.style.opacity = '0.5';
        }

        // Use latest tracked coordinates if available
        if (latestCoords.latitude !== '' && latestCoords.longitude !== '') {
            latInput.value = latestCoords.latitude;
            lngInput.value = latestCoords.longitude;
            form.submit();
            return;
        }

        // Fallback: fetch location if no recent coords
        navigator.geolocation.getCurrentPosition(
            position => {
                latestCoords.latitude = position.coords.latitude;
                latestCoords.longitude = position.coords.longitude;
                latInput.value = position.coords.latitude;
                lngInput.value = position.coords.longitude;
                form.submit();
            },
            error => {
                // Re-enable the button
                if (button) {
                    button.style.pointerEvents = 'auto';
                    button.style.opacity = '1';
                }

                // Show detailed error message
                let errorMessage = 'Location permission is required to logout.\n\n';

                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage +=
                            'You denied location access. Please enable location permissions in your browser settings and try again.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Location information is unavailable. Please check your device settings.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Location request timed out. Please try again.';
                        break;
                    default:
                        errorMessage += 'An unknown error occurred while getting your location.';
                }

                alert(errorMessage);
            }, {
                enableHighAccuracy: true,
                timeout: 500,
                maximumAge: 0
            }
        );
    }
</script>
