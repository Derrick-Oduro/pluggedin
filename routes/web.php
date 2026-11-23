<?php

use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\CategoryManagementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\SuperAdminMarketingController;
use App\Http\Controllers\Admin\SuperAdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\TradeInManagementController;
use App\Http\Controllers\Admin\ShopManagementController;
use App\Http\Controllers\Admin\ProductReportManagementController;
use App\Http\Controllers\Admin\SellerVerificationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\TradeInController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\ProductReportController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}/r/{code}', [ReferralController::class, 'track'])->whereNumber('product')->name('products.referrals.track');
Route::get('/products/{product}', [ProductController::class, 'show'])->whereNumber('product')->name('products.show');

// Services
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

// About & Contact (static pages for now)
Route::view('/about', 'about')->name('about');
Route::view('/campuses', 'campuses')->name('campuses');
Route::view('/contact', 'contact')->name('contact');
Route::view('/terms', 'legal.terms')->name('legal.terms');
Route::view('/privacy', 'legal.privacy')->name('legal.privacy');
Route::post('/contact', [ContactMessageController::class, 'store'])->middleware('throttle:6,1')->name('contact.store');
Route::get('/search', [GlobalSearchController::class, 'index'])->name('search.global');
Route::get('/shops/{shop:slug}', [ShopController::class, 'show'])->name('shops.show');
Route::get('/sellers/{seller}', [SellerController::class, 'show'])->name('sellers.show');

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/move-all-to-cart', [WishlistController::class, 'moveAllToCart'])->name('wishlist.move-all-to-cart'); // before {product}
    Route::post('/wishlist/{product}', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{wishlistItem}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/wishlist/{wishlistItem}/move-to-cart', [WishlistController::class, 'moveToCart'])->name('wishlist.move-to-cart');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/pay', [OrderController::class, 'pay'])->middleware('throttle:checkout')->name('orders.pay');
    Route::get('/payments/paystack/callback', [OrderController::class, 'paystackCallback'])->name('payments.paystack.callback');
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'store'])->middleware('throttle:checkout')->name('orders.store');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create/{service}', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->middleware('throttle:submissions')->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');

    // User Product Uploads
    Route::get('/products/upload', [UserProductController::class, 'create'])->name('products.upload.create');
    Route::post('/products/upload', [UserProductController::class, 'store'])->middleware('throttle:submissions')->name('products.upload.store');

    // PluggedIn trade-in programme (direct exchange quotes, not peer swaps)
    Route::get('/trade-in', [TradeInController::class, 'create'])->name('trade-ins.create');
    Route::post('/trade-in', [TradeInController::class, 'store'])->middleware('throttle:submissions')->name('trade-ins.store');
    Route::get('/my-trade-ins', [TradeInController::class, 'index'])->name('trade-ins.index');
    Route::patch('/my-trade-ins/{tradeIn}/respond', [TradeInController::class, 'respond'])->name('trade-ins.respond');
    Route::get('/open-a-shop', [ShopController::class, 'create'])->name('shops.create');
    Route::post('/open-a-shop', [ShopController::class, 'store'])->middleware('throttle:submissions')->name('shops.store');
    Route::get('/my-shop', [ShopController::class, 'dashboard'])->name('shops.dashboard');
    Route::get('/my-shop/products/create', [ShopController::class, 'createProduct'])->name('shops.products.create');
    Route::post('/my-shop/products', [ShopController::class, 'storeProduct'])->middleware('throttle:submissions')->name('shops.products.store');

    // Product Reviews and Referrals
    Route::get('/my-reviews', [ProductReviewController::class, 'index'])->name('reviews.index');
    Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])->middleware('throttle:submissions')->name('products.reviews.store');
    Route::post('/products/{product}/report', [ProductReportController::class, 'store'])->middleware('throttle:submissions')->name('products.report.store');
    Route::post('/reviews/{review}/report', [ProductReviewController::class, 'report'])->middleware('throttle:submissions')->name('reviews.report');
    Route::post('/products/{product}/referral-links', [ReferralController::class, 'store'])->middleware('throttle:submissions')->name('products.referrals.store');

    // User uploaded products management
    Route::get('/my-uploads', [UserProductController::class, 'index'])->name('products.upload.index');
    Route::get('/my-uploads/{product}/edit', [UserProductController::class, 'edit'])->name('products.upload.edit');
    Route::patch('/my-uploads/{product}', [UserProductController::class, 'update'])->name('products.upload.update');
    Route::patch('/my-uploads/{product}/listing-state', [UserProductController::class, 'changeListingState'])->name('products.upload.state');
    Route::delete('/my-uploads/{product}', [UserProductController::class, 'destroy'])->name('products.upload.destroy');

    // User Dashboard
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notificationId}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::delete('/notifications/{notificationId}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');

    // Dashboard (redirects based on role)
    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('super-admin')) {
            return redirect()->route('superadmin.dashboard');
        }
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    })->name('dashboard');
});

Route::post('/payments/paystack/webhook', [OrderController::class, 'paystackWebhook'])->name('payments.paystack.webhook');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Products Management
    Route::get('/products/pending', [AdminProductController::class, 'pending'])->name('products.pending');
    Route::post('/products/pending/bulk-review', [AdminProductController::class, 'bulkReview'])->name('products.bulk-review');
    Route::patch('/products/{product}/review', [AdminProductController::class, 'review'])->name('products.review');
    Route::resource('products', AdminProductController::class);

    // Reviews Moderation
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/moderate', [AdminReviewController::class, 'moderate'])->name('reviews.moderate');

    // Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/orders/{order}/cash-received', [AdminOrderController::class, 'markCashReceived'])->name('orders.cashReceived');

    // Referral Monitoring
    Route::get('/referrals', [AdminDashboard::class, 'referrals'])->name('referrals.index');

    // Bookings Management
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.updateStatus');
});

// Super Admin Routes
Route::prefix('superadmin')->middleware(['auth', 'superadmin'])->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/audit-logs', [SuperAdminDashboardController::class, 'auditLogs'])->name('audit-logs.index');
    Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::patch('/contact-messages/{message}', [ContactMessageController::class, 'update'])->name('contact-messages.update');
    Route::get('/trade-ins', [TradeInManagementController::class, 'index'])->name('trade-ins.index');
    Route::patch('/trade-ins/{tradeIn}/review', [TradeInManagementController::class, 'review'])->name('trade-ins.review');
    Route::patch('/trade-ins/{tradeIn}/quote', [TradeInManagementController::class, 'quote'])->name('trade-ins.quote');
    Route::patch('/trade-ins/{tradeIn}/complete', [TradeInManagementController::class, 'complete'])->name('trade-ins.complete');
    Route::get('/shops', [ShopManagementController::class, 'index'])->name('shops.index');
    Route::patch('/shops/{shop}', [ShopManagementController::class, 'update'])->name('shops.update');
    Route::get('/product-reports', [ProductReportManagementController::class, 'index'])->name('product-reports.index');
    Route::patch('/product-reports/{report}', [ProductReportManagementController::class, 'update'])->name('product-reports.update');
    Route::get('/sellers', [SellerVerificationController::class, 'index'])->name('sellers.index');
    Route::patch('/sellers/{seller}/verification', [SellerVerificationController::class, 'update'])->name('sellers.verification');

    // User Management
    Route::resource('users', UserManagementController::class)->except(['show']);

    // Category Management
    Route::resource('categories', CategoryManagementController::class)->except(['show']);


    // Marketing and Homepage Controls
    Route::get('/marketing', [SuperAdminMarketingController::class, 'index'])->name('marketing.index');
    Route::post('/marketing/slides', [SuperAdminMarketingController::class, 'storeSlide'])->name('marketing.slides.store');
    Route::post('/marketing/slides/select-live', [SuperAdminMarketingController::class, 'selectLiveSlide'])->name('marketing.slides.select-live');
    Route::post('/marketing/slides/{slide}/make-live', [SuperAdminMarketingController::class, 'makeSlideLive'])->name('marketing.slides.make-live');
    Route::post('/marketing/slides/{slide}/take-off', [SuperAdminMarketingController::class, 'takeSlideOffHomepage'])->name('marketing.slides.take-off');
    Route::patch('/marketing/slides/{slide}', [SuperAdminMarketingController::class, 'updateSlide'])->name('marketing.slides.update');
    Route::delete('/marketing/slides/{slide}', [SuperAdminMarketingController::class, 'destroySlide'])->name('marketing.slides.destroy');
    Route::post('/marketing/slides/{id}/restore', [SuperAdminMarketingController::class, 'restoreSlide'])->name('marketing.slides.restore');

    Route::post('/marketing/campaigns', [SuperAdminMarketingController::class, 'storeCampaign'])->name('marketing.campaigns.store');
    Route::patch('/marketing/campaigns/{campaign}', [SuperAdminMarketingController::class, 'updateCampaign'])->name('marketing.campaigns.update');
    Route::patch('/marketing/campaigns/{campaign}/state', [SuperAdminMarketingController::class, 'changeCampaignState'])->name('marketing.campaigns.state');
    Route::post('/marketing/campaigns/{campaign}/duplicate', [SuperAdminMarketingController::class, 'duplicateCampaign'])->name('marketing.campaigns.duplicate');
    Route::delete('/marketing/campaigns/{campaign}', [SuperAdminMarketingController::class, 'destroyCampaign'])->name('marketing.campaigns.destroy');
    Route::post('/marketing/campaigns/{id}/restore', [SuperAdminMarketingController::class, 'restoreCampaign'])->name('marketing.campaigns.restore');

    // Site-wide Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
