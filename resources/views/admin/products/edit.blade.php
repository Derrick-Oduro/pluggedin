<x-product-form-page
    title="Edit product"
    subtitle="Update details, specifications, inventory and photos."
    badge="Admin catalog"
    :back-href="route('admin.products.index')"
    back-label="Back to products"
    :action="route('admin.products.update', $product)"
    method="PUT"
    submit-label="Save changes"
    :categories="$categories"
    :product="$product"
/>
