<x-app-layout>
    @php
        $unreadCount = auth()->user()->unreadNotifications()->count();
    @endphp

    <section class="page-shell">
        <div class="content-wrap relative z-10 max-w-5xl">
            <header class="flex flex-col justify-between gap-5 border-b border-stone-200 pb-7 sm:flex-row sm:items-end">
                <div>
                    <span class="brand-badge">Your account</span>
                    <h1 class="section-title mt-4">Updates, in one place.</h1>
                    <p class="section-subtitle mt-3">Orders, service bookings, and marketplace activity—without the noise.</p>
                </div>
                @if ($unreadCount > 0)
                    <form action="{{ route('notifications.read-all') }}" method="POST">
                        @csrf
                        <button class="btn-secondary">Mark all as seen</button>
                    </form>
                @endif
            </header>

            <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                <div class="flex gap-2">
                    <a href="{{ route('notifications.index', request()->except(['scope', 'page'])) }}" class="rounded-full px-4 py-2 text-sm font-bold {{ request('scope') !== 'unread' ? 'bg-stone-950 text-white' : 'bg-white text-stone-600 hover:bg-stone-100' }}">All updates</a>
                    <a href="{{ route('notifications.index', array_merge(request()->except('page'), ['scope' => 'unread'])) }}" class="rounded-full px-4 py-2 text-sm font-bold {{ request('scope') === 'unread' ? 'bg-stone-950 text-white' : 'bg-white text-stone-600 hover:bg-stone-100' }}">
                        Unread
                        @if ($unreadCount)
                            <span class="ml-1 text-brand-light">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </div>

                @if (($availableTypes ?? collect())->isNotEmpty())
                    <form method="GET">
                        <input type="hidden" name="scope" value="{{ request('scope') }}">
                        <select name="type" onchange="this.form.submit()" class="h-10 rounded-xl border-stone-200 bg-white py-0 text-sm font-bold text-stone-600 focus:border-brand focus:ring-brand">
                            <option value="">Every type</option>
                            @foreach ($availableTypes as $type)
                                <option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif
            </div>

            <div class="mt-6 space-y-3">
                @forelse ($notifications as $notification)
                    @php
                        $type = $notification->data['type'] ?? 'general';
                        $isOrder = str_contains($type, 'order');
                        $isBooking = str_contains($type, 'booking');
                    @endphp

                    <article class="relative rounded-2xl border p-5 transition {{ $notification->read_at ? 'border-stone-200 bg-white' : 'border-brand-soft bg-brand-tint/40 shadow-[0_14px_30px_-26px_rgba(73,38,22,0.4)]' }}">
                        @if (! $notification->read_at)
                            <span class="absolute left-0 top-7 h-8 w-1 rounded-r-full bg-brand"></span>
                        @endif

                        <div class="flex gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $isOrder ? 'bg-sky-100 text-sky-800' : ($isBooking ? 'bg-violet-100 text-violet-800' : 'bg-brand-soft text-brand-deep') }}">
                                @if ($isOrder)
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M6 10h12M7 14h10M9 18h6" /></svg>
                                @elseif ($isBooking)
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" /></svg>
                                @else
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9 2.2 18a2 2 0 0 0 1.74 3h16.12A2 2 0 0 0 21.8 18L13.7 3.9a2 2 0 0 0-3.4 0Z" /></svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col justify-between gap-1 sm:flex-row">
                                    <h2 class="font-extrabold text-stone-950">{{ $notification->data['title'] ?? 'Account update' }}</h2>
                                    <time class="shrink-0 text-xs font-semibold text-stone-500" datetime="{{ $notification->created_at->toIso8601String() }}">{{ $notification->created_at->diffForHumans() }}</time>
                                </div>
                                <p class="mt-1 text-sm leading-6 text-stone-600">{{ $notification->data['message'] ?? 'There is a new update for your account.' }}</p>

                                <div class="mt-4 flex flex-wrap items-center gap-4">
                                    @if (! empty($notification->data['order_id']))
                                        <a href="{{ route('orders.show', $notification->data['order_id']) }}" class="text-sm font-extrabold text-brand-deep hover:text-brand-deep">View order →</a>
                                    @elseif (! empty($notification->data['booking_id']))
                                        <a href="{{ route('bookings.show', $notification->data['booking_id']) }}" class="text-sm font-extrabold text-brand-deep hover:text-brand-deep">View booking →</a>
                                    @elseif (! empty($notification->data['product_id']))
                                        <a href="{{ route('products.upload.index') }}" class="text-sm font-extrabold text-brand-deep hover:text-brand-deep">View my upload →</a>
                                    @endif

                                    @if (! $notification->read_at)
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button class="text-sm font-bold text-stone-500 hover:text-stone-950">Mark as seen</button>
                                        </form>
                                    @endif

                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Remove this notification?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-sm font-bold text-stone-400 hover:text-red-700">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-stone-300 bg-white p-12 text-center">
                        <p class="font-extrabold text-stone-950">You’re all caught up.</p>
                        <p class="mt-1 text-sm text-stone-600">New account updates will appear here.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">{{ $notifications->links() }}</div>
        </div>
    </section>
</x-app-layout>
