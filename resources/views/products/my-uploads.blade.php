<x-app-layout>
    @php $status = request('status', 'all'); @endphp
    <section class="page-shell">
        <div class="content-wrap relative z-10">
            <x-page-header badge="Selling" title="My uploads" subtitle="Track review status, read reviewer feedback and manage your live listings.">
                <x-slot:actions>
                    <a href="{{ route('products.upload.create') }}" class="btn-primary">Upload new product</a>
                </x-slot:actions>
            </x-page-header>

            <nav class="mb-6 flex flex-wrap gap-2" aria-label="Filter by status">
                @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                    <a href="{{ route('products.upload.index', $value === 'all' ? [] : ['status' => $value]) }}"
                       @class(['rounded-full px-4 py-2 text-sm font-bold transition', 'bg-stone-950 text-white' => $status === $value, 'border border-stone-200 bg-white text-stone-600 hover:border-stone-300 hover:text-stone-950' => $status !== $value])>{{ $label }}</a>
                @endforeach
            </nav>

            <div class="space-y-4">
                @forelse($products as $product)
                    <article class="backend-card">
                        <div class="flex flex-col gap-5 lg:flex-row">
                            <div class="h-28 w-full shrink-0 overflow-hidden rounded-xl bg-stone-100 lg:w-36">
                                @if($product->image_path)<img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">@endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-lg font-extrabold text-stone-950">{{ $product->name }}</h2>
                                    <x-status-badge :status="$product->status" />
                                    @if($product->status === 'approved' && $product->listing_state !== 'active')
                                        <x-status-badge :status="$product->listing_state" />
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-stone-500">{{ $product->category?->name ?? 'N/A' }} · GH₵{{ number_format($product->price, 2) }}@if($product->conditionLabel()) · {{ $product->conditionLabel() }}@endif</p>
                                <p class="mt-2 line-clamp-2 text-sm text-stone-600">{{ $product->description }}</p>
                                @if($product->admin_review_comment)
                                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                                        <p class="font-bold">Reviewer feedback</p>
                                        <p class="mt-1">{{ $product->admin_review_comment }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="flex shrink-0 flex-col gap-2 lg:w-52">
                                <a href="{{ route('products.upload.edit', $product) }}" class="btn-primary">{{ $product->status === 'rejected' ? 'Fix and resubmit' : 'Edit listing' }}</a>
                                <a href="{{ route('products.show', $product) }}" class="btn-secondary">View listing</a>
                                @if($product->status === 'approved')
                                    <form method="POST" action="{{ route('products.upload.state', $product) }}">@csrf @method('PATCH')<input type="hidden" name="listing_state" value="{{ $product->listing_state === 'active' ? 'paused' : 'active' }}"><button class="btn-secondary w-full">{{ $product->listing_state === 'active' ? 'Pause listing' : 'Reactivate listing' }}</button></form>
                                    @if($product->listing_state !== 'sold')
                                        <form method="POST" action="{{ route('products.upload.state', $product) }}">@csrf @method('PATCH')<input type="hidden" name="listing_state" value="sold"><button class="btn-secondary w-full">Mark as sold</button></form>
                                    @endif
                                @endif
                                <form method="POST" action="{{ route('products.upload.destroy', $product) }}" onsubmit="return confirm('Remove this listing permanently?')">@csrf @method('DELETE')<button class="inline-flex h-11 w-full items-center justify-center rounded-xl text-sm font-bold text-red-600 transition hover:bg-red-50">Remove listing</button></form>
                            </div>
                        </div>
                    </article>
                @empty
                    <x-empty-state :title="$status === 'all' ? 'No uploads yet' : 'No '.$status.' uploads'" message="List a device or part you want to sell. Our team reviews it before it goes live." action-label="Create your first upload" :action-href="route('products.upload.create')" />
                @endforelse
            </div>

            <div class="mt-8">{{ $products->links() }}</div>
        </div>
    </section>
</x-app-layout>
