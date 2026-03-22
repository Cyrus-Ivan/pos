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
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Branch Selection -->
        <div class="mt-4">
            <x-input-label for="branch_id" value="Branch" />

            <select id="branch_id" name="branch_id" required
                class="block mt-1 w-full rounded 
           border-gray-300 dark:border-gray-700 
           bg-white dark:bg-gray-900 
           text-gray-900 dark:text-gray-100">
                <option value="" class="text-gray-500">
                    Select Branch
                </option>

                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" class="text-gray-900 dark:text-gray-100">
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>

            <x-input-error :messages="$errors->get('branch_id')" class="mt-2" />
        </div>

        {{-- Camera Preview --}}
        <div class="mt-6">
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
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3" id="loginBtn" disabled>
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

        // CAMERA (single permission request)
        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: "user"
            },
            audio: false
        });

        video.srcObject = stream;
        video.classList.remove('hidden');

        // GEOLOCATION (one-time)
        navigator.geolocation.getCurrentPosition(
            pos => {
                document.getElementById('latitude').value = pos.coords.latitude;
                document.getElementById('longitude').value = pos.coords.longitude;
            },
            () => alert('Location permission is required')
        );

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

            loginBtn.disabled = false;
        };

        // RECAPTURE
        recaptureBtn.onclick = () => {
            preview.classList.add('hidden');
            video.classList.remove('hidden');

            document.getElementById('photo').value = '';
            document.getElementById('taken_at').value = '';

            captureBtn.classList.remove('hidden');
            recaptureBtn.classList.add('hidden');

            loginBtn.disabled = true;
        };
    })();
</script>
