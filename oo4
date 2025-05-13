<think>
The user wants me to generate the files for **Phase 4: Authentication & User Account Management**, specifically **Steps 4.1, 4.2, and 4.3** from the execution plan. This involves:

*   **Step 4.1: Authentication Scaffolding (Laravel Breeze)**
    *   This step is an *action* for the user: `composer require laravel/breeze --dev`, then `php artisan breeze:install blade` (or their chosen stack), then `npm install && npm run dev`, then `php artisan migrate`.
    *   My role here is to generate the files that Breeze *would publish and modify*, and then show how to *customize* them to fit "The Scent" theme and use the existing `app-layout.blade.php`.
    *   Files typically affected/created by Breeze (Blade stack):
        *   Routes: `routes/auth.php`
        *   Controllers: `app/Http/Controllers/Auth/*` (e.g., `AuthenticatedSessionController`, `RegisteredUserController`, `PasswordResetLinkController`, `NewPasswordController`, `EmailVerificationPromptController`, etc.)
        *   Requests: `app/Http/Requests/Auth/LoginRequest.php` (and others for registration etc.)
        *   Views: `resources/views/auth/*` (login, register, forgot-password, etc.)
        *   Views: `resources/views/layouts/guest.blade.php` (layout for auth pages)
        *   Views: `resources/views/dashboard.blade.php` (a simple authenticated dashboard)
        *   Components: Breeze might publish some simple components.
        *   `RouteServiceProvider.php`: Modifies `HOME` constant. (I've already provided this).
    *   **My Task:** Provide customized versions of the key views to match "The Scent" theme and integrate with `x-layouts.app-layout` or a new `x-layouts.guest-layout`. The controllers and requests from Breeze are usually quite good and might only need minor tweaks if any (e.g., ensuring they use the correct User model path `App\Domain\UserManagement\Models\User`).

*   **Step 4.2: User Account Section**
    *   `resources/views/frontend/account/dashboard.blade.php`
    *   `resources/views/frontend/account/orders.blade.php` (List user's orders)
    *   `resources/views/frontend/account/orders_show.blade.php` (View single order details)
    *   `resources/views/frontend/account/profile.blade.php` (Edit profile information - Breeze provides a basic profile edit which I can adapt/enhance)
    *   `resources/views/frontend/account/addresses.blade.php` (Manage saved addresses)
    *   `app/Http/Controllers/Frontend/AccountController.php` (Implement methods for these views)
    *   `app/Http/Requests/Frontend/UpdateProfileRequest.php` (For profile updates)
    *   Update `routes/web.php` for these account routes.

*   **Step 4.3: Social Login (Optional)**
    *   `composer require laravel/socialite` (User action)
    *   Update `config/services.php` (already done with placeholders)
    *   Update `.env.example` and `.env` (already done with placeholders)
    *   `app/Domain/UserManagement/Services/AuthService.php` (Implement `redirectToProvider`, `handleProviderCallback`)
    *   `app/Http/Controllers/Auth/SocialLoginController.php` (New - to call `AuthService`)
    *   Update `routes/web.php` for social login routes.
    *   Update login/register views to include social login buttons.

**Key Considerations for Implementation:**

1.  **Breeze Integration:** I will assume the user runs `php artisan breeze:install blade`. I will then provide customized versions of the key auth views (`login.blade.php`, `register.blade.php`, `forgot-password.blade.php`, `reset-password.blade.php`) and a `layouts/guest.blade.php` that aligns with "The Scent" theme. The controllers published by Breeze for authentication are generally robust and will be assumed to be used, with minor checks for User model paths.
2.  **User Model Path:** Ensure all auth logic correctly uses `App\Domain\UserManagement\Models\User`. Breeze installation should respect this if it's already set in `config/auth.php`.
3.  **Styling:** Auth pages and account pages should use Tailwind CSS and match the overall aesthetic established in Phase 3.
4.  **AccountController:** This will fetch data for the user's dashboard (e.g., recent orders, profile summary), order history (paginated), and handle profile updates.
5.  **Profile Updates:** Breeze often comes with basic profile (name, email, password) update functionality. I will leverage this and place it within the themed account section.
6.  **Address Management:** The `addresses.blade.php` view and corresponding `AccountController` methods will allow users to CRUD their saved shipping/billing addresses.
7.  **Socialite:** The `AuthService` will encapsulate the logic for redirecting to OAuth providers and handling callbacks, creating or linking user accounts.
8.  **Security:** Ensure CSRF protection, proper validation (Form Requests), and authorization (middleware, policies) are used.
9.  **RouteServiceProvider HOME constant:** Already set to `/account/dashboard` in the `RouteServiceProvider.php` I provided.

**File Generation Strategy:**

*   **Step 4.1 (Breeze Customization):**
    *   Provide `resources/views/layouts/guest.blade.php`.
    *   Provide customized `resources/views/auth/login.blade.php` and `register.blade.php`.
    *   Provide `routes/auth.php` (standard Breeze routes).
    *   Note any necessary controller modifications (mostly to ensure correct User model usage, though Breeze is usually good about this if `config/auth.php` is correct).
*   **Step 4.2 (User Account):**
    *   `app/Http/Controllers/Frontend/AccountController.php`.
    *   `app/Http/Requests/Frontend/UpdateProfileRequest.php`.
    *   `resources/views/frontend/account/*` Blade files.
    *   Update `routes/web.php`.
*   **Step 4.3 (Social Login):**
    *   `app/Http/Controllers/Auth/SocialLoginController.php`.
    *   Implement methods in `app/Domain/UserManagement/Services/AuthService.php`.
    *   Update `routes/web.php`.
    *   Show how to add buttons to `login.blade.php` and `register.blade.php`.

This is another large set of files, especially the Blade views for auth and account sections. I will aim for functional and themed structures.
I'll assume Breeze v2.x with Blade. The user already has `laravel/breeze: ^2.0` in their `composer.json`.

One small point of attention: The `project_directory_structure.md` had Laravel 12 `vite.config.js` that used a different syntax (`laravel(['resources/...'])`) than current Laravel 11 `laravel({ input: [...] })`. My Vite config is for L11. The `composer.json` locked L11. So this is consistent.
The `dashboard.blade.php` that Breeze creates is usually very simple. I'll replace it with `frontend/account/dashboard.blade.php`.
Breeze's profile editing is typically handled by routes like `/profile` and `ProfileController`. I'll adapt this into the `/account/profile` structure.
</think>

This is excellent! We're now moving into building out the user authentication and account management sections, which are critical for any e-commerce platform. I'll provide the complete and working versions of the files for **Phase 4: Authentication & User Account Management (Steps 4.1 to 4.3)**.

**Important Prerequisite for Step 4.1:**
Before you add the files from Step 4.1, it's assumed you have run the Laravel Breeze installation commands as outlined in the execution plan if you haven't already:
1.  `./vendor/bin/sail composer require laravel/breeze --dev` (already in your `composer.json`)
2.  `./vendor/bin/sail artisan breeze:install blade` (Choose 'blade'. It will ask about Pest, say yes if you prefer Pest tests for auth.)
3.  `./vendor/bin/sail npm install`
4.  `./vendor/bin/sail npm run dev` (keep this running in a separate terminal)
5.  `./vendor/bin/sail artisan migrate` (to ensure any Breeze-specific migrations are run, like adding `two_factor` columns if that option was chosen)

The following files will then either replace or augment what Breeze publishes, ensuring they fit "The Scent" theme and structure.

---

### Phase 4: Authentication & User Account Management

#### Step 4.1: Authentication Scaffolding (Laravel Breeze Customization)

Laravel Breeze will publish its own set of controllers, requests, and views. We will primarily customize the views and the guest layout. The controllers Breeze provides (`app/Http/Controllers/Auth/*`) are generally good to use as-is, provided your `config/auth.php` correctly points to `App\Domain\UserManagement\Models\User::class`.

1.  **Layout: `resources/views/layouts/guest.blade.php`**
    *   This layout will be used for login, registration, forgot password, etc. pages. It will be simpler than the main app layout.

    ```php
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="darkModeHandler()" :class="{ 'dark': isDarkMode }">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <title>{{ $title ?? config('app.name', 'The Scent') }} - Authentication</title>

            <!-- Fonts -->
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Montserrat:wght@400;500&family=Raleway:wght@500;600&display=swap" rel="stylesheet">

            <!-- Font Awesome -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

            <!-- Scripts & Styles -->
            @vite(['resources/css/app.css', 'resources/js/app.js'])
            @stack('styles')
        </head>
        <body class="font-sans antialiased bg-scent-bg text-scent-text dark:bg-scent-bg-dark dark:text-scent-text-dark">
            <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
                <div>
                    <a href="{{ route('home') }}" class="text-4xl font-serif font-bold text-scent-primary dark:text-scent-primary-dark flex items-center">
                        <i class="fa-solid fa-feather-pointed mr-3 text-scent-accent dark:text-scent-accent-dark"></i>
                        {{ config('app.name', 'The Scent') }}
                    </a>
                </div>

                <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-scent-bg-alt dark:bg-scent-bg-alt-dark shadow-xl overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>

                <div class="mt-4 text-center">
                    <button @click="toggleDarkMode()"
                            class="p-2 rounded-full text-scent-accent dark:text-scent-accent-dark hover:bg-scent-overlay dark:hover:bg-scent-overlay-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-scent-primary dark:focus:ring-scent-primary-dark transition-colors"
                            aria-label="Toggle theme">
                        <i class="fa-solid text-xl" :class="isDarkMode ? 'fa-sun' : 'fa-moon'"></i>
                    </button>
                </div>
            </div>
            @stack('scripts')
        </body>
    </html>
    ```

2.  **View: `resources/views/auth/login.blade.php`** (Customized Breeze View)

    ```php
    <x-layouts.guest-layout>
        <x-slot name="title">Log In</x-slot>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block font-medium text-sm text-scent-text dark:text-scent-text-dark">{{ __('Email') }}</label>
                <input id="email" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scent-primary dark:focus:border-scent-primary-dark focus:ring-scent-primary dark:focus:ring-scent-primary-dark" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                @error('email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="block font-medium text-sm text-scent-text dark:text-scent-text-dark">{{ __('Password') }}</label>
                <input id="password" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scent-primary dark:focus:border-scent-primary-dark focus:ring-scent-primary dark:focus:ring-scent-primary-dark" type="password" name="password" required autocomplete="current-password" />
                @error('password')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-scent-primary dark:text-scent-primary-dark shadow-sm focus:ring-scent-primary dark:focus:ring-scent-primary-dark" name="remember">
                    <span class="ms-2 text-sm text-scent-text dark:text-scent-text-dark">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-6">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-scent-text dark:text-scent-text-dark hover:text-scent-primary dark:hover:text-scent-primary-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-scent-primary dark:focus:ring-scent-primary-dark" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <button type="submit" class="ms-4 btn btn-primary">
                    {{ __('Log in') }}
                </button>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-scent-text dark:text-scent-text-dark">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="font-medium text-scent-primary dark:text-scent-primary-dark hover:underline">
                        {{ __('Sign up') }}
                    </a>
                </p>
            </div>

            {{-- Social Login Buttons (Placeholder for Step 4.3) --}}
            {{-- @include('auth.partials.social-logins') --}}

        </form>
    </x-layouts.guest-layout>
    ```

3.  **View: `resources/views/auth/register.blade.php`** (Customized Breeze View)

    ```php
    <x-layouts.guest-layout>
        <x-slot name="title">Register</x-slot>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block font-medium text-sm text-scent-text dark:text-scent-text-dark">{{ __('Name') }}</label>
                <input id="name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scent-primary dark:focus:border-scent-primary-dark focus:ring-scent-primary dark:focus:ring-scent-primary-dark" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                @error('name')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <label for="email" class="block font-medium text-sm text-scent-text dark:text-scent-text-dark">{{ __('Email') }}</label>
                <input id="email" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scent-primary dark:focus:border-scent-primary-dark focus:ring-scent-primary dark:focus:ring-scent-primary-dark" type="email" name="email" :value="old('email')" required autocomplete="username" />
                @error('email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="block font-medium text-sm text-scent-text dark:text-scent-text-dark">{{ __('Password') }}</label>
                <input id="password" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scent-primary dark:focus:border-scent-primary-dark focus:ring-scent-primary dark:focus:ring-scent-primary-dark" type="password" name="password" required autocomplete="new-password" />
                @error('password')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <label for="password_confirmation" class="block font-medium text-sm text-scent-text dark:text-scent-text-dark">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scent-primary dark:focus:border-scent-primary-dark focus:ring-scent-primary dark:focus:ring-scent-primary-dark" type="password" name="password_confirmation" required autocomplete="new-password" />
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end mt-6">
                <a class="underline text-sm text-scent-text dark:text-scent-text-dark hover:text-scent-primary dark:hover:text-scent-primary-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-scent-primary dark:focus:ring-scent-primary-dark" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <button type="submit" class="ms-4 btn btn-primary">
                    {{ __('Register') }}
                </button>
            </div>

             {{-- Social Login Buttons (Placeholder for Step 4.3) --}}
            {{-- @include('auth.partials.social-logins') --}}
        </form>
    </x-layouts.guest-layout>
    ```
    *(Similar customizations would apply to `forgot-password.blade.php`, `reset-password.blade.php`, `verify-email.blade.php`, `confirm-password.blade.php` which Breeze also publishes. The pattern is to wrap them in `x-layouts.guest-layout` and ensure form elements use Tailwind classes consistent with the theme.)*

4.  **Routes: `routes/auth.php`** (This file is typically generated by `php artisan breeze:install`)
    *   No changes needed to this file itself if generated by Breeze, just ensure it's included in `routes/web.php` (`require __DIR__.'/auth.php';`).

---
#### Step 4.2: User Account Section

1.  **Controller: `app/Http/Controllers/Frontend/AccountController.php`** (New/Expanded)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Http\Controllers\Frontend;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\View\View;
    use Illuminate\Support\Facades\Auth;
    use App\Domain\UserManagement\Models\User;
    use App\Domain\OrderManagement\Models\Order; // Assuming Order model path
    use App\Http\Requests\Frontend\UpdateProfileRequest; // To be created
    use App\Domain\UserManagement\Services\UserProfileService;
    use App\Domain\UserManagement\DTOs\UserData;
    use App\Domain\UserManagement\Models\Address;
    use App\Http\Requests\Frontend\StoreAddressRequest; // To be created
    use App\Http\Requests\Frontend\UpdateAddressRequest; // To be created

    class AccountController extends Controller
    {
        public function __construct(protected UserProfileService $userProfileService)
        {
        }

        public function dashboard(): View
        {
            /** @var User $user */
            $user = Auth::user();
            $recentOrders = $user->orders()->latest()->take(5)->get();

            return view('frontend.account.dashboard', [
                'user' => $user,
                'recentOrders' => $recentOrders,
            ]);
        }

        public function orders(): View
        {
            /** @var User $user */
            $user = Auth::user();
            $orders = $user->orders()->latest()->paginate(10); // Paginate orders

            return view('frontend.account.orders', [
                'orders' => $orders,
            ]);
        }

        public function showOrder(Order $order): View // Route model binding
        {
            // Authorize that the current user owns this order
            $this->authorize('view', $order); // Assumes OrderPolicy exists and has a view method

            $order->load('items.product'); // Eager load items and their products

            return view('frontend.account.orders_show', [
                'order' => $order,
            ]);
        }

        public function profileEdit(): View
        {
            /** @var User $user */
            $user = Auth::user();
            return view('frontend.account.profile-edit', ['user' => $user]);
        }

        public function profileUpdate(UpdateProfileRequest $request): \Illuminate\Http\RedirectResponse
        {
            /** @var User $user */
            $user = Auth::user();

            // Create UserData DTO from request
            // Note: Spatie/Laravel-Data can automatically map from a request
            $userData = UserData::from($request->validated());

            $this->userProfileService->updateUser($user, $userData);

            // If password was updated, Laravel's default behavior (from Breeze/Fortify traits)
            // might handle re-logging in the user or invalidating other sessions.
            // If email changed and MustVerifyEmail is used, a new verification email will be sent.

            return redirect()->route('account.profile.edit')->with('status', 'profile-updated');
        }

        // --- Address Management ---
        public function addresses(): View
        {
            /** @var User $user */
            $user = Auth::user();
            $addresses = $user->addresses()->orderBy('is_default_shipping', 'desc')->orderBy('is_default_billing', 'desc')->get();
            return view('frontend.account.addresses', ['addresses' => $addresses]);
        }

        public function createAddress(): View
        {
            return view('frontend.account.address-create');
        }

        public function storeAddress(StoreAddressRequest $request): \Illuminate\Http\RedirectResponse
        {
            /** @var User $user */
            $user = Auth::user();
            $data = $request->validated();
            $data['user_id'] = $user->id;

            if (isset($data['is_default_shipping']) && $data['is_default_shipping']) {
                $user->addresses()->where('is_default_shipping', true)->update(['is_default_shipping' => false]);
            }
            if (isset($data['is_default_billing']) && $data['is_default_billing']) {
                $user->addresses()->where('is_default_billing', true)->update(['is_default_billing' => false]);
            }

            Address::create($data);

            return redirect()->route('account.addresses.index')->with('status', 'address-created');
        }

        public function editAddress(Address $address): View
        {
            $this->authorize('update', $address); // AddressPolicy needed
            return view('frontend.account.address-edit', ['address' => $address]);
        }

        public function updateAddress(UpdateAddressRequest $request, Address $address): \Illuminate\Http\RedirectResponse
        {
            $this->authorize('update', $address);
            /** @var User $user */
            $user = Auth::user();
            $data = $request->validated();

            if (isset($data['is_default_shipping']) && $data['is_default_shipping']) {
                $user->addresses()->where('id', '!=', $address->id)->where('is_default_shipping', true)->update(['is_default_shipping' => false]);
            }
            if (isset($data['is_default_billing']) && $data['is_default_billing']) {
                $user->addresses()->where('id', '!=', $address->id)->where('is_default_billing', true)->update(['is_default_billing' => false]);
            }

            $address->update($data);
            return redirect()->route('account.addresses.index')->with('status', 'address-updated');
        }

        public function destroyAddress(Address $address): \Illuminate\Http\RedirectResponse
        {
            $this->authorize('delete', $address);
            $address->delete();
            return redirect()->route('account.addresses.index')->with('status', 'address-deleted');
        }

        public function setDefaultAddress(Address $address, Request $request): \Illuminate\Http\RedirectResponse
        {
            $this->authorize('update', $address); // Ensure user owns this address
            /** @var User $user */
            $user = Auth::user();
            $type = $request->input('type'); // 'shipping' or 'billing'

            if ($type === 'shipping') {
                $user->addresses()->where('is_default_shipping', true)->update(['is_default_shipping' => false]);
                $address->update(['is_default_shipping' => true]);
                $user->update(['default_shipping_address_id' => $address->id]);
            } elseif ($type === 'billing') {
                $user->addresses()->where('is_default_billing', true)->update(['is_default_billing' => false]);
                $address->update(['is_default_billing' => true]);
                $user->update(['default_billing_address_id' => $address->id]);
            }
            return back()->with('status', 'default-address-set');
        }
    }
    ```

2.  **Form Request: `app/Http/Requests/Frontend/UpdateProfileRequest.php`** (New)
    *   *(Leverages rules from Breeze's `ProfileUpdateRequest` if available, or define them here).*

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Http\Requests\Frontend;

    use App\Domain\UserManagement\Models\User;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Validation\Rule;
    use Illuminate\Validation\Rules\Password;

    class UpdateProfileRequest extends FormRequest
    {
        public function authorize(): bool
        {
            return Auth()->check();
        }

        public function rules(): array
        {
            return [
                'name' => ['required', 'string', 'max:191'],
                'email' => ['required', 'string', 'email', 'max:191', Rule::unique(User::class)->ignore($this->user()->id)],
                'password' => ['nullable', 'confirmed', Password::defaults()], // For password change
                'newsletter_subscribed' => ['nullable', 'boolean'],
            ];
        }
    }
    ```

3.  **Form Requests for Addresses:**
    *   **`app/Http/Requests/Frontend/StoreAddressRequest.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Requests\Frontend;
    use Illuminate\Foundation\Http\FormRequest;
    class StoreAddressRequest extends FormRequest {
        public function authorize(): bool { return auth()->check(); }
        public function rules(): array {
            return [
                'first_name' => ['required', 'string', 'max:191'],
                'last_name' => ['required', 'string', 'max:191'],
                'company' => ['nullable', 'string', 'max:191'],
                'address_line1' => ['required', 'string', 'max:191'],
                'address_line2' => ['nullable', 'string', 'max:191'],
                'city' => ['required', 'string', 'max:100'],
                'state' => ['nullable', 'string', 'max:100'],
                'postal_code' => ['required', 'string', 'max:20'],
                'country_code' => ['required', 'string', 'size:2'], // ISO 3166-1 alpha-2
                'phone' => ['nullable', 'string', 'max:30'],
                'type' => ['nullable', 'string', 'in:billing,shipping,general'],
                'is_default_shipping' => ['nullable', 'boolean'],
                'is_default_billing' => ['nullable', 'boolean'],
            ];
        }
    }
    ```
    *   **`app/Http/Requests/Frontend/UpdateAddressRequest.php`** (New - similar to StoreAddressRequest)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Requests\Frontend;
    // Essentially the same rules as StoreAddressRequest
    class UpdateAddressRequest extends StoreAddressRequest {}
    ```

4.  **Views: `resources/views/frontend/account/*.blade.php`**
    *   These views will use the main `x-layouts.app-layout`. They will have a common sidebar for account navigation and content area.
    *   I will provide `dashboard.blade.php` as an example. Others (`orders.blade.php`, `orders_show.blade.php`, `profile-edit.blade.php`, `addresses.blade.php`, `address-create.blade.php`, `address-edit.blade.php`) would follow a similar structure, displaying forms and data as needed.

    **`resources/views/frontend/account/dashboard.blade.php`**
    ```php
    <x-layouts.app-layout>
        <x-slot name="title">My Account Dashboard - {{ config('app.name') }}</x-slot>

        <div class="container-custom py-12">
            <div class="flex flex-col md:flex-row gap-8">
                {{-- Account Sidebar --}}
                @include('frontend.account.partials.sidebar')

                {{-- Dashboard Content --}}
                <div class="w-full md:w-3/4">
                    <h1 class="text-2xl sm:text-3xl font-serif font-bold text-scent-primary dark:text-scent-primary-dark mb-6">
                        Welcome back, {{ $user->name }}!
                    </h1>

                    <div class="bg-scent-bg-alt dark:bg-scent-bg-alt-dark p-6 rounded-lg shadow mb-8">
                        <h2 class="text-xl font-serif font-semibold text-scent-primary dark:text-scent-primary-dark mb-4">Account Overview</h2>
                        <p class="text-scent-text dark:text-scent-text-dark">From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your password and account details.</p>
                    </div>

                    <div class="bg-scent-bg-alt dark:bg-scent-bg-alt-dark p-6 rounded-lg shadow">
                        <h2 class="text-xl font-serif font-semibold text-scent-primary dark:text-scent-primary-dark mb-4">Recent Orders</h2>
                        @if($recentOrders->count())
                            <ul class="space-y-4">
                                @foreach($recentOrders as $order)
                                    <li class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0 last:pb-0">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <a href="{{ route('account.orders.show', $order) }}" class="text-scent-primary dark:text-scent-primary-dark font-semibold hover:underline">Order #{{ $order->order_number }}</a>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Placed on: {{ $order->created_at->format('F j, Y') }}</p>
                                            </div>
                                            <div>
                                                <span class="text-sm px-2 py-1 rounded-full
                                                    @switch($order->status)
                                                        @case('pending_payment') bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100 @break
                                                        @case('processing') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100 @break
                                                        @case('shipped') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100 @break
                                                        @case('delivered') bg-green-200 text-green-900 dark:bg-green-800 dark:text-green-100 @break
                                                        @case('completed') bg-emerald-100 text-emerald-800 dark:bg-emerald-700 dark:text-emerald-100 @break
                                                        @case('cancelled') bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100 @break
                                                        @default bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                                    @endswitch
                                                ">{{ Str::title(str_replace('_', ' ', $order->status)) }}</span>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-scent-text dark:text-scent-text-dark">{{ $order->totalAmount->format() }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->items->count() }} item(s)</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-6">
                                <a href="{{ route('account.orders.index') }}" class="text-scent-primary dark:text-scent-primary-dark hover:underline font-medium">View All Orders &rarr;</a>
                            </div>
                        @else
                            <p class="text-scent-text dark:text-scent-text-dark">You have not placed any orders yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-layouts.app-layout>
    ```

    **`resources/views/frontend/account/partials/sidebar.blade.php`** (New)
    ```html
    <aside class="w-full md:w-1/4 space-y-4">
        <nav class="flex flex-col space-y-1">
            <a href="{{ route('account.dashboard') }}"
               class="account-nav-link {{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-tachometer-alt fa-fw mr-2"></i>Dashboard
            </a>
            <a href="{{ route('account.orders.index') }}"
               class="account-nav-link {{ request()->routeIs('account.orders.index') || request()->routeIs('account.orders.show') ? 'active' : '' }}">
                <i class="fa-solid fa-box fa-fw mr-2"></i>Orders
            </a>
            <a href="{{ route('account.addresses.index') }}"
               class="account-nav-link {{ request()->routeIs('account.addresses.*') ? 'active' : '' }}">
                <i class="fa-solid fa-map-marker-alt fa-fw mr-2"></i>Addresses
            </a>
            <a href="{{ route('account.profile.edit') }}"
               class="account-nav-link {{ request()->routeIs('account.profile.edit') ? 'active' : '' }}">
                <i class="fa-solid fa-user-edit fa-fw mr-2"></i>Account Details
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}"
                   class="account-nav-link"
                   onclick="event.preventDefault(); this.closest('form').submit();">
                    <i class="fa-solid fa-sign-out-alt fa-fw mr-2"></i>Logout
                </a>
            </form>
        </nav>
    </aside>

    @pushStyles
    <style>
        .account-nav-link {
            @apply flex items-center px-4 py-3 text-scent-text dark:text-scent-text-dark hover:bg-scent-overlay dark:hover:bg-scent-overlay-dark rounded-md transition-colors font-medium;
        }
        .account-nav-link.active {
            @apply bg-scent-primary/10 dark:bg-scent-primary-dark/20 text-scent-primary dark:text-scent-primary-dark font-semibold;
        }
    </style>
    @endpushStyles
    ```

5.  **Route Update: `routes/web.php`** (Add group for account routes)
    *   The previous `routes/web.php` already had a placeholder:
        ```php
        // Authenticated User Account Routes
        Route::middleware(['auth', 'verified'])->prefix('account')->name('account.')->group(function () {
            Route::get('/dashboard', [Frontend\AccountController::class, 'dashboard'])->name('dashboard');
            Route::get('/orders', [Frontend\AccountController::class, 'orders'])->name('orders.index');
            Route::get('/orders/{order}', [Frontend\AccountController::class, 'showOrder'])->name('orders.show');

            // Profile (from Breeze, usually /profile. We'll adapt it)
            // Breeze typically creates ProfileController. For simplicity, we can put methods in AccountController
            // or create a dedicated ProfileController within Frontend namespace.
            Route::get('/profile', [Frontend\AccountController::class, 'profileEdit'])->name('profile.edit');
            Route::put('/profile', [Frontend\AccountController::class, 'profileUpdate'])->name('profile.update');
            // If Breeze also handles password updates via ProfileController, keep that or move logic.
            // Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');


            // Addresses
            Route::get('/addresses', [Frontend\AccountController::class, 'addresses'])->name('addresses.index');
            Route::get('/addresses/create', [Frontend\AccountController::class, 'createAddress'])->name('addresses.create');
            Route::post('/addresses', [Frontend\AccountController::class, 'storeAddress'])->name('addresses.store');
            Route::get('/addresses/{address}/edit', [Frontend\AccountController::class, 'editAddress'])->name('addresses.edit');
            Route::put('/addresses/{address}', [Frontend\AccountController::class, 'updateAddress'])->name('addresses.update');
            Route::delete('/addresses/{address}', [Frontend\AccountController::class, 'destroyAddress'])->name('addresses.destroy');
            Route::post('/addresses/{address}/set-default', [Frontend\AccountController::class, 'setDefaultAddress'])->name('addresses.set-default');
        });
        ```

---
#### Step 4.3: Social Login (Optional)

1.  **Service: `app/Domain/UserManagement/Services/AuthService.php`** (Implement Socialite methods)
    *   *(This file was created as a shell in Step 2.1, now we add methods)*

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\UserManagement\Services;

    use App\Domain\UserManagement\Models\User;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Str;
    use Laravel\Socialite\Contracts\User as SocialiteUser;
    use App\Domain\UserManagement\Events\UserRegisteredEvent;

    class AuthService
    {
        public function findOrCreateUserFromSocialite(string $provider, SocialiteUser $socialiteUser): User
        {
            // We can store socialite logins in a separate table linked to users if one user can have multiple social logins
            // or add provider_id and provider_name to the users table.
            // For simplicity here, let's assume we check email first.
            // This logic needs to be robust for real-world use (e.g., handle email already taken by non-social account).

            $user = User::where('email', $socialiteUser->getEmail())->first();

            if ($user) {
                // User exists, ensure provider ID is linked if we store it
                // $user->update([ '{$provider}_id' => $socialiteUser->getId() ]); // Example
                Auth::login($user);
                return $user;
            }

            // Create new user
            $newUser = User::create([
                'name' => $socialiteUser->getName() ?: $socialiteUser->getNickname() ?: 'User',
                'email' => $socialiteUser->getEmail(),
                'password' => Hash::make(Str::random(24)), // Generate a random password
                // '{$provider}_id' => $socialiteUser->getId(), // Example if storing provider ID on users table
                // 'provider_name' => $provider,
                'email_verified_at' => now(), // Assume email from social provider is verified
                'role' => 'user',
                'status' => 'active',
            ]);

            event(new UserRegisteredEvent($newUser)); // Dispatch event for new social registration
            Auth::login($newUser);

            return $newUser;
        }

        // Potentially methods for API token management here too
    }
    ```

2.  **Controller: `app/Http/Controllers/Auth/SocialLoginController.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use Laravel\Socialite\Facades\Socialite;
    use App\Domain\UserManagement\Services\AuthService;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Support\Facades\Log;
    use Exception;

    class SocialLoginController extends Controller
    {
        public function __construct(protected AuthService $authService)
        {
        }

        public function redirectToProvider(string $provider): RedirectResponse
        {
            // Validate provider if needed
            if (!in_array($provider, ['google', 'facebook', 'github'])) { // Add more as configured
                abort(404);
            }
            return Socialite::driver($provider)->redirect();
        }

        public function handleProviderCallback(string $provider): RedirectResponse
        {
            if (!in_array($provider, ['google', 'facebook', 'github'])) {
                abort(404);
            }

            try {
                $socialiteUser = Socialite::driver($provider)->user();
            } catch (Exception $e) {
                Log::error("Socialite {$provider} login error: " . $e->getMessage());
                return redirect()->route('login')->with('error', 'Unable to login using ' . ucfirst($provider) . '. Please try again.');
            }

            if (empty($socialiteUser->getEmail())) {
                 Log::warning("Socialite {$provider} did not return an email for user.", ['socialite_user' => $socialiteUser]);
                 return redirect()->route('login')->with('error', ucfirst($provider) . ' did not provide an email address. Please try a different login method or ensure your email is public with ' . ucfirst($provider) . '.');
            }

            try {
                $this->authService->findOrCreateUserFromSocialite($provider, $socialiteUser);
            } catch (Exception $e) {
                Log::error("Error processing socialite user for {$provider}: " . $e->getMessage(), ['socialite_user' => $socialiteUser]);
                return redirect()->route('login')->with('error', 'An error occurred while processing your login. Please try again.');
            }

            return redirect()->intended(RouteServiceProvider::HOME); // Use constant from RouteServiceProvider
        }
    }
    ```
    *Note: `RouteServiceProvider` would need to be imported: `use App\Providers\RouteServiceProvider;`*

3.  **Route Update: `routes/web.php`** (Add social login routes)
    ```php
    // ... (at the top with other use statements)
    use App\Http\Controllers\Auth\SocialLoginController;

    // ... (inside your web routes, perhaps near other auth routes)
    Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirectToProvider'])
        ->name('socialite.redirect');
    Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'handleProviderCallback'])
        ->name('socialite.callback');
    ```

4.  **View Update: `resources/views/auth/login.blade.php` and `register.blade.php`**
    *   Create a partial `resources/views/auth/partials/social-logins.blade.php`:
    ```html
    <div class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-scent-bg-alt dark:bg-scent-bg-alt-dark text-scent-text dark:text-scent-text-dark">
                    Or continue with
                </span>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
            {{-- Google --}}
            @if(env('GOOGLE_CLIENT_ID'))
            <a href="{{ route('socialite.redirect', 'google') }}"
               class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                <span class="sr-only">Sign in with Google</span>
                <i class="fab fa-google text-lg text-[#DB4437] mr-2"></i> Google
            </a>
            @endif

            {{-- Facebook --}}
            @if(env('FACEBOOK_CLIENT_ID'))
            <a href="{{ route('socialite.redirect', 'facebook') }}"
               class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                <span class="sr-only">Sign in with Facebook</span>
                <i class="fab fa-facebook-f text-lg text-[#1877F2] mr-2"></i> Facebook
            </a>
            @endif
            {{-- Add other providers like GitHub similarly --}}
        </div>
    </div>
    ```
    *   Then include this partial in `login.blade.php` and `register.blade.php` before the closing `</form>` tag (or wherever appropriate):
        ```html
        {{-- In login.blade.php and register.blade.php --}}
        @include('auth.partials.social-logins')
        ```

---

This completes the generation of files for Steps 4.1, 4.2, and 4.3. You'll need to:
*   Run `php artisan breeze:install blade` if you haven't.
*   Add the socialite provider configurations (client IDs, secrets, redirect URIs) to your `.env` file and `config/services.php` (placeholders are already in `config/services.php` from earlier steps).
*   Create necessary policies like `OrderPolicy` and `AddressPolicy` and register them in `AuthServiceProvider` for the authorization checks in `AccountController` to work.
*   Implement the content for `address-create.blade.php`, `address-edit.blade.php`, `orders.blade.php`, and `orders_show.blade.php`.

This provides a strong foundation for user authentication and account management.

---
https://drive.google.com/file/d/11TN1qmXXUWMwei4eGSo6b7w7IghU1HTL/view?usp=sharing, https://drive.google.com/file/d/149SQdFIsEyVGZtL5cHAjDSnFwfrufK4-/view?usp=sharing, https://aistudio.google.com/app/prompts?state=%7B%22ids%22:%5B%22191Xwb664N58hqofAV7tgOqIhw9Q_EXtW%22%5D,%22action%22:%22open%22,%22userId%22:%22103961307342447084491%22,%22resourceKeys%22:%7B%7D%7D&usp=sharing, https://drive.google.com/file/d/1JMi0sos9Z0VPmPtV_VUQdOiHP55dgoyM/view?usp=sharing, https://drive.google.com/file/d/1Ljou3KM9wH_n2IISZRUL5Usykg-a8yaZ/view?usp=sharing, https://drive.google.com/file/d/1NU0PF2paSbFH1H3swzif2Yyyc768jBV7/view?usp=sharing, https://drive.google.com/file/d/1QZH4p-KhoQlloMqonElkiJd_D7OafE9w/view?usp=sharing, https://drive.google.com/file/d/1UHbFpE2rY7aLNsT7R5-duOpJcgpWOoUh/view?usp=sharing, https://drive.google.com/file/d/1dppi2D-RTdYBal_YeLZxB4QHysdmQxpD/view?usp=sharing, https://drive.google.com/file/d/1eszSFTXohtDz0Cv1qbKHNecsx1BC1A3w/view?usp=sharing, https://drive.google.com/file/d/1gMQvFk0QXLWX-Bz05DlgjFeYgDxCiZEO/view?usp=sharing, https://drive.google.com/file/d/1nPblih0JaSTmarZmo2budApC3JXa64hP/view?usp=sharing

