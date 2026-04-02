<x-app-layout>
    <section class="page-shell">
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold">Notifications</h1>
                <p class="text-gray-600 dark:text-text-secondary mt-1">Points and referral activity updates.</p>
            </div>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-orange hover:bg-orange-light text-white px-5 py-2.5 rounded-lg font-semibold transition">
                        Mark All Read
                    </button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" class="glass-panel rounded-xl p-4 mb-6 flex flex-col sm:flex-row gap-3 sm:items-end">
            <div>
                <label class="block text-sm mb-2">Scope</label>
                <select name="scope" class="bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-3 py-2">
                    <option value="" {{ request('scope') ? '' : 'selected' }}>All</option>
                    <option value="unread" {{ request('scope') === 'unread' ? 'selected' : '' }}>Unread only</option>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-2">Type</label>
                <select name="type" class="bg-white dark:bg-dark border border-gray-300 dark:border-gray-700 rounded px-3 py-2">
                    <option value="">All types</option>
                    @foreach(($availableTypes ?? collect()) as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="bg-orange hover:bg-orange-light text-white px-5 py-2 rounded-lg font-semibold transition">Apply</button>
        </form>

        <div class="space-y-4">
            @forelse($notifications as $notification)
                <div class="glass-panel border {{ $notification->read_at ? 'border-gray-200/70 dark:border-gray-800' : 'border-orange/40' }} rounded-xl p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold mb-1">{{ $notification->data['title'] ?? 'Notification' }}</p>
                            <p class="text-gray-600 dark:text-text-secondary">{{ $notification->data['message'] ?? 'You have a new notification.' }}</p>
                            <p class="text-xs text-gray-500 dark:text-text-secondary mt-2">{{ $notification->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        @if(! $notification->read_at)
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-sm text-orange hover:text-orange-light font-semibold">Mark Read</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="glass-panel rounded-xl p-10 text-center text-gray-600 dark:text-text-secondary">
                    No notifications yet.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    </div>
    </section>
</x-app-layout>
