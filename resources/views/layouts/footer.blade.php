<footer class="border-t border-gray-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-4 text-sm text-gray-500 sm:flex-row sm:px-6 lg:px-8">
        <span>&copy; {{ date('Y') }} {{ config('app.name', 'Intranet') }}</span>
        <span>{{ __('Powered by Laravel') }} {{ app()->version() }}</span>
    </div>
</footer>
