@section('page-title', 'Categories')

<x-superadmin-layout>
    <div class="space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="brand-badge">Catalogue</p>
                <h1 class="mt-3 text-3xl font-extrabold tracking-[-0.04em] text-stone-950">Categories</h1>
                <p class="mt-1 text-sm text-stone-600">Keep product browsing clear with a simple, well-organised category list.</p>
            </div>
            <a href="{{ route('superadmin.categories.index', ['modal' => 'create-category']) }}" class="backend-btn-primary">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Add category
            </a>
        </div>

        @if($categories->count())
            <div class="backend-table-wrap">
                <div class="flex items-center justify-between border-b border-stone-100 px-5 py-4">
                    <p class="text-sm font-bold text-stone-950">All categories</p>
                    <p class="text-xs font-semibold text-stone-500">{{ $categories->total() }} total</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="backend-table min-w-[620px]">
                        <thead><tr><th class="backend-th">Category</th><th class="backend-th">URL name</th><th class="backend-th">Spec fields</th><th class="backend-th">Products</th><th class="backend-th text-right">Actions</th></tr></thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr class="backend-row">
                                    <td class="backend-td"><span class="font-bold text-stone-950">{{ $category->name }}</span></td>
                                    <td class="backend-td text-stone-500">/{{ $category->slug }}</td><td class="backend-td"><span class="brand-badge">{{ \App\Support\ProductSpecs::templates()[$category->specTemplateKey()]['label'] }}</span>@unless($category->spec_template)<span class="ml-1 text-xs text-stone-400">auto</span>@endunless</td>
                                    <td class="backend-td"><span class="inline-flex rounded-full bg-brand-tint px-2.5 py-1 text-xs font-bold text-brand-deep">{{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}</span></td>
                                    <td class="backend-td"><div class="flex justify-end gap-3"><a class="text-sm font-bold text-brand hover:text-brand-deep" href="{{ route('superadmin.categories.index', ['modal' => 'edit-category-'.$category->id]) }}">Edit</a><a class="text-sm font-bold text-red-600 hover:text-red-800" href="{{ route('superadmin.categories.index', ['modal' => 'delete-category-'.$category->id]) }}">Delete</a></div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="backend-card py-14 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-tint text-brand"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg></div>
                <h2 class="mt-4 text-lg font-extrabold text-stone-950">Start your catalogue</h2><p class="mt-1 text-sm text-stone-600">Add a category to organise products for customers.</p>
                <a href="{{ route('superadmin.categories.index', ['modal' => 'create-category']) }}" class="mt-5 backend-btn-primary">Add category</a>
            </div>
        @endif

        <div>{{ $categories->links() }}</div>
    </div>

    <x-modal name="create-category" maxWidth="md" focusable :show="old('form_context') === 'create-category' || request('modal') === 'create-category'">
        <form action="{{ route('superadmin.categories.store') }}" method="POST">
            @csrf <input type="hidden" name="form_context" value="create-category">
            <div class="border-b border-stone-200 px-6 py-5"><p class="brand-badge">New category</p><h2 class="mt-3 text-xl font-extrabold tracking-tight text-stone-950">Add a category</h2><p class="mt-1 text-sm text-stone-600">The URL name is created automatically from the category name.</p></div>
            <div class="px-6 py-5"><label for="category-name" class="mb-2 block text-sm font-bold text-stone-800">Category name</label><input id="category-name" class="backend-field" type="text" name="name" value="{{ old('form_context') === 'create-category' ? old('name') : '' }}" placeholder="For example, Phones" required autofocus>@error('name') @if(old('form_context') === 'create-category')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@endif @enderror</div><div class="px-6 pb-5"><label for="category-template" class="mb-2 block text-sm font-bold text-stone-800">Spec fields</label><select id="category-template" name="spec_template" class="backend-field"><option value="">Detect from name</option>@foreach(\App\Support\ProductSpecs::templateOptions() as $key => $label)<option value="{{ $key }}" @selected((old('form_context') === 'create-category' ? old('spec_template') : null) === $key)>{{ $label }}</option>@endforeach</select><p class="mt-2 text-xs text-stone-500">Controls which specification fields sellers fill in, for example Laptop shows CPU, RAM, storage, screen and battery.</p></div>
            <div class="flex justify-end gap-3 border-t border-stone-200 bg-stone-50 px-6 py-4"><a href="{{ route('superadmin.categories.index') }}" class="btn-secondary">Cancel</a><button type="submit" class="backend-btn-primary">Save category</button></div>
        </form>
    </x-modal>

    @foreach($categories as $category)
        <x-modal name="edit-category-{{ $category->id }}" maxWidth="md" focusable :show="old('form_context') === 'edit-category-'.$category->id || request('modal') === 'edit-category-'.$category->id">
            <form action="{{ route('superadmin.categories.update', $category) }}" method="POST">
                @csrf @method('PUT') <input type="hidden" name="form_context" value="edit-category-{{ $category->id }}">
                <div class="border-b border-stone-200 px-6 py-5"><p class="brand-badge">Edit category</p><h2 class="mt-3 text-xl font-extrabold tracking-tight text-stone-950">{{ $category->name }}</h2><p class="mt-1 text-sm text-stone-600">Changing the name also updates its URL name.</p></div>
                <div class="px-6 py-5"><label for="category-name-{{ $category->id }}" class="mb-2 block text-sm font-bold text-stone-800">Category name</label><input id="category-name-{{ $category->id }}" class="backend-field" type="text" name="name" value="{{ old('form_context') === 'edit-category-'.$category->id ? old('name') : $category->name }}" required>@error('name') @if(old('form_context') === 'edit-category-'.$category->id)<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@endif @enderror</div><div class="px-6 pb-5"><label for="category-template-{{ $category->id }}" class="mb-2 block text-sm font-bold text-stone-800">Spec fields</label><select id="category-template-{{ $category->id }}" name="spec_template" class="backend-field"><option value="">Detect from name</option>@foreach(\App\Support\ProductSpecs::templateOptions() as $key => $label)<option value="{{ $key }}" @selected((old('form_context') === 'edit-category-'.$category->id ? old('spec_template') : $category->spec_template) === $key)>{{ $label }}</option>@endforeach</select><p class="mt-2 text-xs text-stone-500">Controls which specification fields sellers fill in, for example Laptop shows CPU, RAM, storage, screen and battery.</p></div>
                <div class="flex justify-end gap-3 border-t border-stone-200 bg-stone-50 px-6 py-4"><a href="{{ route('superadmin.categories.index') }}" class="btn-secondary">Cancel</a><button type="submit" class="backend-btn-primary">Save changes</button></div>
            </form>
        </x-modal>

        <x-modal name="delete-category-{{ $category->id }}" maxWidth="sm" :show="request('modal') === 'delete-category-'.$category->id">
            <div class="px-6 py-6"><div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-red-50 text-red-600"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m15 9-6 6m0-6 6 6M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Z" /></svg></div><h2 class="mt-4 text-xl font-extrabold text-stone-950">Delete {{ $category->name }}?</h2><p class="mt-2 text-sm leading-6 text-stone-600">@if($category->products_count) This category has {{ $category->products_count }} products, so it cannot be deleted. @else This action cannot be undone. @endif</p></div>
            <div class="flex justify-end gap-3 border-t border-stone-200 bg-stone-50 px-6 py-4"><a href="{{ route('superadmin.categories.index') }}" class="btn-secondary">Cancel</a>@if(! $category->products_count)<form action="{{ route('superadmin.categories.destroy', $category) }}" method="POST">@csrf @method('DELETE')<button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-red-600 px-4 text-sm font-bold text-white transition hover:bg-red-700">Delete category</button></form>@endif</div>
        </x-modal>
    @endforeach
</x-superadmin-layout>
