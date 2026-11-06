# Implementation Plan — BanglaPuzzle Premium UI/UX Redesign

> **FRONTEND-ONLY**: No controllers, models, routes, migrations, Form Requests, or tests are touched.  
> **Tailwind v4** — no `tailwind.config.js`, no arbitrary bracket values.  
> **55 existing tests must continue to pass.**

---

## Design System Decisions

- **Color palette**: white sidebar (`bg-white`), slate-50 content area (`bg-slate-50`), `blue-600` primary CTA replacing the existing `indigo-600`, `emerald-*` for active/success badges, `red-*` for inactive/danger badges.
- **Cards**: `rounded-xl shadow-sm` throughout admin; `rounded-2xl shadow-sm` on storefront cards (matching existing product card convention).
- **Admin layout**: Keep the sidebar-left + topbar pattern that already exists; upgrade sidebar from `bg-indigo-800` to `bg-white border-r border-slate-200` (light sidebar, premium feel).
- **Storefront layout**: Keep existing structure; upgrade nav and hero sections.
- **Components**: Seven shared Blade components extracted so every page can use them consistently.
- **Flash keys**: `session('success')` / `session('error')` — exactly as existing layout.
- **Image logic**: THREE-CASE: `str_starts_with($product->image, 'http') ? … : ($product->image ? Storage::disk('public')->url($product->image) : asset('images/placeholder.png'))`.
- **`$navCategories`**: shared via `View::composer('layouts.storefront', …)` in `AppServiceProvider`. Available automatically in `layouts/storefront.blade.php` only.

---

## Step-by-Step Plan

- [ ] 1. **Create `resources/views/components/badge.blade.php`**  
      A reusable inline badge. Accepts: `$variant` (emerald|red|blue|gray, default=gray), `$text` (string).  
      Renders `<span>` with `rounded-full px-2.5 py-0.5 text-xs font-semibold` plus variant-specific color classes:  
      - `emerald` → `bg-emerald-100 text-emerald-700`  
      - `red` → `bg-red-100 text-red-700`  
      - `blue` → `bg-blue-100 text-blue-700`  
      - `gray` → `bg-slate-100 text-slate-600`  
      Files: `resources/views/components/badge.blade.php`  
      Verify: `php artisan test --compact` — all 55 tests still pass.

- [ ] 2. **Create `resources/views/components/alert.blade.php`**  
      Dismissible alert bar. Accepts: `$type` (success|error), `$message` (string).  
      Uses Tailwind utility classes:  
      - `success` → `bg-emerald-50 border border-emerald-200 text-emerald-800`  
      - `error` → `bg-red-50 border border-red-200 text-red-700`  
      Include a dismiss button (`×`) using plain JS (`this.closest('[role=alert]').remove()`).  
      Files: `resources/views/components/alert.blade.php`  
      Verify: `php artisan test --compact`

- [ ] 3. **Create `resources/views/components/empty-state.blade.php`**  
      Centered empty-state card. Accepts: `$title`, `$description`, `$icon` (SVG path data string), `$actionLabel` (optional), `$actionUrl` (optional).  
      Renders: `bg-white rounded-2xl shadow-sm p-16 text-center` with icon circle, heading, text, and optional link button.  
      Files: `resources/views/components/empty-state.blade.php`  
      Verify: `php artisan test --compact`

- [ ] 4. **Create `resources/views/components/admin/stat-card.blade.php`**  
      Dashboard stat tile. Accepts: `$label`, `$value`, `$subtext` (optional), `$iconPath` (SVG path d= string), `$iconBg` (Tailwind bg class, e.g. `bg-blue-100`), `$iconColor` (Tailwind text class, e.g. `text-blue-600`).  
      Renders: `bg-white rounded-xl shadow-sm p-6 flex items-center gap-4` with icon circle + numeric value + sub-label.  
      Files: `resources/views/components/admin/stat-card.blade.php`  
      Verify: `php artisan test --compact`

- [ ] 5. **Create `resources/views/components/admin/page-header.blade.php`**  
      Page header row. Accepts: `$title`, `$createRoute` (optional named route string), `$createLabel` (optional, default `+ New`).  
      Renders a `<div class="flex items-center justify-between mb-6">` with an `<h2>` title on the left and an optional blue CTA button (linking to `route($createRoute)`) on the right.  
      Files: `resources/views/components/admin/page-header.blade.php`  
      Verify: `php artisan test --compact`

- [ ] 6. **Create `resources/views/components/storefront/product-card.blade.php`**  
      Storefront product card (replaces existing `storefront/partials/product-card.blade.php`).  
      Accepts: `$product` (Product model).  
      Must use THREE-CASE image logic:  
      ```php
      $imageUrl = str_starts_with($product->image, 'http')
          ? $product->image
          : ($product->image
              ? Storage::disk('public')->url($product->image)
              : asset('images/placeholder.png'));
      ```  
      Renders: hover lift card with aspect-square image, discount badge, category label, product name, price row with strikethrough old price, orange "View Details" CTA.  
      Link: `route('storefront.product', $product->slug)`.  
      Exact design tokens: `rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all`.  
      Files: `resources/views/components/storefront/product-card.blade.php`  
      Verify: `php artisan test --compact`

- [ ] 7. **Create `resources/views/components/storefront/breadcrumb.blade.php`**  
      Multi-level breadcrumb. Accepts: `$items` (array of `['label' => string, 'url' => string|null]`).  
      Renders `<nav aria-label="Breadcrumb">` with `›` separators. Last item has no link and uses `text-white` or `text-gray-700` (caller controls via a `$dark` bool prop: default `false` = light on dark hero, `true` = dark on white bg).  
      Files: `resources/views/components/storefront/breadcrumb.blade.php`  
      Verify: `php artisan test --compact`

- [ ] 8. **Rewrite `resources/views/layouts/admin.blade.php`**  
      Full rewrite. Keep all existing `@yield`/`@stack` slot names:  
      - `@yield('title')` in `<title>`  
      - `@yield('page-title')` in topbar h1  
      - `@yield('content')` in `<main>`  
      - `@stack('scripts')` before `</body>`  
      New design:  
      - Body: `bg-slate-50 min-h-screen flex`  
      - Sidebar: `w-64 min-h-screen bg-white border-r border-slate-200 flex flex-col shrink-0`  
      - Brand area: logo mark (blue-600 square icon) + "BanglaPuzzle" text + "Admin" chip  
      - Nav links: active = `bg-blue-50 text-blue-700 font-semibold`, idle = `text-slate-600 hover:bg-slate-100`; keep all four routes: dashboard, categories, subcategories, products  
      - Logout form at bottom: `POST admin.logout` + `@csrf`  
      - Topbar: `bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between`  
      - Topbar right: `Auth::user()->name` in a small avatar chip  
      - Flash: use `<x-alert>` component for `session('success')` and `session('error')`  
      - `@vite(['resources/css/app.css', 'resources/js/app.js'])` in `<head>`  
      Files: `resources/views/layouts/admin.blade.php`  
      Verify: `php artisan test --compact`

- [ ] 9. **Rewrite `resources/views/layouts/storefront.blade.php`**  
      Full rewrite. Keep existing slot names:  
      - `@yield('meta_title')`, `@yield('meta_description')`, `@yield('og_tags')`, `@yield('content')`, `@stack('scripts')`  
      - `$navCategories` is available via `View::composer` — do not add it to the controller  
      Preserve existing structure: topbar → sticky header → flash → `<main>` → footer.  
      Upgrade: richer logo treatment, upgraded mobile hamburger, flash via `<x-alert>` component.  
      Topbar: `bg-slate-900 text-slate-400 text-xs h-9`.  
      Header: `bg-white shadow-sm sticky top-0 z-50`.  
      Keep mobile toggle JS (`nav-toggle` / `mobile-nav` ids) identical so existing views work.  
      Footer: three-column grid preserved.  
      Files: `resources/views/layouts/storefront.blade.php`  
      Verify: `php artisan test --compact`

- [ ] 10. **Rewrite `resources/views/admin/auth/login.blade.php`**  
       Standalone page (no layout extends — already standalone in current code).  
       Keep: `@vite`, `POST admin.login.post`, `@csrf`, `email`/`password`/`remember` fields with `old()` values, `session('error')` + `$errors->any()` display.  
       New design: full-height split layout or centered card.  
       Use `<x-alert>` component for error messages.  
       Error field styling: `@error('email') border-red-400 @enderror`.  
       Files: `resources/views/admin/auth/login.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 11. **Rewrite `resources/views/admin/dashboard/index.blade.php`**  
       Extends `layouts.admin`. Sections: `title` = "Dashboard", `page-title` = "Dashboard".  
       Variables: `$categoryCount`, `$activeCategoryCount`, `$subcategoryCount`, `$productCount`, `$activeProductCount`, `$inactiveProductCount` (all from DashboardController).  
       Use `<x-admin.stat-card>` for each of the 5 stats.  
       Stat card parameters:  
       - Total Categories: `$label="Total Categories"`, `$value=$categoryCount`, `$subtext="$activeCategoryCount active"`, blue  
       - Total Subcategories: `$label="Subcategories"`, `$value=$subcategoryCount`, purple  
       - Total Products: `$label="Total Products"`, `$value=$productCount`, blue  
       - Active Products: `$label="Active Products"`, `$value=$activeProductCount`, emerald  
       - Inactive Products: `$label="Inactive Products"`, `$value=$inactiveProductCount`, red  
       Add quick-action links row (→ Categories, → Products, → Subcategories).  
       Files: `resources/views/admin/dashboard/index.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 12. **Rewrite `resources/views/admin/categories/index.blade.php`**  
       Extends `layouts.admin`. Sections: `title` = "Categories", `page-title` = "Categories".  
       Variables: `$categories` (paginated, has `subcategories_count`, `products_count`, `id`, `name`, `slug`, `is_active`).  
       Use `<x-admin.page-header>` with `createRoute="admin.categories.create"` and `createLabel="+ New Category"`.  
       Search form: `GET admin.categories.index` with `name=search` input and clear link when `request('search')` is set.  
       Table: `bg-white rounded-xl shadow-sm overflow-hidden` — columns: #, Name, Slug, Subcategories, Products, Status, Actions.  
       Status toggle: `POST admin.categories.toggleActive` + `@csrf` — use `<x-badge>` with `variant=emerald` for active, `variant=red` for inactive.  
       Edit: `GET admin.categories.edit`.  
       Delete: `POST admin.categories.destroy` + `@csrf` + `@method('DELETE')` + `onsubmit confirm()`.  
       Empty state: `<x-empty-state>` component.  
       Pagination: `$categories->links()` when `$categories->hasPages()`.  
       Files: `resources/views/admin/categories/index.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 13. **Rewrite `resources/views/admin/categories/create.blade.php`**  
       Extends `layouts.admin`. Sections: `title` = "Create Category", `page-title` = "Create Category".  
       No variables from controller (create() passes nothing).  
       Form: `POST admin.categories.store` + `@csrf`.  
       Fields: `name` (required, maxlength=100), `slug` (optional, maxlength=120, font-mono), `description` (textarea, rows=3), `is_active` checkbox (default checked via `old('is_active', '1')`).  
       All `@error` blocks with `border-red-400` styling.  
       Back link → `admin.categories.index`.  
       JS: `@push('scripts')` — slug autofill from name on blur (if slug is empty).  
       Files: `resources/views/admin/categories/create.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 14. **Rewrite `resources/views/admin/categories/edit.blade.php`**  
       Extends `layouts.admin`. Sections: `title` = "Edit Category", `page-title` = "Edit Category".  
       Variables: `$category` (Category model with `id`, `name`, `slug`, `description`, `is_active`).  
       Form: `POST admin.categories.update($category)` + `@csrf` + `@method('PUT')`.  
       Fields: same as create but pre-populated with `old('field', $category->field)`.  
       Back link → `admin.categories.index`.  
       JS: `@push('scripts')` — slug autofill on blur.  
       Files: `resources/views/admin/categories/edit.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 15. **Rewrite `resources/views/admin/subcategories/index.blade.php`**  
       Extends `layouts.admin`. Sections: `title` = "Subcategories", `page-title` = "Subcategories".  
       Variables: `$subcategories` (paginated, with `category` eager-loaded), `$categories` (all, for filter dropdown).  
       Use `<x-admin.page-header>` with `createRoute="admin.subcategories.create"`.  
       Category filter: `<select name="category_id" onchange="this.form.submit()">` in GET form + clear link.  
       Table: columns: #, Name, Category, Slug, Status, Actions.  
       Status toggle: `POST admin.subcategories.toggleActive` + `@csrf` — use `<x-badge>`.  
       Edit/Delete actions: same pattern as categories index.  
       Empty state: `<x-empty-state>`.  
       Pagination.  
       Files: `resources/views/admin/subcategories/index.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 16. **Rewrite `resources/views/admin/subcategories/create.blade.php`**  
       Extends `layouts.admin`. Sections: `title` = "Create Subcategory".  
       Variables: `$categories` (all Category records ordered by name).  
       Form: `POST admin.subcategories.store` + `@csrf`.  
       Fields: `category_id` select (required), `name` (required, maxlength=100), `slug` (optional, maxlength=120), `description` (textarea), `is_active` checkbox (default checked).  
       `old()` restoration on all fields.  
       Back link → `admin.subcategories.index`.  
       JS: slug autofill on blur.  
       Files: `resources/views/admin/subcategories/create.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 17. **Rewrite `resources/views/admin/subcategories/edit.blade.php`**  
       Extends `layouts.admin`. Sections: `title` = "Edit Subcategory".  
       Variables: `$subcategory` (Subcategory model), `$categories` (all Category records).  
       Form: `POST admin.subcategories.update($subcategory)` + `@csrf` + `@method('PUT')`.  
       Pre-populate all fields with `old('field', $subcategory->field)`.  
       `category_id` uses `old('category_id', $subcategory->category_id)`.  
       JS: slug autofill on blur.  
       Files: `resources/views/admin/subcategories/edit.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 18. **Rewrite `resources/views/admin/products/index.blade.php`**  
       Extends `layouts.admin`. Sections: `title` = "Products".  
       Variables: `$products` (paginated, with category + subcategory), `$categories`, `$subcategories` (may be empty collection).  
       Filter form: `GET admin.products.index` — inputs: `search` text, `category_id` select, `subcategory_id` select, `is_active` select, `sort` select; Filter submit + Clear link when any filter is active; "+ New Product" CTA on right.  
       Table: columns: Image (thumbnail), Name, Category, Subcategory, Old Price, New Price, Status, Actions.  
       Image: THREE-CASE logic (same as existing).  
       Status toggle: `POST admin.products.toggleActive` + `@csrf` + `<x-badge>`.  
       Edit/Delete: same action patterns.  
       Empty state: `<x-empty-state>`.  
       Pagination.  
       JS: `@push('scripts')` — AJAX subcategory filter reload on category_id change using `route('admin.subcategories.byCategory', ['category' => '__ID__'])` URL template (identical to existing JS).  
       Files: `resources/views/admin/products/index.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 19. **Rewrite `resources/views/admin/products/create.blade.php`**  
       Extends `layouts.admin`. Sections: `title` = "Create Product".  
       Variables: `$categories` (all Category records ordered by name).  
       Form: `POST admin.products.store` + `@csrf` + `enctype="multipart/form-data"`.  
       Fields: `category_id` select, `subcategory_id` select (AJAX-populated), `name` (maxlength=150), `slug` (optional), `description` (textarea, rows=4), `image` file input (accept="image/jpeg,image/jpg,image/png,image/webp") with preview div (`id="image-preview-wrap"` hidden + `id="image-preview"`), `old_price` number, `new_price` number, `is_active` checkbox (default checked).  
       All `old()` values and `@error` displays.  
       Back link → `admin.products.index`.  
       JS: `@push('scripts')` — three behaviors:  
         1. AJAX subcategory load on category change (restore `old('category_id')` / `old('subcategory_id')` on page load)  
         2. Slug autofill from name on blur (if slug empty)  
         3. Image preview on file change (FileReader API)  
       Files: `resources/views/admin/products/create.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 20. **Rewrite `resources/views/admin/products/edit.blade.php`**  
       Extends `layouts.admin`. Sections: `title` = "Edit Product".  
       Variables: `$product` (Product model), `$categories` (all), `$subcategories` (subcategories for current category).  
       Form: `POST admin.products.update($product)` + `@csrf` + `@method('PUT')` + `enctype="multipart/form-data"`.  
       Fields: same as create, pre-populated with `old('field', $product->field)`.  
       Current image display: if `$product->image` exists, show existing thumbnail using THREE-CASE logic (`str_starts_with` check) + "upload to replace" hint text.  
       JS: same three behaviors as create; initial category/subcategory loaded from server-rendered `$subcategories` collection (no AJAX on page load — only on category change).  
       `currentCategoryId` / `currentSubcategoryId` Blade variables interpolated into JS for AJAX re-select.  
       Files: `resources/views/admin/products/edit.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 21. **Rewrite `resources/views/storefront/home.blade.php`**  
       Extends `layouts.storefront`. Section: `meta_title` = "BanglaPuzzle Shop — Home". Section: `content`.  
       Variables: `$categories` (Collection with `subcategories_count`), `$products` (Collection, max 8).  
       Preserve all existing sections in order:  
       1. Hero banner (gradient, CTA buttons, stats row)  
       2. USP trust strip (4-column grid)  
       3. Categories showcase (`id="categories"`) — grid with colored icon tiles using `<x-storefront.product-card>` NOT used here (categories only)  
       4. Promotional banner  
       5. Latest products (`id="products"`) — grid using `<x-storefront.product-card :product="$product">` for each item  
       6. Empty state via `<x-empty-state>` if `$products->isEmpty()`  
       7. Newsletter strip  
       Use `Str::plural()` for subcategory count on category tiles.  
       Files: `resources/views/storefront/home.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 22. **Rewrite `resources/views/storefront/category.blade.php`**  
       Extends `layouts.storefront`. Section: `meta_title` = `$category->name . ' — BanglaPuzzle Shop'`. Section: `content`.  
       Variables: `$category` (active Category), `$subcategories` (active subcategories of that category), `$products` (paginated LengthAwarePaginator, 12 per page).  
       Use `<x-storefront.breadcrumb :items="[['label'=>'Home','url'=>route('home')], ['label'=>$category->name,'url'=>null]]">`.  
       Hero: gradient banner with inline breadcrumb and category title + optional description.  
       Subcategory filter pills: `route('storefront.subcategory', [$category->slug, $subcategory->slug])`.  
       Products grid: `<x-storefront.product-card :product="$product">` in grid; `$products->links()` if `hasPages()`.  
       Empty state: `<x-empty-state>` with back-to-home link.  
       Files: `resources/views/storefront/category.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 23. **Rewrite `resources/views/storefront/subcategory.blade.php`**  
       Extends `layouts.storefront`. Section: `meta_title` = `$subcategory->name . ' — ' . $subcategory->category->name`.  
       Variables: `$category`, `$subcategory` (both active), `$products` (paginated).  
       Use `<x-storefront.breadcrumb :items="[['label'=>'Home','url'=>route('home')], ['label'=>$category->name,'url'=>route('storefront.category',$category->slug)], ['label'=>$subcategory->name,'url'=>null]]">`.  
       Hero: gradient banner with breadcrumb, subcategory title + optional description.  
       Products grid: `<x-storefront.product-card :product="$product">` + pagination.  
       Empty state: `<x-empty-state>` with back-to-category link using `route('storefront.category', $subcategory->category->slug)`.  
       Files: `resources/views/storefront/subcategory.blade.php`  
       Verify: `php artisan test --compact`

- [ ] 24. **Rewrite `resources/views/storefront/product.blade.php`**  
       Extends `layouts.storefront`. Sections: `meta_title`, `meta_description`, `og_tags`, `content`.  
       Variables: `$product` (active Product with eager-loaded category + subcategory), `$related` (up to 4 active related products).  
       `@section('og_tags')`: OG meta tags using THREE-CASE image logic for `og:image`.  
       `@section('meta_description', Str::limit($product->description, 160))`.  
       Main content in `@section('content')`:  
       - `$imageUrl` computed with THREE-CASE logic  
       - Breadcrumb via `<x-storefront.breadcrumb :items="[Home, $category, $subcategory, $product->name]">` (last item has `url=null`)  
       - Two-column grid (md:5 columns: 2 image + 3 details)  
       - Image column: `rounded-2xl overflow-hidden shadow-lg`, aspect-square  
       - Details column: category/subcategory `<x-badge>` chips, `<h1>` product name, price block with strikethrough + discount badge, description (`whitespace-pre-line`), decorative "Add to Cart" button, trust icons  
       - Related products section: `<x-storefront.product-card :product="$relatedProduct">` grid  
       Files: `resources/views/storefront/product.blade.php`  
       Verify: `php artisan test --compact`

---

## Notes for the Implementer

1. **Tailwind v4 constraint**: No `tailwind.config.js`. Use only standard utility classes. No `[arbitrary-values]`. No `prose` class.  
2. **`@use Illuminate\Support\Facades\Storage`**: Not needed in Blade — `Storage::disk('public')->url()` works in `@php` blocks; the `Storage` facade is always available.  
3. **`Str::plural` / `Str::limit`**: These are Blade-available through the `Str` facade — but you must use the full `\Illuminate\Support\Str::` form in `@php` blocks, or `{{ Str::plural(...) }}` inline (the existing home.blade.php already uses `Str::plural` this way, confirming it resolves correctly in this app).  
4. **Run `vendor/bin/pint --dirty --format agent` after each file**.  
5. **Never run `npm run build` inline** — the plan step says to ask the user if assets are missing.  
6. **Keep the existing partial** `storefront/partials/product-card.blade.php` in place (don't delete it) — the new component should coexist. Views that are rewritten will switch to `<x-storefront.product-card>`.  
7. **Test run command**: `php artisan test --compact` from the workspace root.
