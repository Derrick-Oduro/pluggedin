<x-app-layout>
    <section class="page-shell">
    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        @php($unreadCount = auth()->user()->unreadNotifications()->count())

        <div class="flex flex-wrap items-end justify-between gap-3 mb-6">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold">Notifications</h1>
                <p class="text-gray-600 dark:text-text-secondary mt-1">Compact feed for updates and action links.</p>
            </div>
            @if($unreadCount > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="h-10 px-4 rounded-lg bg-orange hover:bg-orange-light text-white text-sm font-semibold transition">
                        Mark All Read
                    </button>
                </form>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
            <div class="backend-card p-3 rounded-xl">
                <p class="text-xs text-gray-500 dark:text-text-secondary">Unread</p>
                <p class="text-lg font-semibold">{{ $unreadCount }}</p>
            </div>
            <div class="backend-card p-3 rounded-xl">
                <p class="text-xs text-gray-500 dark:text-text-secondary">Total</p>
                <p class="text-lg font-semibold">{{ $notifications->total() }}</p>
            </div>
            <div class="backend-card p-3 rounded-xl">
                <p class="text-xs text-gray-500 dark:text-text-secondary">Page</p>
                <p class="text-lg font-semibold">{{ $notifications->currentPage() }} / {{ max(1, $notifications->lastPage()) }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg px-3 py-2 text-sm mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" class="glass-panel rounded-xl p-3 mb-4 grid grid-cols-1 md:grid-cols-4 gap-2">
            <div>
                <label class="block text-xs mb-1">Scope</label>
                <select name="scope" class="backend-field h-10 text-sm">
                    <option value="" {{ request('scope') ? '' : 'selected' }}>All</option>
                    <option value="unread" {{ request('scope') === 'unread' ? 'selected' : '' }}>Unread only</option>
                </select>
            </div>
            <div>
                <label class="block text-xs mb-1">Type</label>
                <select name="type" class="backend-field h-10 text-sm">
                    <option value="">All types</option>
                    @foreach(($availableTypes ?? collect()) as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 flex items-end gap-2">
                <button class="h-10 px-4 rounded-lg bg-orange hover:bg-orange-light text-white text-sm font-semibold transition">Apply</button>
                <a href="{{ route('notifications.index') }}" class="h-10 inline-flex items-center backend-btn-muted text-sm">Reset</a>
            </div>
        </form>

        <div class="space-y-2">
            @forelse($notifications as $notification)
                <div class="glass-panel border {{ $notification->read_at ? 'border-gray-200/70 dark:border-gray-800' : 'border-orange/40' }} rounded-xl p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-sm">{{ $notification->data['title'] ?? 'Notification' }}</p>
                            <p class="text-sm text-gray-600 dark:text-text-secondary mt-0.5">{{ $notification->data['message'] ?? 'You have a new notification.' }}</p>
                            <p class="text-xs text-gray-500 dark:text-text-secondary mt-1">{{ $notification->created_at->format('M d, Y H:i') }}</p>
                            @if(!empty($notification->data['order_id']))
                                <a href="{{ route('orders.show', $notification->data['order_id']) }}" class="inline-flex mt-1 text-xs font-semibold text-orange hover:text-orange-light">
                                    View Order #{{ $notification->data['order_id'] }}
                                </a>
                            @elseif(!empty($notification->data['booking_id']))
                                <a href="{{ route('bookings.show', $notification->data['booking_id']) }}" class="inline-flex mt-1 text-xs font-semibold text-orange hover:text-orange-light">
                                    View Booking #{{ $notification->data['booking_id'] }}
                                </a>
                            @elseif(!empty($notification->data['product_id']))
                                <a href="{{ route('products.upload.index') }}" class="inline-flex mt-1 text-xs font-semibold text-orange hover:text-orange-light">
                                    View My Uploads
                                </a>
                            @endif
                        </div>
                        @if(! $notification->read_at)
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="h-8 px-3 rounded-lg border border-orange/40 text-xs text-orange hover:bg-orange/10 font-semibold">Mark Read</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="glass-panel rounded-xl p-8 text-center text-gray-600 dark:text-text-secondary">
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
