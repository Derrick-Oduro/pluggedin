<x-product-form-page
    title="Add shop inventory"
    subtitle="New shop products are reviewed before they appear in your public storefront."
    :badge="$shop->name"
    :back-href="route('shops.dashboard')"
    back-label="Back to My Shop"
    :action="route('shops.products.store')"
    submit-label="Submit for review"
    :categories="$categories"
/>
