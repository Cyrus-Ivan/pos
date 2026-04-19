<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4" x-data="{ show: false }">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-10" x-bind:type="show ? 'text' : 'password'"
                    type="password" name="password" required autocomplete="current-password" />

                <button type="button" @mousedown="show = true" @mouseup="show = false" @mouseleave="show = false"
                    @touchstart="show = true" @touchend="show = false"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 focus:outline-none">
                    <!-- Eye Icon (hidden when show=true) -->
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <!-- Eye Slash Icon (shown when show=true) -->
                    <svg x-show="show" x-cloak style="display: none;" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.52-3.41M5.636 5.636a9.965 9.965 0 016.364-2.636c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-2.012 3.659M15 12a3 3 0 01-2.908 3.997M12 9.003a3 3 0 00-3.997 2.908M3 3l18 18" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>


        {{-- Camera Preview --}}
        <div class="mt-6">
            <div id="camera-error"
                class="hidden mb-2 p-3 text-sm text-red-700 bg-red-100 rounded-md dark:bg-red-200 dark:text-red-800"
                role="alert"></div>

            <video id="video" autoplay playsinline class="w-full rounded hidden"></video>
            <canvas id="canvas" class="hidden"></canvas>

            <img id="preview" class="hidden rounded border" />

            <input type="hidden" name="photo" id="photo">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="taken_at" id="taken_at">

            <button type="button" id="capture" class="mt-3 w-full px-4 py-2 bg-indigo-600 text-white rounded">
                Capture Photo
            </button>
            <button type="button" id="recapture"
                class=" mt-3 w-full px-4 py-2 bg-indigo-600 text-white rounded hidden">Recapture</button>

            <ul id="photo-error"
                class="hidden mt-3 mb-2 p-3 text-sm text-red-700 bg-red-100 rounded-md dark:bg-red-200 dark:text-red-800"
                role="alert">
                <li>A photo is required to login.</li>
            </ul>
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3" id="loginBtn">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<script>
    (async () => {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const preview = document.getElementById('preview');

        const captureBtn = document.getElementById('capture');
        const recaptureBtn = document.getElementById('recapture');
        const loginBtn = document.getElementById('loginBtn');
        const cameraError = document.getElementById('camera-error');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const loginForm = document.getElementById('loginForm');
        const photoError = document.getElementById('photo-error');

        loginForm.addEventListener('submit', function(e) {
            const hasPhoto = document.getElementById('photo').value !== '';
            if (!hasPhoto) {
                e.preventDefault();
                photoError.classList.remove('hidden');
                photoError.classList.add('block');
            }
        });

        function checkLoginState() {
            const hasPhoto = document.getElementById('photo').value !== '';
            if (hasPhoto) {
                photoError.classList.add('hidden');
                photoError.classList.remove('block');
            }
        }

        emailInput.addEventListener('input', checkLoginState);
        passwordInput.addEventListener('input', checkLoginState);

        checkLoginState();

        // CAMERA (single permission request)
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "user"
                },
                audio: false
            });

            video.srcObject = stream;
            video.classList.remove('hidden');
        } catch (error) {
            cameraError.textContent =
                "Camera is disabled, missing, or cannot be accessed. Please check your permissions and hardware.";
            cameraError.classList.remove('hidden');
            captureBtn.disabled = true;
            captureBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }

        // CAPTURE
        captureBtn.onclick = () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);

            const dataUrl = canvas.toDataURL('image/jpeg');

            document.getElementById('photo').value = dataUrl;
            document.getElementById('taken_at').value = Math.floor(Date.now() / 1000);

            // UI switch: live → preview
            preview.src = dataUrl;
            preview.classList.remove('hidden');

            video.classList.add('hidden');
            captureBtn.classList.add('hidden');
            recaptureBtn.classList.remove('hidden');

            checkLoginState();
        };

        // RECAPTURE
        recaptureBtn.onclick = () => {
            preview.classList.add('hidden');
            video.classList.remove('hidden');

            document.getElementById('photo').value = '';
            document.getElementById('taken_at').value = '';

            captureBtn.classList.remove('hidden');
            recaptureBtn.classList.add('hidden');

            checkLoginState();
        };
    })();
</script>
