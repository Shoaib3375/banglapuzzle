<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\Auth\UserProfileController;
use App\Http\Controllers\Auth\UserRegisterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / storefront routes
|--------------------------------------------------------------------------
*/
Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/category/{slug}', [StorefrontController::class, 'category'])->name('storefront.category');
Route::get('/category/{categorySlug}/{subcategorySlug}', [StorefrontController::class, 'subcategory'])->name('storefront.subcategory');
Route::get('/product/{slug}', [StorefrontController::class, 'product'])->name('storefront.product');

Route::get('/cart', [StorefrontController::class, 'cart'])->name('storefront.cart');
Route::get('/checkout', [StorefrontController::class, 'checkout'])->name('storefront.checkout');

// Public order routes (guests allowed)
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store')->middleware('throttle:10,1');
Route::get('/orders/{order:order_number}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])
    ->name('admin.login');

Route::post('/admin/login', [AdminLoginController::class, 'login'])
    ->name('admin.login.post')
    ->middleware('throttle:5,1');

Route::post('/admin/logout', [AdminLoginController::class, 'logout'])
    ->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Customer (storefront user) auth & profile routes
|--------------------------------------------------------------------------
*/
// Guest-only customer auth routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [UserRegisterController::class, 'showRegisterForm'])->name('user.register');
    Route::post('/register', [UserRegisterController::class, 'register'])->name('user.register.post')->middleware('throttle:10,1');

    Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('user.login');
    Route::post('/login', [UserLoginController::class, 'login'])->name('user.login.post')->middleware('throttle:5,1');
});

// Logout (requires an authenticated session)
Route::post('/logout', [UserLoginController::class, 'logout'])->name('user.logout')->middleware('auth');

// Customer-only profile routes
Route::middleware(['auth', 'customer'])->group(function () {
    Route::get('/profile', [UserProfileController::class, 'show'])->name('storefront.profile');
    Route::post('/profile', [UserProfileController::class, 'update'])->name('storefront.profile.update');
    Route::post('/profile/password', [UserProfileController::class, 'changePassword'])->name('storefront.profile.password');

    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.index');
});

/*
|--------------------------------------------------------------------------
| Admin protected routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Categories
        Route::resource('categories', CategoryController::class);
        Route::post('categories/{category}/toggle-active', [CategoryController::class, 'toggleActive'])
            ->name('categories.toggleActive');

        // Subcategories
        Route::resource('subcategories', SubcategoryController::class);
        Route::post('subcategories/{subcategory}/toggle-active', [SubcategoryController::class, 'toggleActive'])
            ->name('subcategories.toggleActive');

        // AJAX: subcategories filtered by category
        Route::get('subcategories-by-category/{category}', [SubcategoryController::class, 'byCategory'])
            ->name('subcategories.byCategory');

        // Products
        Route::get('products/check-slug', [ProductController::class, 'checkSlug'])
            ->name('products.checkSlug');
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/toggle-active', [ProductController::class, 'toggleActive'])
            ->name('products.toggleActive');

        // Orders
        Route::resource('orders', AdminOrderController::class)->only(['index', 'show']);
        Route::post('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('orders.updateStatus');
    });
