# Verification — Customer Authentication

Feature: customer (user) authentication, profile management, and checkout pre-fill for the BanglaPuzzle storefront. Implemented per `plan.md`. Admin auth untouched.

Commit SHA: `7a07b49`

---

## Files created / changed

Created:
- `database/migrations/2026_11_07_164500_add_profile_fields_to_users_table.php` — adds nullable string columns `phone`, `address`, `city`, `district`, `postcode` to `users` (after `is_admin`); `down()` drops them.
- `app/Http/Controllers/Auth/UserRegisterController.php` — `showRegisterForm()` + `register()` (validates name/email/password, creates `is_admin=false` user, logs in, redirects to `storefront.profile`).
- `app/Http/Controllers/Auth/UserLoginController.php` — `showLoginForm()` + `login()` (`Auth::attempt` with `is_admin=false`, regenerate session, `intended`/`home`) + `logout()`.
- `app/Http/Controllers/Auth/UserProfileController.php` — `show()`, `update()` (profile + address, `Rule::unique(...)->ignore($user->id)`), `changePassword()` (`Hash::check` + `Hash::make`).
- `app/Http/Middleware/EnsureIsCustomer.php` — redirects guests to `user.login`, admins to `admin.dashboard`, otherwise passes.
- `resources/views/storefront/auth/login.blade.php`
- `resources/views/storefront/auth/register.blade.php`
- `resources/views/storefront/auth/profile.blade.php` — one form covers Personal Information + Delivery Address; separate Change Password form; district select reuses the 18 options from checkout.

Changed:
- `app/Models/User.php` — added `phone`, `address`, `city`, `district`, `postcode` to `$fillable`.
- `bootstrap/app.php` — registered `customer` middleware alias; replaced the static guest-redirect with a context-aware closure (`admin/*` -> `admin.login`, else `user.login`); added `redirectUsersTo(fn () => route('home'))`. (Pint converted the FQCNs to `use` imports.)
- `routes/web.php` — added customer auth/profile route groups (guest: register/login, login throttled `5,1`; auth: logout; `['auth','customer']`: profile GET/update/password). Admin routes unchanged.
- `resources/views/layouts/storefront.blade.php` — desktop right-icons + mobile nav: guests see Sign In/Register; logged-in non-admin sees a user dropdown (avatar + `Str::limit(name,12)` + chevron, reusing `showDrop`/`hideDrop`/`.nav-dropdown`) with My Profile + Sign Out form; logged-in admin shows neither. Cart link kept.
- `resources/views/storefront/checkout.blade.php` — `@php $authUser = Auth::user(); @endphp` + pre-fill `old('field', $authUser?->field ?? '')` on full_name(name)/phone/email/address/city/district/postcode. Form action, field names, and JS unchanged.
- `tests/Feature/StorefrontTest.php` — appended 5 tests (`test_user_can_register`, `test_user_can_login`, `test_guest_cannot_access_profile`, `test_authenticated_user_can_view_profile`, `test_user_can_update_profile`) and `use App\Models\User;`. Existing 72 tests untouched.

Not staged (per instructions): `.agents/tasks/*`, `.env`, `vendor/`, `node_modules/`, `public/build/`.

---

## Commands run and results

1. `php artisan migrate --no-interaction`
   - Result: PASS. `2026_11_07_164500_add_profile_fields_to_users_table` applied (413.27ms DONE).

2. `php artisan test --compact`
   - Result: PASS. 77 tests, 77 passed, 188 assertions (existing 72 + 5 new), exit code 0.

3. `php artisan route:list --path=login`
   - Result: PASS. Shows `user.login` (GET/POST `login`) and `admin.login` (GET/POST `admin/login`) as separate routes. Customer profile routes (`storefront.profile`, `storefront.profile.update`, `storefront.profile.password`) and `user.register`/`user.register.post`/`user.logout` also registered.

4. `npm run build`
   - Result: PASS. Vite v8.3.6 built client environment for production; 3 modules transformed; manifest + assets written to `public/build/`; built in 734ms; `BUILD_EXIT=0`.

5. `vendor/bin/pint --dirty --format agent`
   - Result: PASS. Fixed `binary_operator_spaces` / `unary_operator_spaces` / `not_operator_with_successor_space` on the three controllers and `fully_qualified_strict_types` / `ordered_imports` on `bootstrap/app.php`. Exit code 0.

---

## Notes
- `is_admin` column is added by `2025_01_01_000001_add_is_admin_to_users_table.php`, which runs before the new profile migration, so `->after('is_admin')` is valid on MySQL (and a no-op on the SQLite test DB).
- The `password` is cast as `hashed` on the `User` model; controllers pass `Hash::make()` explicitly (already-hashed values are not double-hashed).
- `redirectUsersTo` takes a callable, so `route('home')` is wrapped in a closure to avoid resolving the URL too early during bootstrap (which had caused a "Class config does not exist" fatal).
- Flash messages are rendered globally by the storefront layout, so the auth views only render a validation-summary `<x-alert>` + inline `@error` blocks (no duplicated session flash).
