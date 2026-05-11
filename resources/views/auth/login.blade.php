<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" x-data="loginComponent()" @submit="submitForm($event)">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" name="email" :value="old('email')" required autofocus
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4" x-data="{ show: false }">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-10" x-bind:type="show ? 'text' : 'password'"
                    type="password" name="password" required />

                <button type="button" @mousedown="show = true" @mouseup="show = false" @mouseleave="show = false"
                    @touchstart="show = true" @touchend="show = false"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 focus:outline-none">
                    <!-- Eye Icon (hidden when show=true) -->
                    <svg x-show="!show" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        class="h-5 w-5 transition-colors duration-200 text-slate-400 dark:text-slate-500">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <!-- Eye Slash Icon (shown when show=true) -->
                    <svg x-show="show" x-cloak style="display: none;"
                        class="h-5 w-5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.52-3.41M5.636 5.636a9.965 9.965 0 016.364-2.636c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-2.012 3.659M15 12a3 3 0 01-2.908 3.997M12 9.003a3 3 0 00-3.997 2.908M3 3l18 18" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded bg-slate-100 dark:bg-slate-950 border dark:border-slate-500 border-slate-600 text-indigo-600 shadow-sm"
                    name="remember">
                <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3" id="loginBtn">
                {{ __('LOG IN') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
