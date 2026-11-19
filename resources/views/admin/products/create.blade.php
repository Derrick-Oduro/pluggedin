<x-product-form-page
    title="Add new product"
    subtitle="Catalog products created by admins are published immediately."
    badge="Admin catalog"
    :back-href="route('admin.products.index')"
    back-label="Back to products"
    :action="route('admin.products.store')"
    submit-label="Create product"
    :categories="$categories"
    default-condition="new"
    :default-stock="1"
/>
