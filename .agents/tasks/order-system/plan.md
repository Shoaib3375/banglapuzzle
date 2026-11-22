# Implementation Plan — BanglaPuzzle Order System

## Codebase conventions observed

- Migrations use `new class extends Migration` anonymous class syntax; FKs declared with `->foreign()->references()->on()->onDelete()` (verbose form, not `foreignId`). Decimal columns use `decimal(12, 2)` and `indexes()` added separately.
- Models: no `__construct`, explicit `casts()` method (not property), `$fillable` array, relationships as plain methods.
- Controllers: typed constructor property injection where DI used; `->with('success', '...')` flash; return `view('dot.path', compact(...))`.
- Admin namespace: `App\Http\Controllers\Admin\*`. Route resource names follow `admin.orders.*`.
- Blade: `@extends('layouts.admin')`, `@section('title', ...)`, `@section('page-title', ...)`, `@section('content')`. Uses `x-admin.stat-card`, `x-admin.page-header`, `x-badge`, `x-empty-state`, `x-alert` components.
- `x-badge` variants: `emerald`, `red`, `blue`, `gray` (default).
- `x-empty-state` takes `icon` (SVG path string or emoji), `title`, `description`, optional `actionLabel`+`actionUrl`.
- `x-admin.page-header` takes `title`, optional `createRoute`, `createLabel`.
- `x-admin.stat-card` takes `label`, `value`, `subtext`, `iconBg`, `iconColor`, `iconPath`.
- Cart JS: `Cart.getItems()` returns items with shape `{ id, name, price, oldPrice, image, url, category, qty }`.
- Tests: `use RefreshDatabase`, `User::factory()->create(['is_admin' => true])`, actingAs pattern, PHPUnit style assertions.
- Image rendering (three-case): `str_starts_with($img, 'http') ? $img : ($img ? Storage::disk('public')->url($img) : asset('images/placeholder.png'))` — see `product-card.blade.php`.
- Admin sidebar active pattern: `request()->routeIs('admin.xxx.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-100'`.
- Storefront layout: `@extends('layouts.storefront')`, `@section('meta_title', ...)`, `@section('content')`.
- Flash keys: `session('success')` / `session('error')` — already wired in `layouts/admin.blade.php`.
- Pint required after any PHP file change: `vendor/bin/pint --dirty --format agent`.

---

## Ordered implementation steps

- [ ] 1. Create orders migration
      `php artisan make:migration create_orders_table --no-interaction`
      Then fill: `id`, `user_id` (nullable unsignedBigInteger, FK → users, SET NULL), `order_number` (string, unique), `full_name`, `phone`, `email` (nullable), `address`, `city`, `district`, `postcode` (nullable), `landmark` (nullable), `note` (nullable text), `payment_method` (string, default `cod`), `subtotal`/`delivery_charge`/`total` (decimal 10,2, delivery_charge default 0), `status` (string, default `pending`), timestamps. Index `user_id`, `status`.
      Files: new migration file under `database/migrations/`
      Verify: `php artisan migrate` exits 0

- [ ] 2. Create order_items migration
      `php artisan make:migration create_order_items_table --no-interaction`
      Fill: `id`, `order_id` (unsignedBigInteger, FK → orders, cascade), `product_id` (nullable unsignedBigInteger, FK → products, SET NULL), `product_name`/`product_slug` (string), `product_image` (nullable string), `price`/`old_price` (decimal 10,2; old_price default 0), `quantity` (unsignedInteger), `subtotal` (decimal 10,2), timestamps.
      Files: new migration file under `database/migrations/`
      Verify: `php artisan migrate` exits 0 (cumulative)

- [ ] 3. Create Order model and OrderItem model
      `php artisan make:model Order --no-interaction`
      `php artisan make:model OrderItem --no-interaction`
      Order model: STATUS_ constants (PENDING/CONFIRMED/PROCESSING/SHIPPED/DELIVERED/CANCELLED), `$fillable` (all columns), `casts()` (subtotal/delivery_charge/total → `decimal:2`), `belongsTo(User::class)`, `hasMany(OrderItem::class)`, static `generateOrderNumber()` using `'BP-'.strtoupper(Str::random(8))`, `scopeForUser`, `getStatusLabelAttribute()`, `getStatusColorAttribute()` returning Tailwind badge color.
      OrderItem model: `$fillable`, `casts()` (price/old_price/subtotal → `decimal:2`), `belongsTo(Order::class)`, `belongsTo(Product::class)->withDefault()`, `getDiscountPercentageAttribute()`.
      Files: `app/Models/Order.php`, `app/Models/OrderItem.php`
      Verify: `php artisan tinker --execute 'echo App\Models\Order::class;'` exits 0

- [ ] 4. Update User model — add orders relationship
      Add `hasMany(Order::class)` method and `use App\Models\Order` import to `app/Models/User.php`.
      Files: `app/Models/User.php`
      Verify: `php artisan test --compact` — all 79 tests still pass

- [ ] 5. Create OrderFactory
      `php artisan make:factory OrderFactory --model=Order --no-interaction`
      States: default (pending, cod, realistic address fields, random subtotal/delivery/total), plus `forUser(int $userId)` state.
      Also create `OrderItemFactory` for tests.
      Files: `database/factories/OrderFactory.php`, `database/factories/OrderItemFactory.php`
      Verify: Factory instantiates without error (checked in step 7 tests)

- [ ] 6. Add routes for orders (storefront + customer + admin)
      In `routes/web.php`:
      - Public (no auth): `Route::post('/orders', ...)` named `orders.store`, throttle 10,1; `Route::get('/orders/{order:order_number}', ...)` named `orders.show`.
      - Inside existing `['auth','customer']` group: `Route::get('/my-orders', ...)` named `orders.index`.
      - Inside existing `['auth','admin']` + prefix `admin` group: `Route::resource('orders', Admin\OrderController::class)->only(['index','show'])` + `Route::post('orders/{order}/status', ...)` named `orders.updateStatus`.
      Add imports: `use App\Http\Controllers\OrderController;` and `use App\Http\Controllers\Admin\OrderController as AdminOrderController;` (alias to avoid collision). Or use inline FQCN.
      Files: `routes/web.php`
      Verify: `php artisan route:list --path=orders` shows all 5 routes

- [ ] 7. Create storefront OrderController
      `php artisan make:controller OrderController --no-interaction`
      Methods: `store(Request $request)`, `show(Order $order)`, `myOrders()`.
      `store`: validate → load products from DB → use `$product->new_price` (never client price) → calculate subtotal/delivery/total → `DB::transaction` to create Order + OrderItems → return JSON `{success, order_number, redirect}` or 422 JSON on validation failure.
      `show`: privacy check (if `user_id` set and auth user ≠ owner → 403) → `$order->load('items.product')` → return view.
      `myOrders`: `Order::forUser(Auth::id())->with('items')->latest()->paginate(10)` → return view.
      Files: `app/Http/Controllers/OrderController.php`
      Verify: `php artisan route:list --path=orders` still clean

- [ ] 8. Create admin OrderController
      `php artisan make:controller Admin/OrderController --no-interaction`
      Methods: `index(Request $request)` (paginate 20, filter by `?status=`, search by `?search=` on order_number/full_name), `show(Order $order)`, `updateStatus(Request $request, Order $order)`.
      Files: `app/Http/Controllers/Admin/OrderController.php`
      Verify: PHP syntax clean (Pint pass)

- [ ] 9. Update checkout.blade.php — wire form to server
      Change `<form id="checkout-form">` to include `action="{{ route('orders.store') }}"` and `method="POST"`. Move `@csrf` inside the form if it isn't already (it is already inside).
      Also add `name="payment_method"` to the radio inputs (currently `name="payment"` — rename to `payment_method` to match validation).
      Replace the entire JS `form.addEventListener('submit', ...)` block with the fetch-based handler that: reads `Cart.getItems()` (shape: `{id, qty, price}`), appends `items[i][product_id]`, `items[i][quantity]`, `items[i][price]` to FormData, fetches `form.action` as POST with `X-Requested-With: XMLHttpRequest`, on success calls `Cart.clear()` then `window.location.href = data.redirect`, on 422 shows server validation errors by matching `data.errors` keys to `[data-error="field"]` elements.
      Files: `resources/views/storefront/checkout.blade.php`
      Verify: `npm run build` exits 0

- [ ] 10. Create order confirmation view (storefront/orders/show.blade.php)
       `mkdir resources/views/storefront/orders`
       Create `resources/views/storefront/orders/show.blade.php`:
       - `@extends('layouts.storefront')`, meta_title with order number
       - Hero: green checkmark circle, "Order Placed Successfully!", order_number in `font-mono`
       - Two-column grid (lg): LEFT = items list (image using three-case logic on `product_image`, product_name, qty, price, subtotal); RIGHT = summary card (full_name, phone, address/city/district, payment_method badge, subtotal/delivery/total)
       - "Continue Shopping" → `route('home')`; if `Auth::check() && !Auth::user()->is_admin`: "View My Orders" → `route('orders.index')`
       Files: `resources/views/storefront/orders/show.blade.php`
       Verify: view renders (tested in step 13)

- [ ] 11. Create my orders view (storefront/orders/index.blade.php)
       Create `resources/views/storefront/orders/index.blade.php`:
       - `@extends('layouts.storefront')`, meta_title "My Orders - BanglaPuzzle"
       - Breadcrumb: Home > My Orders
       - Table/card list: order_number (link to orders.show), date (`created_at->format('d M Y')`), items count (`$order->items->count()`), total (formatted), status badge using `x-badge` with variant from `$order->status_color`, View button
       - `$orders->links()` for pagination
       - Empty state using `x-empty-state` if no orders
       Files: `resources/views/storefront/orders/index.blade.php`
       Verify: view renders (tested in step 13)

- [ ] 12. Create admin order views
       Create `resources/views/admin/orders/index.blade.php`:
       - `@extends('layouts.admin')`, title/page-title "Orders"
       - `<x-admin.page-header title="Orders" />` (no create button for orders)
       - Status filter tabs: All | Pending | Confirmed | Processing | Shipped | Delivered | Cancelled (using GET `?status=` param, active tab highlighted with `bg-blue-600 text-white`)
       - Search bar (GET `?search=`)
       - Table: Order #, Customer (name + email or "Guest"), Date, Items count, Total (৳ formatted), Payment, Status badge (`x-badge`), Actions (View link)
       - Pagination
       - Empty state if no orders
       Create `resources/views/admin/orders/show.blade.php`:
       - `@extends('layouts.admin')`, title with order number
       - Back link to `admin.orders.index`
       - Customer info card, delivery address card
       - Items table (image three-case, name, qty, price, subtotal) in a `bg-white rounded-xl shadow-sm overflow-hidden` wrapper
       - Order summary (subtotal, delivery, total)
       - Status update form: `<select name="status">` with all STATUS_ values, `POST` to `route('admin.orders.updateStatus', $order)` with `@csrf`
       Files: `resources/views/admin/orders/index.blade.php`, `resources/views/admin/orders/show.blade.php`
       Verify: views render in tests

- [ ] 13. Update admin sidebar — add Orders link
        In `resources/views/layouts/admin.blade.php`, after the Products `<a>` block, add an Orders `<a>` block following the exact same pattern:
        - `href="{{ route('admin.orders.index') }}"`
        - Active: `request()->routeIs('admin.orders.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-100'`
        - SVG clipboard icon (path: `M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2`), `w-4 h-4 shrink-0`, stroke-width 2
        - Label: "Orders"
        Files: `resources/views/layouts/admin.blade.php`
        Verify: `php artisan test --compact` passes

- [ ] 14. Update admin DashboardController + dashboard view
        In `app/Http/Controllers/Admin/DashboardController.php`: add `use App\Models\Order;`, add `'orderCount' => Order::count()` and `'pendingOrderCount' => Order::where('status', 'pending')->count()` to the view data array.
        In `resources/views/admin/dashboard/index.blade.php`: add two stat cards after the existing five, following the same `x-admin.stat-card` pattern:
        - Total Orders: iconBg `bg-blue-100`, iconColor `text-blue-600`, iconPath clipboard path
        - Pending Orders: iconBg `bg-orange-100`, iconColor `text-orange-600`, iconPath hourglass path (`M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z`)
        Files: `app/Http/Controllers/Admin/DashboardController.php`, `resources/views/admin/dashboard/index.blade.php`
        Verify: `php artisan test --filter=test_admin_can_access_dashboard --compact` passes (add `orderCount`/`pendingOrderCount` assertions there)

- [ ] 15. Update profile page — add My Orders section
        In `resources/views/storefront/auth/profile.blade.php`, add a new card section at the bottom (after the change-password card) with a "My Orders" heading, a brief description, and a link button to `route('orders.index')`.
        Files: `resources/views/storefront/auth/profile.blade.php`
        Verify: profile view renders (existing `test_authenticated_user_can_view_profile` still passes)

- [ ] 16. Write and run storefront order tests (append to StorefrontTest.php)
        Append to `tests/Feature/StorefrontTest.php`:
        - `test_guest_can_place_order`: POST `/orders` with valid items (create product in DB), assert 200 JSON `success=true`, assertDatabaseHas orders
        - `test_authenticated_user_order_linked_to_account`: same but actingAs customer, assert order `user_id` = customer id
        - `test_order_confirmation_page_loads`: create Order via factory, GET `/orders/{order_number}` as guest, assert 200
        - `test_my_orders_page_requires_auth`: GET `/my-orders` as guest, assertRedirect to `user.login`
        Files: `tests/Feature/StorefrontTest.php`
        Verify: `php artisan test tests/Feature/StorefrontTest.php --compact` — all tests pass

- [ ] 17. Create admin order tests
        Create `tests/Feature/Admin/OrderAdminTest.php` (new file, not appended to CategoryCrudTest):
        - `test_admin_can_view_orders_list`: actingAs admin, create 2 orders via factory, GET `admin.orders.index`, assertStatus 200
        - `test_admin_can_view_order_detail`: GET `admin.orders.show`, assertStatus 200, assertSee order number
        - `test_admin_can_update_order_status`: POST `admin.orders.updateStatus` with `status=confirmed`, assertRedirect, assertDatabaseHas `status=confirmed`
        - `test_guest_cannot_access_admin_orders`: GET `admin.orders.index` as guest, assertRedirect to admin.login
        Files: `tests/Feature/Admin/OrderAdminTest.php`
        Verify: `php artisan test tests/Feature/Admin/OrderAdminTest.php --compact` — all pass

- [ ] 18. Run full test suite + build
        Run `vendor/bin/pint --dirty --format agent` first to fix formatting.
        Run `php artisan test --compact` — all tests pass (≥ 87 tests, 0 failures).
        Run `npm run build` — exits 0.
        Run `php artisan route:list --path=orders` — verify all 5 order routes present.
        Files: (no new files)
        Verify: 0 failures, 0 errors

- [ ] 19. Commit
        `git add` all new/modified files (excluding .env, vendor, node_modules, public/build, .agents/).
        Commit message: `feat: order system — database orders, admin order management, customer order history`
        Files: all changed files
        Verify: `git log --oneline -1` shows the commit
