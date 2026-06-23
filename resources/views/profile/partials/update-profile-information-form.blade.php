<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="location" :value="__('Location')" />
            <div class="mt-1 flex rounded-md shadow-sm">
                <x-text-input id="location" name="location" type="text" class="block w-full flex-1 rounded-none rounded-l-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600" :value="old('location', $user->location)" placeholder="Your location address" />
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $user->latitude) }}" />
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $user->longitude) }}" />
                <button type="button" id="btnDetectLocationTailwind" onclick="detectLocationTailwind()" class="inline-flex items-center px-4 rounded-r-md border border-l-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    <i class="fas fa-map-marker-alt mr-1"></i> Auto Detect
                </button>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('location')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    @push('scripts')
    <script>
        function detectLocationTailwind() {
            const btn = document.getElementById('btnDetectLocationTailwind');
            const input = document.getElementById('location');
            const originalText = btn.innerHTML;

            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting...';

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.display_name) {
                                input.value = data.display_name;
                                document.getElementById('latitude').value = lat;
                                document.getElementById('longitude').value = lon;
                            } else {
                                alert('Could not resolve address for your coordinates.');
                            }
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        })
                        .catch(error => {
                            console.error('Error reverse geocoding:', error);
                            alert('Error getting address details.');
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        });
                },
                function (error) {
                    console.error('Geolocation error:', error);
                    alert('Unable to retrieve your location. Please ensure location permissions are granted.');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }
    </script>
    @endpush
</section>
