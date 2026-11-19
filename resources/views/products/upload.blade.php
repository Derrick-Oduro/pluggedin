<x-product-form-page
    title="Sell your tech"
    subtitle="Listings are reviewed by the PluggedIn team before they go live. Monthly limit: {{ $used }}/{{ $limit }} used ({{ $remaining }} remaining)."
    badge="New listing"
    :back-href="route('user.dashboard')"
    back-label="Back to dashboard"
    :action="route('products.upload.store')"
    submit-label="Submit for review"
    :categories="$categories"
/>
