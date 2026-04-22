<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" x-data="loginComponent()" @submit="submitForm($event)">
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
        <div class="mt-6" x-init="initCamera()">
            <div x-show="cameraError" x-text="cameraError" x-cloak style="display: none"
                class="mb-2 p-3 text-sm text-red-700 bg-red-100 rounded-md dark:bg-red-200 dark:text-red-800"
                role="alert"></div>

            <video x-ref="video" autoplay playsinline class="w-full rounded" x-show="!isCaptured && !cameraError"
                x-cloak style="display: none;"></video>
            <canvas x-ref="canvas" class="hidden"></canvas>

            <img x-ref="preview" class="w-full rounded border" :src="photoData" x-show="isCaptured" x-cloak
                style="display: none;" />

            <input type="hidden" name="photo" :value="photoData">
            <input type="hidden" name="taken_at" :value="timestamp">

            <x-primary-button type="button" @click="toggleCapture" x-bind:disabled="cameraError !== null"
                class="w-full mt-4 justify-center"
                x-bind:class="{ 'opacity-50 cursor-not-allowed': cameraError !== null }">
                <span x-text="isCaptured ? 'Recapture' : 'Capture Photo'"></span>
            </x-primary-button>

            <ul x-show="showPhotoError" x-cloak style="display: none;"
                class="mt-3 mb-2 p-3 text-sm text-red-700 bg-red-100 rounded-md dark:bg-red-200 dark:text-red-800"
                role="alert">
                <li>A photo is required to login.</li>
            </ul>
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-gray-800 dark:text-gray-200 shadow-sm"
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
    function loginComponent() {
        return {
            isCaptured: false,
            cameraError: null,
            showPhotoError: false,
            photoData: '',
            timestamp: '',

            async initCamera() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: "user"
                        },
                        audio: false
                    });
                    this.$refs.video.srcObject = stream;
                } catch (error) {
                    this.cameraError =
                        "Camera is disabled, missing, or cannot be accessed. Please check your permissions and hardware.";
                }
            },

            toggleCapture() {
                if (!this.isCaptured) {
                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;

                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0);

                    this.photoData = canvas.toDataURL('image/jpeg');
                    this.timestamp = Math.floor(Date.now() / 1000);

                    this.isCaptured = true;
                    this.showPhotoError = false;
                } else {
                    this.photoData = '';
                    this.timestamp = '';
                    this.isCaptured = false;
                }
            },

            submitForm(e) {
                if (!this.isCaptured) {
                    e.preventDefault();
                    this.showPhotoError = true;
                }
            }
        }
    }
</script>
