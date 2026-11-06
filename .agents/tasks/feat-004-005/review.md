# Public storefront + PHPUnit suite and deployment docs

This change adds the read-only public storefront (home, category, subcategory, and product pages) with strict triple-activation visibility rules and per-page SEO metadata, plus the complete PHPUnit feature suite covering admin auth, category/subcategory/product CRUD, and the storefront, and the README and VPS deployment documentation. Storefront visibility is enforced server-side so a product is reachable only when the product, its category, and its subcategory are all active — any break in that chain resolves to a 404 via `firstOrFail` — and the admin surface stays behind `auth` plus an `is_admin` middleware. Recorded verification shows a fresh migrate+seed, a clean Vite build, and all 55 tests passing with zero failures.

**Watch for:** a minor documentation gap — `VPS_DEPLOYMENT.md` covers PHP-FPM install, `php.ini` tweaks, and socket verification but has no explicit PHP-FPM pool (`www.conf`) config note that the spec listed.

**Verdict**: APPROVED

## High-level view

**Storefront visibility and routing.** `StorefrontController` is the only public surface and applies the same three-way active check (product + category + subcategory) on every page, with category/subcategory lookups scoped so a subcategory slug under the wrong category 404s. Routes are registered as `home`, `storefront.category`, `storefront.subcategory`, `storefront.product`; the admin area is a separate `auth`+`admin` group with its own `admin.*` name prefix.

**Admin security boundary.** Every admin route sits behind `['auth','admin']`; the `admin` alias resolves to `EnsureIsAdmin`, which denies anyone unauthenticated or without `is_admin`. Output is escaped with `{{ }}` throughout the storefront (no `{!! !!}` on user data), and the storefront has no forms, so there is no CSRF surface there. Product validation keeps the subcategory-must-belong-to-category rule via a `withValidator` after-hook rather than a `Rule::exists(...)->where(...)` form, which is functionally equivalent and tested.

**Storefront image handling.** Product images render through `Storage::disk('public')->url(...)` in both the card partial and the product page (including `og:image`), with an `asset('images/placeholder.png')` fallback when no image is set. No manual `storage/` path concatenation appears in the storefront views.

**Money representation.** Prices are stored as `decimal(12,2)` in the `products` migration; the `Product` model casts them to `decimal:2`. Float appears only in the `discount_percentage` accessor and the Blade price comparisons, purely for computing a percentage or ordering prices — never as a storage type.

**Test coverage.** Every scenario listed in FEAT-005 is present and the recorded `php artisan test` run is green (55 tests, 129 assertions, 0 failures, exit 0). The product CRUD tests predate this FEAT and kept their more descriptive method names; FEAT-005 added the `search_filters_products` and `filter_by_category_filters_products` cases to them, completing the spec list.

**Documentation.** `README.md` carries all required sections (overview, tech stack with exact versions, Laravel-13-on-PHP-8.4 rationale, local setup, demo admin, features, running tests, `.env` note). `VPS_DEPLOYMENT.md` is a complete Ubuntu/Nginx/PHP-8.4-FPM/MySQL guide with a full Nginx vhost and the `fastcgi_pass` socket, and opens with an explicit, unambiguous statement that no live deployment was performed and no public URL is claimed — satisfying the anti-fabrication requirement.

<details>
<summary>Issues (1)</summary>

1. **VPS PHP-FPM pool config note missing** — `VPS_DEPLOYMENT.md` covers PHP-FPM install, `php.ini` production tweaks, socket verification, and `systemctl restart php8.4-fpm`, but never touches the FPM pool file (`/etc/php/8.4/fpm/pool.d/www.conf`) the spec called out (e.g. `listen` socket path, `pm` settings, `user`/`group`). Non-blocking — the guide deploys correctly as written. Optionally add a short pool-config note for completeness.

</details>

<details><summary>Details</summary>

### Storefront visibility rules and routing

The storefront holds one invariant everywhere a product appears: the product, its category, and its subcategory must all be `is_active`, and the chain can only break one way — into a 404. `category()` and `subcategory()` confirm the parent is active via `firstOrFail()` before listing products; `product()` repeats the full triple check on the product itself.

A deliberate scoping choice: `subcategory($categorySlug, $subcategorySlug)` resolves the subcategory through `$category->subcategories()->where('slug', $subcategorySlug)->where('is_active', true)->firstOrFail()` rather than a global slug lookup. This is what makes a subcategory slug valid under one category but 404 under another — the behavior `test_subcategory_page_returns_404_when_subcategory_belongs_to_different_category` asserts. Related products are sourced from the same subcategory with `where('id', '!=', $product->id)` and the same triple-active filter, `take(4)`.

### Admin security boundary and auth

`auth` runs first on the admin group and sends guests to `admin.login` (via `redirectGuestsTo` in `bootstrap/app.php`); `EnsureIsAdmin` then denies anyone without `is_admin`. One behavioral nuance worth flagging: an *authenticated* non-admin is redirected to the login page with an `error` flash rather than receiving a 403 — access is still denied, but the UX is "send a logged-in regular user to log in again" instead of "forbidden."

The subcategory-must-belong-to-category rule is implemented in `StoreProductRequest`/`UpdateProductRequest` as a `withValidator` after-hook that loads the subcategory and compares its `category_id` to the submitted `category_id`, adding a `subcategory_id` error on mismatch. This is a deliberate, documented alternative to `Rule::exists('subcategories','id')->where('category_id', ...)` and is equally strict, so validation was not weakened. Deletion guards behave as "blocked, not cascaded": `test_cannot_destroy_category_with_subcategories` and `test_cannot_destroy_subcategory_with_products` both assert a session `error` and that the row survives.

### Test coverage (FEAT-005)

The `ProductCrudTest` cases carry more descriptive names than the spec's shorthand — `index_returns_200` is `test_admin_can_access_products_index`, `store_creates_product_with_image` is `test_admin_can_create_product_with_valid_data`, etc. — because that class was written in an earlier FEAT and FEAT-005 extended it with the two remaining spec cases (`test_search_filters_products`, `test_filter_by_category_filters_products`) rather than renaming the existing ones. `ExampleTest` gained `use RefreshDatabase`, which is required now that `/` runs DB queries through the storefront layout's nav composer and the home controller; without it the in-memory SQLite has no `categories` table and the request 500s.

Not tested: there is no explicit case for an *authenticated non-admin* being blocked from an admin route — only the unauthenticated redirect (`test_admin_routes_require_auth`) and the non-admin login rejection. The middleware logic covers it, but a dedicated assertion would lock the behavior.

### Documentation and the no-fabrication stance

`README.md` carries every required section — overview, tech stack with exact versions, Laravel-13-on-PHP-8.4 rationale, step-by-step local setup, demo admin credentials, features, running tests, and the "never commit `.env`" note. `VPS_DEPLOYMENT.md` is a complete guide with the full PHP extension list, a full Nginx vhost (`root` at `/public`, `try_files`, `fastcgi_pass unix:/run/php/php8.4-fpm.sock`), production `.env` values, `migrate --force`, `storage:link`, ownership/permissions, and `config:cache`/`route:cache`/`view:cache`. It opens with an unambiguous statement that no live deployment was performed, no server was provisioned, and no public URL is provided — directly satisfying the anti-fabrication requirement. The single spec item it omits is a dedicated PHP-FPM pool (`www.conf`) config note (see Issues).

### Working-tree notes (out of scope)

`git status` shows uncommitted modifications to `AGENTS.md`, `CLAUDE.md`, `composer.json`, and `composer.lock`, plus untracked tooling directories (`.claude`, `.junie`, `.kiro`, `.mcp.json`, `boost.json`). The verify record states these are pre-existing and were left untouched this iteration, and none appear in the committed `9ce5da1..HEAD` diff, so no FEAT-004/005 source is uncommitted and no dependency change was introduced by this work.

</details>

<details><summary>File map</summary>

Full diff: `git -C e:\interview\BanglaPuzzle diff 9ce5da1 HEAD`.

- `app/Http/Controllers/StorefrontController.php` — new public storefront controller; triple-active visibility, `firstOrFail` 404s, scoped subcategory lookup, related-products query.
- `routes/web.php` — public storefront routes named `home`/`storefront.category`/`storefront.subcategory`/`storefront.product`; admin login/logout routes (login POST throttled); admin resource group behind `auth`+`admin`.
- `app/Models/User.php` — `is_admin` and `email_verified_at` added to `$fillable`; `is_admin` cast to `boolean`.
- `app/Providers/AppServiceProvider.php` — `View::composer` for the storefront layout, providing active categories for the nav.
- `resources/views/layouts/storefront.blade.php` — storefront layout: header/nav, mobile toggle, flash messages, footer, SEO/Vite head.
- `resources/views/storefront/home.blade.php` — hero, category cards, latest 8 products grid.
- `resources/views/storefront/category.blade.php` — breadcrumb, subcategory pills, paginated product grid.
- `resources/views/storefront/subcategory.blade.php` — breadcrumb, header, paginated product grid.
- `resources/views/storefront/product.blade.php` — product detail with `og:` tags, image/placeholder, prices + discount badge, related products.
- `resources/views/storefront/partials/product-card.blade.php` — shared card: `Storage::disk('public')` image with placeholder fallback, price/discount.
- `tests/Feature/Admin/AuthTest.php` — admin login/logout/auth-gate tests.
- `tests/Feature/Admin/CategoryCrudTest.php` — category CRUD, slug, toggle, delete-guard, search tests.
- `tests/Feature/Admin/SubcategoryCrudTest.php` — subcategory CRUD, composite-slug uniqueness, AJAX `byCategory`, delete-guard, toggle.
- `tests/Feature/Admin/ProductCrudTest.php` — reformatted; added `search_filters_products` and `filter_by_category_filters_products` cases.
- `tests/Feature/StorefrontTest.php` — full storefront visibility/404/discount suite.
- `tests/Feature/ExampleTest.php` — enabled `RefreshDatabase` for the now-DB-backed home route.
- `README.md` — replaced with all required sections.
- `VPS_DEPLOYMENT.md` — new; full VPS guide with Nginx vhost; explicit no-live-deployment notice.

</details>
