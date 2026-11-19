@props([
    'categories',
    'product' => null,
    'defaultCondition' => null,
    'defaultStock' => 1,
])

@php
    $templates = \App\Support\ProductSpecs::templates();
    $conditions = \App\Support\ProductSpecs::conditions();
    $categoryTemplates = $categories->mapWithKeys(fn ($c) => [$c->id => $c->specTemplateKey()]);
    $selectedCategory = (string) old('category_id', $product?->category_id);
    $savedSpecs = old('specs', $product?->specs ?? []);
    $existingImages = $product?->galleryImages() ?? [];
@endphp

<div
    x-data="{
        map: @js($categoryTemplates),
        category: @js($selectedCategory),
        get template() { return this.map[this.category] ?? 'general' },
    }"
    class="space-y-6"
>
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-bold">Please fix the highlighted fields.</p>
            <ul class="mt-2 list-disc space-y-0.5 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 1. Basics --}}
    <section class="backend-card space-y-5">
        <div>
            <h2 class="text-lg font-extrabold tracking-[-.02em] text-stone-950">Product basics</h2>
            <p class="section-subtitle">Pick the category first. The spec fields below change to match it.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="pf-category" class="mb-1.5 block text-sm font-bold text-stone-800">Category <span class="text-brand">*</span></label>
                <select id="pf-category" name="category_id" x-model="category" required class="backend-field">
                    <option value="">Select category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($selectedCategory === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="pf-condition" class="mb-1.5 block text-sm font-bold text-stone-800">Condition <span class="text-brand">*</span></label>
                <select id="pf-condition" name="condition" required class="backend-field">
                    <option value="">Select condition</option>
                    @foreach ($conditions as $value => $label)
                        <option value="{{ $value }}" @selected(old('condition', $product?->condition ?? $defaultCondition) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('condition') <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="pf-name" class="mb-1.5 block text-sm font-bold text-stone-800">Listing title <span class="text-brand">*</span></label>
            <input id="pf-name" type="text" name="name" value="{{ old('name', $product?->name) }}" required maxlength="255"
                   placeholder="e.g. HP EliteBook 840 G8 - i5, 16GB RAM, 512GB SSD" class="backend-field">
            @error('name') <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="pf-description" class="mb-1.5 block text-sm font-bold text-stone-800">Description <span class="text-brand">*</span></label>
            <textarea id="pf-description" name="description" rows="5" required class="backend-field h-auto py-3"
                      placeholder="Describe the item, how it has been used, and anything a buyer should know.">{{ old('description', $product?->description) }}</textarea>
            @error('description') <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="pf-price" class="mb-1.5 block text-sm font-bold text-stone-800">Price (GH₵) <span class="text-brand">*</span></label>
                <input id="pf-price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $product?->price) }}" required class="backend-field">
                @error('price') <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="pf-stock" class="mb-1.5 block text-sm font-bold text-stone-800">Quantity in stock <span class="text-brand">*</span></label>
                <input id="pf-stock" type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', $product?->stock_quantity ?? $defaultStock) }}" required class="backend-field">
                @error('stock_quantity') <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    {{-- 2. Category-specific specs. Hidden templates are disabled so they don't submit. --}}
    @foreach ($templates as $templateKey => $template)
        <section x-show="template === @js($templateKey)" x-cloak class="backend-card space-y-6">
            <fieldset x-bind:disabled="template !== @js($templateKey)" class="space-y-6">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-extrabold tracking-[-.02em] text-stone-950">{{ $template['label'] }} specifications</h2>
                        <p class="section-subtitle">Fields marked <span class="font-bold text-brand">*</span> are required. Fill in as much as you can, because buyers filter and compare on these.</p>
                    </div>
                    <span class="brand-badge">{{ $template['label'] }}</span>
                </div>

                @foreach ($template['groups'] as $groupName => $fields)
                    <div>
                        @if (count($template['groups']) > 1)
                            <h3 class="mb-3 text-[11px] font-extrabold uppercase tracking-[.14em] text-stone-500">{{ $groupName }}</h3>
                        @endif
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($fields as $key => $field)
                                @php
                                    $id = "spec-{$templateKey}-{$key}";
                                    $value = $savedSpecs[$key] ?? '';
                                    $required = ! empty($field['required']);
                                @endphp
                                <div @class(['sm:col-span-2 lg:col-span-3' => $field['type'] === 'textarea'])>
                                    <label for="{{ $id }}" class="mb-1.5 block text-sm font-bold text-stone-800">
                                        {{ $field['label'] }}@if(! empty($field['suffix']) && $field['type'] === 'number') <span class="font-semibold text-stone-500">({{ $field['suffix'] }})</span>@endif
                                        @if($required)<span class="text-brand">*</span>@endif
                                    </label>

                                    @if ($field['type'] === 'select')
                                        <select id="{{ $id }}" name="specs[{{ $key }}]" class="backend-field" @required($required)>
                                            <option value="">{{ $required ? 'Select' : 'Not specified' }}</option>
                                            @foreach ($field['options'] as $option)
                                                <option value="{{ $option }}" @selected((string) $value === (string) $option)>
                                                    {{ $option }}{{ ! empty($field['suffix']) ? ' '.$field['suffix'] : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif ($field['type'] === 'textarea')
                                        <textarea id="{{ $id }}" name="specs[{{ $key }}]" rows="3" class="backend-field h-auto py-3"
                                                  placeholder="{{ $field['placeholder'] ?? '' }}" @required($required)>{{ $value }}</textarea>
                                    @else
                                        <input id="{{ $id }}" name="specs[{{ $key }}]" value="{{ $value }}"
                                               type="{{ $field['type'] === 'number' ? 'number' : 'text' }}"
                                               @if($field['type'] === 'number') step="any" min="0" @endif
                                               placeholder="{{ $field['placeholder'] ?? '' }}" class="backend-field" @required($required)>
                                    @endif

                                    @error("specs.$key") <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </fieldset>
        </section>
    @endforeach

    {{-- 3. Box contents and photos --}}
    <section class="backend-card space-y-5">
        <div>
            <h2 class="text-lg font-extrabold tracking-[-.02em] text-stone-950">What's included and photos</h2>
            <p class="section-subtitle">Clear photos of the actual item get approved and sold faster.</p>
        </div>

        <div>
            <label for="pf-included" class="mb-1.5 block text-sm font-bold text-stone-800">What's in the box</label>
            <textarea id="pf-included" name="whats_included" rows="2" class="backend-field h-auto py-3"
                      placeholder="e.g. Laptop, original 65W charger, sleeve">{{ old('whats_included', $product?->whats_included) }}</textarea>
            @error('whats_included') <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="pf-images" class="mb-1.5 block text-sm font-bold text-stone-800">Photos <span class="font-semibold text-stone-500">(up to 5, max 8 MB each)</span></label>
            @if ($existingImages)
                <div class="mb-3 flex flex-wrap gap-2">
                    @foreach ($existingImages as $image)
                        <img src="{{ asset('storage/'.$image) }}" alt="" class="h-16 w-16 rounded-lg border border-stone-200 object-cover">
                    @endforeach
                </div>
                <p class="mb-2 text-xs text-stone-500">Uploading new photos replaces the current ones.</p>
            @endif
            <input id="pf-images" type="file" name="images[]" multiple accept="image/*"
                   class="block w-full rounded-xl border border-dashed border-stone-300 bg-stone-50 p-3 text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand file:px-3 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-brand-deep">
            @error('images') <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
            @error('images.*') <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
        </div>
    </section>
</div>
