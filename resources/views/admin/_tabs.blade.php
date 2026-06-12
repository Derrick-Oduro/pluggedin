<div class="backend-sidebar sticky top-24">
    <p class="text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-text-secondary mb-3">Admin Tabs</p>
    <nav class="space-y-1" id="admin-tabs">
        <a href="{{ route('admin.dashboard') }}" class="backend-tab" data-tab="dashboard">Dashboard</a>
        <a href="{{ route('admin.products.pending') }}" class="backend-tab" data-tab="pending">Moderation Queue</a>
        <a href="{{ route('admin.reviews.index') }}" class="backend-tab" data-tab="reviews">Review Moderation</a>
        <a href="{{ route('admin.products.index') }}" class="backend-tab" data-tab="products">Products</a>
        <a href="{{ route('admin.orders.index') }}" class="backend-tab" data-tab="orders">Orders</a>
        <a href="{{ route('admin.bookings.index') }}" class="backend-tab" data-tab="bookings">Bookings</a>
        <a href="{{ route('admin.referrals.index') }}" class="backend-tab" data-tab="referrals">Referral Analytics</a>
        @if(auth()->user()->hasRole('super-admin'))
            <a href="{{ route('superadmin.dashboard') }}" class="backend-tab text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10" data-tab="superadmin">Super Admin Panel</a>
        @endif
    </nav>
</div>

<script>
    (function(){
        const STORAGE_KEY = 'pluggedin_admin_active_tab';
        const tabs = document.querySelectorAll('#admin-tabs .backend-tab');

        function setActiveByKey(key){
            tabs.forEach(a => a.classList.remove('backend-tab-active'));
            const match = Array.from(tabs).find(a => a.dataset.tab === key);
            if(match) match.classList.add('backend-tab-active');
        }

        function setActiveByUrl(){
            const currentPath = window.location.pathname.replace(/\/$/, '');
            const match = Array.from(tabs).find(a => {
                try{ return new URL(a.href).pathname.replace(/\/$/, '') === currentPath; }catch(e){return false}
            });
            if(match){
                tabs.forEach(a => a.classList.remove('backend-tab-active'));
                match.classList.add('backend-tab-active');
            }
        }

        // Apply stored selection, otherwise fallback to URL-based
        document.addEventListener('DOMContentLoaded', function(){
            const stored = localStorage.getItem(STORAGE_KEY);
            if(stored){
                setActiveByKey(stored);
            } else {
                setActiveByUrl();
            }

            // Save selection on click so it persists across full-page navigations
            tabs.forEach(a => {
                a.addEventListener('click', function(){
                    if(this.dataset.tab){
                        try{ localStorage.setItem(STORAGE_KEY, this.dataset.tab); }catch(e){}
                    }
                });
            });
        });
    })();
</script>
