# BanglaPuzzle — E-Commerce Management System

A Laravel 13 e-commerce management system built for the BanglaPuzzle assignment. It provides a
full admin back office for managing categories, subcategories, and products (with image
handling), plus a public storefront with SEO-friendly URLs and metadata.

---

## Project Overview

BanglaPuzzle is an e-commerce management system for the BanglaPuzzle assignment. The
application exposes two surfaces:

- **Admin back office** (`/admin`) — protected by authentication and an admin-only middleware.
  Admins sign in, then manage categories, subcategories, and products through full CRUD with
  image upload/replace/delete, active-status toggling, search, and filtering.
- **Public storefront** (`/`, `/category/{slug}`, `/category/{cat}/{sub}`, `/product/{slug}`)
  — a customer-facing catalog with strict visibility rules and per-page SEO metadata.

A product is publicly visible only when the product, its category, and its subcategory are all
active; otherwise the storefront returns a 404.

---

## Features

- **Admin authentication & authorization** — dedicated admin login/logout, `EnsureIsAdmin`
  middleware guarding every admin route, and a `redirectGuestsTo` that sends unauthenticated
  admin requests to the admin login page.
- **Category CRUD** — create/edit/delete categories with unique slugs, active-status toggle,
  search by name, and a guard that prevents deleting a category which still has subcategories.
- **Subcategory CRUD** — subcategories scoped to a category, with a **composite unique
  constraint** `(category_id, slug)` so the same slug is allowed in different categories but
  not within the same one, a category filter, an AJAX endpoint to fetch active subcategories
  for a given category, and a guard that prevents deleting a subcategory which still has
  products.
- **Product CRUD** — full product management with image upload, replace, and delete via
  `ProductImageService`, validation that `new_price <= old_price`, validation that the chosen
  subcategory belongs to the chosen category, search, and category/subcategory/status filters.
- **Image handling** — product images are stored on the `public` disk under `products/` with a
  UUID filename, deleted automatically when a product is deleted or its image is replaced, and
  served through the `storage` symlink.
- **Public storefront + SEO** — unique slugs for categories/subcategories/products, dynamic
  `<title>` and meta description, Open Graph tags on product pages, breadcrumbs, and proper
  404s for inactive or non-existent records.

---

## Tech Stack

| Component      | Version   |
| -------------- | --------- |
| Laravel        | 13.40.0   |
| PHP            | 8.4.12    |
| MySQL          | 8.0.43    |
| Tailwind CSS   | v4        |
| Vite           | (bundled) |
| PHPUnit        | 12        |

---

## Architecture Overview

The application is a Laravel monolith.

- **Controllers** — admin controllers live under `app/Http/Controllers/Admin/`
  (`CategoryController`, `SubcategoryController`, `ProductController`, `DashboardController`)
  and the auth controller under `app/Http/Controllers/Auth/AdminLoginController.php`. The public
  storefront is served by `app/Http/Controllers/StorefrontController.php` at the root of the
  controllers directory.
- **Form Requests** — validation is centralized in `app/Http/Requests/Admin/`
  (`StoreCategoryRequest`, `UpdateCategoryRequest`, `StoreSubcategoryRequest`,
  `UpdateSubcategoryRequest`, `StoreProductRequest`, `UpdateProductRequest`). Product requests
  use a `withValidator` after-hook to enforce that the selected subcategory belongs to the
  selected category, and a custom message for the `new_price <= old_price` rule.
- **ProductImageService** — `app/Services/ProductImageService.php` encapsulates storing,
  deleting, and replacing product images on the `public` disk, keeping controllers thin.
- **Eloquent models & relationships** — `Category hasMany Subcategory` and `Category hasMany
  Product`; `Subcategory belongsTo Category` and `Subcategory hasMany Product`; `Product
  belongsTo Category` and `Product belongsTo Subcategory`. Each model auto-generates a unique
  slug in a `boot()` hook, exposes an `active` scope, and `Product` exposes a
  `discount_percentage` accessor.
- **Middleware** — the `admin` middleware alias (`EnsureIsAdmin`) is registered in
  `bootstrap/app.php` and applied to the admin route group; `redirectGuestsTo` points
  unauthenticated admin requests at the admin login page.
- **Database transactions** — product store/update/delete wrap DB writes and image operations
  together so a failed image write rolls back the DB row, and the old image is only deleted
  after a successful update.

---

## Database Schema

Three tables back the catalog:

- **categories** — `id`, `name`, `slug` (unique), `description`, `is_active`, timestamps.
- **subcategories** — `id`, `category_id` (FK → categories, `onDelete: restrict`), `name`,
  `slug`, `description`, `is_active`, timestamps. A **composite unique index**
  `unique(category_id, slug)` makes slugs unique within a category. `category_id` is indexed.
- **products** — `id`, `category_id` (FK → categories), `subcategory_id` (FK →
  subcategories), `name`, `slug` (unique), `description`, `image` (nullable), `old_price`,
  `new_price` (both `decimal(12,2)`), `is_active`, timestamps. Foreign keys use
  `onDelete: restrict`; `category_id`, `subcategory_id`, and `is_active` are indexed.

Relationships and a consistency rule:

- `Category` **hasMany** `Subcategory` and **hasMany** `Product`.
- `Subcategory` **belongsTo** `Category` and **hasMany** `Product`.
- `Product` **belongsTo** `Category` and **belongsTo** `Subcategory`.
- A product's `subcategory_id` must reference a subcategory whose `category_id` matches the
  product's `category_id`. This is enforced both at the DB level (foreign keys) and in the
  product form-request validation (after-hook).

---

## Installation

Requirements: PHP 8.4 with the usual Laravel extensions, MySQL 8.0, Composer, Node.js 20+,
and npm.

```bash
# 1. Get the code
git clone <repo-url> banglapuzzle
cd banglapuzzle

# 2. Install PHP dependencies
composer install

# 3. Environment
cp .env.example .env
php artisan key:generate

# 4. Configure the MySQL database in .env
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=banglapuzzle
#    DB_USERNAME=root
#    DB_PASSWORD=your_password

# 5. Create the schema and seed demo data
php artisan migrate:fresh --seed

# 6. Link the public storage disk so product images are web-accessible
php artisan storage:link

# 7. Build frontend assets (Tailwind CSS v4 + Vite)
npm install
npm run build

# 8. Serve the application
php artisan serve
```

The application will be available at http://localhost:8000.

> **Note:** never commit `.env` to git. It contains your local DB credentials and app key and
> is already git-ignored.

---

## Demo Admin

After running `php artisan migrate:fresh --seed`, the `DatabaseSeeder` creates an admin user:

- **URL:** http://localhost:8000/admin/login
- **Email:** admin@banglapuzzle.com
- **Password:** password

The seeder also creates 5 categories, 3 subcategories per category (15 total), and 5 products
per subcategory (75 total) for browsing and testing.

---

## Running Tests

The test suite runs on **SQLite in-memory** (configured in `phpunit.xml`), so no MySQL is
required to run the tests.

```bash
# Run the whole suite
php artisan test

# Run a single suite by filter
php artisan test --filter AuthTest
php artisan test --filter CategoryCrudTest
php artisan test --filter SubcategoryCrudTest
php artisan test --filter ProductCrudTest
php artisan test --filter StorefrontTest
```

Product image tests use `Storage::fake('public')` so file operations are isolated and never
touch the real disk.

---

## Laravel 13 Rationale

This project uses Laravel 13.40.0 on PHP 8.4. PHP 8.4 fully supports Laravel 13 — its
minimum PHP requirement is satisfied and no downgrade is needed. Laravel 13 ships with the
fluent middleware API used here (`->withMiddleware(...)` with `$middleware->alias(...)` and
`redirectGuestsTo(...)`), there is no `Http/Kernel.php`, and Tailwind CSS v4 is wired in via
`@tailwindcss/vite`. Using the current, supported stack is preferable to downgrading.

---

## VPS Deployment

See [`VPS_DEPLOYMENT.md`](./VPS_DEPLOYMENT.md) for a complete Ubuntu 22.04/24.04 deployment
guide with Nginx, PHP 8.4-FPM, MySQL, and Let's Encrypt.

---

## Known Limitations

- **No cart/checkout** — this is a catalog/management system only; there is no shopping cart,
  order, or payment flow.
- **No canonical URLs or sitemap** — basic SEO (unique slugs, dynamic titles, meta
  descriptions, and Open Graph tags) is implemented; canonical link tags and a sitemap are
  not included.
- **Single admin role** — authorization is a binary `is_admin` flag; there are no granular
  roles or permissions.
- **Local file storage** — product images are stored on the local `public` disk; there is no
  cloud/object storage integration.
- **No soft deletes** — deleting a category/subcategory/product removes the row (guarded
  against deleting categories that have subcategories and subcategories that have products);
  records are not restorable.
