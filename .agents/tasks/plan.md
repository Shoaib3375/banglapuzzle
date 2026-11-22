# Implementation Plan — Product Slug & SEO URL Management

## Design Decisions

**Decision 1 — Boot auto-slug vs. service.**
Remove the `creating`/`updating` hooks from `Product::boot()` entirely. The controller will always call `ProductSlugService` before `Product::create()` or `$product->update()`. This prevents double-slug generation (boot() would fire even when a slug is already set), gives the service sole responsibility for slug normalization, and makes the flow easier to test. The boot() method still exists but only if there is other logic; if there is nothing left, remove it entirely. The `generateUniqueSlug()` static helper on the model can also be removed once the service exists.

**Decision 2 — check-slug route placement.**
Add `GET admin/products/check-slug` as a named explicit route (`admin.products.checkSlug`) *before* `Route::resource('products', ...)` inside the auth+admin middleware group. Laravel matches routes top-down; putting it before the resource prevents the string `check-slug` from being captured as `{product}` by `products.show`.

**Decision 3 — Uniqueness check across both tables.**
`ProductSlugService::makeUnique()` queries both `products.slug` (excluding the current product ID on updates) AND `product_slug_histories.old_slug`. This prevents reusing a slug that previously pointed elsewhere, which would break existing 301-bookmarked URLs.

**Decision 4 — StorefrontController redirect flow.**
Change `StorefrontController::product()` from `firstOrFail()` to `first()`. If no live product matches the slug, query `product_slug_histories` for the slug. If found, resolve the current product and issue a `redirect()->to(route('storefront.product', $product->slug), 301)`. Return 404 only if neither table matches.

**Decision 5 — SQLite-compatible migration.**
The `product_slug_histories` migration uses `$table->unsignedBigInteger('product_id')` + `$table->foreign('product_id')` with `->references('id')->on('products')->onDelete('cascade')`. This is the same pattern used in the existing products migration and is SQLite-compatible.

**Decision 6 — Create form JS: input event, not blur.**
Replace the existing `blur` listener on the name field with an `input` listener so the slug auto-generates in real time. Track a boolean `slugManuallyEdited`; once the admin edits the slug field directly, stop auto-generating. The URL preview div updates on every slug `input` event. Availability check fires on slug `blur` (debounced, skips if slug is empty or identical to the last checked value).

**Decision 7 — Edit form JS: slug locked from load.**
On the edit form, `slugManuallyEdited` is initialised to `true` (slug already exists). Name changes never overwrite the slug. Show a warning banner when the slug field value differs from the original loaded value. Availability check on blur passes `?exclude_id={{ $product->id }}`.

**Decision 8 — Storefront layout canonical tag.**
The storefront layout already contains `<link rel="canonical" href="{{ url()->current() }}">`. No layout change is needed for the canonical. Add `og:url` in the product view's `@section('og_tags')` using `route('storefront.product', $product->slug)`.

---

## Implementation Order

- [ ] 1. Create the `ProductSlugHistory` migration.
      Create `database/migrations/2025_01_01_000005_create_product_slug_histories_table.php`.
      Columns: `id` (bigIncrements), `product_id` (unsignedBigInteger), `old_slug` (string, unique), `timestamps`.
      Foreign key: `product_id` → `products.id` onDelete cascade (using the manual `->foreign()` pattern matching the products migration for SQLite compatibility).
      Add index on `old_slug` for redirect lookups.
      Files: `database/migrations/2025_01_01_000005_create_product_slug_histories_table.php`
      Verify: `php artisan migrate --env=testing` runs without error (SQLite in-memory); or run `php artisan test --compact` — RefreshDatabase will apply all migrations.

- [ ] 2. Create the `ProductSlugHistory` model.
      Use `php artisan make:model ProductSlugHistory --no-interaction`.
      Set `$fillable = ['product_id', 'old_slug']`.
      Add `belongsTo(Product::class)` relationship.
      Files: `app/Models/ProductSlugHistory.php`
      Verify: `php artisan tinker --execute 'echo App\Models\ProductSlugHistory::class;'` — no error.

- [ ] 3. Add the `slugHistories` relationship to the `Product` model and remove boot auto-slug logic.
      In `app/Models/Product.php`:
      - Remove the entire `boot()` method (both `creating` and `updating` hooks).
      - Remove the `generateUniqueSlug()` static method (will be replaced by the service).
      - Add `public function slugHistories(): HasMany { return $this->hasMany(ProductSlugHistory::class); }` with the correct import.
      Files: `app/Models/Product.php`
      Verify: `php artisan test --compact tests/Feature/Admin/ProductCrudTest.php` — all existing product CRUD tests still pass (slug is now set by controller before create).

- [ ] 4. Create `ProductSlugService`.
      Create `app/Services/ProductSlugService.php` with these public methods:
      - `generate(string $name): string` — calls `Str::slug($name)`, returns `'product'` if result is empty.
      - `normalize(string $slug): string` — lowercases, replaces non-`[a-z0-9]` runs with `-`, trims hyphens, collapses consecutive hyphens. This is the canonical normalizer used on all inputs.
      - `makeUnique(string $base, ?int $excludeProductId = null): string` — queries `products.slug` (excluding `$excludeProductId`) AND `product_slug_histories.old_slug`; if `$base` is free in both tables, returns it; otherwise appends `-2`, `-3`, etc. until a free slug is found.
      - `generateUnique(string $name, ?int $excludeProductId = null): string` — calls `generate()` then `makeUnique()`.
      - `resolveForStore(string $name, ?string $submittedSlug): string` — if `$submittedSlug` is non-empty, normalize it then call `makeUnique()`; else call `generateUnique($name)`.
      - `resolveForUpdate(string $name, ?string $submittedSlug, string $existingSlug, int $productId): string` — if `$submittedSlug` is non-empty and differs from `$existingSlug` after normalization, normalize then `makeUnique($normalized, $productId)`; if same as existing (or empty), return `$existingSlug` unchanged.
      Files: `app/Services/ProductSlugService.php`
      Verify: Unit test in step 12 covers this; for a quick smoke test run `php artisan tinker --execute 'echo app(\App\Services\ProductSlugService::class)->generate("iPhone 17 Pro Max");'` — should print `iphone-17-pro-max`.

- [ ] 5. Update `StoreProductRequest` form request.
      In `app/Http/Requests/Admin/StoreProductRequest.php`:
      - Change the `name` max from 150 to 255 to match the task spec (keep existing `required|string`).
      - Change the slug rule from `['nullable', 'string', 'unique:products,slug']` to `['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/']`. Remove the `unique` rule from the Form Request — uniqueness is enforced by the service before this point, and the DB unique constraint is the final backstop. Keeping a unique rule here would reject a slug the service already made unique, and would also reject an empty slug (which is valid and triggers auto-generation).
      - Keep all other rules unchanged.
      Files: `app/Http/Requests/Admin/StoreProductRequest.php`
      Verify: `php artisan test --compact tests/Feature/Admin/ProductCrudTest.php` — all tests pass.

- [ ] 6. Update `UpdateProductRequest` form request.
      In `app/Http/Requests/Admin/UpdateProductRequest.php`:
      - Apply the same slug rule change as step 5: `['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/']`. Remove the `Rule::unique(...)->ignore(...)` — uniqueness handled by the service + DB constraint.
      - Keep all other rules unchanged.
      Files: `app/Http/Requests/Admin/UpdateProductRequest.php`
      Verify: `php artisan test --compact tests/Feature/Admin/ProductCrudTest.php` — all tests pass.

- [ ] 7. Update `ProductController` — inject service, update `store`, `update`, add `checkSlug`.
      In `app/Http/Controllers/Admin/ProductController.php`:
      - Add `ProductSlugService` as a constructor-injected readonly property alongside `ProductImageService`.
      - **`store()`**: Replace the inline `Product::generateUniqueSlug()` call with `$data['slug'] = $this->slugService->resolveForStore($data['name'], $data['slug'] ?? null);` before the DB transaction. This sets the slug in `$data` before `Product::create($data)`.
      - **`update()`**: Inside the DB transaction, before `$product->update($data)`:
        1. Capture `$oldSlug = $product->slug`.
        2. Call `$data['slug'] = $this->slugService->resolveForUpdate($data['name'], $data['slug'] ?? null, $oldSlug, $product->id);`.
        3. If `$data['slug'] !== $oldSlug`, create a `ProductSlugHistory` record: `ProductSlugHistory::create(['product_id' => $product->id, 'old_slug' => $oldSlug]);`. Wrap the history insert inside the same transaction so it rolls back if the product update fails.
      - **`checkSlug()` (new method)**: Accepts `Request $request`. Reads `$request->input('slug')` and `$request->input('exclude_id')`. Normalizes the slug via the service. Queries `products` table (excluding `exclude_id` if present) for slug existence, also checks `product_slug_histories.old_slug`. Returns `response()->json(['available' => $isAvailable, 'normalized' => $normalizedSlug])`.
      - Import `ProductSlugHistory` model and `ProductSlugService`.
      Files: `app/Http/Controllers/Admin/ProductController.php`
      Verify: `php artisan test --compact tests/Feature/Admin/ProductCrudTest.php` — all existing tests still pass.

- [ ] 8. Register the `checkSlug` route in `routes/web.php`.
      Inside the admin middleware group, add this route **before** `Route::resource('products', ...)`:
      ```php
      Route::get('products/check-slug', [ProductController::class, 'checkSlug'])
          ->name('products.checkSlug');
      ```
      Files: `routes/web.php`
      Verify: `php artisan route:list --name=products.checkSlug` shows the route; `php artisan route:list --name=admin.products.show` still shows the resource route. Also run `php artisan test --compact` — all 55 tests pass.

- [ ] 9. Update `StorefrontController::product()` with 301 redirect logic.
      In `app/Http/Controllers/StorefrontController.php`:
      - Add `use App\Models\ProductSlugHistory;` import.
      - Change the `product()` method to use `first()` instead of `firstOrFail()`.
      - After `first()` returns `null`, query `ProductSlugHistory::where('old_slug', $slug)->with('product')->first()`. If found and the product is still active (recheck `is_active`, category active, subcategory active), return `redirect()->to(route('storefront.product', $history->product->slug), 301)`. Otherwise abort(404).
      - If the initial `first()` returns a product, proceed as before.
      Files: `app/Http/Controllers/StorefrontController.php`
      Verify: `php artisan test --compact tests/Feature/StorefrontTest.php` — all existing storefront tests pass (they use live slugs so they never hit the redirect path). New tests in step 12 cover the redirect.

- [ ] 10. Update the admin `create.blade.php` with SEO & URL Settings section.
      Replace the current simple slug field `<div class="mb-5">…</div>` block (and its surrounding context) with a full "SEO & URL Settings" card section. Insert it between the Name field and the Description field. The section must contain:
      - A card header: "SEO & URL Settings".
      - **Slug input**: `id="slug"`, `name="slug"`, monospace font, `value="{{ old('slug') }}"`, with a "Manual" badge (`id="slug-manual-badge"`, hidden by default) that appears once the admin edits the slug directly, an `@error('slug')` block below.
      - **URL preview div** (`id="slug-url-preview"`): shows `<span id="slug-url-text"></span>` beneath the slug input. Initially populated from `url('/product/')` + the current slug value. Label: "Product URL Preview".
      - **Availability indicator** (`id="slug-availability"`): hidden span that shows "✓ Available" (green) or "✗ Already in use" (red) after an AJAX check. Only appears after user interaction.
      Replace the existing `@push('scripts')` block's slug logic entirely. New JS behaviour:
      - `slugManuallyEdited = false` on load (or `= true` if `old('slug')` is non-empty from a failed submit).
      - Name `input` event: if `!slugManuallyEdited`, compute slug via `toSlug(value)`, set `slugInput.value`, update URL preview, clear availability indicator.
      - Slug `input` event: set `slugManuallyEdited = true`, show manual badge, update URL preview, clear availability indicator.
      - Slug `blur` event: if slug is non-empty, call `checkSlugAvailability()` via `fetch` to `{{ route('admin.products.checkSlug') }}?slug=VALUE`.
      - `toSlug(str)` function: `str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '')`.
      - URL preview base: `const baseUrl = '{{ url('/product/') }}/';` — use the Laravel `url()` helper, not a hardcoded domain.
      - Initialise preview on page load from `old('slug')` or empty slug with name if `old('name')` is present.
      Files: `resources/views/admin/products/create.blade.php`
      Verify: Load the create form in the browser (`GET /admin/products/create`) — the SEO section renders. Verify `php artisan test --compact tests/Feature/Admin/ProductCrudTest.php` — `test_admin_can_view_create_form` passes.

- [ ] 11. Update the admin `edit.blade.php` with SEO & URL Settings section and slug-change warning.
      Apply the same SEO & URL Settings card replacement as step 10, but with these differences:
      - `value="{{ old('slug', $product->slug) }}"` (pre-filled).
      - `slugManuallyEdited = true` on load (slug already exists, name changes must never overwrite it).
      - Add a warning banner (`id="slug-change-warning"`, hidden by default): "⚠ Changing this URL will create a 301 redirect from the old URL. Visitors and search engines will be sent to the new URL automatically." Show it whenever the slug input value differs from `originalSlug`.
      - The availability check fetch appends `&exclude_id={{ $product->id }}`.
      - URL preview shows the current live URL on load.
      - Name `input` event: does NOT update slug (because `slugManuallyEdited` starts `true`). The name field is still editable; just slug is decoupled.
      Files: `resources/views/admin/products/edit.blade.php`
      Verify: Load edit form — SEO section shows pre-filled slug, warning is hidden. Run `php artisan test --compact tests/Feature/Admin/ProductCrudTest.php` — `test_admin_can_view_edit_form` passes.

- [ ] 12. Add SEO metadata to `storefront/product.blade.php`.
      In `@section('og_tags')`, add `<meta property="og:url" content="{{ route('storefront.product', $product->slug) }}">` after the existing `og:type` tag.
      The canonical tag is already handled by the layout (`<link rel="canonical" href="{{ url()->current() }}">`), so no further change is needed in the layout.
      Files: `resources/views/storefront/product.blade.php`
      Verify: `php artisan test --compact tests/Feature/StorefrontTest.php` — all storefront tests pass.

- [ ] 13. Write the `ProductSlugServiceTest` unit test.
      Use `php artisan make:test --phpunit --unit ProductSlugServiceTest --no-interaction`.
      Test cases (all use `RefreshDatabase` via a Feature test sub-class is fine, but since this queries the DB, make it a Feature test — `php artisan make:test --phpunit ProductSlugServiceTest --no-interaction`):
      - `test_generate_produces_correct_slug`: `'iPhone 17 Pro Max'` → `'iphone-17-pro-max'`.
      - `test_generate_handles_special_chars`: `"Men's Casual Shirt"` → `'mens-casual-shirt'`.
      - `test_generate_returns_product_for_empty_name`: empty string → `'product'`.
      - `test_normalize_collapses_consecutive_hyphens`: `'hello--world'` → `'hello-world'`.
      - `test_normalize_trims_leading_trailing_hyphens`: `'-hello-world-'` → `'hello-world'`.
      - `test_make_unique_appends_suffix_for_duplicate`: Create a product with slug `'widget'`; `makeUnique('widget')` returns `'widget-2'`.
      - `test_make_unique_skips_history_slugs`: Create a product slug history row with `old_slug = 'widget-old'`; `makeUnique('widget-old')` returns `'widget-old-2'`.
      - `test_make_unique_excludes_current_product_on_update`: Create product with slug `'widget'`; `makeUnique('widget', $product->id)` returns `'widget'` (not a duplicate of itself).
      - `test_resolve_for_store_uses_submitted_slug_when_provided`: `resolveForStore('Widget', 'my-slug')` returns `'my-slug'`.
      - `test_resolve_for_store_generates_from_name_when_slug_empty`: `resolveForStore('Widget', '')` returns `'widget'`.
      - `test_resolve_for_update_preserves_slug_when_not_changed`: existing slug `'widget'`; `resolveForUpdate('Widget', 'widget', 'widget', $id)` returns `'widget'`.
      - `test_resolve_for_update_uses_new_slug_when_changed`: `resolveForUpdate('Widget', 'widget-premium', 'widget', $id)` returns `'widget-premium'`.
      Files: `tests/Feature/ProductSlugServiceTest.php`
      Verify: `php artisan test --compact tests/Feature/ProductSlugServiceTest.php` — all new tests pass.

- [ ] 14. Write the `ProductSlugRedirectTest` feature test.
      Use `php artisan make:test --phpunit ProductSlugRedirectTest --no-interaction`.
      Test cases:
      - `test_old_slug_redirects_301_to_current_slug`: Create product with slug `'widget-old'`; update it to slug `'widget-new'` (which creates a history row); `GET /product/widget-old` returns 301 redirecting to `/product/widget-new`.
      - `test_unknown_slug_returns_404`: `GET /product/does-not-exist` returns 404.
      - `test_current_slug_returns_200`: `GET /product/widget-new` returns 200.
      - `test_redirect_does_not_fire_for_inactive_product`: Create product, update slug (creates history), deactivate product; `GET /product/old-slug` returns 404 (not 301).
      - `test_product_slug_history_saved_on_slug_change`: Programmatically update product slug via the admin `PUT` endpoint; assert `product_slug_histories` table has the old slug.
      - `test_product_slug_history_not_saved_when_slug_unchanged`: Programmatically update product name only (slug stays the same); assert `product_slug_histories` table has zero rows.
      Files: `tests/Feature/ProductSlugRedirectTest.php`
      Verify: `php artisan test --compact tests/Feature/ProductSlugRedirectTest.php` — all new tests pass.

- [ ] 15. Run Pint formatter and the full test suite.
      Run `vendor/bin/pint --dirty --format agent` to fix all PHP style issues across all modified/created PHP files.
      Then run `php artisan test --compact` — all tests (55 existing + new) pass.
      Files: all modified `.php` files
      Verify: `php artisan test --compact` exits with no failures.

---

## File Summary

| Action | Path |
|---|---|
| Create | `database/migrations/2025_01_01_000005_create_product_slug_histories_table.php` |
| Create | `app/Models/ProductSlugHistory.php` |
| Modify | `app/Models/Product.php` |
| Create | `app/Services/ProductSlugService.php` |
| Modify | `app/Http/Requests/Admin/StoreProductRequest.php` |
| Modify | `app/Http/Requests/Admin/UpdateProductRequest.php` |
| Modify | `app/Http/Controllers/Admin/ProductController.php` |
| Modify | `routes/web.php` |
| Modify | `app/Http/Controllers/StorefrontController.php` |
| Modify | `resources/views/admin/products/create.blade.php` |
| Modify | `resources/views/admin/products/edit.blade.php` |
| Modify | `resources/views/storefront/product.blade.php` |
| Create | `tests/Feature/ProductSlugServiceTest.php` |
| Create | `tests/Feature/ProductSlugRedirectTest.php` |
