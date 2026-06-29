<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profilinformationen') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Aktualisiere Avatar, Name und E-Mail-Adresse deines Kontos.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div x-data="{ photoName: null, photoPreview: '{{ $user->avatar_url }}' }">
            <x-input-label :value="__('Avatar')" />

            <input type="file" class="hidden" name="avatar" accept="image/*"
                   x-ref="avatar"
                   x-on:change="
                        photoName = $refs.avatar.files[0].name;
                        const reader = new FileReader();
                        reader.onload = (e) => { photoPreview = e.target.result; };
                        reader.readAsDataURL($refs.avatar.files[0]);
                   " />

            <div class="mt-2 flex items-center gap-4">
                <span class="inline-flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-gray-200 text-lg font-semibold text-gray-600">
                    <template x-if="photoPreview">
                        <img :src="photoPreview" class="h-16 w-16 rounded-full object-cover" alt="Avatar" />
                    </template>
                    <template x-if="! photoPreview">
                        <span>{{ $user->initials }}</span>
                    </template>
                </span>

                <x-secondary-button type="button" x-on:click.prevent="$refs.avatar.click()">
                    {{ __('Bild auswählen') }}
                </x-secondary-button>

                @if ($user->avatar_path)
                    <label class="inline-flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ms-2">{{ __('Avatar entfernen') }}</span>
                    </label>
                @endif
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="first_name" :value="__('Vorname')" />
                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" autocomplete="given-name" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>

            <div>
                <x-input-label for="last_name" :value="__('Nachname')" />
                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Anzeigename')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('E-Mail')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Speichern') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Gespeichert.') }}</p>
            @endif
        </div>
    </form>
</section>
