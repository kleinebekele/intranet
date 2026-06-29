<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Benachrichtigungen') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Lege fest, wie du über Aktivitäten informiert werden möchtest.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.notifications') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <label class="flex items-start gap-3">
            <input type="checkbox" name="notify_email" value="1"
                   @checked(old('notify_email', $user->notify_email))
                   class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span>
                <span class="block text-sm font-medium text-gray-900">{{ __('E-Mail-Benachrichtigungen') }}</span>
                <span class="block text-sm text-gray-600">{{ __('Erhalte Benachrichtigungen per E-Mail.') }}</span>
            </span>
        </label>

        <label class="flex items-start gap-3">
            <input type="checkbox" name="notify_browser" value="1"
                   @checked(old('notify_browser', $user->notify_browser))
                   class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span>
                <span class="block text-sm font-medium text-gray-900">{{ __('Browser-Benachrichtigungen') }}</span>
                <span class="block text-sm text-gray-600">{{ __('Erhalte Hinweise direkt im Intranet.') }}</span>
            </span>
        </label>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Speichern') }}</x-primary-button>

            @if (session('status') === 'notifications-updated')
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
