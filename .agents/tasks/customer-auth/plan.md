# Implementation Plan — Customer (User) Authentication

Goal: add customer authentication (register / login / profile) to the BanglaPuzzle storefront, integrated into the existing Laravel 13 / PHP 8.4 / Tailwind v4 codebase without touching admin auth.

Workspace: `e:/interview/BanglaPuzzle` (Windows / PowerShell). Use absolute paths for every read/write. The `guest` middleware here is overridden (see decision 1); account for that.

---

## Design decisions (recorded — implement exactly these)

1. **Guest redirect must be context-aware.** `bootstrap/app.php` sets `redirectGuestsTo(fn () => route('admin.login'))`, so a guest hitting `/profile` (guarded `['auth','customer']`) would be sent to the **admin** login because `auth` runs before `customer`. Confirmed in `vendor/.../Authenticate.php` that the `redirectGuestsTo` callable receives `Request $request`. Replace it with a closure: `admin/*` paths → `route('admin.login')`, everything else → `route('user.login')`. This preserves admin behavior and makes customer guests land on the customer login. It is in scope because `bootstrap/app.php` is already a file we modify to register the `customer` alias.

2. **Logged-in redirect for guest pages.** Add `redirectUsersTo(route('home'))` so an authenticated user who lands on `/login` or `/register` is sent to the storefront home instead of the default `/home` (which 404s). The admin login page is `/admin/login` (no `guest` middleware), so it is unaffected.

3. **Password hashing.** The `User` model casts `password` as `hashed` (auto-hashes on set, no-ops if already hashed). Per the spec wording ("bcrypt-hashed password"), controllers pass `Hash::make($plaintext)` explicitly; the cast detects the already-hashed value and does not double-hash. `changePassword` uses `Hash::make()` as well and verifies the old one with `Hash::check()`.

4. **Test user creation.** The `UserFactory` default password is `'password'` (cached static) and `is_admin` defaults to `false` via the migration default; there is no factory `customer()` state. Tests use `User::factory()->create(['is_admin' => false])` and authenticate with password `'password'`.

5. **Flash messages in auth views.** The storefront layout already renders `session('success')` / `session('error')` globally right under the header. Auth views must NOT re-render session flash (would duplicate). Inline field validation errors use the `@error('field')` directive, matching `admin/auth/login.blade.php`. A validation summary uses `<x-alert type="error" :message="$errors->first()" />` exactly like the admin login view.

6. **Header for a logged-in admin.** Show the customer avatar dropdown only when `Auth::check() && ! Auth::user()->is_admin`. For a logged-in admin browsing the storefront, no customer auth UI is shown (admins use the admin panel). For guests, show Sign In + Register.

7. **Profile page = one form for both cards.** "Personal Information" (left) and "Delivery Address" (right) are a single `<form action="{{ route('storefront.profile.update') }}" method="POST">` with `@csrf`; both "Save Changes" and "Save Address" are `type="submit"` inside it. "Change Password" is a separate `<form action="{{ route('storefront.profile.password') }}" method="POST">` below, full width.

8. **No npm build needed.** All changes are Blade/PHP; Blade is served directly via the existing `@vite([...])` in the layout. Do not run `npm run build/dev`.

---

## Implementation order

- [ ] 1. Add profile fields migration + update `User` model `$fillable`.
      Run `php artisan make:migration add_profile_fields_to_users_table --table=users --no-interaction`. Edit the generated file: `up()` adds nullable string columns after `is_admin` — `$table->string('phone')->nullable()`, `$table->string('address')->nullable()`, `$table->string('city')->nullable()`, `$table->string('district')->nullable()`, `$table->string('postcode')->nullable()`; `down()` drops those five columns (`$table->dropColumn(['phone','address','city','district','postcode'])`).
      Then in `e:/interview/BanglaPuzzle/app/Models/User.php` add `'phone','address','city','district','postcode'` to the existing `$fillable` array (keep `name,email,password,is_admin,email_verified_at`).
      Files: `database/migrations/<timestamp>_add_profile_fields_to_users_table.php`, `app/Models/User.php`
      Verify: `php artisan test --compact` — all existing tests pass; `RefreshDatabase` runs the new migration against the in-memory SQLite cleanly (proves the migration applies without error).

- [ ] 2. Create `EnsureIsCustomer` middleware + register alias + fix guest/user redirects in `bootstrap/app.php`.
      Run `php artisan make:middleware EnsureIsCustomer --no-interaction`. Implement `handle(Request $request, Closure $next): Response`: if `! Auth::check()` return `redirect()->route('user.login')->with('error', 'Please sign in to continue.')`; if `Auth::user()->is_admin` return `redirect()->route('admin.dashboard')`; otherwise `return $next($request)`. Add `use Illuminate\Support\Facades\Auth;`.
      Edit `e:/interview/BanglaPuzzle/bootstrap/app.php` inside `->withMiddleware()`: add `'customer' => \App\Http\Middleware\EnsureIsCustomer::class` to the `alias([...])` array (keep the existing `'admin'` alias). Replace `$middleware->redirectGuestsTo(fn () => route('admin.login'));` with:
      ```php
      $middleware->redirectGuestsTo(function (Request $request) {
          return $request->is('admin/*') ? route('admin.login') : route('user.login');
      });
      $middleware->redirectUsersTo(route('home'));
      ```
      (`Request` is already imported at the top of `bootstrap/app.php`.)
      Files: `app/Http/Middleware/EnsureIsCustomer.php`, `bootstrap/app.php`
      Verify: `php artisan test --compact` — existing tests still pass; `php artisan route:list` loads with no error (proves `bootstrap/app.php` parses and the alias registers).

- [ ] 3. Create the three customer auth controllers.
      Run (one per controller, all `--no-interaction`):
      `php artisan make:controller Auth/UserRegisterController`
      `php artisan make:controller Auth/UserLoginController`
      `php artisan make:controller Auth/UserProfileController`
      (Default plain controllers extending `App\Http\Controllers\Controller` — do NOT pass `--resource`/`--api`.)

      **UserRegisterController** (`app/Http/Controllers/Auth/UserRegisterController.php`): imports `App\Models\User`, `Illuminate\Http\Request`, `Illuminate\Support\Facades\Auth`, `Illuminate\Support\Facades\Hash`.
      - `showRegisterForm()` → `return view('storefront.auth.register');`
      - `register(Request $request)`: validate `name` (`required|string|max:100`), `email` (`required|email|unique:users,email`), `password` (`required|string|min:8|confirmed`); create `User::create(['name'=>..., 'email'=>..., 'password'=>Hash::make($validated['password']), 'is_admin'=>false])`; `Auth::login($user)`; `return redirect()->route('storefront.profile')->with('success', 'Account created successfully.');`

      **UserLoginController** (`app/Http/Controllers/Auth/UserLoginController.php`): imports `Illuminate\Http\Request`, `Illuminate\Support\Facades\Auth`.
      - `showLoginForm()` → `return view('storefront.auth.login');`
      - `login(Request $request)`: validate `email` (`required|email`), `password` (`required|string`); `$remember = $request->boolean('remember')`; on `Auth::attempt(['email'=>..., 'password'=>..., 'is_admin'=>false], $remember)` regenerate session and `return redirect()->intended(route('home'))->with('success', 'Signed in successfully.')`; on failure `return back()->with('error', 'Invalid credentials')->withInput($request->only('email'))`. (Throttling is on the route, not here.)
      - `logout(Request $request)`: `Auth::logout();` invalidate session + regenerate token; `return redirect()->route('home');`

      **UserProfileController** (`app/Http/Controllers/Auth/UserProfileController.php`): imports `Illuminate\Http\Request`, `Illuminate\Support\Facades\Auth`, `Illuminate\Support\Facades\Hash`, `Illuminate\Validation\Rule`.
      - `show()` → `return view('storefront.auth.profile', ['user' => Auth::user()]);`
      - `update(Request $request)`: `$user = Auth::user();` validate `name` (`required|string|max:100`), `email` (`required|email`, `Rule::unique('users','email')->ignore($user->id)`), `phone` (`nullable|string|max:20`), `address` (`nullable|string|max:255`), `city` (`nullable|string|max:100`), `district` (`nullable|string|max:100`), `postcode` (`nullable|string|max:20`); `$user->update($validated);` `return back()->with('success', 'Profile updated successfully.');`
      - `changePassword(Request $request)`: validate `current_password` (`required|string`), `password` (`required|string|min:8|confirmed`); `$user = Auth::user();` if `! Hash::check($request->current_password, $user->password)` `return back()->with('error', 'Current password is incorrect.')`; `$user->update(['password' => Hash::make($request->password)]);` `return back()->with('success', 'Password changed successfully.');`
      Files: `app/Http/Controllers/Auth/UserRegisterController.php`, `app/Http/Controllers/Auth/UserLoginController.php`, `app/Http/Controllers/Auth/UserProfileController.php`
      Verify: `php artisan test --compact` — existing tests still pass (no routes wired yet); `php -l` not required (Artisan `test` compiles everything).

- [ ] 4. Wire customer routes in `routes/web.php` (do not modify any admin routes).
      Add `use` imports near the top: `use App\Http\Controllers\Auth\UserLoginController;`, `use App\Http\Controllers\Auth\UserRegisterController;`, `use App\Http\Controllers\Auth\UserProfileController;`. Append after the existing public routes (before the admin protected group is fine):
      ```php
      // Guest-only customer auth routes
      Route::middleware('guest')->group(function () {
          Route::get('/register', [UserRegisterController::class, 'showRegisterForm'])->name('user.register');
          Route::post('/register', [UserRegisterController::class, 'register'])->name('user.register.post');
          Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('user.login');
          Route::post('/login', [UserLoginController::class, 'login'])->name('user.login.post')->middleware('throttle:5,1');
      });

      Route::post('/logout', [UserLoginController::class, 'logout'])->name('user.logout')->middleware('auth');

      // Customer-only profile routes
      Route::middleware(['auth', 'customer'])->group(function () {
          Route::get('/profile', [UserProfileController::class, 'show'])->name('storefront.profile');
          Route::post('/profile', [UserProfileController::class, 'update'])->name('storefront.profile.update');
          Route::post('/profile/password', [UserProfileController::class, 'changePassword'])->name('storefront.profile.password');
      });
      ```
      Files: `routes/web.php`
      Verify: `php artisan route:list --path=profile` and `php artisan route:list --name=user.` list the new named routes; `php artisan test --compact` still passes existing tests.

- [ ] 5. Create the three auth Blade views under `resources/views/storefront/auth/`. Each `@extends('layouts.storefront')` and sets `@section('meta_title', ...)`. Do NOT render session flash (the layout does it); use `@error('field')` for inline validation errors and a `<x-alert type="error" :message="$errors->first()" />` summary block (matches `admin/auth/login.blade.php`). Card style matches the admin login card: `bg-white rounded-2xl shadow-lg p-8`, inputs use `w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500` with `@error('field') border-red-400 @enderror`. Every form has `@csrf` and a named-route `action`.
      - `login.blade.php`: centered `max-w-md mx-auto px-4 py-12` card; title "Sign In", subtitle "Welcome back"; fields Email (`value="{{ old('email') }}"`) + Password + remember-me checkbox; full-width blue submit "Sign In" to `route('user.login.post')`; below card: `Don't have an account? <a href="{{ route('user.register') }}">Register</a>`.
      - `register.blade.php`: same card; title "Create Account"; fields Full Name (`value="{{ old('name') }}"`), Email (`value="{{ old('email') }}"`), Password, Confirm Password; submit to `route('user.register.post')`; below card: `Already have an account? <a href="{{ route('user.login') }}">Sign In</a>`. All four fields show `@error`.
      - `profile.blade.php`: top has a `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10` wrapper; title "My Profile". ONE form wraps two cards in `grid grid-cols-1 md:grid-cols-2 gap-6`, `action="{{ route('storefront.profile.update') }}" method="POST" @csrf`. LEFT card "Personal Information": inputs Name (`value="{{ old('name', $user->name ?? '') }}"`), Email (`value="{{ old('email', $user->email ?? '') }}"`), Phone (`value="{{ old('phone', $user->phone ?? '') }}"`); submit button "Save Changes". RIGHT card "Delivery Address": Address (`value="{{ old('address', $user->address ?? '') }}"`), City (`value="{{ old('city', $user->city ?? '') }}"`), District `<select name="district">` reusing the EXACT 18 options from `checkout.blade.php`:
        ```blade
        @foreach(['Dhaka','Chittagong','Sylhet','Rajshahi','Khulna','Barisal','Rangpur','Mymensingh','Gazipur','Narayanganj','Comilla','Jessore','Bogra','Dinajpur','Tangail','Faridpur','Noakhali','Cox\'s Bazar'] as $d)
        <option value="{{ $d }}" @selected($d === old('district', $user->district ?? ''))>{{ $d }}</option>
        @endforeach
        ```
        Postcode (`value="{{ old('postcode', $user->postcode ?? '') }}"`); submit button "Save Address" (same form). BELOW both, full width, a SEPARATE "Change Password" card: `<form action="{{ route('storefront.profile.password') }}" method="POST"> @csrf`; fields Current Password, New Password, Confirm New Password; submit "Change Password". All profile/inputs use `old('field', $user->field ?? '')` so validation repopulates AND saved values show. Use `@error('field')` on each input.
      Files: `resources/views/storefront/auth/login.blade.php`, `resources/views/storefront/auth/register.blade.php`, `resources/views/storefront/auth/profile.blade.php`
      Verify: rendered/covered by the new tests in step 8 (`test_authenticated_user_can_view_profile` GETs `/profile` and asserts 200 + content). Confirm no PHP/Blade compile errors with `php artisan view:cache` (optional) — primary verification is the test suite in step 8.

- [ ] 6. Add customer auth links to the storefront layout header (desktop + mobile).
      Edit `e:/interview/BanglaPuzzle/resources/views/layouts/storefront.blade.php`. In the desktop `{{-- Right icons --}}` `<div class="hidden md:flex items-center gap-3">`, keep the cart `<a>` as-is and add before it:
      - **Guest:** `<a href="{{ route('user.login') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">Sign In</a>` and `<a href="{{ route('user.register') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">Register</a>`.
      - **Logged-in non-admin** (`@auth` + `@if(! Auth::user()->is_admin)`): a dropdown reusing the existing `showDrop/hideDrop` pattern — wrapper `<div class="relative" onmouseenter="showDrop(this)" onmouseleave="hideDrop(this)">` containing the trigger button (a circular avatar `w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold` showing `{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}` plus `{{ Str::limit(Auth::user()->name, 12) }}` and a chevron `<svg class="drop-arrow">`) and a `.nav-dropdown` panel (reuse the same inline-styled `.nav-dropdown` div pattern used by the category dropdowns) containing a "My Profile" link to `route('storefront.profile')` and a Sign Out form:
        ```blade
        <form method="POST" action="{{ route('user.logout') }}">
            @csrf
            <button type="submit" class="...">Sign Out</button>
        </form>
        ```
      Use `@guest ... @else @if(! Auth::user()->is_admin) ... @endif @endauth` so a logged-in admin shows neither guest links nor the customer dropdown.
      In the mobile nav (`#mobile-nav`), before the Cart block, add a separated section: guests get "Sign In" and "Register" links; logged-in non-admin gets "My Profile" and a "Sign Out" form (POST `route('user.logout')` + `@csrf`). Match existing mobile link classes.
      Files: `resources/views/layouts/storefront.blade.php`
      Verify: covered by the new login/register/profile tests (they render the layout with the header). Full `php artisan test --compact` passes.

- [ ] 7. Pre-fill checkout from the logged-in user.
      Edit `e:/interview/BanglaPuzzle/resources/views/storefront/checkout.blade.php`. Add a `@php $authUser = Auth::user(); @endphp` block near the top of `@section('content')` (before the breadcrumb). Add `value="..."` attributes (and a selected district) to the existing inputs WITHOUT changing field names, the form id/action, or the JS:
      - `full_name`: `value="{{ old('full_name', $authUser?->name ?? '') }}"`
      - `phone`: `value="{{ old('phone', $authUser?->phone ?? '') }}"`
      - `email`: `value="{{ old('email', $authUser?->email ?? '') }}"`
      - `address`: `value="{{ old('address', $authUser?->address ?? '') }}"`
      - `city`: `value="{{ old('city', $authUser?->city ?? '') }}"`
      - district `<select>`: each option becomes `<option value="{{ $d }}" @selected($d === old('district', $authUser?->district ?? ''))>{{ $d }}</option>`
      - `postcode`: `value="{{ old('postcode', $authUser?->postcode ?? '') }}"`
      Keep `old(...)` first so validation repopulation still wins. (The checkout form is submitted client-side by the existing JS; adding values is purely UX.)
      Files: `resources/views/storefront/checkout.blade.php`
      Verify: `php artisan test --compact` — full suite still passes (guest checkout renders with `null` fills via `?? ''`, no regression).

- [ ] 8. Append the 5 customer auth tests to `tests/Feature/StorefrontTest.php` (do NOT recreate the file, do NOT modify any existing test method). Add `use App\Models\User;` to the top `use` block (keep existing imports). Append inside the class after the last existing method. Match the file's PHPUnit style (`test_*` methods, `route()`, `assertStatus`/`assertRedirect`/`assertSee`, factories, `RefreshDatabase` via the class `use`).
      ```php
      public function test_user_can_register(): void
      {
          $response = $this->post(route('user.register.post'), [
              'name' => 'Test Customer',
              'email' => 'customer@example.com',
              'password' => 'password',
              'password_confirmation' => 'password',
          ]);

          $response->assertRedirect(route('storefront.profile'));
          $this->assertAuthenticated();
          $this->assertDatabaseHas('users', [
              'email' => 'customer@example.com',
              'is_admin' => false,
          ]);
      }

      public function test_user_can_login(): void
      {
          $user = User::factory()->create(['is_admin' => false]);

          $response = $this->post(route('user.login.post'), [
              'email' => $user->email,
              'password' => 'password',
          ]);

          $response->assertRedirect(route('home'));
          $this->assertAuthenticated();
      }

      public function test_guest_cannot_access_profile(): void
      {
          $response = $this->get(route('storefront.profile'));

          $response->assertRedirect(route('user.login'));
      }

      public function test_authenticated_user_can_view_profile(): void
      {
          $user = User::factory()->create([
              'name' => 'Jane Customer',
              'is_admin' => false,
          ]);

          $response = $this->actingAs($user)->get(route('storefront.profile'));

          $response->assertStatus(200);
          $response->assertSee('Personal Information');
          $response->assertSee('Jane Customer');
      }

      public function test_user_can_update_profile(): void
      {
          $user = User::factory()->create(['is_admin' => false]);

          $response = $this->actingAs($user)->post(route('storefront.profile.update'), [
              'name' => 'Updated Name',
              'email' => $user->email,
              'phone' => '01700000000',
              'address' => 'House 1',
              'city' => 'Dhaka',
              'district' => 'Dhaka',
              'postcode' => '1207',
          ]);

          $response->assertStatus(302);
          $response->assertSessionHas('success', 'Profile updated successfully.');
          $this->assertDatabaseHas('users', [
              'id' => $user->id,
              'name' => 'Updated Name',
              'phone' => '01700000000',
              'district' => 'Dhaka',
          ]);
      }
      ```
      Note: `test_guest_cannot_access_profile` asserts a redirect to `route('user.login')`, which depends on decision 1 (context-aware `redirectGuestsTo`). The factory default password `'password'` is used for the login test.
      Files: `tests/Feature/StorefrontTest.php`
      Verify: `php artisan test --compact tests/Feature/StorefrontTest.php` — all existing tests in that file plus the 5 new ones pass.

- [ ] 9. Format PHP and run the full suite.
      Run `vendor/bin/pint --dirty --format agent` (per project rules — fixes style on changed PHP files; do NOT run `--test`). Then run `php artisan test --compact` and confirm every test passes (existing suite + the 5 new tests). Do NOT commit or push.
      Files: (none — formatting/verification only)
      Verify: `vendor/bin/pint --dirty --format agent` exits cleanly; `php artisan test --compact` reports all green with the 5 new tests included.

---

## Constraints honored
- Admin auth untouched: `AdminLoginController`, `EnsureIsAdmin`, and all `/admin/*` routes are unchanged; admin guest redirects still go to `admin.login` via the context-aware closure.
- No existing test modified; only the 5 new methods appended.
- No new npm packages; Blade-only frontend changes; no `npm run build` needed.
- `@csrf` on every form, Blade echo `{{ }}` for output, named routes via `route()` throughout, flash keys `success`/`error`.
- No email verification, OAuth, or password reset.

## Notes for the implementer
- `Auth::user()`, `Str::limit()`, and `Hash::make()` resolve in Blade/controllers without extra imports (facades are aliased globally; confirmed by existing `Str::limit` use in `storefront/product.blade.php`).
- The `storefront.profile` route name already exists for the public storefront; the new customer `storefront.profile` name reuses the `storefront.*` namespace — verify it does not collide with any existing `storefront.profile` route via `php artisan route:list` (none exists today).
- If `redirect()->back()` in `update`/`changePassword` returns a redirect to `/` under test (no referer), asserting `assertStatus(302)` + `assertSessionHas` + `assertDatabaseHas` is sufficient — do not assert a specific redirect URL there.
