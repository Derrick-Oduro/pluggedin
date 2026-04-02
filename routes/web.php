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
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
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
Route::view('/contact', 'contact')->name('contact');

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

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'store'])->name('orders.store');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create/{service}', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');

    // User Product Uploads
    Route::get('/products/upload', [UserProductController::class, 'create'])->name('products.upload.create');
    Route::post('/products/upload', [UserProductController::class, 'store'])->name('products.upload.store');

    // Product Reviews and Referrals
    Route::get('/my-reviews', [ProductReviewController::class, 'index'])->name('reviews.index');
    Route::post('/products/{product}/reviews', [ProductReviewController::class, 'store'])->name('products.reviews.store');
    Route::post('/reviews/{review}/report', [ProductReviewController::class, 'report'])->name('reviews.report');
    Route::post('/products/{product}/referral-links', [ReferralController::class, 'store'])->name('products.referrals.store');

    // User uploaded products management
    Route::get('/my-uploads', [UserProductController::class, 'index'])->name('products.upload.index');
    Route::get('/my-uploads/{product}/edit', [UserProductController::class, 'edit'])->name('products.upload.edit');
    Route::patch('/my-uploads/{product}', [UserProductController::class, 'update'])->name('products.upload.update');

    // User Dashboard
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notificationId}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/appearance', [SettingsController::class, 'appearance'])->name('settings.appearance');
    Route::post('/settings/appearance', [SettingsController::class, 'updateAppearance'])->name('settings.appearance.update');

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

    // User Management
    Route::resource('users', UserManagementController::class)->except(['show']);

    // Category Management
    Route::resource('categories', CategoryManagementController::class)->except(['show']);

    // Marketing and Homepage Controls
    Route::get('/marketing', [SuperAdminMarketingController::class, 'index'])->name('marketing.index');
    Route::post('/marketing/slides', [SuperAdminMarketingController::class, 'storeSlide'])->name('marketing.slides.store');
    Route::patch('/marketing/slides/{slide}', [SuperAdminMarketingController::class, 'updateSlide'])->name('marketing.slides.update');
    Route::delete('/marketing/slides/{slide}', [SuperAdminMarketingController::class, 'destroySlide'])->name('marketing.slides.destroy');

    Route::post('/marketing/campaigns', [SuperAdminMarketingController::class, 'storeCampaign'])->name('marketing.campaigns.store');
    Route::patch('/marketing/campaigns/{campaign}', [SuperAdminMarketingController::class, 'updateCampaign'])->name('marketing.campaigns.update');
    Route::delete('/marketing/campaigns/{campaign}', [SuperAdminMarketingController::class, 'destroyCampaign'])->name('marketing.campaigns.destroy');
});

require __DIR__.'/auth.php';
