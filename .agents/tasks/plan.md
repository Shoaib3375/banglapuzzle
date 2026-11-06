# Implementation Plan — BanglaPuzzle E-Commerce System

> **Environment facts confirmed during exploration**
> - Laravel 13.40.0, PHP 8.4.12, Composer 2.8.11
> - MySQL 8.0.43 (DB: banglapuzzle, host 127.0.0.1:3306, user root)
> - Tailwind CSS v4 already wired: `@tailwindcss/vite ^4.0.0` in package.json, `tailwindcss()` plugin in vite.config.js, `@import 'tailwindcss'` in app.css — **do NOT touch Tailwind config**
> - Tests use SQLite in-memory (phpunit.xml), so no MySQL needed for tests
> - SESSION_DRIVER=database, CACHE_STORE=database — sessions table already in users migration
> - Laravel 13 uses `bootstrap/app.php` fluent API — no Http/Kernel.php exists; middleware registered via `->withMiddleware()`
> - User model uses PHP 8 attribute syntax (`#[Fillable]`, `#[Hidden]`) — maintain that style
> - phpunit.xml uses `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:` for test isolation

---

## Phase 2 — Foundation and Database

- [ ] 1. Install Node dependencies.
      Run `npm install` from project root. No config changes needed — Tailwind v4 is already fully configured.
      Files: `package-lock.json` (generated), `node_modules/` (generated)
      Verify: `npm run build` exits 0 and produces `public/build/manifest.json`.

- [ ] 2. Add `is_admin` column to the users table via a new migration.
      Create `database/migrations/2025_01_01_000001_add_is_admin_to_users_table.php`.
      Add `$table->boolean('is_admin')->default(false)->after('remember_token');` in `up()`, drop it in `down()`.
      Files: `database/migrations/2025_01_01_000001_add_is_admin_to_users_table.php`
      Verify: `php artisan migrate:fresh` runs without errors.

- [ ] 3. Create the `categories` migration.
      File: `database/migrations/2025_01_01_000002_create_categories_table.php`
      Columns: id, name (string 100), slug (string 120, unique), description (nullable text), is_active (boolean default true), timestamps.
      Files: `database/migrations/2025_01_01_000002_create_categories_table.php`
      Verify: `php artisan migrate:fresh` runs without errors.

- [ ] 4. Create the `subcategories` migration.
      File: `database/migrations/2025_01_01_000003_create_subcategories_table.php`
      Columns: id, category_id (unsignedBigInteger, FK → categories.id, onDelete restrict), name (string 100), slug (string 120), description (nullable text), is_active (boolean default true), timestamps.
      Indexes: `$table->index('category_id')`, `$table->unique(['category_id', 'slug'])`.
      Files: `database/migrations/2025_01_01_000003_create_subcategories_table.php`
      Verify: `php artisan migrate:fresh` runs without errors.

- [ ] 5. Create the `products` migration.
      File: `database/migrations/2025_01_01_000004_create_products_table.php`
      Columns: id, category_id (FK → categories, restrict), subcategory_id (FK → subcategories, restrict), name (string 150), slug (string, unique), description (text), image (nullable string), old_price (decimal 12,2 default 0), new_price (decimal 12,2 default 0), is_active (boolean default true), timestamps.
      Indexes on: category_id, subcategory_id, is_active (separate `$table->index()` calls).
      Files: `database/migrations/2025_01_01_000004_create_products_table.php`
      Verify: `php artisan migrate:fresh` runs without errors.

- [ ] 6. Update the User model to expose `is_admin`.
      The model uses PHP 8 attribute `#[Fillable]` — change to traditional `protected $fillable` array that includes `is_admin`, OR add `is_admin` to the `#[Fillable]` attribute. Cast `is_admin` to boolean in the `casts()` method.
      Decision: switch to the conventional `$fillable` / `$hidden` array properties so it's easier to maintain and extend; remove the `#[Fillable]` and `#[Hidden]` attributes.
      Files: `app/Models/User.php`
      Verify: `php artisan migrate:fresh` + `php artisan tinker --execute="echo App\Models\User::make(['is_admin'=>true])->is_admin ? 'ok' : 'fail';"` prints `ok`.

- [ ] 7. Create the Category Eloquent model.
      File: `app/Models/Category.php`
      - `$fillable`: name, slug, description, is_active
      - `casts()`: is_active → boolean
      - Relationships: `hasMany(Subcategory::class)`, `hasMany(Product::class)`
      - Local scope: `scopeActive($query)` → `$query->where('is_active', true)`
      - Slug auto-generation: override `boot()` to listen to `creating`/`updating` — if slug empty, generate via `Str::slug($this->name)`, then ensure uniqueness by appending a numeric suffix if needed.
      Files: `app/Models/Category.php`
      Verify: `php artisan test --filter CategoryModelTest` (test created in Phase 10).

- [ ] 8. Create the Subcategory Eloquent model.
      File: `app/Models/Subcategory.php`
      - `$fillable`: category_id, name, slug, description, is_active
      - `casts()`: is_active → boolean, category_id → integer
      - Relationships: `belongsTo(Category::class)`, `hasMany(Product::class)`
      - Scope: `scopeActive($query)`
      - Slug auto-generation same as Category, but uniqueness checked within same category_id.
      Files: `app/Models/Subcategory.php`
      Verify: `php artisan test --filter SubcategoryModelTest`.

- [ ] 9. Create the Product Eloquent model.
      File: `app/Models/Product.php`
      - `$fillable`: category_id, subcategory_id, name, slug, description, image, old_price, new_price, is_active
      - `casts()`: is_active → boolean, old_price → decimal:2, new_price → decimal:2
      - Relationships: `belongsTo(Category::class)`, `belongsTo(Subcategory::class)`
      - Scopes: `scopeActive($query)`, `scopeByCategory($query, $id)`, `scopeBySubcategory($query, $id)`
      - Accessor: `getDiscountPercentageAttribute()` — computes discount % when old_price > new_price
      - Slug auto-generation same pattern.
      Files: `app/Models/Product.php`
      Verify: `php artisan test --filter ProductModelTest`.

- [ ] 10. Create CategoryFactory, SubcategoryFactory, ProductFactory.
      Files: `database/factories/CategoryFactory.php`, `database/factories/SubcategoryFactory.php`, `database/factories/ProductFactory.php`
      - CategoryFactory: realistic Bengali e-commerce category names (Electronics, Fashion, Home & Living, Beauty, Books, etc.); auto-generates slug; is_active true.
      - SubcategoryFactory: requires category_id state; Bengali subcategory names; is_active true.
      - ProductFactory: requires category_id + subcategory_id; realistic product names and descriptions; old_price 100–10000, new_price ≤ old_price; is_active true.
      Verify: `php artisan tinker --execute="Database\Factories\CategoryFactory::new()->make()->toArray();"` returns valid array.

- [ ] 11. Rewrite DatabaseSeeder.
      File: `database/seeders/DatabaseSeeder.php`
      Create: 1 admin user (name: "Admin", email: admin@banglapuzzle.com, password: password, is_admin: true, email_verified_at: now()).
      Then 5 categories, for each category 3 subcategories, for each subcategory 5 products — all seeded with factories.
      Call: `$this->call([CategorySeeder::class])` pattern, or inline everything in DatabaseSeeder.
      Decision: inline in DatabaseSeeder to keep it simple; no extra seeder classes needed.
      Files: `database/seeders/DatabaseSeeder.php`
      Verify: `php artisan migrate:fresh --seed` exits 0; `php artisan tinker --execute="echo App\Models\Product::count();"` prints 75.

---

## Phase 3 — Authentication

- [ ] 12. Create Admin login Blade views.
      Files: `resources/views/admin/auth/login.blade.php`
      Full-page centered login form with email + password + remember me checkbox. Uses `@vite` for CSS. CSRF token included. Shows session error messages. Clean Tailwind v4 styling (indigo-600 primary button).
      Verify: visual only at this stage; full auth test in Phase 10.

- [ ] 13. Create Auth\AdminLoginController.
      File: `app/Http/Controllers/Auth/AdminLoginController.php`
      Methods: `showLoginForm()` → returns `admin.auth.login` view; `login(Request $request)` → validates email+password, uses `Auth::attempt(['email', 'password', 'is_admin' => true], $remember)`, regenerates session on success, redirects to `/admin/dashboard`; `logout()` → Auth::logout(), session invalidate+regenerate, redirect to `/admin/login`.
      Decision: use `Auth::attempt()` with `is_admin => true` guard so non-admin accounts cannot log in even with correct credentials.
      Files: `app/Http/Controllers/Auth/AdminLoginController.php`
      Verify: `php artisan route:list` shows admin.login routes.

- [ ] 14. Create EnsureIsAdmin middleware.
      File: `app/Http/Middleware/EnsureIsAdmin.php`
      Logic: if `!Auth::check() || !Auth::user()->is_admin` → redirect to `route('admin.login')` with error flash. Otherwise pass through.
      Files: `app/Http/Middleware/EnsureIsAdmin.php`
      Verify: class exists; registered in next step.

- [ ] 15. Register middleware and routes in bootstrap/app.php and routes/web.php.
      In `bootstrap/app.php`, inside `->withMiddleware()`: register alias `'admin'` pointing to `App\Http\Middleware\EnsureIsAdmin::class`.
      In `routes/web.php`:
        - `Route::get('/admin/login', ...)->name('admin.login')`
        - `Route::post('/admin/login', ...)->name('admin.login.post')->middleware('throttle:5,1')`
        - `Route::post('/admin/logout', ...)->name('admin.logout')`
        - `Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(fn() => ...)` — initially just a dashboard stub
      Files: `bootstrap/app.php`, `routes/web.php`
      Verify: `php artisan route:list` shows all admin routes; `php artisan test --filter AuthTest`.

---

## Phase 4 — Category & Subcategory CRUD (Admin)

- [ ] 16. Create Admin Category Form Requests.
      Files: `app/Http/Requests/Admin/StoreCategoryRequest.php`, `app/Http/Requests/Admin/UpdateCategoryRequest.php`
      Rules: name required|string|max:100; slug nullable|string|max:120|unique:categories,slug (UpdateRequest ignores current record via `$this->route('category')`); description nullable|string; is_active nullable|boolean.
      Verify: `php artisan test --filter CategoryCrudTest`.

- [ ] 17. Create Admin CategoryController (resource).
      File: `app/Http/Controllers/Admin/CategoryController.php`
      Methods: index (paginate 15, search by name), create, store (use StoreCategoryRequest, auto-slug if not supplied via `Str::slug()` + uniqueness loop), show (redirect to edit), edit, update (UpdateCategoryRequest, same slug logic), destroy (check no subcategories exist → if so redirect with error; otherwise delete).
      Also add `toggleActive(Category $category)` method that flips `is_active` and redirects back.
      Files: `app/Http/Controllers/Admin/CategoryController.php`
      Verify: `php artisan test --filter CategoryCrudTest`.

- [ ] 18. Create Category admin Blade views.
      Files:
        - `resources/views/layouts/admin.blade.php` — full admin layout with sidebar nav (Dashboard, Categories, Subcategories, Products), topbar, flash messages section, `@stack('scripts')`
        - `resources/views/admin/categories/index.blade.php` — table with search input, pagination, toggle active button, edit/delete links; shows subcategory count and product count per row
        - `resources/views/admin/categories/create.blade.php`
        - `resources/views/admin/categories/edit.blade.php`
      Forms use `@csrf`. Delete uses a POST form with `@method('DELETE')`. All text escaped via `{{ }}`.
      Verify: `php artisan test --filter CategoryCrudTest`.

- [ ] 19. Create Admin Subcategory Form Requests.
      Files: `app/Http/Requests/Admin/StoreSubcategoryRequest.php`, `app/Http/Requests/Admin/UpdateSubcategoryRequest.php`
      Rules: category_id required|exists:categories,id; name required|string|max:100; slug nullable|string|max:120 + composite unique rule `Rule::unique('subcategories')->where('category_id', $this->input('category_id'))->ignore($this->route('subcategory'))`; description nullable; is_active boolean.
      Verify: `php artisan test --filter SubcategoryCrudTest`.

- [ ] 20. Create Admin SubcategoryController (resource).
      File: `app/Http/Controllers/Admin/SubcategoryController.php`
      Methods: index (paginate 15, filter by category_id query param), create, store, edit, update, destroy (block if has products), toggleActive.
      Also: `byCategory(Category $category)` method at GET `/admin/subcategories-by-category/{category}` returning JSON array of `{id, name}` for AJAX dropdown.
      Files: `app/Http/Controllers/Admin/SubcategoryController.php`
      Verify: `php artisan test --filter SubcategoryCrudTest`.

- [ ] 21. Create Subcategory admin Blade views.
      Files:
        - `resources/views/admin/subcategories/index.blade.php` — table with category filter dropdown, pagination, toggle/edit/delete
        - `resources/views/admin/subcategories/create.blade.php` — category select, name, slug (auto-fill JS), description, is_active
        - `resources/views/admin/subcategories/edit.blade.php`
      Verify: `php artisan test --filter SubcategoryCrudTest`.

- [ ] 22. Register Category and Subcategory routes inside the admin group.
      In `routes/web.php` admin group:
        - `Route::resource('categories', CategoryController::class)`
        - `Route::post('categories/{category}/toggle-active', ...)->name('categories.toggleActive')`
        - `Route::resource('subcategories', SubcategoryController::class)`
        - `Route::post('subcategories/{subcategory}/toggle-active', ...)->name('subcategories.toggleActive')`
        - `Route::get('subcategories-by-category/{category}', ...)->name('subcategories.byCategory')`
      Files: `routes/web.php`
      Verify: `php artisan route:list --path=admin` shows all CRUD routes.

---

## Phase 5 — Product CRUD (Admin)

- [ ] 23. Create Admin Product Form Requests.
      Files: `app/Http/Requests/Admin/StoreProductRequest.php`, `app/Http/Requests/Admin/UpdateProductRequest.php`
      Rules:
        - category_id: required|exists:categories,id
        - subcategory_id: required|exists:subcategories,id + custom rule that subcategory's category_id matches category_id input
        - name: required|string|max:150
        - slug: nullable|string|unique:products,slug (ignore current on update)
        - description: required|string
        - image: StoreRequest: required|image|mimes:jpeg,jpg,png,webp|max:2048; UpdateRequest: nullable|image|mimes:jpeg,jpg,png,webp|max:2048
        - old_price: required|numeric|min:0
        - new_price: required|numeric|min:0|lte:old_price
        - is_active: nullable|boolean
      Files: `app/Http/Requests/Admin/StoreProductRequest.php`, `app/Http/Requests/Admin/UpdateProductRequest.php`
      Verify: `php artisan test --filter ProductCrudTest`.

- [ ] 24. Create ProductImageService.
      File: `app/Services/ProductImageService.php`
      Methods:
        - `store(UploadedFile $file): string` — generates unique filename via `Str::uuid().'.'.$file->extension()`, stores to `Storage::disk('public')->putFileAs('products', $file, $filename)`, returns stored path string.
        - `delete(?string $path): void` — if path not null and `Storage::disk('public')->exists($path)`, deletes it.
      Decision: centralise all image I/O here so controllers stay thin and tests can mock this service.
      Files: `app/Services/ProductImageService.php`
      Verify: `php artisan test --filter ProductImageServiceTest`.

- [ ] 25. Create Admin ProductController.
      File: `app/Http/Controllers/Admin/ProductController.php`
      - inject `ProductImageService` via constructor
      - `index`: paginate 15, search by name, filter by category_id, subcategory_id, is_active; sort by newest (default) or price
      - `create`: pass all categories (active); JS will load subcategories via AJAX
      - `store`: use StoreProductRequest; call `$imageService->store()` FIRST, then create product; if DB create fails, call `$imageService->delete()` to clean up (wrap in try/catch or DB transaction)
      - `edit`: pass all categories and current product's subcategories
      - `update`: use UpdateProductRequest; if new image uploaded, call `$imageService->store()` to get new path, update product in DB transaction, THEN call `$imageService->delete($oldPath)` — only after successful DB update
      - `destroy`: call `$imageService->delete()` only after successful DB delete (inside transaction or after `$product->delete()` confirms)
      - `toggleActive`: flip is_active, redirect back
      Files: `app/Http/Controllers/Admin/ProductController.php`
      Verify: `php artisan test --filter ProductCrudTest`.

- [ ] 26. Create Product admin Blade views.
      Files:
        - `resources/views/admin/products/index.blade.php` — table with filters (category, subcategory, status selects + search), paginated results with thumbnail, toggle/edit/delete
        - `resources/views/admin/products/create.blade.php` — all fields, category+subcategory AJAX chained dropdowns, image preview JS
        - `resources/views/admin/products/edit.blade.php` — same as create, current image shown with "keep/replace" pattern
      The AJAX subcategory dropdown in create/edit calls `GET /admin/subcategories-by-category/{id}` (from Step 20) and populates the subcategory `<select>` via vanilla JS (no library needed).
      Verify: `php artisan test --filter ProductCrudTest`.

- [ ] 27. Register Product routes and Admin Dashboard.
      In `routes/web.php` admin group:
        - `Route::resource('products', ProductController::class)`
        - `Route::post('products/{product}/toggle-active', ...)->name('products.toggleActive')`
        - `Route::get('dashboard', AdminDashboardController@index)->name('dashboard')`
      Create `app/Http/Controllers/Admin/AdminDashboardController.php` with `index()` returning stats (total categories, subcategories, products, active/inactive counts) to `admin.dashboard` view.
      Files: `routes/web.php`, `app/Http/Controllers/Admin/AdminDashboardController.php`, `resources/views/admin/dashboard.blade.php`
      Verify: `php artisan route:list --path=admin` is complete; `php artisan test --filter AdminDashboardTest`.

- [ ] 28. Run `php artisan storage:link` to create the public symlink.
      This links `public/storage` → `storage/app/public` so uploaded images are web-accessible.
      Files: `public/storage` symlink (created by command)
      Verify: `php artisan storage:link` exits 0; `test -L public/storage` (or Windows equivalent).

---

## Phase 6 — Public Storefront

- [ ] 29. Create StorefrontController.
      File: `app/Http/Controllers/StorefrontController.php`
      Methods:
        - `home()`: fetch latest 8 active products (with active category+subcategory) + all active categories (with subcategory count); pass to `storefront.home`
        - `category(string $slug)`: find active category by slug or abort(404); paginate 12 active products in that category whose subcategory is also active; pass to `storefront.category`
        - `subcategory(string $categorySlug, string $subcategorySlug)`: find active category→find active subcategory (belongs to that category) or abort(404); paginate 12 products; pass to `storefront.subcategory`
        - `product(string $slug)`: find active product whose category and subcategory are both active, or abort(404); fetch 4 related active products from same subcategory; pass to `storefront.product`
      Files: `app/Http/Controllers/StorefrontController.php`
      Verify: `php artisan test --filter StorefrontTest`.

- [ ] 30. Create storefront Blade views and layout.
      Files:
        - `resources/views/layouts/storefront.blade.php` — clean white/slate layout, header with site name + category nav, footer, `@vite(['resources/css/app.css','resources/js/app.js'])`, SEO `<meta>` slot, OG slot
        - `resources/views/storefront/home.blade.php` — hero banner, category cards grid, latest products grid with product cards (image, name, prices, discount badge)
        - `resources/views/storefront/category.blade.php` — category header, subcategory pills, products grid, pagination
        - `resources/views/storefront/subcategory.blade.php` — subcategory header, products grid, pagination
        - `resources/views/storefront/product.blade.php` — large image, name, old/new price, discount %, category+subcategory breadcrumb, description, related products
        - `resources/views/storefront/partials/product-card.blade.php` — reusable product card component
      All views use `{{ }}` for output. No raw `{!! !!}` except for trusted pagination links.
      Verify: `php artisan test --filter StorefrontTest`.

- [ ] 31. Register storefront routes.
      In `routes/web.php` (public, no auth middleware):
        - `Route::get('/', [StorefrontController::class, 'home'])->name('home')`
        - `Route::get('/category/{slug}', ...)->name('storefront.category')`
        - `Route::get('/category/{categorySlug}/{subcategorySlug}', ...)->name('storefront.subcategory')`
        - `Route::get('/product/{slug}', ...)->name('storefront.product')`
      Move the old welcome route stub.
      Files: `routes/web.php`
      Verify: `php artisan route:list --path=category` shows storefront routes.

---

## Phase 7 — UI/UX Polish

- [ ] 32. Admin layout: sidebar, flash messages, active nav state.
      `resources/views/layouts/admin.blade.php` (created in step 18) finalized with:
        - Indigo-700 sidebar, white content area
        - Active nav link highlight (compare current route name)
        - `@if(session('success'))` / `@if(session('error'))` flash alert divs (auto-dismiss via JS)
        - Breadcrumb slot
        - `@stack('scripts')` before `</body>`
      Files: `resources/views/layouts/admin.blade.php`
      Verify: `npm run build` exits 0.

- [ ] 33. Add placeholder image for products.
      Store a simple SVG placeholder at `public/images/placeholder.png` (can be a minimal 1×1 PNG or a real placeholder).
      In Product views, display `Storage::url($product->image)` when image exists, otherwise show `/images/placeholder.png`.
      Files: `public/images/placeholder.png`, updated product views
      Verify: product views render without broken images.

- [ ] 34. JavaScript: AJAX subcategory chaining + slug auto-fill + image preview.
      All JS is vanilla, placed in `@push('scripts')` blocks in relevant Blade views (no global app.js changes needed for admin features).
        - Category select change → fetch `/admin/subcategories-by-category/{id}` → repopulate subcategory `<select>`
        - Name input blur → auto-fill slug field (convert to slug format) if slug is empty
        - Image file input change → show preview `<img>` below the input
      Files: `resources/views/admin/products/create.blade.php`, `resources/views/admin/products/edit.blade.php`, `resources/views/admin/categories/create.blade.php`, `resources/views/admin/subcategories/create.blade.php`
      Verify: `npm run build` exits 0.

---

## Phase 8 — Security Hardening

- [ ] 35. Security audit pass — no code changes if already correct.
      Confirm in each controller/view:
        - All forms have `@csrf`
        - No raw `{!! !!}` on user-supplied data
        - `$fillable` arrays are complete and do not allow `is_admin` mass-assignment on User (User's `$fillable` should NOT include `is_admin`; it's set directly in the seeder)
        - All file uploads validated with `mimes:` not just extension
        - DB queries use Eloquent (no raw SQL with user input)
        - Login rate limiting is active (throttle:5,1 middleware on POST login)
      Files: review only; fix any issues found in place
      Verify: `php artisan test` — full suite passes.

---

## Phase 9 — Documentation

- [ ] 36. Write README.md.
      File: `README.md`
      Sections: Project overview, Tech stack (Laravel 13, PHP 8.4, MySQL 8, Tailwind v4), Local setup (clone → composer install → cp .env.example .env → key:generate → DB config → migrate:fresh --seed → storage:link → npm install → npm run build → php artisan serve), Admin credentials (admin@banglapuzzle.com / password), Feature list, Testing instructions.
      Rationale for Laravel 13: PHP 8.4 fully supports it; no downgrade required.
      Files: `README.md`
      Verify: file exists and renders correctly on GitHub.

- [ ] 37. Write VPS_DEPLOYMENT.md.
      File: `VPS_DEPLOYMENT.md`
      Sections: Prerequisites (Ubuntu 22.04, Nginx, PHP 8.4-FPM, MySQL 8, Composer, Node 20+), Step-by-step deployment (git clone, composer install --no-dev, .env production config, migrate --force, storage:link, npm install + build, Nginx vhost config snippet, PHP-FPM config, systemd restart, file permissions: storage + bootstrap/cache writable by www-data).
      Files: `VPS_DEPLOYMENT.md`
      Verify: file exists.

---

## Phase 10 — Tests

- [ ] 38. Create PHPUnit Feature tests for Authentication.
      File: `tests/Feature/Admin/AuthTest.php`
      Test cases (all use `RefreshDatabase`):
        - `test_admin_login_page_loads` → GET /admin/login returns 200
        - `test_admin_can_login_with_valid_credentials` → POST /admin/login with is_admin user → redirects to dashboard
        - `test_non_admin_cannot_login` → POST /admin/login with is_admin=false user → redirected back with error
        - `test_login_requires_valid_credentials` → wrong password → validation error
        - `test_admin_can_logout` → authenticated POST /admin/logout → redirects to login
        - `test_admin_routes_require_auth` → unauthenticated GET /admin/dashboard → redirects to login
      Verify: `php artisan test --filter AuthTest` — all pass.

- [ ] 39. Create PHPUnit Feature tests for Category CRUD.
      File: `tests/Feature/Admin/CategoryCrudTest.php`
      Test cases: index accessible to admin, create page loads, store creates record and slugifies name, update modifies record, delete removes record, cannot delete category with subcategories, toggle active flips status, search filters results.
      All use `RefreshDatabase` + `actingAs($adminUser)`.
      Verify: `php artisan test --filter CategoryCrudTest` — all pass.

- [ ] 40. Create PHPUnit Feature tests for Subcategory CRUD.
      File: `tests/Feature/Admin/SubcategoryCrudTest.php`
      Test cases: same CRUD pattern; also test composite slug uniqueness, byCategory AJAX endpoint returns JSON, cannot delete subcategory with products.
      Verify: `php artisan test --filter SubcategoryCrudTest` — all pass.

- [ ] 41. Create PHPUnit Feature tests for Product CRUD.
      File: `tests/Feature/Admin/ProductCrudTest.php`
      Test cases: index, create, store with image (`Storage::fake('public')`, `UploadedFile::fake()->image()`), update with new image (old image deleted), update without new image (old image preserved), delete (image deleted), new_price > old_price rejected, subcategory belonging to wrong category rejected.
      Use `Storage::fake('public')` in all image tests.
      Verify: `php artisan test --filter ProductCrudTest` — all pass.

- [ ] 42. Create PHPUnit Feature tests for Storefront.
      File: `tests/Feature/StorefrontTest.php`
      Test cases: home page loads (200), inactive product not shown on home, category page returns 200 for active category, category page returns 404 for inactive category, subcategory page returns 200, subcategory belonging to wrong category returns 404, product detail page returns 200, inactive product returns 404, product with inactive category returns 404, product with inactive subcategory returns 404.
      Verify: `php artisan test --filter StorefrontTest` — all pass.

- [ ] 43. Run full test suite and confirm all pass.
      Command: `php artisan test`
      Expected: all tests in tests/Feature/ and tests/Unit/ pass with 0 failures.
      Files: none
      Verify: `php artisan test` exits 0.

---

## Key implementation decisions (reference for coder)

| Decision | Choice | Rationale |
|---|---|---|
| Laravel version | 13.40.0 (already installed) | PHP 8.4 is compatible; no downgrade needed |
| Auth scaffolding | Manual controller + `Auth::attempt()` | No Breeze/Jetstream — keeps it minimal and auditable |
| Admin guard | `is_admin` column on users table | Simplest single-guard approach; no need for separate guard config |
| Slug generation | `Str::slug()` in model `boot()` via `creating`/`updating` events | Auto-generates; controller can still override; loops to ensure uniqueness |
| Image storage | `Storage::disk('public')->putFileAs('products', $file, $uuid.$ext)` | Uses Laravel's abstraction; no path concatenation; `storage:link` required |
| Image delete safety | Delete old image only after DB update succeeds | Prevents orphaned files or missing image references |
| Tailwind | v4 already configured via Vite plugin | Do NOT add tailwind.config.js or run `npx tailwindcss init` |
| Middleware registration | `->withMiddleware()` alias in `bootstrap/app.php` | Laravel 13 fluent API, no Http/Kernel.php |
| Test DB | SQLite in-memory (phpunit.xml already set) | Fast; no MySQL needed for tests |
| JS approach | Vanilla JS in `@push('scripts')` | No build step change; no npm package additions |
| Money columns | `decimal(12,2)` | No floating point; safe for monetary values |
| Storefront 404 | `abort(404)` for inactive items | Proper HTTP semantics; no redirect |
