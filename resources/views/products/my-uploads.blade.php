<x-app-layout>
    <section class="relative overflow-hidden py-20 bg-gradient-to-b from-white via-orange-50/40 to-white dark:from-dark dark:via-dark-secondary dark:to-dark">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange mb-3">Creator Tools</p>
                    <h1 class="text-5xl sm:text-6xl font-bold">My Uploads</h1>
                    <p class="text-gray-600 dark:text-text-secondary mt-3">Track moderation status, review feedback, and resubmit rejected products.</p>
                </div>
                <a href="{{ route('products.upload.create') }}" class="inline-flex items-center bg-orange hover:bg-orange-light text-white px-5 py-2.5 rounded-lg font-semibold transition">Upload New Product</a>
            </div>

            @if(session('success'))
                <div class="bg-green-500/20 border border-green-500 text-green-500 rounded-lg p-4 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <x-filter-bar :action="route('products.upload.index')" title="Filter Uploads" formClass="grid grid-cols-1 md:grid-cols-3 gap-2">
                @php($status = request('status', 'all'))
                <select name="status" class="backend-field h-10 text-sm">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <div class="flex gap-2">
                    <button class="h-10 bg-orange hover:bg-orange-light text-white px-5 rounded-lg text-sm font-semibold transition">Filter</button>
                    <a href="{{ route('products.upload.index') }}" class="h-10 inline-flex items-center backend-btn-muted text-sm">Reset</a>
                </div>
            </x-filter-bar>

            <div class="space-y-4">
                @forelse($products as $product)
                    <div class="bg-white dark:bg-dark-secondary rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <h2 class="text-2xl font-bold">{{ $product->name }}</h2>
                                    <span class="text-xs px-3 py-1 rounded-full
                                        @if($product->status === 'approved') bg-green-500/20 text-green-500
                                        @elseif($product->status === 'rejected') bg-red-500/20 text-red-500
                                        @else bg-yellow-500/20 text-yellow-500 @endif">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-text-secondary">Category: {{ $product->category?->name ?? 'N/A' }} • Price: ${{ number_format($product->price, 2) }}</p>
                                <p class="text-gray-600 dark:text-text-secondary mt-3">{{ $product->description }}</p>
                                @if($product->admin_review_comment)
                                    <div class="mt-4 rounded-lg border border-red-300/50 bg-red-50/60 dark:bg-red-500/10 dark:border-red-500/40 p-3">
                                        <p class="text-sm font-semibold text-red-600 dark:text-red-400">Admin feedback</p>
                                        <p class="text-sm text-red-600/90 dark:text-red-300 mt-1">{{ $product->admin_review_comment }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col gap-3 lg:w-56">
                                <a href="{{ route('products.show', $product) }}" class="text-center px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 hover:border-orange hover:text-orange transition">View Product</a>
                                <a href="{{ route('products.upload.edit', $product) }}" class="text-center px-4 py-2.5 rounded-lg bg-orange hover:bg-orange-light text-white font-semibold transition">
                                    {{ $product->status === 'rejected' ? 'Fix and Resubmit' : 'Edit Upload' }}
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-dark-secondary rounded-2xl border border-gray-200 dark:border-gray-800 text-center py-14 px-6">
                        <p class="text-gray-600 dark:text-text-secondary text-lg mb-4">No uploads found for this filter.</p>
                        <a href="{{ route('products.upload.create') }}" class="inline-flex bg-orange hover:bg-orange-light text-white px-6 py-3 rounded-lg font-semibold transition">Create your first upload</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $products->links() }}</div>
        </div>
    </section>
</x-app-layout>
