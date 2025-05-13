# Project Technical Status Update: The Scent E-commerce Platform

**Document Version:** 1.3 (Updated after Phase 7 Completion)
**Date:** May 13, 2024
**Project Version:** Target v1.0 (In Development)
**Based on Execution Plan Steps Completed:** 1.1-1.3, 2.1-2.6, 3.1-3.3, 4.1-4.3, 5.1-5.3, 6.1-6.4, 7.1-7.3

## 1. Introduction & Project Overview

Welcome to "The Scent," a modern, full-featured, and beautifully crafted e-commerce platform designed to showcase and sell premium natural aromatherapy products. This project serves not only as a functional storefront but also as a reference implementation demonstrating best practices in modern web application development using PHP and the Laravel framework. It is architected from the ground up for extensibility, maintainability, security, and a seamless, mindful user experience.

**Project Vision:** To be a digital sanctuary where users can mindfully explore and purchase high-quality aromatherapy products, marrying mindful UX with robust engineering.

**Core Goals:**
1.  **Flawless & Mindful User Experience:** Intuitive, responsive, accessible, mobile-first.
2.  **Robust & Reliable Backend:** Stable, secure, well-tested e-commerce platform.
3.  **Developer Ergonomics & Maintainability:** Clean, readable, testable codebase.
4.  **Future-Proof Architecture:** Modular, DDD-influenced design for extensibility.

**Technology Stack:**
*   **Backend Framework:** Laravel 11 (currently `v11.44.7` locked)
*   **PHP Version:** ^8.2 (Sail runtime using 8.4, application targets ^8.2)
*   **Database:** MariaDB (via `mariadb:11` Docker image)
*   **Frontend Core:** Blade, Tailwind CSS 3, Alpine.js
*   **Asset Bundling:** Vite
*   **Development Environment:** Docker via Laravel Sail
*   **Key Supporting Packages:** `brick/money` (monetary values), `spatie/laravel-data` (DTOs), Laravel Sanctum (API Auth), Laravel Socialite (Social Logins), Laravel Breeze (Auth Scaffolding), Stripe PHP SDK.

This document details the work accomplished so far, covering foundational setup, core domain modeling, public-facing frontend implementation, authentication, user account management, core e-commerce backend logic (cart, checkout, order processing), the admin panel, and the initial API layer.

## 2. Current Development Status

Development has progressed significantly through the execution plan, establishing not only the foundation but also core functional aspects of the application, including a versioned API.

**Completed Phases/Steps from Execution Plan:**

*   **Phase 1: Core Laravel Configuration & Base Structure (Steps 1.1 - 1.3) - COMPLETE**
    *   Environment, core Laravel configs, custom app config, frontend build tools, base Blade layouts, partials, core CSS/JS (including dark mode), core Service Providers, and `MonetaryAmount` Value Object/Cast are implemented.

*   **Phase 2: Domain Modeling & Core Business Logic (Steps 2.1 - 2.6) - COMPLETE**
    *   **User Management:** `User`, `Address` models, VOs, DTOs, basic `UserProfileService` structure, events.
    *   **Catalog:** `Product`, `Category`, `ProductAttribute` models, `ProductRepository`, basic `ProductQueryService`, DTOs, events.
    *   **Inventory:** `InventoryMovement` model, `InventoryService` with stock adjustment logic, exceptions, events.
    *   **Order Management:** `Order`, `OrderItem` models, `OrderNumberService`, basic `OrderService` structure, DTOs, events.
    *   **Cart, Checkout, Payment, Promotion (Structures):** `CartItem` model, `CartService` (session & DB user carts), basic `CheckoutService`, `PaymentGateway` contract, basic `StripeGateway` and `PaymentService`, `Coupon` model, basic `CouponService`.
    *   **Utility Domain:** All utility models (`QuizResult`, `NewsletterSubscriber`, `AuditLog`, `EmailLog`, `TaxRate`, `TaxRateHistory`), migrations, factories, and basic service structures.

*   **Phase 3: Frontend Implementation - Public Facing (Steps 3.1 - 3.3) - COMPLETE**
    *   **Landing Page:** `HomeController` and `home.blade.php` with partials for all sections from `sample_landing_page.html`, including JS for interactivity.
    *   **Product Listing & Detail:** `ProductController`, `products/index.blade.php` (with pagination/filtering), `products/show.blade.php`. `CategoryComposer` implemented.
    *   **Scent Quiz & Newsletter:** `QuizController`, basic `QuizService`, `quiz/show.blade.php` (Alpine.js UI). `NewsletterController`, `NewsletterService`, and `NewsletterSubscriptionRequest` created for handling newsletter sign-ups via an AJAX form partial.

*   **Phase 4: Authentication & User Account Management (Steps 4.1 - 4.3) - COMPLETE**
    *   **Auth Scaffolding:** Laravel Breeze (Blade stack) installed and views (`login`, `register`, `guest` layout) customized for "The Scent" theme.
    *   **User Account Section:** `AccountController` implemented with methods for dashboard, order history/details, profile editing. Corresponding Blade views created. `UpdateProfileRequest` for validation. Address management (CRUD) for user profiles implemented.
    *   **Social Login:** `AuthService` updated with Socialite logic. `SocialLoginController` created. Routes and view partials for social login buttons added.

*   **Phase 5: E-commerce Core - Cart, Checkout, Orders (Steps 5.1 - 5.3) - COMPLETE**
    *   **Shopping Cart:** `CartService` fully implemented for item management, totals, coupon application. `CartController` handles web requests. `cart/index.blade.php` allows interaction. `CartComposer` provides global cart count.
    *   **Checkout Process:** `CheckoutService` orchestrates checkout (address, shipping placeholder, payment prep). `PaymentService` and `StripeGateway` handle PaymentIntent creation. `CheckoutController` manages frontend requests. `checkout/index.blade.php` with Stripe Elements JS in `checkout.js` implemented for payment form. Basic Tax/Coupon integration in `CartService` totals.
    *   **Order Creation & Post-Payment:** `StripeWebhookController` handles `payment_intent.succeeded`. `ProcessSuccessfulOrderJob` created. `OrderService`'s order creation logic implemented (uses CartService, creates Order/Items, clears cart, dispatches `OrderPlacedEvent`). `UpdateInventoryOnOrderPlacedListener` and `SendOrderConfirmationEmailListener` implemented. `OrderConfirmationEmail` Mailable and Blade view created. `checkout/success.blade.php` created.

*   **Phase 6: Admin Panel Implementation (Steps 6.1 - 6.4) - COMPLETE**
    *   **Admin Base & Dashboard:** `EnsureUserIsAdmin` middleware, `admin.blade.php` layout, basic `Admin\DashboardController` and view created. Admin route group established.
    *   **Product & Category Management:** `Admin\ProductController`, `Admin\CategoryController`, associated Form Requests, Policies, and example CRUD Blade views implemented. Image upload for products handled.
    *   **Order Management:** `Admin\OrderController` for listing, viewing, and updating order statuses. `OrderPolicy` implemented. Admin Blade views for orders created.
    *   **User & Tax Rate Management:** `Admin\UserController` for listing users and editing roles/status. `Admin\TaxRateController` for CRUD of `TaxRate` models. Associated Form Requests and Policies implemented. Example admin Blade views created.

*   **Phase 7: API Development (Steps 7.1 - 7.3) - COMPLETE**
    *   **API Authentication & Base Setup:** `/api/v1` routes structured. `Api\V1\AuthController` implemented for Sanctum token-based registration, login, logout, and user retrieval. `LoginRequest` and `RegisterRequest` for API validation created. `UserResource` for formatting user data.
    *   **Product & Cart API Endpoints:** `Api\V1\ProductController` and `CategoryController` implemented for public browsing. `Api\V1\CartController` implemented for authenticated users to manage their cart (add, update, remove, apply/remove coupon). Associated API Resources (`ProductResource`, `CategoryResource`, `CartItemResource`, `CartResource`) created.
    *   **Checkout & Order API Endpoints:** `Api\V1\CheckoutController` implemented for preparing PaymentIntents and completing orders (stateless, for authenticated users). `Api\V1\OrderController` implemented for users to list and view their own orders. Associated API Resources (`OrderResource`, `OrderItemResource`) created.

**Key Achievements to Date:**
*   Local development environment (Sail) is fully operational; database migrations are successful.
*   A comprehensive domain layer is in place with models, services, DTOs, and events for all core e-commerce and utility functions.
*   The public-facing frontend provides a rich user experience for browsing, product discovery, a scent quiz, and newsletter signup.
*   Full user authentication (Breeze-based email/password, Socialite social login) and a complete user account management section are functional.
*   The core e-commerce transactional flow (cart, Stripe checkout, order creation, inventory updates, email notifications) is implemented.
*   A functional admin panel allows management of products, categories, orders, users, and tax rates, protected by authorization.
*   A versioned RESTful API (`/api/v1/`) provides endpoints for authentication, product browsing, cart management, checkout, and order retrieval, secured with Laravel Sanctum.

The application is now a feature-rich platform with both a user-facing web interface and a developer-friendly API for most core functionalities.

## 3. Detailed File Breakdown (Cumulative)

This section details significant files created or modified across all completed phases (1-7). Files detailed in the previous status update (v1.2) will be briefly mentioned for completeness, with new additions or significant changes from Phase 7 highlighted.

### 3.1. Configuration Files (`config/`)
*(No new files or major changes in Phase 7. These were set up in Phase 1.)*
*   `config/app.php`: Core settings, provider registration.
*   `config/auth.php`: User provider model path set to `App\Domain\UserManagement\Models\User::class`.
*   `config/database.php`: Standard, configured for Sail.
*   `config/filesystems.php`: Standard.
*   `config/sanctum.php`: Standard Sanctum configuration, crucial for API authentication.
*   `config/services.php`: Contains keys for Stripe (used by API and web) and Socialite.
*   `config/thescent.php`: Custom app configurations.

### 3.2. Application Core (`app/`)

#### `app/Providers/`
*(No new providers in Phase 7. Existing providers (`AuthServiceProvider`, `RouteServiceProvider`, `EventServiceProvider`, `DomainServiceProvider`, `ViewServiceProvider`) function as previously described.)*
*   `AuthServiceProvider.php`: Policies for models like `Product`, `Order`, `User`, `Category`, `Address`, `TaxRate` are registered here.
*   `RouteServiceProvider.php`: Defines API rate limiter (`RateLimiter::for('api', ...)`).

#### `app/Http/Middleware/`
*(No new middleware specific to Phase 7. `EnsureUserIsAdmin.php` created in Phase 6 is key for admin routes. `auth:sanctum` middleware is used for API routes.)*

#### `app/Domain/` - Key Models, Services, DTOs, Events, Policies
*(Domain layer components were primarily built in Phase 2 and utilized/expanded in Phases 4, 5, 6. No new *core domain* models or services were created in Phase 7; Phase 7 focused on exposing existing domain functionality via an API layer.)*

#### `app/Http/Controllers/Api/V1/` (New Directory and Files for Phase 7)

This directory houses all controllers for the v1 API. They are generally thin, validating input via Form Requests, calling Domain Services, and returning data formatted by API Resources.

*   **`AuthController.php`** (New - Phase 7.1)
    *   **Purpose:** Handles API user registration, login (token issuance), logout (token revocation), and fetching authenticated user details.
    *   **Key Methods:**
        *   `register(RegisterRequest $request)`: Creates a new user, issues a Sanctum token. Returns `UserResource` and token.
        *   `login(LoginRequest $request)`: Authenticates user, issues a Sanctum token. Returns `UserResource` and token.
        *   `logout(Request $request)`: Revokes the current user's Sanctum token.
        *   `user(Request $request)`: Returns `UserResource` for the authenticated user.
    *   **Dependencies:** `RegisterRequest`, `LoginRequest`, `UserResource`, `User` model, `Auth` facade, `Hash` facade, `UserRegisteredEvent`.
    *   **Code Snippet (login method):**
        ```php
        public function login(LoginRequest $request): JsonResponse
        {
            $credentials = $request->validated();
            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials.'], 401);
            }
            /** @var \App\Domain\UserManagement\Models\User $user */
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken; // Device name can be passed from request
            return response()->json([
                'message' => 'Logged in successfully.',
                'user' => UserResource::make($user),
                'token' => $token,
            ]);
        }
        ```

*   **`ProductController.php`** (New - Phase 7.2)
    *   **Purpose:** Provides API endpoints for browsing products.
    *   **Key Methods:**
        *   `index(Request $request)`: Returns a paginated list of active products using `ProductQueryService`. Supports filtering (e.g., by category) and sorting via request parameters. Returns `ProductResource::collection()`.
        *   `show(Product $product)`: (Route model bound by slug) Returns details for a single active product. Returns `ProductResource`.
    *   **Dependencies:** `ProductQueryService`, `Product` model, `ProductResource`.

*   **`CategoryController.php`** (New - Phase 7.2)
    *   **Purpose:** Provides API endpoints for browsing categories.
    *   **Key Methods:**
        *   `index()`: Returns a list of categories (e.g., top-level). Returns `CategoryResource::collection()`.
        *   `show(Category $category)`: (Route model bound by slug) Returns details for a single category, potentially with products or children. Returns `CategoryResource`.
    *   **Dependencies:** `Category` model, `CategoryResource`.

*   **`CartController.php`** (New - Phase 7.2)
    *   **Purpose:** Manages the authenticated user's shopping cart via API. All methods are protected by `auth:sanctum`.
    *   **Key Methods:**
        *   `index()`: Returns the current user's cart contents and totals via `CartResource`.
        *   `storeItem(Request $request)`: Adds a product to the cart. Validates `product_id`, `quantity`. Uses `CartService`. Returns `CartItemResource`.
        *   `updateItem(Request $request, int $cartItemId)`: Updates quantity of an item. Uses `CartService`. Returns `CartItemResource` or updated `CartResource`.
        *   `destroyItem(int $cartItemId)`: Removes an item. Uses `CartService`. Returns updated `CartResource`.
        *   `applyCoupon(Request $request)`: Applies a coupon to the cart. Uses `CartService`. Returns updated `CartResource`.
        *   `removeCoupon()`: Removes applied coupon. Uses `CartService`. Returns updated `CartResource`.
    *   **Dependencies:** `CartService`, `Product` model, `CartResource`, `CartItemResource`, `InvalidCouponException`.

*   **`CheckoutController.php`** (New - Phase 7.3)
    *   **Purpose:** Handles the API-driven checkout process for authenticated users.
    *   **Key Methods:**
        *   `prepare(Request $request)`: Initiates checkout. Calls `CheckoutService->initiateCheckout()` to get Stripe `client_secret` and order summary. Returns this data as JSON.
        *   `complete(StoreCheckoutApiRequest $request)`: Finalizes the order after client-side payment confirmation. Receives `payment_intent_id` and shipping details. Calls `CheckoutService->completeOrder()`. Returns `OrderResource`.
    *   **Dependencies:** `CheckoutService`, `StoreCheckoutApiRequest`, `OrderResource`, `User` model.

*   **`OrderController.php`** (New - Phase 7.3)
    *   **Purpose:** Allows authenticated users to view their order history via API.
    *   **Key Methods:**
        *   `index(Request $request)`: Returns paginated list of the authenticated user's orders. Returns `OrderResource::collection()`.
        *   `show(Request $request, Order $order)`: (Route model bound by ID) Returns details for a specific order, authorizing that the user owns it. Returns `OrderResource`.
    *   **Dependencies:** `Order` model, `OrderResource`, `User` model.

#### `app/Http/Requests/Api/V1/` (New Directory and Files for Phase 7)

*   **`LoginRequest.php`**
    *   **Purpose:** Validates API login requests.
    *   **Rules:** `email` (required, email), `password` (required, string), `device_name` (sometimes, string).
*   **`RegisterRequest.php`**
    *   **Purpose:** Validates API registration requests.
    *   **Rules:** `name` (required, string), `email` (required, email, unique), `password` (required, string, confirmed, Password defaults).
*   **`StoreCheckoutApiRequest.php`**
    *   **Purpose:** Validates data submitted to the API for completing a checkout.
    *   **Rules:** `payment_intent_id` (required), shipping address fields (required: name, address1, city, state, postal_code, country_code), `customer_notes` (nullable).

#### `app/Http/Resources/` (New/Expanded for Phase 7)

These resources transform Eloquent models into JSON responses suitable for an API, ensuring consistent structure and controlled data exposure.

*   **`UserResource.php`**: Formats `User` model data (id, name, email, role, status, timestamps).
*   **`CategoryResource.php`**: Formats `Category` model data.
*   **`ProductAttributeResource.php`**: Formats `ProductAttribute` model data.
*   **`ProductResource.php`**: Formats `Product` model data, including its price as a structured object (minor amount, decimal amount, currency, formatted string) using the `MonetaryAmount` accessor. Eager loads and includes `CategoryResource` and `ProductAttributeResource` when present.
*   **`CartItemResource.php`**: Formats `CartItem` model data, including its associated `ProductResource` and calculated item total.
*   **`CartResource.php`**: Wraps a collection of `CartItemResource` and the cart totals array (subtotal, discount, shipping, tax, grand total, applied coupon details) provided by `CartService`. Each monetary total is formatted as a structured object.
*   **`OrderItemResource.php`**: Formats `OrderItem` model data, including product snapshot details and monetary values for unit price and total.
*   **`OrderResource.php`**: Formats `Order` model data, including user details (via `UserResource`), shipping address, all monetary values (subtotal, total, etc. via `MonetaryAmount` accessors), items (via `OrderItemResource::collection`), status, and timestamps.

### 3.7. Resource Files (`resources/`)
*(No direct changes in Phase 7, as this phase focused on the API backend. Frontend files from Phase 3 remain.)*

### 3.8. Route Files (`routes/`)

*   **`routes/api.php`** (Significantly updated in Phase 7.1, 7.2, 7.3)
    *   **Purpose:** Defines all versioned API endpoints.
    *   **Structure:**
        *   All routes prefixed with `/api/v1`.
        *   Public routes for product/category browsing.
        *   Authentication routes (`/auth/register`, `/auth/login`) using `Api\V1\AuthController`.
        *   Routes protected by `auth:sanctum` middleware for:
            *   `POST /auth/logout`, `GET /user`.
            *   Cart management (`/cart/*`) via `Api\V1\CartController`.
            *   Checkout (`/checkout/*`) via `Api\V1\CheckoutController`.
            *   User orders (`/orders/*`) via `Api\V1\OrderController`.
    *   **Code Snippet (Example of cart group):**
        ```php
        Route::middleware('auth:sanctum')->prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [V1CartController::class, 'index'])->name('index');
            Route::post('/items', [V1CartController::class, 'storeItem'])->name('items.store');
            Route::put('/items/{cartItemId}', [V1CartController::class, 'updateItem'])->name('items.update');
            Route::delete('/items/{cartItemId}', [V1CartController::class, 'destroyItem'])->name('items.destroy');
            // ... coupon routes
        });
        ```

### 3.9. Other Files
*   **`composer.json`, `package.json`**: No changes in Phase 7.
*   **`.env.example`**: No direct changes for API phase, but existing variables like `SANCTUM_STATEFUL_DOMAINS` (if using SPA auth) and general app settings are relevant.

## 4. Architectural Notes & Design Patterns (API Context)

*   **API Controllers as Application Service Entry Points:** The new API controllers in `app/Http/Controllers/Api/V1/` serve as entry points for API requests. They are kept "thin" by delegating core business logic to the existing Domain Services. This maintains consistency with the web controllers and adheres to the layered architecture.
*   **Laravel Sanctum for Authentication:** API authentication is handled using Sanctum's token-based mechanism. This is suitable for SPAs, mobile apps, or third-party server-to-server communication.
*   **API Resources for Transformation:** Laravel API Resources are used extensively to transform Eloquent models and collections into standardized JSON responses. This provides a flexible "transformer" layer, allowing control over data shaping and preventing accidental exposure of sensitive model attributes. It also helps in consistently including relationships and metadata.
*   **Form Requests for API Validation:** API-specific Form Requests (e.g., `Api\V1\LoginRequest`) handle validation of incoming request data, keeping controllers focused on orchestration.
*   **Statelessness:** The API endpoints are designed to be stateless, with each request containing all necessary information (e.g., Bearer token for authentication).
*   **Content Negotiation:** While not explicitly implemented in detail, a production API should respect `Accept` headers (defaulting to `application/json`).

## 5. Onboarding Notes for New Developers (API Context)

*   **API Documentation:** (Future Step) An OpenAPI (Swagger) specification should be generated or written to document all API endpoints, request/response schemas, and authentication methods. This will be essential for API consumers.
*   **Sanctum:** Understand how Laravel Sanctum issues and validates API tokens. Refer to Sanctum documentation.
*   **API Resources:** Familiarize yourself with how `app/Http/Resources/*` files transform data. They are key to understanding the API's output.
*   **API Routing:** All API routes are defined in `routes/api.php` and are versioned under `/v1/`.
*   **Testing APIs:** Feature tests for API endpoints are crucial. Use Laravel's HTTP testing helpers to simulate API requests and assert JSON responses. Tools like Postman or Insomnia are invaluable for manual API exploration.
*   **Error Handling:** API errors should return appropriate HTTP status codes and structured JSON error messages (Laravel's default JSON error responses are a good start).

## 6. Guidance for QA Team (API Context)

*   **API Endpoints Ready for Testing:**
    *   **Authentication:**
        *   `POST /api/v1/auth/register` (successful registration, validation errors)
        *   `POST /api/v1/auth/login` (successful login with token, invalid credentials)
        *   `POST /api/v1/auth/logout` (authenticated, token revocation)
        *   `GET /api/v1/user` (authenticated, retrieve user details)
    *   **Public Catalog:**
        *   `GET /api/v1/products` (list, pagination, basic filtering/sorting if implemented)
        *   `GET /api/v1/products/{slug}` (retrieve single product)
        *   `GET /api/v1/categories` (list categories)
        *   `GET /api/v1/categories/{slug}` (retrieve single category)
    *   **Authenticated Cart Management:** (Requires Bearer token)
        *   `GET /api/v1/cart`
        *   `POST /api/v1/cart/items` (add product, check stock)
        *   `PUT /api/v1/cart/items/{cartItemId}` (update quantity, remove if 0)
        *   `DELETE /api/v1/cart/items/{cartItemId}`
        *   `POST /api/v1/cart/coupon` (apply valid/invalid coupon)
        *   `DELETE /api/v1/cart/coupon`
    *   **Authenticated Checkout & Orders:** (Requires Bearer token)
        *   `POST /api/v1/checkout/prepare` (initiate payment, get client secret)
        *   `POST /api/v1/checkout/complete` (simulate order completion with payment_intent_id and shipping data)
        *   `GET /api/v1/orders` (list user's own orders)
        *   `GET /api/v1/orders/{orderId}` (view user's own specific order, check authorization)
*   **Testing Tools:** Postman, Insomnia, or any HTTP client for making API requests.
*   **Focus Areas:**
    *   Correct HTTP status codes for success and errors.
    *   JSON response structure (as defined by API Resources).
    *   Authentication: Ensure protected routes require tokens and public routes are accessible.
    *   Validation: Test with invalid/missing data in request bodies.
    *   Data integrity: Verify actions performed via API reflect correctly in the database.

## 7. Conclusion & Recommendations for Next Steps

The implementation of the v1 API layer marks another major milestone. The application now has a robust interface for programmatic interaction, supporting key e-commerce functionalities. The frontend, admin panel, and API are all built upon a shared, well-structured domain layer.

**Recommendations for Next Steps (Following the Execution Plan):**

1.  **Phase 8: Testing, Refinement & Polish (CRITICAL):**
    *   **Comprehensive Testing:** This is now the most critical phase.
        *   Write extensive **Unit Tests** for all Domain Services, complex Model logic, Value Objects, and DTOs.
        *   Write thorough **Feature Tests** for ALL web routes (frontend, auth, admin) and ALL API endpoints. Cover success cases, validation errors, authorization failures, and edge scenarios.
        *   The goal should be high, meaningful code coverage.
    *   **E2E Tests (Cypress):** Develop E2E tests for primary user flows (registration, login, browsing, full add-to-cart & checkout cycle, viewing orders, key admin operations).
    *   **UI/UX Review & Polish:** Conduct a thorough review of both frontend and admin UIs for consistency, responsiveness, usability, and visual appeal. Address any remaining styling issues from the `sample_landing_page.html` adaptation.
    *   **Accessibility (a11y) Audit:** Perform detailed accessibility testing.
    *   **Performance Optimization:** Profile the application, optimize queries, implement caching, and ensure frontend assets are optimized.

2.  **Phase 9: Deployment Preparation:**
    *   Finalize production configurations (`.env.example`, security headers).
    *   Develop and test production `Dockerfile` and `fly.toml` (or Lightsail deployment scripts).
    *   Set up and rigorously test the CI/CD pipeline in a staging environment.

3.  **Documentation:**
    *   Generate/write OpenAPI documentation for the v1 API.
    *   Ensure the main `README.md` is up-to-date with setup and architectural details.

Once Phase 8 is thoroughly completed, the application will be in a strong position for a v1.0 production launch.
```

---

Now for the `deployment_guide_for_production.md`. This will be a new document.

```markdown
# Deployment Guide for Production: The Scent E-commerce Platform

**Document Version:** 1.0
**Date:** May 13, 2024

## 1. Introduction

This document provides a comprehensive guide for deploying "The Scent" Laravel e-commerce application to a production environment. It covers general deployment principles and provides specific steps for two recommended platforms: **Fly.io** (container-based, generally simpler for Laravel applications designed with Docker) and **AWS Lightsail** (VPS-based, more manual server setup).

**Prerequisites for any deployment:**
*   A fully tested and stable version of the application code in a Git repository (e.g., `main` branch or a release tag).
*   Access to a production-ready database (e.g., managed MariaDB/MySQL service).
*   API keys and credentials for all third-party services used in production (Stripe, mail provider, etc.).
*   A custom domain name configured for your application.
*   Familiarity with basic server administration and command-line interfaces.

## 2. General Deployment Principles

These principles are crucial for a smooth and reliable production deployment, regardless of the chosen hosting platform:

*   **Environment Parity:** Strive to keep your production environment as close as possible to your development (Laravel Sail) and any staging environments. This includes PHP version (target ^8.2 as per `composer.json`, though Sail dev uses 8.4), MariaDB version (target MariaDB 11.x), Redis version, and key system libraries. Using Docker for production (as with Fly.io) greatly helps.
*   **Source Control:** All code must be version-controlled with Git. Deployments should be made from specific, stable branches (e.g., `main`) or tags.
*   **Configuration Management via Environment Variables:**
    *   **NEVER commit your production `.env` file or any sensitive credentials to your Git repository.**
    *   Use environment variables for all configurations that differ between environments (local, staging, production). This includes database credentials, API keys, mail settings, `APP_ENV`, `APP_DEBUG`, `APP_KEY`, etc.
    *   Utilize the hosting platform's secret management tools (e.g., Fly.io Secrets, AWS Systems Manager Parameter Store, or a securely managed `.env` file on a VPS with strict permissions).
*   **Build Artifacts (Build Step):**
    *   **Composer Dependencies:** During your deployment build process, run `composer install --optimize-autoloader --no-dev --prefer-dist`.
        *   `--no-dev`: Excludes development dependencies not needed in production.
        *   `--optimize-autoloader`: Creates a more performant class autoloader.
        *   `--prefer-dist`: Downloads package archives instead of cloning from Git where possible.
    *   **Frontend Assets:** Compile and minify your frontend assets using `npm run build` (or `yarn build`). This will generate optimized files in the `public/build` directory. These built assets must be deployed with your application.
*   **Laravel Production Optimizations (Release/Post-Deploy Step):**
    After new code is deployed to the server(s), run these Artisan commands:
    *   `php artisan config:cache`: Caches all configuration files. *After this, `.env` changes require re-caching.*
    *   `php artisan route:cache`: Caches route definitions.
    *   `php artisan view:cache`: Caches compiled Blade views.
    *   `php artisan event:cache`: (For Laravel 11+ if not using full auto-discovery or specific listeners are defined) Caches event listeners.
    *   `php artisan storage:link`: Creates the symbolic link from `public/storage` to `storage/app/public`.
    *   **To clear all caches (if needed before re-caching):** `php artisan optimize:clear`
*   **Database Migrations (Release/Post-Deploy Step):**
    *   Run `php artisan migrate --force` as part of your deployment process to apply database schema changes. The `--force` flag is required for running migrations in a non-interactive production environment.
*   **File Permissions (Server Setup/Deploy Step):**
    *   Ensure the web server user (e.g., `www-data`, `daemon`) has write access to the `storage/` and `bootstrap/cache/` directories.
*   **Queue Workers (Server Setup & Ongoing):**
    *   Set up a robust queue worker process using Supervisor (on a VPS) or as a separate process type (on platforms like Fly.io).
    *   Use a persistent queue driver like Redis or a database in production: `QUEUE_CONNECTION=redis` or `QUEUE_CONNECTION=database` in your production `.env`.
    *   Ensure workers are monitored and automatically restarted if they fail.
*   **Task Scheduling (Server Setup & Ongoing):**
    *   Configure a cron job (on a VPS) or a scheduled task runner (on PaaS) to execute `php artisan schedule:run` every minute.
*   **HTTPS Enforcement (Server Setup):**
    *   Enforce HTTPS for all traffic. Obtain and configure SSL/TLS certificates (Let's Encrypt is a common free option). Platforms like Fly.io often handle this automatically.
*   **Logging (Application & Server Setup):**
    *   Configure Laravel to write logs to `stderr` (standard error output) when running in containers (e.g., `LOG_CHANNEL=stderr`). This allows the container platform (like Fly.io) to collect and manage logs.
    *   For VPS, configure Monolog to write to files with rotation or send to a centralized logging service.
    *   Set `LOG_LEVEL` to `error` or `warning` in production to reduce noise.
*   **Monitoring & Error Tracking (Post-Deploy & Ongoing):**
    *   Implement application performance monitoring (APM) and error tracking services (e.g., Sentry, Bugsnag, Flare, Datadog).
*   **Backups (Ongoing):**
    *   Implement regular, automated backups for your production database.
    *   If using file storage (e.g., for product images), ensure that is also backed up.
    *   Managed database services usually offer automated backup solutions.
*   **Zero-Downtime Deployments (Advanced Goal):**
    *   **Laravel Maintenance Mode:** Use `php artisan down --refresh=15 --secret="your_secure_bypass_secret"` before critical changes and `php artisan up` after deployment. The secret allows you to access the site during maintenance. Customize the `resources/views/errors/503.blade.php` view.
    *   **Strategies:** Platforms like Fly.io use rolling deploys by default. For VPS environments, tools like [Deployer](https://deployer.org/) can implement atomic deploys (symlink switching).

## 3. Deploying to Fly.io

Fly.io deploys applications as Docker containers. This guide assumes you have a production-ready `Dockerfile` (see section 3.3 in the previous status update for an example structure to adapt) and a `fly.toml` configuration file.

**3.1. Prerequisites for Fly.io:**
*   A Fly.io account.
*   `flyctl` (Fly CLI) installed locally and authenticated (`fly auth login`).
*   Your application code pushed to a Git repository.
*   Production `Dockerfile` in your project root.
*   `fly.toml` file in your project root (can be generated by `fly launch` and then customized).

**3.2. External Services Setup (Database, Redis):**

*   **Production Database (Managed Service - CRITICAL):**
    *   **Do not run your production database as a Fly.io app VM alongside your application VMs for critical data unless you are an expert in managing database replication, backups, and failover yourself.**
    *   Use a managed database service: PlanetScale, Aiven for MySQL/MariaDB, AWS RDS, Google Cloud SQL, etc.
    *   Create your production database (e.g., `the_scent_production`) and a dedicated user with a strong password.
    *   Ensure the database is in a region geographically close to your primary Fly.io application region.
    *   Obtain connection details: Host, Port, Database Name, Username, Password.
    *   Configure firewall rules on the managed database service to allow connections from Fly.io (either by allowing Fly.io's shared outbound IPs or, preferably, by setting up private networking between Fly.io and your DB provider if available).
*   **Production Redis (Fly Redis or Managed):**
    *   **Fly Redis:**
        ```bash
        fly redis create --org <your-org-slug> --name the-scent-redis --region <your-primary-region> --plan <plan-name>
        # Example: fly redis create --org personal --name the-scent-redis --region ams --plan Free # for small needs
        ```
        This will output a connection URL like `redis://default:<password>@your-redis-name.flycast:6379`.
    *   **External Managed Redis:** Alternatively, use a service like Upstash or Aiven for Redis.

**3.3. Configuring `fly.toml` for Production:**

Your `fly.toml` file defines your application, build process, services, and deployment strategy on Fly.io.

```toml
# fly.toml (Production Example for The Scent)
app = "the-scent-production" # Your unique Fly.io app name
primary_region = "ams"       # e.g., Amsterdam. Choose region(s) for deployment.

# Build settings
[build]
  dockerfile = "Dockerfile" # Path to your production Dockerfile
  # Optionally pass build arguments to your Dockerfile
  # [build.args]
  #   PHP_VERSION = "8.2" # Ensure this matches what your Dockerfile expects/installs
  #   NODE_VERSION = "20"

# Environment variables that are NOT secret
[env]
  APP_ENV = "production"
  APP_DEBUG = "false" # CRITICAL for production
  LOG_CHANNEL = "stderr"
  LOG_LEVEL = "error" # Or 'warning'
  DB_CONNECTION = "mysql" # Laravel uses 'mysql' driver for MariaDB
  CACHE_DRIVER = "redis"
  SESSION_DRIVER = "redis"
  QUEUE_CONNECTION = "redis" # Recommended for production queues
  # APP_URL will be set by Fly or via secrets for your custom domain
  # SESSION_SECURE_COOKIE = "true" # Should be true if using HTTPS (Fly forces HTTPS)
  # SESSION_DOMAIN will be set via secrets for your custom domain

# Web application service
[http_service]
  internal_port = 8080 # The port your app's web server (Nginx/Caddy/Apache) listens on INSIDE the container
  force_https = true
  auto_stop_machines = false # For production, usually keep machines running
  auto_start_machines = true
  min_machines_running = 1   # Minimum number of app instances
  processes = ["app"]        # Corresponds to the [processes.app] group

  # Health checks for the web process
  [[http_service.checks]]
    grace_period = "10s"
    interval = "30s"
    method = "GET"
    path = "/healthz" # Add a simple health check route in your Laravel app
    port = 8080
    protocol = "http"
    timeout = "5s"
    tls_skip_verify = false

# Virtual machine configuration for the 'app' process group
[[vm]]
  cpu_kind = "shared"
  cpus = 1
  memory_mb = 512 # Start with this and monitor; adjust as needed

# Deployment strategy
[deploy]
  release_command = "/var/www/html/entrypoint.sh release" # Points to a command within your entrypoint.sh or directly "php /var/www/html/artisan migrate --force"
  strategy = "rolling" # Or "canary" or "bluegreen"

# Define application processes
[processes]
  # The 'app' process runs your web server, started by Supervisor via entrypoint.sh
  app = "/usr/bin/supervisord -c /etc/supervisor/supervisord.conf"
  
  # Example 'worker' process for Laravel queues
  # worker = "php /var/www/html/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 --max-jobs=1000"

# Optional: Define a different VM configuration for the 'worker' process group
# [[vm]]
#   cpu_kind = "shared"
#   cpus = 1
#   memory_mb = 256 # Workers might need less memory than web servers
#   processes = ["worker"]
```
*   **Health Check Route:** Add a simple health check route to your `routes/web.php` or `routes/api.php` that returns a 200 OK status, e.g.:
    ```php
    // routes/web.php (or api.php, no auth needed)
    Route::get('/healthz', function () {
        // Optionally check DB connection: \Illuminate\Support\Facades\DB::connection()->getPdo();
        return response()->json(['status' => 'ok']);
    });
    ```
*   **Release Command in `entrypoint.sh`:**
    Your `entrypoint.sh` (copied into the Docker image) should handle the release command logic.
    ```bash
    #!/bin/sh
    set -e

    COMMAND="$1"

    if [ "$COMMAND" = "release" ]; then
        echo "Running release command: Migrating database..."
        php /var/www/html/artisan migrate --force
        # Add any other release tasks here (e.g., seeding specific prod data if needed ONCE)
        echo "Release command finished."
        exit 0
    elif [ "$COMMAND" = "web" ]; then
        echo "Running Laravel optimizations..."
        php /var/www/html/artisan config:cache
        php /var/www/html/artisan route:cache
        php /var/www/html/artisan view:cache
        php /var/www/html/artisan event:cache

        if [ ! -L /var/www/html/public/storage ]; then
            echo "Linking storage..."
            php /var/www/html/artisan storage:link
        fi
        
        echo "Starting Supervisor for web..."
        exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
    elif [ "$COMMAND" = "worker" ]; then
        echo "Running Laravel optimizations for worker..."
        php /var/www/html/artisan config:cache
        php /var/www/html/artisan route:cache
        # Note: Workers might not need view:cache or event:cache if events are not discovered that way
        
        echo "Starting Queue Worker..."
        exec php /var/www/html/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 --max-jobs=1000
    else
        exec "$@"
    fi
    ```
    And your `fly.toml` `[processes]` would change to:
    ```toml
    [processes]
      app = "/var/www/html/entrypoint.sh web"
      # worker = "/var/www/html/entrypoint.sh worker" # If you have a worker process
    ```

**3.4. Setting Production Secrets on Fly.io:**

Use `fly secrets set -a your-app-name VARIABLE_NAME="value"` for all sensitive configurations from your `.env` file. **Generate a new, strong `APP_KEY` specifically for production.**

```bash
# Example secrets (replace 'the-scent-production' with your app name)
fly secrets set -a the-scent-production APP_KEY="base64:YOUR_NEW_PRODUCTION_APP_KEY"
fly secrets set -a the-scent-production APP_URL="https://your-actual-domain.com"
fly secrets set -a the-scent-production SESSION_DOMAIN="your-actual-domain.com" # Note the leading dot if sharing subdomains

fly secrets set -a the-scent-production DB_HOST="your_managed_db_host_from_step_3.1"
fly secrets set -a the-scent-production DB_PORT="your_managed_db_port"
fly secrets set -a the-scent-production DB_DATABASE="your_production_database_name"
fly secrets set -a the-scent-production DB_USERNAME="your_production_db_user"
fly secrets set -a the-scent-production DB_PASSWORD='your_production_db_password'

fly secrets set -a the-scent-production REDIS_URL="your_fly_redis_or_managed_redis_url" # e.g., redis://default:pass@host:port

fly secrets set -a the-scent-production MAIL_MAILER="smtp_or_mailgun_or_ses"
# ... other MAIL_ settings for your chosen provider ...
fly secrets set -a the-scent-production MAIL_FROM_ADDRESS="noreply@yourdomain.com"
fly secrets set -a the-scent-production MAIL_FROM_NAME="The Scent"

fly secrets set -a the-scent-production STRIPE_KEY="pk_live_YOUR_STRIPE_PUBLISHABLE_KEY"
fly secrets set -a the-scent-production STRIPE_SECRET="sk_live_YOUR_STRIPE_SECRET_KEY"
fly secrets set -a the-scent-production STRIPE_WEBHOOK_SECRET="whsec_YOUR_PRODUCTION_STRIPE_WEBHOOK_SECRET"
# ... any other secrets (Socialite, custom config values if sensitive) ...
```

**3.5. Deploying to Fly.io:**

```bash
fly deploy -a the-scent-production --remote-only # Or --local-only to build locally
```
This command will:
1.  Read `fly.toml`.
2.  Build the Docker image using your `Dockerfile` (either remotely on Fly.io or locally).
3.  Push the image to Fly.io's registry.
4.  Run the `release_command` specified in `fly.toml` (e.g., database migrations).
5.  Deploy new instances of your application and workers (if defined) using a rolling strategy by default.

**3.6. Post-Deployment on Fly.io:**

*   **Custom Domain & TLS:**
    ```bash
    fly domains add www.yourdomain.com -a the-scent-production
    # Follow DNS instructions to update A/AAAA records.
    fly certs create www.yourdomain.com -a the-scent-production # Fly handles Let's Encrypt.
    ```
*   **Scaling:**
    ```bash
    fly scale count 2 -a the-scent-production # Scale 'app' process to 2 machines
    # fly scale count worker=1 -a the-scent-production # If you have a 'worker' process
    ```
*   **Monitoring & Logs:**
    ```bash
    fly logs -a the-scent-production
    fly status -a the-scent-production
    fly dashboard -a the-scent-production # Opens web dashboard
    ```
*   **Accessing a Console:**
    ```bash
    fly ssh console -a the-scent-production # SSH into a running instance
    # php artisan tinker
    # php artisan queue:monitor redis # (or your queue connection)
    ```

## 4. Deploying to AWS Lightsail (LAMP Blueprint)

This method involves more manual server configuration. It's assumed you have a Lightsail instance with a LAMP (PHP 8.2+) blueprint.

**4.1. Initial Server Setup (Recap from README):**
1.  Create Lightsail Instance (LAMP PHP 8.2+).
2.  Attach Static IP.
3.  Configure Firewall (HTTP:80, HTTPS:443, SSH:22 open).
4.  Connect via SSH.
5.  Update system: `sudo apt update && sudo apt upgrade -y`.
6.  Install Git, Composer, Node.js (e.g., v20 via NodeSource), NPM:
    ```bash
    sudo apt install -y git zip unzip
    # Composer
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
    # Node.js & NPM (Example for Node 20)
    curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
    sudo apt-get install -y nodejs
    ```
7.  **Database Setup:**
    *   **Stop Bitnami's default MySQL/MariaDB:** `sudo /opt/bitnami/ctlscript.sh stop mysql`
    *   **Install fresh MariaDB Server (Recommended):**
        ```bash
        sudo apt install -y mariadb-server mariadb-client
        sudo mysql_secure_installation # Secure your installation
        ```
    *   Create your production database and user:
        ```bash
        sudo mysql -u root -p # Enter root password set during secure_installation
        # Inside MariaDB prompt:
        CREATE DATABASE the_scent_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
        CREATE USER 'scent_prod_user'@'localhost' IDENTIFIED BY 'YourVeryStrongProductionDbPassword!';
        GRANT ALL PRIVILEGES ON the_scent_production.* TO 'scent_prod_user'@'localhost';
        FLUSH PRIVILEGES;
        EXIT;
        ```
        *(Replace placeholders with your actual strong credentials)*.

**4.2. Application Deployment:**

This typically involves a deployment script or a tool like Deployer.org. Here's a conceptual manual flow for a single server:

1.  **Choose Deployment Directory:** e.g., `/var/www/the-scent-app`.
    ```bash
    sudo mkdir -p /var/www/the-scent-app
    sudo chown your_ssh_user:www-data /var/www/the-scent-app # So your user can write
    ```
2.  **Clone/Pull Code:**
    ```bash
    cd /var/www/the-scent-app
    git clone your-repo-url current_release # Or use Deployer for release management
    cd current_release
    git checkout main # Or your production tag/branch
    ```
3.  **Install Dependencies:**
    ```bash
    composer install --optimize-autoloader --no-dev --prefer-dist
    npm ci
    npm run build
    ```
4.  **Configure `.env` File:**
    ```bash
    cp .env.example .env
    nano .env # Or your preferred editor
    ```
    Fill in **ALL production values**: `APP_NAME`, `APP_ENV=production`, `APP_KEY` (generate one: `php artisan key:generate`), `APP_DEBUG=false`, `APP_URL=https://yourdomain.com`.
    Database: `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=the_scent_production`, `DB_USERNAME=scent_prod_user`, `DB_PASSWORD=YourVeryStrongProductionDbPassword!`.
    Mail, Stripe (Live keys), Redis (if installed locally on VPS: `REDIS_HOST=127.0.0.1`), Session/Cache/Queue drivers (`redis` or `database`).
    **Secure it:** `sudo chmod 600 .env` and `sudo chown www-data:www-data .env`.

5.  **Set File Permissions:**
    The web server (Apache user, often `daemon` or `www-data` on Bitnami) needs write access.
    ```bash
    cd /var/www/the-scent-app/current_release # Or your app root
    sudo chown -R your_ssh_user:www-data . # Own by your user, group by web server
    sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
    sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
    # Ensure your SSH user is part of www-data group if needed, or use ACLs
    ```
6.  **Configure Apache Virtual Host:**
    *   Disable default Bitnami app/vhost if active.
    *   Create `/opt/bitnami/apache2/conf/vhosts/the-scent-production-vhost.conf`:
        ```apache
        <VirtualHost *:80>
            ServerName yourdomain.com
            ServerAlias www.yourdomain.com
            DocumentRoot "/var/www/the-scent-app/current_release/public"
            <Directory "/var/www/the-scent-app/current_release/public">
                Options Indexes FollowSymLinks
                AllowOverride All
                Require all granted
            </Directory>
            ErrorLog "/var/log/apache2/the-scent-error.log"
            CustomLog "/var/log/apache2/the-scent-access.log" common
        </VirtualHost>
        ```
    *   Include it: Add `Include "/opt/bitnami/apache2/conf/vhosts/the-scent-production-vhost.conf"` to `/opt/bitnami/apache2/conf/bitnami/bitnami-apps-vhosts.conf`.
    *   Test config: `sudo apachectl configtest`
    *   Restart Apache: `sudo /opt/bitnami/ctlscript.sh restart apache`

7.  **Run Laravel Production Commands:**
    ```bash
    cd /var/www/the-scent-app/current_release
    sudo -u www-data php artisan config:cache # Run as web server user if possible
    sudo -u www-data php artisan route:cache
    sudo -u www-data php artisan view:cache
    sudo -u www-data php artisan event:cache
    sudo -u www-data php artisan storage:link
    sudo -u www-data php artisan migrate --force
    ```

8.  **Configure HTTPS (Let's Encrypt using Bitnami's tool):**
    Point your domain DNS (A record) to your Lightsail Static IP.
    ```bash
    sudo /opt/bitnami/bncert-tool
    ```
    Follow prompts for domain names, email, redirects. It will update Apache config.

9.  **Set Up Cron Job for Scheduler:**
    ```bash
    sudo crontab -e -u www-data # Run as web server user
    ```
    Add: `* * * * * cd /var/www/the-scent-app/current_release && /opt/bitnami/php/bin/php artisan schedule:run >> /dev/null 2>&1`
    *(Adjust `/opt/bitnami/php/bin/php` if your PHP path is different).*

10. **Set Up Queue Workers with Supervisor:**
    *   Install Supervisor: `sudo apt install -y supervisor`
    *   Create `/etc/supervisor/conf.d/the-scent-worker.conf`:
        ```ini
        [program:the-scent-worker]
        process_name=%(program_name)s_%(process_num)02d
        command=/opt/bitnami/php/bin/php /var/www/the-scent-app/current_release/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
        autostart=true
        autorestart=true
        user=www-data ; Run as web server user
        numprocs=2     ; Number of worker processes
        redirect_stderr=true
        stdout_logfile=/var/www/the-scent-app/current_release/storage/logs/worker.log
        stopwaitsecs=3600
        ```
    *   Reload Supervisor:
        ```bash
        sudo supervisorctl reread
        sudo supervisorctl update
        sudo supervisorctl start the-scent-worker:*
        ```

**4.3. Using a Deployment Tool (e.g., Deployer):**
For VPS deployments, using a tool like [Deployer](https://deployer.org/) is highly recommended to automate the above steps (Git pull, composer/npm install, symlink switching for atomic deploys, running Artisan commands, etc.). This makes deployments reliable and repeatable.

## 5. Post-Deployment Checklist (Recap)

1.  **Verify Application URL** and HTTPS.
2.  **Test Key User Flows** (registration, login, browsing, cart, checkout with a test payment).
3.  **Check `.env` Values** (especially `APP_DEBUG=false`, `APP_ENV=production`, live API keys).
4.  **Monitor Logs** (Laravel, web server, queue worker).
5.  **Verify Queue Workers** are running and processing jobs.
6.  **Verify Scheduled Tasks** are running.
7.  **Confirm Backup Strategy** is active.
8.  **Set up external monitoring and error tracking.**

This guide should provide a solid foundation for deploying "The Scent". Remember to adapt paths and commands to your specific server setup. Always test deployments in a staging environment first!
