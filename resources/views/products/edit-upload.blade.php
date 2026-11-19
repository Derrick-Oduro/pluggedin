<x-product-form-page
    title="Edit listing"
    subtitle="Saving changes sends this listing back to the review queue."
    badge="My uploads"
    :back-href="route('products.upload.index')"
    back-label="Back to my uploads"
    :action="route('products.upload.update', $product)"
    method="PATCH"
    submit-label="Save and resubmit"
    :categories="$categories"
    :product="$product"
>
    @if ($product->admin_review_comment)
        <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
            <p class="font-bold">Reviewer note</p>
            <p class="mt-1">{{ $product->admin_review_comment }}</p>
        </div>
    @endif
</x-product-form-page>
