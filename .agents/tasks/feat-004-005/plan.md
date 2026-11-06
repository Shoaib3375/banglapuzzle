# Implementation Plan — FEAT-004 (Public Storefront + SEO) & FEAT-005 (PHPUnit Tests + README + VPS_DEPLOYMENT.md)

BanglaPuzzle Laravel 13 e-commerce build. FEAT-001/002/003 are DONE and COMMITTED on
`main` — this plan does not touch them except one required, minimal, low-risk fix to
`User::$fillable` (see FEAT-005 item 1) that is needed for the documented demo admin to
actually work. Do NOT redo prior FEATs.

Environment: Windows + PowerShell, PHP 8.4 (`C:\php8\php.exe`), Composer, Laravel 13.40,
MySQL 8.0 (DB `banglapuzzle`, root, 127.0.0.1:3306). Tests run on SQLite in-memory
(`phpunit.xml`: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`) — no MySQL needed for tests.
Tailwind CSS v4 is configured via `@tailwindcss/vite` in `vite.config.js` and
`@import 'tailwindcss'` in `resources/css/app.css` — do NOT add `tailwind.config.js` and do
NOT downgrade to v3. `.env` already has `SESSION_DRIVER=database`, `CACHE_STORE=database`.

Verified baseline before this work: `php artisan test` → **17 tests, 51 assertions, 0
failures** (includes existing `AdminDashboardTest` + `ProductCrudTest`).

Decision on workflow restructuring: the work is already two coherent, sequential FEATs
(FEAT-004 → FEAT-005, where FEAT-005's `StorefrontTest` depends on FEAT-004's routes/
controller/views). It does not decompose further into separable features, so this plan is
written as a single plan file. The existing workflow tail (`code-feat-004` → `code-feat-005`
→ `converge-loop` → `finalize`) implements it. The converge loop's stop contract must hold:
it stops ONLY when `e:\interview\BanglaPuzzle\.agents\tasks\feat-004-005\review.json` has
JSON path `verdict == "APPROVED"`, written by the `semantic_reviewer` as the loop's last
step.

---

## 1. Existing conventions to follow (read these files; do NOT modify unless listed)

Confirmed by reading the repo. Reuse these exactly.

- **Admin route names** (in `routes/web.php`): `admin.login`, `admin.login.post`,
  `admin.logout`, `admin.dashboard`, `admin.categories.*` (resource +
  `admin.categories.toggleActive`), `admin.subcategories.*` (resource +
  `admin.subcategories.toggleActive` + `admin.subcategories.byCategory`), `admin.products.*`
  (resource + `admin.products.toggleActive`). Admin group is
  `Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(...)`.
- **Middleware alias**: `'admin' => \App\Http\Middleware\EnsureIsAdmin::class` registered in
  `bootstrap/app.php` via `->withMiddleware(fn (Middleware $m) => $m->alias([...]))`
  (Laravel 13 fluent API; there is NO `Http/Kernel.php`). `EnsureIsAdmin` redirects non-admins
  to `route('admin.login')` (it does not abort 403). `redirectGuestsTo` is set to
  `route('admin.login')` — so unauthenticated admin requests redirect to admin login (this is
  what `AdminDashboardTest::test_guest_cannot_access_dashboard` asserts).
- **Auto-slug boot()**: `Category`, `Subcategory`, `Product` each have a `static::boot()` that
  auto-generates a unique slug on `creating`/`updating` when `slug` is empty, via
  `generateUniqueSlug(...)`. `Subcategory::generateUniqueSlug` scopes uniqueness to its
  `category_id`. Storefront relies on these slugs — never duplicate/overwrite them.
- **Discount accessor**: `Product::getDiscountPercentageAttribute()` returns an `?int`
  (rounded percentage) when `old_price > new_price > 0`, else `null`. Access as
  `$product->discount_percentage`. Storefront renders the discount badge from this.
- **Active scopes**: `Category::scopeActive`, `Subcategory::scopeActive`, `Product::scopeActive`
  all filter `is_active = true`. Use them (`->active()`) for storefront visibility.
- **Relationships**: `Category->hasMany(Subcategory)`, `Category->hasMany(Product)`;
  `Subcategory->belongsTo(Category)`, `Subcategory->hasMany(Product)`; `Product->belongsTo(Category)`,
  `Product->belongsTo(Subcategory)`. Eager-load with `->with(['category','subcategory'])` (avoids N+1,
  matches admin controllers).
- **Storage convention**: `App\Services\ProductImageService` stores product files via
  `Storage::disk('public')->putFileAs('products', $file, Str::uuid().'.'.ext)` and returns the
  relative path `products/<uuid>.<ext>` (stored in `products.image`). Storefront image URLs MUST
  use `Storage::disk('public')->url($product->image)` (NOT `asset()`), matching
  `admin/products/index.blade.php`. When `$product->image` is null, render the placeholder
  `asset('images/placeholder.png')` (same pattern as admin index).
- **Form Request patterns**: all admin store/update requests extend `FormRequest`, return
  `authorize(): true`, and define `rules()`. `StoreProductRequest`/`UpdateProductRequest` use
  `withValidator(Validator $validator)` with `$validator->after(...)` to enforce that the
  chosen subcategory belongs to the chosen category, and a custom `messages()` for
  `new_price.lte`. `StoreSubcategoryRequest` uses `Rule::unique('subcategories','slug')->where('category_id', ...)`.
  Reuse these patterns for any new requests (FEAT-004/005 add no new requests, but tests must
  match these rules).
- **Validation rules already in place** (tests in FEAT-005 must exercise these): product
  `image` required on create (nullable on update), `mimes:jpeg,jpg,png,webp`, `max:2048`;
  `old_price` required numeric min:0; `new_price` required numeric min:0 `lte:old_price`;
  `is_active` nullable boolean; subcategory must belong to category (after-hook).
- **Blade style** (from `resources/views/layouts/admin.blade.php` and admin views): Tailwind v4
  utility classes, `@vite(['resources/css/app.css','resources/js/app.js'])` in `<head>`,
  `@extends('layouts.admin')` + `@section('title'|'page-title'|'content')`, flash messages via
  `@if(session('success'))/@if(session('error'))`, prices rendered `৳{{ number_format($p,2) }}`,
  images with `object-cover` thumbnails, confirmation dialogs before deletes. The storefront
  layout/views should follow the same Tailwind idiom but with a customer-facing (non-admin)
  look — header with site logo + category nav, responsive, footer with copyright.
- **Seeder**: `DatabaseSeeder` creates 1 admin (`admin@banglapuzzle.com` / `password`), 5
  categories, 3 subcategories each (15), 5 products each (75). Factories:
  `UserFactory` (default password `password`, hashed cast keeps it), `CategoryFactory` (cycles a
  fixed name list, `is_active=true`), `SubcategoryFactory` (needs `category_id` via `->forCategory($id)`),
  `ProductFactory` (needs category+subcategory via `->forSubcategory($catId,$subId)`).

### Critical latent bug to fix before FEAT-005 docs (confirmed empirically)

`User::$fillable = ['name','email','password']` — **`is_admin` is NOT mass-assignable**
(verified: `(new User)->isFillable('is_admin') === false`). The committed
`DatabaseSeeder` calls `User::create([...'is_admin' => true, 'email_verified_at' => now()])`,
which silently drops both `is_admin` and `email_verified_at`. Result: the seeded admin has
`is_admin = false` (column default) and **cannot log in** through `AdminLoginController`
(which requires `is_admin => true` in `Auth::attempt`). The existing tests still pass only
because they create admins via `User::factory()->create(['is_admin' => true])` (factories use
`forceFill`, bypassing `$fillable`). This breaks the FEAT-005 README "Demo admin setup"
(`admin@banglapuzzle.com`/`password`) and the `php artisan migrate:fresh --seed` verification.
Fix is in FEAT-005 item 1.

---

## 2. Storefront STRICT visibility rules (apply in every storefront query)

A product is publicly visible ONLY when **all three** are true:
`product.is_active = true` AND `category.is_active = true` AND `subcategory.is_active = true`.

- Home latest products: `Product::active()->whereHas('category', is_active=true)->whereHas('subcategory', is_active=true)`.
- Category page products: category is fetched active (`firstOrFail` → 404 otherwise), then
  `Product::active()->where('category_id', $cat->id)->whereHas('subcategory', is_active=true)`.
- Subcategory page: category fetched active (404 if not), subcategory fetched active AND within
  that category (404 if not — this is what makes `/category/{wrongCat}/{subSlug}` return 404),
  then `Product::active()->where('subcategory_id', $sub->id)`.
- Product page: `Product::where('slug',$slug)->where('is_active',true)->whereHas('category', is_active=true)->whereHas('subcategory', is_active=true)->firstOrFail()`.
  Any miss → `ModelNotFoundException` → Laravel renders **404** (use `assertNotFound()`/`assertStatus(404)` in tests).
- Related products on product page: **4** active products from the **same subcategory**,
  excluding the current product (`where('id','!=',$product->id)`), same visibility constraints,
  `->latest()->take(4)->get()`.
- Inactive / nonexistent categories, subcategories, and products must `abort(404)` (via
  `firstOrFail()`). Never leak inactive records to the storefront.
- Eager-load `['category','subcategory']` on every product query (performance + avoids N+1).

---

# FEAT-004 — Public Storefront + SEO

Implement in this order. Each item leaves the app buildable. Routes and Blade compile checks
are the per-item verification; the full `StorefrontTest` pass happens in FEAT-005.

- [ ] **F4-1. Create `app/Http/Controllers/StorefrontController.php`** with four methods
  (`home`, `category`, `subcategory`, `product`) implementing exactly the strict visibility
  rules in section 2. Controller location is the root `Http/Controllers/` dir (NOT an
  `Admin/` or `Storefront/` subfolder) per FEAT-004 spec.
  - `home()`: `$categories = Category::active()->withCount('subcategories')->orderBy('name')->get();`
    `$products = Product::active()->whereHas('category', fn($q)=>$q->where('is_active',true))->whereHas('subcategory', fn($q)=>$q->where('is_active',true))->with(['category','subcategory'])->latest()->take(8)->get();`
    `return view('storefront.home', compact('categories','products'));`
  - `category(string $slug)`: `$category = Category::where('slug',$slug)->where('is_active',true)->firstOrFail();`
    `$subcategories = $category->subcategories()->where('is_active',true)->orderBy('name')->get();`
    `$products = Product::active()->where('category_id',$category->id)->whereHas('subcategory', fn($q)=>$q->where('is_active',true))->with(['category','subcategory'])->latest()->paginate(12);`
    `return view('storefront.category', compact('category','subcategories','products'));`
  - `subcategory(string $categorySlug, string $subcategorySlug)`: `$category = Category::where('slug',$categorySlug)->where('is_active',true)->firstOrFail();`
    `$subcategory = $category->subcategories()->where('slug',$subcategorySlug)->where('is_active',true)->firstOrFail();`
    `$products = Product::active()->where('subcategory_id',$subcategory->id)->with(['category','subcategory'])->latest()->paginate(12);`
    `return view('storefront.subcategory', compact('category','subcategory','products'));`
  - `product(string $slug)`: `$product = Product::where('slug',$slug)->where('is_active',true)->whereHas('category', fn($q)=>$q->where('is_active',true))->whereHas('subcategory', fn($q)=>$q->where('is_active',true))->with(['category','subcategory'])->firstOrFail();`
    `$related = Product::active()->where('subcategory_id',$product->subcategory_id)->where('id','!=',$product->id)->whereHas('category', fn($q)=>$q->where('is_active',true))->whereHas('subcategory', fn($q)=>$q->where('is_active',true))->with(['category','subcategory'])->latest()->take(4)->get();`
    `return view('storefront.product', compact('product','related'));`
  Files: `app/Http/Controllers/StorefrontController.php`
  Verify: `C:\php8\php.exe artisan route:list` runs without error after F4-2 (controller alone has
  no routes yet; lint with `C:\php8\php.exe -l app/Http/Controllers/StorefrontController.php` →
  "No syntax errors detected").

- [ ] **F4-2. Register storefront routes in `routes/web.php`** (public, no auth) and remove the
  original `Route::get('/', fn() => view('welcome'))`. Add `use App\Http\Controllers\StorefrontController;`.
  ```
  Route::get('/', [StorefrontController::class, 'home'])->name('home');
  Route::get('/category/{slug}', [StorefrontController::class, 'category'])->name('storefront.category');
  Route::get('/category/{categorySlug}/{subcategorySlug}', [StorefrontController::class, 'subcategory'])->name('storefront.subcategory');
  Route::get('/product/{slug}', [StorefrontController::class, 'product'])->name('storefront.product');
  ```
  Keep all admin routes unchanged. (The unused `resources/views/welcome.blade.php` may optionally be
  deleted — it is referenced nowhere after the `/` route changes — but deletion is not required.)
  Files: `routes/web.php`
  Verify: `C:\php8\php.exe artisan route:list --path=category` shows `storefront.category` and
  `storefront.subcategory`; `C:\php8\php.exe artisan route:list --path=product` shows
  `storefront.product`; `C:\php8\php.exe artisan route:list` shows `home` and no errors. Admin
  routes still present (`--path=admin` lists them).

- [ ] **F4-3. Add the storefront View Composer in `app/Providers/AppServiceProvider.php`.** In
  `boot()`, register a composer that gives every `layouts.storefront` render the active
  categories for the header nav:
  ```php
  use Illuminate\Support\Facades\View;
  // in boot():
  View::composer('layouts.storefront', function ($view) {
      $view->with('navCategories', \App\Models\Category::where('is_active', true)
          ->withCount('subcategories')->orderBy('name')->get());
  });
  ```
  Files: `app/Providers/AppServiceProvider.php`
  Verify: `C:\php8\php.exe -l app/Providers/AppServiceProvider.php` → no syntax errors;
  `C:\php8\php.exe artisan config:clear` runs clean.

- [ ] **F4-4. Create `resources/views/layouts/storefront.blade.php`** — clean HTML skeleton with:
  `@vite(['resources/css/app.css','resources/js/app.js'])` in `<head>`;
  `<title>@yield('meta_title', 'BanglaPuzzle Shop')</title>`;
  `<meta name="description" content="@yield('meta_description')">`;
  OG tags via `@yield('og_tags')`;
  header with site logo link `BanglaPuzzle Shop` to `route('home')` and a nav listing
  `$navCategories` (from the F4-3 composer) as links to `route('storefront.category', $c->slug)`,
  with a responsive hamburger menu (CSS-only or minimal inline JS);
  `@yield('content')`;
  footer with copyright `© {{ date('Y') }} BanglaPuzzle Shop`.
  Follow the Tailwind idiom of `layouts/admin.blade.php` (utility classes, `@vite`).
  Files: `resources/views/layouts/storefront.blade.php`
  Verify: `C:\php8\php.exe artisan view:cache` compiles without error (compiles all Blade).

- [ ] **F4-5. Create `resources/views/storefront/partials/product-card.blade.php`** — accepts
  `$product`. Card shows: thumbnail — `Storage::disk('public')->url($product->image)` when
  present, else `asset('images/placeholder.png')` (same convention as admin index), with
  `object-cover` and consistent aspect ratio; product name; old price with strikethrough when
  `$product->old_price > $product->new_price`; new price (e.g.
  `৳{{ number_format($product->new_price, 2) }}`); discount badge
  `{{ $product->discount_percentage }}% OFF` rendered only when
  `$product->discount_percentage` is not null; whole card linked to
  `route('storefront.product', $product->slug)`.
  Files: `resources/views/storefront/partials/product-card.blade.php`
  Verify: `C:\php8\php.exe artisan view:cache` compiles without error.

- [ ] **F4-6. Create `resources/views/storefront/home.blade.php`** — `@extends('layouts.storefront')`;
  `@section('meta_title', 'BanglaPuzzle Shop — Home')`;
  `@section('content')`: hero banner (slate-800 bg, white text, tagline); category cards grid
  (responsive 2–4 cols) linking each `$category` to `route('storefront.category', $category->slug)`
  and showing `$category->subcategories_count`; `Latest Products` heading; products grid using
  `@foreach($products as $product) @include('storefront.partials.product-card', ['product'=>$product])`;
  empty state message if `$products->isEmpty()`.
  Files: `resources/views/storefront/home.blade.php`
  Verify: `C:\php8\php.exe artisan view:cache` compiles; after F4-7..F4-9 and a seeded DB,
  `C:\php8\php.exe artisan test --filter ExampleTest` → GET `/` returns 200 (home renders).

- [ ] **F4-7. Create `resources/views/storefront/category.blade.php`** — `@extends('layouts.storefront')`;
  `@section('meta_title', $category->name.' — BanglaPuzzle Shop')`; `@section('content')`:
  category name + description; subcategory pills linking to
  `route('storefront.subcategory', [$category->slug, $sub->slug])` for each `$subcategories`;
  products grid via the product-card partial; `{{ $products->links() }}` pagination; empty state.
  Files: `resources/views/storefront/category.blade.php`
  Verify: `C:\php8\php.exe artisan view:cache` compiles without error.

- [ ] **F4-8. Create `resources/views/storefront/subcategory.blade.php`** —
  `@extends('layouts.storefront')`;
  `@section('meta_title', $subcategory->name.' — '.$subcategory->category->name)`;
  `@section('content')`: breadcrumb Home > `$subcategory->category->name` > `$subcategory->name`
  (Home links to `route('home')`, category to `route('storefront.category', $subcategory->category->slug)`);
  subcategory name + description; products grid via product-card partial;
  `{{ $products->links() }}` pagination; empty state.
  Files: `resources/views/storefront/subcategory.blade.php`
  Verify: `C:\php8\php.exe artisan view:cache` compiles without error.

- [ ] **F4-9. Create `resources/views/storefront/product.blade.php`** — `@extends('layouts.storefront')`;
  `@section('meta_title', $product->name)`;
  `@section('meta_description', Str::limit($product->description, 160))` (`Str` facade alias is
  available in Blade);
  `@section('og_tags')` block rendering `og:title`, `og:description`, `og:image` =
  `Storage::disk('public')->url($product->image)` (only if `$product->image`), `og:type=product`;
  `@section('content')`: two-column layout (image left, details right on `md+`); breadcrumb
  Home > Category > Subcategory > Product; product name; prices (old price strikethrough when
  applicable, new price large `৳...`); discount badge
  `{{ $product->discount_percentage }}% OFF` when `$product->discount_percentage` is not null;
  description text; related products grid (`@foreach($related as $r) @include('storefront.partials.product-card', ['product'=>$r])`, max 4, with a "Related Products" heading and only if
  `$related->isNotEmpty()`).
  Files: `resources/views/storefront/product.blade.php`
  Verify: `C:\php8\php.exe artisan view:cache` compiles without error.

- [ ] **F4-10. Build frontend assets.** Run the Vite/Tailwind build so the storefront picks up
  compiled CSS.
  Files: (no source edits; produces `public/build/` which is git-ignored)
  Verify: `npm run build` exits 0 and `public/build/` is created (do NOT commit `public/build`).

- [ ] **F4-11. FEAT-004 acceptance smoke (seeded data).** `C:\php8\php.exe artisan migrate:fresh --seed`
  and `C:\php8\php.exe artisan storage:link` (idempotent), then with `php artisan serve` running
  confirm by HTTP: `GET /` → 200; `GET /category/{active-slug}` → 200 (use a seeded active
  category slug); `GET /product/{active-slug}` → 200 and the discount badge or price renders;
  inactive records 404 (temporarily flip a category/subcategory/product `is_active=0` via tinker
  and re-request). Re-run `C:\php8\php.exe artisan migrate:fresh --seed` afterward to restore
  active data.
  Files: (no edits)
  Verify: `C:\php8\php.exe artisan test --filter ExampleTest` still green (GET `/` = 200). Full
  `StorefrontTest` pass is validated in FEAT-005.

FEAT-004 acceptance criteria from the spec (verifiable once FEAT-005 `StorefrontTest` exists):
GET `/` 200; active category 200; inactive category 404; active subcategory 200; subcategory in
wrong category 404; active product 200; inactive product 404; product with inactive category 404;
product page shows discount % when `old_price > new_price`; `php artisan test --filter StorefrontTest` passes.

---

# FEAT-005 — PHPUnit Feature Tests + README + VPS_DEPLOYMENT.md

Depends on FEAT-004 (storefront routes/controller/views must exist). Tests use SQLite
in-memory (already in `phpunit.xml`) + `RefreshDatabase` + `Storage::fake('public')` for image
tests. Admin auth in tests via `$this->actingAs(User::factory()->create(['is_admin'=>true]))`.

- [ ] **F5-1. Fix the latent `is_admin` mass-assignment bug in `app/Models/User.php`.** Add
  `is_admin` and `email_verified_at` to `$fillable` so the seeder's `User::create([... 'is_admin'=>true ...])`
  persists them:
  ```php
  protected $fillable = ['name', 'email', 'password', 'is_admin', 'email_verified_at'];
  ```
  Files: `app/Models/User.php`
  Verify: `C:\php8\php.exe artisan tinker --execute="var_dump((new App\Models\User)->isFillable('is_admin'))"`
  → `bool(true)`. Then `C:\php8\php.exe artisan migrate:fresh --seed` and
  `C:\php8\php.exe artisan tinker --execute="var_dump(App\Models\User::where('email','admin@banglapuzzle.com')->value('is_admin'))"`
  → `bool(true)`. Existing test suite must remain green: `C:\php8\php.exe artisan test` still
  17+ passing (factories use `forceFill`, so adding to `$fillable` does not regress them).

- [ ] **F5-2. Create `tests/Feature/Admin/AuthTest.php`** (`use RefreshDatabase`). setUp not
  strictly required; create users inline. Methods:
  - `test_admin_login_page_loads`: `GET route('admin.login')` → `assertStatus(200)`.
  - `test_admin_can_login`: create `User::factory()->create(['is_admin'=>true])`, `POST route('admin.login.post')` with that email + `password` → `assertRedirect(route('admin.dashboard'))`.
  - `test_non_admin_cannot_login`: create user `is_admin=false`, `$this->from(route('admin.login'))->post(route('admin.login.post'), [...])` → `assertSessionHas('error')` and NOT redirected to dashboard.
  - `test_wrong_password_rejected`: `$this->from(route('admin.login'))->post(route('admin.login.post'), ['email'=>$admin->email,'password'=>'wrong'])` → `assertSessionHas('error')`.
  - `test_admin_can_logout`: `actingAs($admin)->post(route('admin.logout'))` → `assertRedirect(route('admin.login'))`.
  - `test_admin_routes_require_auth`: `GET route('admin.categories.index')` (no auth) → `assertRedirect(route('admin.login'))`.
  Note: login POST is throttled `5,1`; total login POSTs here = 3 (under limit) and `CACHE_STORE=array`
  in `phpunit.xml` resets per test, so throttle will not interfere. Use `$this->from(...)` for
  failure cases because `back()` needs a referer to redirect to the login page deterministically.
  Files: `tests/Feature/Admin/AuthTest.php`
  Verify: `C:\php8\php.exe artisan test --filter AuthTest` → all pass.

- [ ] **F5-3. Create `tests/Feature/Admin/CategoryCrudTest.php`** (`use RefreshDatabase`). setUp
  creates `$this->admin = User::factory()->create(['is_admin'=>true])`. Methods:
  - `test_index_accessible_to_admin`: `actingAs($admin)->get(route('admin.categories.index'))` → 200, `assertViewIs('admin.categories.index')`.
  - `test_create_page_loads`: `get(route('admin.categories.create'))` → 200.
  - `test_store_creates_category_with_slug`: POST `route('admin.categories.store')` with `name=>'Test Category'`, `slug=>'test-cat'`, `is_active=>'1'` → `assertRedirect(route('admin.categories.index'))`, `assertSessionHas('success')`, `assertDatabaseHas('categories',['slug'=>'test-cat'])`.
  - `test_store_auto_generates_slug_if_omitted`: POST with `name=>'Auto Slug Cat'`, no slug → `assertDatabaseHas('categories',['name'=>'Auto Slug Cat','slug'=>'auto-slug-cat'])` (model `boot()` auto-slugs).
  - `test_update_modifies_category`: create a category, `actingAs($admin)->put(route('admin.categories.update',$cat), ['name'=>'Updated Name','description'=>'d','is_active'=>'1'])` → `assertRedirect(route('admin.categories.index'))`, `assertDatabaseHas('categories',['id'=>$cat->id,'name'=>'Updated Name'])`.
  - `test_destroy_removes_category`: create empty category, `delete(route('admin.categories.destroy',$cat))` → `assertRedirect(route('admin.categories.index'))`, `assertDatabaseMissing('categories',['id'=>$cat->id])`.
  - `test_cannot_destroy_category_with_subcategories`: create category + a subcategory in it, `delete(route('admin.categories.destroy',$cat))` → `assertSessionHas('error')`, `assertDatabaseHas('categories',['id'=>$cat->id])` (delete blocked).
  - `test_toggle_active_flips_status`: create active category, `post(route('admin.categories.toggleActive',$cat))` → `assertDatabaseHas('categories',['id'=>$cat->id,'is_active'=>false])`.
  - `test_search_filters_results`: create `Category::factory()->create(['name'=>'Alpha Cat'])` and `['name'=>'Beta Cat']`, `actingAs($admin)->get(route('admin.categories.index',['search'=>'Alpha']))` → `assertSee('Alpha Cat')`, `assertDontSee('Beta Cat')`.
  Files: `tests/Feature/Admin/CategoryCrudTest.php`
  Verify: `C:\php8\php.exe artisan test --filter CategoryCrudTest` → all pass.

- [ ] **F5-4. Create `tests/Feature/Admin/SubcategoryCrudTest.php`** (`use RefreshDatabase`). setUp
  creates admin + `$this->category = Category::factory()->create(['is_active'=>true])`. Methods:
  - `test_index_returns_200`: `actingAs($admin)->get(route('admin.subcategories.index'))` → 200.
  - `test_filter_by_category`: create sub in `$this->category` and sub in a second category; `get(route('admin.subcategories.index',['category_id'=>$this->category->id]))` → `assertSee` first, `assertDontSee` second.
  - `test_create_page_loads`: `get(route('admin.subcategories.create'))` → 200.
  - `test_store_creates_subcategory`: POST with `category_id`, `name=>'Test Sub'`, `is_active=>'1'` → `assertRedirect(route('admin.subcategories.index'))`, `assertSessionHas('success')`, `assertDatabaseHas('subcategories',['name'=>'Test Sub','category_id'=>$this->category->id])`.
  - `test_composite_slug_unique_within_category`: create a sub in `$this->category` with slug `'shared'`; POST a second sub in the same category with slug `'shared'` → `assertSessionHasErrors('slug')` (rejected); create a second category and POST a sub there with slug `'shared'` → `assertRedirect(route('admin.subcategories.index'))` (allowed — uniqueness is scoped to `category_id`).
  - `test_by_category_returns_json`: create 2 active subcategories in `$this->category`, `get(route('admin.subcategories.byCategory',$this->category))` → `assertStatus(200)`, `assertJsonCount(2)` (only active returned; `byCategory` selects `id,name`).
  - `test_cannot_destroy_subcategory_with_products`: create sub + a `Product::factory()->forSubcategory($catId,$subId)->create()`, `delete(route('admin.subcategories.destroy',$sub))` → `assertSessionHas('error')`, `assertDatabaseHas('subcategories',['id'=>$sub->id])`.
  - `test_toggle_active_flips_status`: create active sub, `post(route('admin.subcategories.toggleActive',$sub))` → `assertDatabaseHas('subcategories',['id'=>$sub->id,'is_active'=>false])`.
  Files: `tests/Feature/Admin/SubcategoryCrudTest.php`
  Verify: `C:\php8\php.exe artisan test --filter SubcategoryCrudTest` → all pass.

- [ ] **F5-5. AUGMENT the existing `tests/Feature/Admin/ProductCrudTest.php` — do NOT recreate it.**
  This file ALREADY EXISTS and covers most of FEAT-005's product tests. Creating a second file
  with the same class name `ProductCrudTest` in namespace `Tests\Feature\Admin` would be a
  duplicate class declaration → fatal error. Map of FEAT-005 named tests → existing methods
  (all already passing in the baseline):
  - `index_returns_200` → exists `test_admin_can_access_products_index` (+ `test_guest_cannot_access_products_index`).
  - `create_page_loads` → exists `test_admin_can_view_create_form`.
  - `store_creates_product_with_image` → exists `test_admin_can_create_product_with_valid_data` (uses `UploadedFile::fake()->image('product.jpg',400,400)`, asserts `Storage::disk('public')->assertExists($product->image)`).
  - `update_without_new_image_preserves_existing_image` → exists `test_update_without_new_image_preserves_existing_image`.
  - `update_with_new_image_replaces_and_deletes_old` → exists `test_update_with_new_image_replaces_old_image` (asserts old path deleted, new path exists).
  - `destroy_deletes_product_and_image` → exists `test_admin_can_delete_product_and_image_is_removed`.
  - `new_price_greater_than_old_price_fails_validation` → exists `test_store_fails_when_new_price_exceeds_old_price`.
  - `subcategory_from_wrong_category_fails_validation` → exists `test_store_fails_when_subcategory_belongs_to_wrong_category`.
  - `toggle_active_flips_status` → exists `test_admin_can_toggle_product_active_status`.
  - `search_filters_products` → **MISSING — ADD** `test_search_filters_products`.
  - `filter_by_category_filters_products` → **MISSING — ADD** `test_filter_by_category_filters_products`.

  Add ONLY these two methods to the existing file (keep the existing `setUp` which already
  provides `$this->admin`, `$this->category`, `$this->subcategory`):
  - `test_search_filters_products`: create `Product::factory()->forSubcategory($this->category->id,$this->subcategory->id)->create(['name'=>'Alpha Widget'])` and `['name'=>'Beta Gadget']`; `actingAs($admin)->get(route('admin.products.index',['search'=>'Alpha']))` → `assertSee('Alpha Widget')`, `assertDontSee('Beta Gadget')`.
  - `test_filter_by_category_filters_products`: create a second `$otherCategory = Category::factory()->create()` and `$otherSub = Subcategory::factory()->forCategory($otherCategory->id)->create()`; create `Product::factory()->forSubcategory($this->category->id,$this->subcategory->id)->create(['name'=>'In Cat One'])` and `Product::factory()->forSubcategory($otherCategory->id,$otherSub->id)->create(['name'=>'In Cat Two'])`; `actingAs($admin)->get(route('admin.products.index',['category_id'=>$this->category->id]))` → `assertSee('In Cat One')`, `assertDontSee('In Cat Two')`.
  Files: `tests/Feature/Admin/ProductCrudTest.php` (edit only — add 2 methods).
  Verify: `C:\php8\php.exe artisan test --filter ProductCrudTest` → all pass (existing + 2 new).

- [ ] **F5-6. Create `tests/Feature/StorefrontTest.php`** (`use RefreshDatabase`). setUp creates
  `$this->category = Category::factory()->create(['is_active'=>true])`,
  `$this->subcategory = Subcategory::factory()->forCategory($this->category->id)->create(['is_active'=>true])`,
  `$this->product = Product::factory()->forSubcategory($this->category->id,$this->subcategory->id)->create(['is_active'=>true])`.
  Methods (each uses strict visibility; use `assertStatus(404)` / `assertNotFound()` for 404s):
  - `test_home_page_returns_200`: `get(route('home'))` → 200.
  - `test_home_page_does_not_show_inactive_product`: create inactive product; `get(route('home'))` → `assertDontSee($inactive->name)` (home shows only active products via `whereHas`).
  - `test_category_page_returns_200_for_active_category`: `get(route('storefront.category',$this->category->slug))` → 200.
  - `test_category_page_returns_404_for_inactive_category`: create inactive category; `get(route('storefront.category',$inactive->slug))` → 404.
  - `test_category_page_returns_404_for_nonexistent_category`: `get(route('storefront.category','no-such-slug'))` → 404.
  - `test_subcategory_page_returns_200_for_active_subcategory`: `get(route('storefront.subcategory',[$this->category->slug,$this->subcategory->slug]))` → 200.
  - `test_subcategory_page_returns_404_for_inactive_subcategory`: create inactive sub under the active category; `get(route('storefront.subcategory',[$this->category->slug,$inactiveSub->slug]))` → 404.
  - `test_subcategory_page_returns_404_when_subcategory_belongs_to_different_category`: create `$otherCat = Category::factory()->create(['is_active'=>true])`; `get(route('storefront.subcategory',[$otherCat->slug,$this->subcategory->slug]))` → 404 (subcategory not found within `$otherCat`).
  - `test_product_page_returns_200_for_active_product`: `get(route('storefront.product',$this->product->slug))` → 200.
  - `test_product_page_returns_404_for_inactive_product`: create inactive product (active cat/sub); `get(route('storefront.product',$inactive->slug))` → 404.
  - `test_product_page_returns_404_when_category_is_inactive`: create product active whose category is inactive; `get(route('storefront.product',$product->slug))` → 404 (`whereHas('category', is_active)` excludes it).
  - `test_product_page_returns_404_when_subcategory_is_inactive`: create product active whose subcategory is inactive (category active); `get(route('storefront.product',$product->slug))` → 404.
  - `test_product_page_shows_discount_percentage_when_applicable`: create product `old_price=>1000, new_price=>800` (active cat/sub); `get(route('storefront.product',$product->slug))` → `assertSee('20%')` (discount accessor = 20, rendered `20% OFF`).
  Files: `tests/Feature/StorefrontTest.php`
  Verify: `C:\php8\php.exe artisan test --filter StorefrontTest` → all pass.

- [ ] **F5-7. Whole-suite green check.**
  Files: (no edits)
  Verify: `C:\php8\php.exe artisan test` → exits 0, all suites pass (Unit + Feature: Auth,
  Category, Subcategory, Product, Storefront, AdminDashboard, Example).

- [ ] **F5-8. Write `README.md` at project root.** Professional README covering (align with
  assignment §14): Project Overview (e-commerce management system for the BanglaPuzzle
  assignment); Features (list the implemented areas 4.1–4.6: admin auth+authorization, Category
  CRUD, Subcategory CRUD, Product CRUD with image lifecycle, Public storefront + SEO, automated
  tests); Tech Stack with versions (Laravel 13.40.0, PHP 8.4, MySQL 8.0.43, Tailwind CSS v4, Vite,
  PHPUnit 12); Laravel version rationale (PHP 8.4 fully supports Laravel 13; no downgrade needed);
  Architecture overview (Laravel monolith: Controllers/Admin + StorefrontController, Form Requests,
  `ProductImageService`, Eloquent models/relationships, middleware alias `admin`, DB transactions);
  Database schema & relationships (categories, subcategories with composite unique
  `(category_id,slug)`, products with FKs + `is_active` indexes; the consistency rule that a
  product's subcategory must belong to its category); Local Setup (clone → `composer install` →
  `cp .env.example .env` → `php artisan key:generate` → configure DB in `.env` →
  `php artisan migrate:fresh --seed` → `php artisan storage:link` → `npm install` → `npm run build`
  → `php artisan serve`); Demo admin (http://localhost:8000/admin/login,
  admin@banglapuzzle.com / password — produced by `DatabaseSeeder`); Storage configuration
  (`Storage::disk('public')` under `storage/app/public/products`, `php artisan storage:link`);
  Running tests (`php artisan test`, runs on SQLite in-memory, no MySQL needed); VPS deployment
  reference (link to `VPS_DEPLOYMENT.md`); Known limitations. Note explicitly: never commit `.env`
  to git.
  Files: `README.md`
  Verify: file exists; sections present (manual review). `C:\php8\php.exe artisan test` unaffected.

- [ ] **F5-9. Write `VPS_DEPLOYMENT.md` at project root.** Ubuntu 22.04/24.04 guide (align with
  assignment §13): Prerequisites (Ubuntu 22.04/24.04, Nginx 1.24+, PHP 8.4-FPM with extensions
  mbstring, xml, curl, zip, mysql, bcmath, intl, gd, pdo, tokenizer, fileinfo, openssl, ctype, json,
  pcre, phar, dom, filter, hash, session, simplexml, spl, xmlreader, xmlwriter, iconv; MySQL 8.0;
  Composer 2; Node.js 20+ + npm); Server prep + Nginx + PHP-FPM + Composer + MySQL install &
  secure setup; Step-by-step deploy: `git clone` → `composer install --optimize-autoloader --no-dev`
  → `cp .env.example .env` (set `APP_ENV=production`, `APP_DEBUG=false`, generate `APP_KEY`, DB
  creds) → `php artisan key:generate` → `php artisan migrate --force` (note: never `migrate:fresh`
  on production) → `php artisan storage:link` → `npm install` → `npm run build` →
  `chown -R www-data:www-data storage bootstrap/cache` → `chmod -R 775 storage bootstrap/cache`;
  Nginx vhost config snippet (full `server` block, `root` pointing to the Laravel `public`
  directory, `try_files $uri $uri/ /index.php?$query_string`, fastcgi pass to the PHP-FPM socket —
  note: use the actual installed socket path, e.g. `unix:/run/php/php8.4-fpm.sock`, do not assume);
  PHP-FPM pool config note; `systemctl restart nginx php8.4-fpm`; DNS + HTTPS/Let's Encrypt
  (certbot); Laravel production optimization (`php artisan config:cache && php artisan route:cache
  && php artisan view:cache`); Backup & restore procedure; Log inspection (`storage/logs/laravel.log`,
  `journalctl`); Troubleshooting. State clearly that live deployment was not performed unless it
  actually was — do not fabricate a URL.
  Files: `VPS_DEPLOYMENT.md`
  Verify: file exists with the Nginx `server` block snippet (manual review).

---

## 3. Test setup patterns (reference for FEAT-005 authors)

- `use Illuminate\Foundation\Testing\RefreshDatabase;` + `use Tests\TestCase;`. Do NOT add
  `$this->seed()` unless a test needs seeded rows (the Storefront/Admin tests build their own data
  via factories).
- Image tests: `Storage::fake('public')` at the top of the test method; upload with
  `UploadedFile::fake()->image('product.jpg', 800, 800)`; assert with
  `Storage::disk('public')->assertExists($product->image)` / `assertMissing(...)`. Existing
  `ProductCrudTest` already demonstrates the pattern (it uses 400×400; new tests may use 800×800 per
  the spec — both pass `image` validation).
- Admin acting-as: `$this->actingAs(User::factory()->create(['is_admin' => true]))`. Factories use
  `forceFill`, so `is_admin` is set regardless of `$fillable` (the F5-1 fix does not change this).
- Factories need their relation ids: `Category::factory()->create(...)`;
  `Subcategory::factory()->forCategory($catId)->create(...)`; `Product::factory()->forSubcategory($catId,$subId)->create(...)`.
- The seeded admin (`admin@banglapuzzle.com` / `password`) is created by `DatabaseSeeder` and is
  only valid after the F5-1 `$fillable` fix. Tests do not depend on the seeder.

## 4. Verification commands (run from `e:\interview\BanglaPuzzle`)

- Routes: `C:\php8\php.exe artisan route:list --path=category` and `--path=product` (FEAT-004).
- Blade compile: `C:\php8\php.exe artisan view:cache` (compiles all views; errors surface syntax issues).
- Frontend build: `npm run build` (Vite + Tailwind v4; produces git-ignored `public/build/`).
- Per-suite tests: `C:\php8\php.exe artisan test --filter AuthTest|CategoryCrudTest|SubcategoryCrudTest|ProductCrudTest|StorefrontTest`.
- Full suite: `C:\php8\php.exe artisan test` (SQLite in-memory; must exit 0).
- Seeded data: `C:\php8\php.exe artisan migrate:fresh --seed` (MySQL `banglapuzzle` DB; needed for
  the demo admin and manual storefront smoke — not for the test suite).
- Storage symlink: `C:\php8\php.exe artisan storage:link` (idempotent; needed to serve product
  images at `/storage/products/...`).
- `is_admin` fix check: `C:\php8\php.exe artisan tinker --execute="var_dump(App\Models\User::where('email','admin@banglapuzzle.com')->value('is_admin'))"` → `bool(true)` after seed.

## 5. Commit plan

Two separate commits, on the working branch (never push; never commit to `main` directly unless
explicitly asked). Stage specific paths — do NOT use `git add -A` / `git add .`. Never commit
`.env`, `node_modules`, `vendor`, or `public/build` (all already git-ignored; staging specific files
avoids any risk).

- **Commit A — FEAT-004 (storefront):** stage
  `app/Http/Controllers/StorefrontController.php`, `routes/web.php`,
  `app/Providers/AppServiceProvider.php`, `resources/views/layouts/storefront.blade.php`,
  `resources/views/storefront/partials/product-card.blade.php`,
  `resources/views/storefront/home.blade.php`, `resources/views/storefront/category.blade.php`,
  `resources/views/storefront/subcategory.blade.php`, `resources/views/storefront/product.blade.php`
  (and optionally `resources/views/welcome.blade.php` deletion if removed). Example message:
  `feat(storefront): public storefront pages, SEO meta tags, and category nav`. Verify before
  commit: `C:\php8\php.exe artisan route:list` + `view:cache` clean.

- **Commit B — FEAT-005 (tests + docs):** stage `app/Models/User.php`,
  `tests/Feature/Admin/AuthTest.php`, `tests/Feature/Admin/CategoryCrudTest.php`,
  `tests/Feature/Admin/SubcategoryCrudTest.php`, `tests/Feature/Admin/ProductCrudTest.php`,
  `tests/Feature/StorefrontTest.php`, `README.md`, `VPS_DEPLOYMENT.md`. Example message:
  `test: PHPUnit feature tests for auth/crud/storefront + README + VPS deployment docs`. Verify
  before commit: `C:\php8\php.exe artisan test` exits 0.

## 6. Notes / assumptions

- `StorefrontController` lives at `app/Http/Controllers/StorefrontController.php` (root of
  `Controllers/`), matching the FEAT-004 spec — NOT an `Admin/` or `Storefront/` subfolder, even
  though the assignment's suggested structure mentions `Controllers/Storefront/`. Follow the
  FEAT-004 spec; the assignment structure is only a suggestion.
- The converge loop's reviewer must write
  `e:\interview\BanglaPuzzle\.agents\tasks\feat-004-005\review.json` with `{"verdict":"APPROVED"}`
  (or `CHANGES_REQUESTED`) on every iteration; the loop stops ONLY on `verdict == APPROVED`.
- Canonical URLs and a sitemap are "if time permits" in the assignment (§7) and are NOT required
  by FEAT-004 acceptance criteria; implement only the required SEO (unique slugs, dynamic
  `<title>`, meta description, OG tags on product pages, proper 404s). Canonical/sitemap are
  optional enhancements.
- Do not run `migrate:fresh` against the production/MySQL DB as part of tests (tests use SQLite
  in-memory). `migrate:fresh --seed` is only for the local MySQL `banglapuzzle` smoke and the
  demo-admin verification.
