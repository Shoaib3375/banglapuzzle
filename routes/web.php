<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Auth\AdminLoginController;
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

/*
|--------------------------------------------------------------------------
| Admin authentication routes (public)
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])
    ->name('admin.login');

Route::post('/admin/login', [AdminLoginController::class, 'login'])
    ->name('admin.login.post')
    ->middleware('throttle:5,1');

Route::post('/admin/logout', [AdminLoginController::class, 'logout'])
    ->name('admin.logout');

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
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/toggle-active', [ProductController::class, 'toggleActive'])
            ->name('products.toggleActive');
    });
