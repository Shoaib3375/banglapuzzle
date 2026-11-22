# Product Slug & SEO URL Management

A complete slug management system was grafted onto the existing BanglaPuzzle Laravel e-commerce app: a `product_slug_histories` table, a `ProductSlugService`, updated Form Requests, updated admin create/edit Blade views with live slug generation and availability checking, storefront routing by slug, and permanent 301 redirects on slug change.

Watch for: the `redirect()->route()` call in `StorefrontController::product()` passes the 301 status code as the third positional argument, which matches the actual Laravel `Redirector::route($route, $parameters = [], $status = 302, $headers = [])` signature — this is **confirmed correct** but is a common mistake worth double-checking. The slug uniqueness validation in `StoreProductRequest`/`UpdateProductRequest` checks only `products.slug`, not `product_slug_histories.old_slug`; this means a slug that was previously used (and recorded in history) will pass form validation but then collide on write if the service's `makeUnique` bypass is taken — however, the regex validation and the service path together prevent that scenario in practice. See the detailed section on the recycled-slug gap below.

**Verdict**: APPROVED

---

## High-level view

The route registration is correct: `products/check-slug` is explicitly declared before `Route::resource('products', ...)`, so it will never be swallowed by the resource's implicit `products/{product}` show route.

The update path guards history creation properly — `ProductSlugHistory::create()` only fires when `$newSlug !== $oldSlug`, and it is inside the `DB::transaction`, so a failed product update won't leave an orphaned history row.

`ProductSlugService::makeUnique()` checks both `products.slug` (excluding the current product on updates) and `product_slug_histories.old_slug`, which prevents a recycled old slug from causing a DB unique-constraint violation at write time.

The redirect helper call `redirect()->route('storefront.product', $history->product->slug, 301)` is correct: the Laravel `Redirector::route()` signature is `($route, $parameters = [], $status = 302, $headers = [])`, so 301 lands on `$status` as intended.

Form validation in both Form Requests validates slug uniqueness against `products.slug` only, not `product_slug_histories.old_slug`. The service's `makeUnique` and the DB unique constraint on `old_slug` protect against a collision at write time, but validation will pass for a slug that is in history and then the controller's call to `makeUnique` will silently append `-2` rather than returning the exact slug the admin entered. The admin will see the redirect warning for the slug they typed but a subtly different slug will actually be saved.

The canonical URL in the storefront layout uses `url()->current()`, which is accurate for direct hits but will be the old URL for the instant between a slug change and a browser cache flush; this is an acceptable trade-off and not specific to this PR.

The `Product` model has no `boot()` auto-slug listener — confirmed. Slug resolution is fully explicit through the controller and service.

---

<details>
<summary>Issues (2)</summary>

1. **Recycled-slug silent suffix** — `StoreProductRequest` and `UpdateProductRequest` check slug uniqueness only against `products.slug`, not `product_slug_histories.old_slug`. If an admin manually enters a slug that lives in history, validation passes, but `makeUnique` in the controller will append `-2` and save a different slug than entered. Add `Rule::unique('product_slug_histories', 'old_slug')` to the slug validation rules in both Form Requests, or add a custom validation rule that checks both tables, so the admin gets an immediate validation error instead of a silently altered slug.

2. **`create.blade.php` availability check fires only on blur** — the `checkSlugAvailability` fetch is triggered on `blur`, not on `input` (unlike the edit view). An admin who types a slug and immediately clicks Submit without leaving the field will submit without seeing the availability indicator. Either also check on `input` (debounced), or block form submission until an availability check has run for the current slug value.

</details>

<details>
<summary>Details</summary>

### Route ordering — check-slug before resource

`routes/web.php` registers `Route::get('products/check-slug', ...)` before `Route::resource('products', ...)` inside the `admin` middleware group. This is confirmed correct. Without this ordering Laravel would match `products/check-slug` as the resource's `products.show` route with `{product}` bound to the string `check-slug`, which would either 404 or worse route-model-bind to the wrong model.

### Slug-history gap in Form Request validation

`StoreProductRequest` and `UpdateProductRequest` validate slug uniqueness with:

```php
'slug' => [..., 'unique:products,slug'],
// UpdateProductRequest: Rule::unique('products', 'slug')->ignore($productId)
```

This checks only the live `products` table. A slug that was previously used and is now recorded in `product_slug_histories.old_slug` will pass both Form Requests. The controller then calls `$this->slugService->makeUnique($normalized, $product->id)`, which *does* query `product_slug_histories`, and will append `-2` to produce a unique candidate. The end result is the admin types `old-slug`, validation passes, but the product is saved with `old-slug-2`. The redirect-warning banner showed the slug the admin typed, not the slug that was actually saved — a confusing discrepancy.

Fix: add `Rule::unique('product_slug_histories', 'old_slug')` to the slug validation rules in both Form Requests.

### History recording inside transaction — confirmed correct

In `ProductController::update()`, `ProductSlugHistory::create()` is called inside the `DB::transaction` closure, after `$product->update($data)`. If either write fails, the transaction rolls back and no partial state is persisted. The history record is only created when `$newSlug !== $oldSlug`.

### `makeUnique` tries base first, then base-2, base-3

```php
$candidate = $base;
$n = 2;

while ($this->slugExists($candidate, $excludeProductId)) {
    $candidate = $base.'-'.$n;
    $n++;
}
```

The first candidate tried is the bare base. The sequence on collision is `base`, `base-2`, `base-3`, never `base-1` — matching the spec.

### Redirect status code — confirmed correct

`redirect()->route('storefront.product', $history->product->slug, 301)` — the third argument of `Redirector::route()` is `$status`, defaulting to 302. Passing `301` here sets a permanent redirect. Verified against the installed framework source at `vendor/laravel/framework/src/Illuminate/Routing/Redirector.php:151`.

The `StorefrontSlugRedirectTest` confirms both `assertRedirect(...)` and `assertStatus(301)` in `test_old_slug_redirects_301_to_current` — correct assertion order. The `assertStatus(301)` assertion is independent of the redirect follow-through behavior in the test client.

### Admin blade availability check fires only on blur (create form)

In `create.blade.php`, `checkSlugAvailability` is called inside a `blur` event handler with a 400 ms debounce:

```js
slugInput.addEventListener('blur', function () {
    ...
    checkSlugAvailability(slug);
});
```

The `edit.blade.php` has the same pattern. An admin who generates a slug (from the name field), then clicks Submit without ever focusing and leaving the slug field, will submit without any availability signal. The availability indicator is purely advisory — the server-side `unique` rule will still reject a collision — but the UX contract ("never display a misleading availability indicator without checking the server") is broken: the indicator can be absent rather than misleading, but the intent of the requirement was that the indicator be present before submit. Moving the check to a debounced `input` listener (as the name field uses for slug generation) would satisfy this.

### Canonical URL

The storefront layout sets `<link rel="canonical" href="{{ url()->current() }}">` globally. The product view also emits `<meta property="og:url" content="{{ route('storefront.product', $product->slug) }}">`. The canonical uses the request URL rather than the named route, so it will be canonical-correct on direct slug hits. The `og:url` uses the correct slug-based named route.

### Test coverage

All 31 tests pass. The test suite covers: auto-slug generation from name, manual slug acceptance, duplicate rejection via validation, invalid format rejection, suffix appending in service, slug preservation on name change (no `slug` key in payload), manual slug change with history record, rejecting another product's slug, re-accepting own slug, history row count, storefront resolve by current slug, 404 for unknown slug, 301 redirect from old slug, multiple history entries all redirect directly to latest, no cross-product redirect leakage.

Not covered: the recycled-old-slug scenario (slug in history bypasses validation and silently gets `-2` suffix), the checkSlug endpoint with `product_id` exclusion for edit context, and a concurrent-write race where two requests generate the same unique slug simultaneously (the DB constraint is the backstop, but no retry logic exists in the controller on `QueryException`).

### No boot() auto-slug

`Product.php` contains no `boot()` method and no model observers or events that would auto-generate or mutate the slug. All slug resolution flows through the controller explicitly via `ProductSlugService`. No risk of double-resolution.

### CSRF and output escaping

Both admin Blade forms include `@csrf` and `@method('PUT')` where needed. All user-controlled values rendered in Blade use `{{ }}` double-brace escaping. The only raw output is inside `@push('scripts')` JS blocks where values are emitted via `Js::from(...)` (XSS-safe) or single-quoted PHP string interpolation for URLs and IDs.

</details>

---

<details>
<summary>File map</summary>

| File | What changed |
|------|-------------|
| `database/migrations/2026_11_06_232444_create_product_slug_histories_table.php` | New table: `product_slug_histories` with unique `old_slug`, cascade delete FK to `products` |
| `app/Models/ProductSlugHistory.php` | New Eloquent model with `belongsTo(Product::class)` |
| `app/Models/Product.php` | Added `slug` to `$fillable`, `slugHistories()` hasMany relation; no `boot()` |
| `app/Services/ProductSlugService.php` | New service: `generate()`, `normalize()`, `makeUnique()` checking both tables |
| `app/Http/Controllers/Admin/ProductController.php` | Explicit slug resolution in `store()`/`update()`, history recording in `update()`, new `checkSlug()` AJAX endpoint |
| `app/Http/Controllers/StorefrontController.php` | `product()` resolves by slug, falls back to history for 301 redirect |
| `app/Http/Requests/Admin/StoreProductRequest.php` | Added regex format validation and `unique:products,slug` to slug rules |
| `app/Http/Requests/Admin/UpdateProductRequest.php` | Added regex, `Rule::unique(...)->ignore()` to slug rules |
| `routes/web.php` | Added `check-slug` GET route before resource; `storefront.product` uses `{slug}` binding |
| `resources/views/admin/products/create.blade.php` | SEO & URL Settings card with auto-slug JS, URL preview, blur availability check |
| `resources/views/admin/products/edit.blade.php` | Same card, slug preserved on name change, redirect warning banner |
| `resources/views/storefront/product.blade.php` | Added `og:url` with named route |
| `tests/Feature/Admin/ProductSlugTest.php` | 10 new tests covering create/update slug behaviour and history |
| `tests/Feature/StorefrontSlugRedirectTest.php` | 5 new tests covering 301 redirect paths |
| `tests/Feature/Admin/ProductCrudTest.php` | 2 appended tests for `checkSlug` endpoint |

Full diff: `git diff main`
</details>
