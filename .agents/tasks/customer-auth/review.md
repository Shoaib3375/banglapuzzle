# Customer authentication, profile, and checkout pre-fill

This change adds customer authentication to the BanglaPuzzle storefront — registration, login with logout, a profile/address page with a separate password change, checkout pre-fill from the logged-in user, and header auth links (desktop + mobile) — alongside a context-aware guest redirect so a customer guest hitting `/profile` no longer gets bounced to the admin login. Admin auth is untouched: the admin login route, `EnsureIsAdmin`, and every `/admin/*` route are unchanged, and the new redirect closure keeps `admin/*` paths pointing at `admin.login`. The customer login uses an `is_admin => false` credential guard — symmetric with the existing admin login's `is_admin => true` — and the POST is throttled `5,1`; every state-changing form carries `@csrf` and all output is Blade-escaped. The five new tests are appended after the existing suite without modifying any of the 72 prior tests.

**Watch for:** the registration POST is intentionally unthrottled (only login is rate-limited), and the `is_admin` login guard plus the admin-bounced-from-profile path are correct by inspection but have no regression test.

**Verdict**: APPROVED

## High-level view

The gating design is the subtle part. Because the profile route is guarded `['auth', 'customer']`, the `auth` middleware runs first; the old static `redirectGuestsTo(fn () => route('admin.login'))` would have sent a customer guest to the admin login. The fix replaces it with a closure that inspects the request path — `admin/*` still goes to `admin.login`, everything else goes to `user.login` — and adds `redirectUsersTo(route('home'))` so an already-authenticated user hitting `/login` or `/register` lands on the storefront home rather than the default `/home` (which 404s). `EnsureIsCustomer` then handles the authenticated-but-wrong-role case: an admin reaching `/profile` is redirected to `admin.dashboard`.

Customer login security rests on the `is_admin` credential in `Auth::attempt`. Adding `'is_admin' => false` to the credentials turns the lookup into a where clause that will not match an admin row, so an admin cannot authenticate through the customer form; this mirrors the admin controller's `'is_admin' => true`. The login POST carries `throttle:5,1`, the session is regenerated on success, and the redirect uses `intended()` falling back to `home`.

Registration, profile update, and password change are each narrow and mass-assignment-safe: `register` builds the user from only the validated fields plus a hardcoded `is_admin => false`, `update` writes only the validated profile/address fields (no `is_admin`, no `password`), and `changePassword` verifies the current password with `Hash::check` before writing a `Hash::make`'d new one. The `User` model casts `password` as `hashed`, so passing an already-hashed value does not double-hash.

The header and Blade surface is gated with `@guest … @else @if (! Auth::user()->is_admin) … @endif @endguest`, so a guest sees Sign In/Register, a customer sees an avatar dropdown (reusing the existing `showDrop`/`hideDrop`/`.nav-dropdown` machinery) with My Profile and a Sign Out form, and a logged-in admin sees neither. All six forms in the diff carry `@csrf`; output is `{{ }}`-escaped with no raw `{!! !!}` anywhere; and the auth views deliberately omit session-flash rendering because the layout already renders `session('success')`/`session('error')` globally.

Checkout pre-fill is additive and guest-safe: a single `@php $authUser = Auth::user(); @endphp` feeds `old('field', $authUser?->field ?? '')` on every checkout input, so validation repopulation (`old()`) wins, the authenticated user's saved values fill in next, and a guest gets empty strings via null-safe `?->` + `?? ''`. The form action, field names, and client-side JS are unchanged.

Tests are appended, not rewritten: `use App\Models\User;` joins the imports and five methods are added after the final existing test, covering register, login, guest-blocked-from-profile, authenticated profile view, and profile update. The factory's cached default password `'password'` is what the login test relies on, and that matches the factory.

<details>
<summary>Issues (2)</summary>

1. **Registration POST is unthrottled** — the `/register` POST route has no `throttle` middleware (only `/login` does, `5,1`). Bot-driven account creation is therefore unbounded. This is consistent with the plan, which scoped throttling to the login route only, so it is non-blocking; consider adding a registration throttle for hardening.
2. **Key security paths lack regression tests** — the suite confirms register/login/view/update but does not assert that an admin is rejected by the customer login (`is_admin => false` guard), that an admin is redirected away from `/profile`, that `changePassword` rejects a wrong current password, or that logout works. The `is_admin` guard is correct by inspection, but a test locking in "admin cannot log in as customer" would protect the most security-critical branch. Non-blocking; the code is correct today.

</details>

<details>
<summary>Details</summary>

### Context-aware guest redirect and the customer guard

`bootstrap/app.php` previously set `$middleware->redirectGuestsTo(fn () => route('admin.login'))` — a single global target. That is wrong for customer pages: the profile route is `['auth', 'customer']`, and `auth` short-circuits before `customer` ever runs, so a guest visiting `/profile` would have been redirected to the admin login. The replacement closure takes the `Request` and branches on `$request->is('admin/*')`, preserving the admin behavior exactly while routing every other guarded path to `user.login`. The `test_guest_cannot_access_profile` case exercises exactly this path (GET `/profile` as guest → 302 to `user.login`).

`redirectUsersTo(fn () => route('home'))` handles the inverse: an authenticated user who lands on the guest-only `/login` or `/register` is sent to the storefront home instead of the framework default `/home`, which does not exist in this app and would 404. The admin login route at `/admin/login` is a standalone `Route::get` with no `guest` middleware, so `redirectUsersTo` does not affect it — an admin visiting `/admin/login` while logged in still sees the form, unchanged. Wrapping `route('home')` in a closure rather than passing the resolved URL defers resolution to request time — resolving it eagerly during bootstrap triggered a "Class config does not exist" fatal.

`EnsureIsCustomer` has two branches: an unauthenticated request is sent to `user.login` with an error flash, and an authenticated admin is sent to `admin.dashboard`. In practice the guest branch is dead code for the profile route, because `auth` always rejects the guest first; it would only matter if the middleware were ever applied without `auth` preceding it. The admin branch is the meaningful one: an admin reaching `/profile` is bounced to `admin.dashboard` rather than viewing/editing a customer profile.

### Customer login: the is_admin guard and throttle

The login controller calls `Auth::attempt(['email' => …, 'password' => …, 'is_admin' => false], $remember)`. The `is_admin => false` credential is the guard the review asks about, and it is the symmetric counterpart of `AdminLoginController::login`, which attempts with `'is_admin' => true`. Because Eloquent's user provider turns every non-password credential into a `where` clause, an admin row (`is_admin = 1`) will not match an attempt that constrains `is_admin = false`, so an admin cannot authenticate through the customer form. The `is_admin` column is cast as `boolean` on the model, and the admin controller's symmetric `'is_admin' => true` attempt is the established pattern.

On success the session is regenerated (mitigating session fixation) and the redirect is `intended(route('home'))`, so a customer who was redirected to login from a guarded page returns there, otherwise lands on home. On failure it returns `back()->with('error', 'Invalid credentials')->withInput($request->only('email'))` — repopulating email but not password. The POST `/login` route carries `throttle:5,1`, matching the admin login route. The one gap is that no test asserts an admin is rejected here (see Issues).

### Registration, profile update, and password change

`UserRegisterController::register` validates `name` (required, string, max 100), `email` (required, email, unique on `users`), and `password` (required, string, min 8, confirmed), then creates the user from the validated array plus a hardcoded `'is_admin' => false`. Because `$request->validate()` returns only the rules' keys, a client-supplied `is_admin` field is discarded — there is no mass-assignment escalation path. `Auth::login($user)` authenticates without "remember" and redirects to `storefront.profile`. The `User` model casts `password` as `hashed`; the controller passes `Hash::make($validated['password'])`, and the cast recognizes an already-hashed value and does not re-hash, so there is no double-hash. Unlike `/login`, the `/register` POST carries no `throttle`, so automated account creation is unbounded (see Issues).

`UserProfileController::update` validates the same name/email rules but relaxes email uniqueness with `Rule::unique('users', 'email')->ignore($user->id)` so a user can keep their own email, then calls `$user->update($validated)`. The validated set is exactly the fillable profile/address fields (`name`, `email`, `phone`, `address`, `city`, `district`, `postcode`); `password` and `is_admin` are not in it, so the update cannot touch them. All five new columns were added to `$fillable`. The profile view renders Personal Information and Delivery Address as a single form (both submit buttons are `type="submit"` inside it, so either save submits the whole profile with current values pre-filled), with a separate Change Password form below.

`changePassword` validates `current_password` and `password` (min 8, confirmed), then `Hash::check`s the current password against the stored hash before writing `Hash::make($request->password)` via `$user->update`. A successful password change does not invalidate other sessions/devices — out of scope here, but worth knowing if shared-device sessions matter.

### Header auth surface and Blade form safety

The desktop header uses `@guest … @else @if (! Auth::user()->is_admin) … @endif @endguest`. `@guest` compiles to `if(auth()->guest())`, so `@else` works inside it. Guests see Sign In/Register text links; an authenticated non-admin sees an avatar (initial via `strtoupper(substr(name,0,1))`, name via `Str::limit(name, 12)`, chevron) whose hover opens a `.nav-dropdown` panel reusing the same `showDrop`/`hideDrop` handlers and inline styles as the category dropdowns; a logged-in admin falls through both the `@else` and the `@if (! is_admin)` and renders nothing — no guest links, no customer dropdown. The mobile nav mirrors this with plain links and a POST Sign Out form. The Sign Out forms target `route('user.logout')` with `@csrf`.

Every state-changing form in the diff has `@csrf`: login, register, profile update, change password, desktop Sign Out, mobile Sign Out. All output uses `{{ }}` (escaped) — the avatar initial, the truncated name, the `$user` field pre-fills, the district options — with no `{!! !!}` anywhere, so there is no reflected-XSS surface from user-controlled name/profile values. The auth views render a validation summary via `<x-alert type="error" :message="$errors->first()" />` and inline `@error('field')` blocks, matching `admin/auth/login.blade.php`, and deliberately do not re-render `session('success')`/`session('error')` because the layout already renders those globally immediately after the header — so a post-login or post-update flash shows once, not twice.

### Checkout pre-fill

`checkout.blade.php` adds `@php $authUser = Auth::user(); @endphp` and gives each existing input a `value="{{ old('field', $authUser?->field ?? '') }}"`, with the district `<select>` options gaining `@selected($d === old('district', $authUser?->district ?? ''))`. The ordering is deliberate: `old()` first means a failed-validation redirect repopulates the submitted value; otherwise the authenticated user's saved value fills in; for a guest (`$authUser` null) the null-safe `?->` yields null and `?? ''` yields an empty string. The checkout route is public (not behind `auth`), so the guest path is exercised on every anonymous checkout. The form's `id`, `action`, field `name`s, and the existing client-side validation JS are untouched — the values are purely initial-fill and do not interfere with the JS reading those inputs.

### Test coverage

Five methods are appended to `tests/Feature/StorefrontTest.php` after `test_product_page_shows_discount_percentage_when_applicable`, with `use App\Models\User;` added to the import block and nothing else in the file changed — consistent with the recorded 72-untouched / 77-total result. Covered: successful registration (redirect to profile, authenticated, `is_admin=false` in DB), successful login (redirect to home, authenticated), guest blocked from profile (redirect to `user.login`, exercising the context-aware redirect), authenticated profile view (200, sees "Personal Information" and the user's name), and profile update (302, success flash, DB has the new values). The login test relies on the factory's cached default password `'password'`, which matches `UserFactory`.

Not covered (see Issues): the `is_admin => false` guard rejecting an admin at the customer login, an admin being redirected from `/profile` to `admin.dashboard` (the `EnsureIsCustomer` admin branch), `changePassword` happy and wrong-current-password paths, logout, registration validation failures (duplicate email, short password, mismatched confirmation), email-uniqueness conflict on profile update, the throttle, and checkout pre-fill rendering the authenticated user's values. The most security-relevant of these is the untested `is_admin` guard; the code is correct by inspection, but a regression test would be the cheapest way to keep it that way.

</details>

<details>
<summary>File map</summary>

- `database/migrations/2026_11_07_164500_add_profile_fields_to_users_table.php` — adds nullable `phone`, `address`, `city`, `district`, `postcode` after `is_admin`; `down()` drops them.
- `app/Models/User.php` — appends the five new columns to `$fillable` (password stays `hashed`, `is_admin` stays `boolean`).
- `bootstrap/app.php` — registers the `customer` middleware alias; replaces the static guest redirect with a path-aware closure; adds `redirectUsersTo(route('home'))`.
- `app/Http/Middleware/EnsureIsCustomer.php` — guests → `user.login`, admins → `admin.dashboard`, otherwise passes.
- `app/Http/Controllers/Auth/UserRegisterController.php` — `showRegisterForm` + `register` (validate, create `is_admin=false`, login, redirect to profile).
- `app/Http/Controllers/Auth/UserLoginController.php` — `showLoginForm` + `login` (`is_admin=false` attempt, regenerate, `intended`/home) + `logout`.
- `app/Http/Controllers/Auth/UserProfileController.php` — `show`, `update` (profile + address, unique-ignore-self email), `changePassword` (`Hash::check` + `Hash::make`).
- `routes/web.php` — guest group (register/login, login throttled `5,1`), `auth`-guarded logout, `['auth','customer']` profile routes; admin routes unchanged.
- `resources/views/layouts/storefront.blade.php` — desktop right-icons and mobile nav: guest Sign In/Register, customer avatar dropdown (reuses `showDrop`/`hideDrop`/`.nav-dropdown`) with My Profile + Sign Out, admin sees neither.
- `resources/views/storefront/auth/login.blade.php` — Sign In card, email/password/remember, `@csrf`, validation summary + inline `@error`, link to register.
- `resources/views/storefront/auth/register.blade.php` — Create Account card, name/email/password/confirm, `@csrf`, validation summary + inline `@error`, link to login.
- `resources/views/storefront/auth/profile.blade.php` — one form over Personal Information + Delivery Address cards (both submits), separate Change Password form; district select reuses the 18 checkout options; `old('field', $user->field ?? '')` pre-fill throughout.
- `resources/views/storefront/checkout.blade.php` — `@php $authUser = Auth::user(); @endphp` and `old('field', $authUser?->field ?? '')` pre-fill on full_name/phone/email/address/city/district/postcode; form action/names/JS unchanged.
- `tests/Feature/StorefrontTest.php` — appends `use App\Models\User;` and five tests (register, login, guest-blocked, profile view, profile update); existing tests unmodified.

Full diff: `git diff 616f995 7a07b49`.

</details>
