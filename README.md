# BanglaPuzzle E-Commerce Platform

A complete, production-quality e-commerce management system built for the **Bangla Puzzle Limited** Laravel technical assignment.

**Candidate:** Md Shoaib Islam
**Position:** Mid-Level Laravel Developer
**Submission Deadline:** 8 November 2026

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Features](#2-features)
3. [Technology Stack](#3-technology-stack)
4. [Architecture Overview](#4-architecture-overview)
5. [Database Schema](#5-database-schema)
6. [Installation](#6-installation)
7. [Environment Configuration](#7-environment-configuration)
8. [Demo Credentials](#8-demo-credentials)
9. [Running the Application](#9-running-the-application)
10. [Running Tests](#10-running-tests)
11. [Routes Reference](#11-routes-reference)
12. [VPS Deployment](#12-vps-deployment)
13. [Known Limitations](#13-known-limitations)

---

## 1. Project Overview

BanglaPuzzle is a full-featured Laravel e-commerce platform with:

- A **public storefront** for customers to browse categories, products, manage a cart, check out, and track orders
- An **admin dashboard** for managing categories, subcategories, products, and orders
- **Customer authentication** with profile and delivery address management
- **SEO-friendly URLs** with automatic slug generation and 301 redirect history
- **Fully responsive** mobile-first design using Tailwind CSS v4

---

## 2. Features

### Admin Panel
| Feature | Details |
|---|---|
| Authentication | Secure login/logout, rate-limited (5 attempts/min), session fixation protection |
| Dashboard | Live statistics: categories, subcategories, products, active products, total orders, pending orders |
| Category CRUD | Create, list, search, edit, delete, toggle active, auto-slug generation |
| Subcategory CRUD | Create under a category, filter by category, edit, delete, toggle active, composite unique slug per category |
| Product CRUD | Create, list (search/filter/sort/paginate), edit, delete, toggle active |
| Product Images | Upload JPEG/JPG/PNG/WebP ≤ 2 MB, unique filenames, image preview, replace/delete lifecycle |
| Slug Management | Auto-generated slugs, manual override, live URL preview, availability checker (AJAX), 301 redirect history |
| Order Management | View all orders, filter by status, search by order number/customer, update status (pending → delivered) |

### Public Storefront
| Feature | Details |
|---|---|
| Homepage | Announcement bar, split hero with product mosaic, USP strip, category grid, latest products, promo banner, trust section |
| Category browsing | Category page with subcategory filter pills, paginated products |
| Subcategory browsing | Subcategory page with breadcrumb, paginated products |
| Product detail | Full product page: image, pricing, discount %, description, Add to Cart, qty stepper, related products |
| Cart | localStorage-based cart with qty controls, order summary, delivery calculation |
| Checkout | Delivery address form (pre-filled from profile), payment method (COD/bKash/Nagad), server-side order creation |
| Order confirmation | Order reference number, items, address, totals |
| Customer auth | Register, login, logout, profile management, delivery address save, password change |
| My Orders | Order history with status tracking |
| SEO | Unique slugs, meta titles, meta descriptions, canonical URLs, Open Graph tags |
| Navigation | Category hover dropdowns with subcategories (desktop), accordion (mobile) |
| Responsive | Fully mobile-friendly at 360px, 390px, 768px, 1024px, 1440px |

---

## 3. Technology Stack

| Component | Version |
|---|---|
| PHP | 8.4.12 |
| Laravel | 13.40.0 |
| MySQL | 8.0.43 |
| Tailwind CSS | v4 (via @tailwindcss/vite) |
| Vite | 8.3.6 |
| PHPUnit | 12.5.12 |
| Node.js | 22.19.0 |
| npm | 10.9.3 |
| Composer | 2.8.11 |

**Why Laravel 13?** PHP 8.4.12 is fully compatible with Laravel 13. No downgrade was required.

**Why Tailwind v4?** Already configured in the project via `@tailwindcss/vite` plugin — no `tailwind.config.js` needed. Uses `@import 'tailwindcss'` in `app.css`.

---

## 4. Architecture Overview

The application is a **Laravel monolith** following clean MVC conventions:

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin CRUD controllers (Category, Subcategory, Product, Order, Dashboard)
│   │   ├── Auth/           # AdminLoginController, UserLoginController, UserRegisterController, UserProfileController
│   │   ├── OrderController.php      # Storefront order placement + history
│   │   └── StorefrontController.php # Public pages (home, category, product, cart, checkout)
│   ├── Middleware/
│   │   ├── EnsureIsAdmin.php    # Redirects non-admins away from /admin/*
│   │   └── EnsureIsCustomer.php # Redirects admins away from /profile, /my-orders
│   └── Requests/Admin/          # Form Request classes for all CRUD operations
├── Models/
│   ├── User.php            # is_admin boolean, profile fields, orders relationship
│   ├── Category.php        # hasMany Subcategory, hasMany Product, scopeActive
│   ├── Subcategory.php     # belongsTo Category, hasMany Product, scopeActive
│   ├── Product.php         # belongsTo Category+Subcategory, discount accessor
│   ├── ProductSlugHistory.php  # Tracks previous slugs for 301 redirects
│   ├── Order.php           # Status constants, belongs to User, hasMany OrderItem
│   └── OrderItem.php       # Product snapshot at purchase time
├── Services/
│   ├── ProductImageService.php  # Storage::disk('public') image lifecycle
│   └── ProductSlugService.php   # Slug generation, normalization, uniqueness
└── Providers/
    └── AppServiceProvider.php   # View composer: navCategories on every storefront page
```

**Key patterns:**
- **Form Requests** for all admin validation (slug format, price rules, category/subcategory consistency)
- **Service classes** for image and slug operations — controllers stay thin
- **Middleware aliases** registered via `bootstrap/app.php` fluent API (Laravel 13 style — no `Http/Kernel.php`)
- **View composer** injects `$navCategories` into every storefront layout render
- **DB::transaction** wrapping order + order items creation
- **DB price enforcement** — server always uses `$product->new_price` from DB, never trusts client-submitted prices

---

## 5. Database Schema

### Tables

| Table | Description |
|---|---|
| `users` | Customers and admins. `is_admin` boolean differentiates roles. Profile fields: `phone`, `address`, `city`, `district`, `postcode` |
| `categories` | Top-level categories. `slug` unique. `is_active` boolean |
| `subcategories` | Belong to a category. Composite unique `(category_id, slug)`. `is_active` boolean |
| `products` | Belong to category + subcategory. `slug` unique. `decimal(12,2)` prices. `is_active` boolean |
| `product_slug_histories` | Previous slugs for 301 redirects. `old_slug` unique globally |
| `orders` | Customer orders. Snapshots delivery address at time of purchase. `user_id` nullable (guest orders) |
| `order_items` | Line items per order. Snapshots product name/price/image at time of purchase |
| `sessions` | Laravel database sessions |
| `cache` | Laravel database cache |
| `jobs` | Laravel database queue |

### Key Relationships

```
Category ──── hasMany ──── Subcategory ──── hasMany ──── Product
    │                                                        │
    └──── hasMany ─────────────────────────────── Product ──┘
                                                     │
                                              ProductSlugHistory

User ──── hasMany ──── Order ──── hasMany ──── OrderItem ──── belongsTo ──── Product
```

### Critical Constraints

- `subcategories(category_id, slug)` — composite unique index (slug unique within category)
- `products.slug` — unique globally
- `product_slug_histories.old_slug` — unique globally (prevents recycling old URLs)
- `orders.user_id` — nullable (guest checkout allowed)
- `order_items.product_id` — nullable (product may be deleted after purchase; snapshot data preserved)
- All monetary values: `decimal(12,2)` — never float

---

## 6. Installation

### Prerequisites

- PHP 8.3+ (8.4.12 recommended)
- Composer 2.x
- MySQL 8.0+
- Node.js 18+ and npm
- Git

### Steps

```bash
# 1. Clone the repository
git clone <repository-url> BanglaPuzzle
cd BanglaPuzzle

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure your database in .env (see §7)

# 6. Run migrations and seed demo data
php artisan migrate:fresh --seed

# 7. Create the storage symlink
php artisan storage:link

# 8. Install Node dependencies and build assets
npm install
npm run build

# 9. Start the development server
php artisan serve
```

Open **http://localhost:8000** in your browser.

---

## 7. Environment Configuration

Copy `.env.example` to `.env` and update:

```env
APP_NAME=BanglaPuzzle
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=banglapuzzle
DB_USERNAME=root
DB_PASSWORD=your_password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

> **Production note:** Set `APP_ENV=production` and `APP_DEBUG=false`. Run `php artisan config:cache`, `route:cache`, and `view:cache`.

---

## 8. Demo Credentials

### Admin Account
| Field | Value |
|---|---|
| URL | http://localhost:8000/admin/login |
| Email | admin@banglapuzzle.com |
| Password | password |

### Customer Account (created via registration)
| Field | Value |
|---|---|
| URL | http://localhost:8000/register |
| Register with any email/password | — |

> Demo data: **5 categories**, **15 subcategories**, **75 products** seeded with realistic Bangladeshi e-commerce data and Picsum product images.

---

## 9. Running the Application

```bash
# Development server
php artisan serve

# Vite asset watcher (separate terminal)
npm run dev

# Fresh database with demo data
php artisan migrate:fresh --seed

# Rebuild compiled assets
npm run build
```

---

## 10. Running Tests

```bash
# Full test suite
php artisan test

# Specific test group
php artisan test --filter AuthTest
php artisan test --filter CategoryCrudTest
php artisan test --filter SubcategoryCrudTest
php artisan test --filter ProductCrudTest
php artisan test --filter ProductSlugTest
php artisan test --filter StorefrontTest
php artisan test --filter StorefrontSlugRedirectTest
php artisan test --filter OrderAdminTest
```

**Current test results:** 88 tests, 211 assertions, 0 failures

| Suite | Tests | Coverage |
|---|---|---|
| AuthTest | 6 | Admin login, logout, guest redirect, rate limiting |
| CategoryCrudTest | 9 | Full CRUD, slug, delete blocking, search |
| SubcategoryCrudTest | 8 | Full CRUD, composite slug, AJAX endpoint, delete blocking |
| ProductCrudTest | 14 | Full CRUD, image lifecycle, price validation, subcategory consistency |
| ProductSlugTest | 10 | Slug generation, uniqueness, history, update preservation |
| StorefrontTest | 20 | Public pages, cart, auth, orders, security boundaries |
| StorefrontSlugRedirectTest | 5 | 301 redirects, chains, isolation |
| OrderAdminTest | 3 | Admin order list, detail, status update |
| ExampleTest | 2 | Baseline |

Tests use **SQLite in-memory** — no MySQL connection required for running tests.

---

## 11. Routes Reference

### Public Storefront
| Method | URI | Name |
|---|---|---|
| GET | / | home |
| GET | /category/{slug} | storefront.category |
| GET | /category/{categorySlug}/{subcategorySlug} | storefront.subcategory |
| GET | /product/{slug} | storefront.product |
| GET | /cart | storefront.cart |
| GET | /checkout | storefront.checkout |
| POST | /orders | orders.store |
| GET | /orders/{order_number} | orders.show |

### Customer Auth
| Method | URI | Name |
|---|---|---|
| GET | /register | user.register |
| POST | /register | user.register.post |
| GET | /login | user.login |
| POST | /login | user.login.post |
| POST | /logout | user.logout |
| GET | /profile | storefront.profile |
| POST | /profile | storefront.profile.update |
| POST | /profile/password | storefront.profile.password |
| GET | /my-orders | orders.index |

### Admin Panel
| Method | URI | Name |
|---|---|---|
| GET | /admin/login | admin.login |
| POST | /admin/login | admin.login.post |
| POST | /admin/logout | admin.logout |
| GET | /admin/dashboard | admin.dashboard |
| GET/POST | /admin/categories | admin.categories.index / store |
| GET/PUT/DELETE | /admin/categories/{id} | admin.categories.show / update / destroy |
| POST | /admin/categories/{id}/toggle-active | admin.categories.toggleActive |
| GET/POST | /admin/subcategories | admin.subcategories.index / store |
| PUT/DELETE | /admin/subcategories/{id} | admin.subcategories.update / destroy |
| POST | /admin/subcategories/{id}/toggle-active | admin.subcategories.toggleActive |
| GET | /admin/subcategories-by-category/{id} | admin.subcategories.byCategory |
| GET/POST | /admin/products | admin.products.index / store |
| PUT/DELETE | /admin/products/{id} | admin.products.update / destroy |
| POST | /admin/products/{id}/toggle-active | admin.products.toggleActive |
| GET | /admin/products/check-slug | admin.products.checkSlug |
| GET | /admin/orders | admin.orders.index |
| GET | /admin/orders/{id} | admin.orders.show |
| POST | /admin/orders/{id}/status | admin.orders.updateStatus |

---

## 12. VPS Deployment

See **[VPS_DEPLOYMENT.md](VPS_DEPLOYMENT.md)** for the complete Ubuntu 22.04/24.04 deployment guide including:
- Nginx configuration with Laravel `public/` as document root
- PHP 8.4-FPM setup with all required extensions
- MySQL 8 setup and database creation
- SSL/HTTPS via Let's Encrypt (certbot)
- Production optimisation commands
- File permissions and ownership
- Backup and log inspection

> **Note:** Live deployment was not performed — no VPS access was available. The documentation is accurate and reproducible.

---

## 13. Known Limitations

| Item | Note |
|---|---|
| Cart persistence | Cart uses localStorage — clears on browser data reset. A server-side cart would require additional DB design. |
| Payment processing | bKash and Nagad payment methods are UI-only. No payment gateway integration. |
| Email notifications | No order confirmation emails. Mail driver is set to `log`. |
| Product images | Only one image per product. Multi-image support not implemented. |
| Password reset | No "forgot password" / email-based reset flow. |
| Stock management | No inventory/stock quantity tracking. |
| Search | Basic name-based search. No full-text or fuzzy search. |
| Caching | No application-level caching (Redis/Memcached). Suitable for assignment scope. |
| Guest order tracking | Guests can view their order via the direct URL but have no order history page. |
| Deployment | Documentation only — no live server URL. |

---

## Project Structure

```
BanglaPuzzle/
├── app/
│   ├── Http/Controllers/{Admin,Auth}/
│   ├── Http/Middleware/
│   ├── Http/Requests/Admin/
│   ├── Models/
│   ├── Providers/
│   └── Services/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
│   └── images/placeholder.png
├── resources/
│   ├── css/app.css
│   ├── js/app.js          ← Cart store (localStorage)
│   └── views/
│       ├── admin/
│       ├── components/
│       ├── layouts/
│       └── storefront/
├── routes/web.php
├── tests/Feature/
├── .env.example
├── README.md
├── VPS_DEPLOYMENT.md
└── banglapuzzle.sql       ← Database export
```

---

*Built with ❤️ in Bangladesh — BanglaPuzzle Limited Technical Assignment 2026*
