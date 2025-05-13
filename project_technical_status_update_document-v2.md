# Project Technical Status Update: The Scent E-commerce Platform

**Document Version:** 1.2 (Updated after Phases 1-6 Completion)
**Date:** May 13, 2024
**Project Version:** Target v1.0 (In Development)
**Based on Execution Plan Steps Completed:** 1.1-1.3, 2.1-2.6, 3.1-3.3, 4.1-4.3, 5.1-5.3, 6.1-6.4

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

This document details the work accomplished so far, covering foundational setup, core domain modeling, public-facing frontend implementation, authentication, user account management, core e-commerce backend logic (cart, checkout, order processing), and the admin panel.

## 2. Current Development Status

Development has progressed significantly through the execution plan, establishing not only the foundation but also core functional aspects of the application.

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
    *   **Product & Category Management:** `Admin\ProductController`, `Admin\CategoryController`, associated Form Requests (`Store/UpdateProductRequest`, `Store/UpdateCategoryRequest`), Policies (`ProductPolicy`, `CategoryPolicy`), and example CRUD Blade views (`index` and forms) implemented. Image upload for products handled.
    *   **Order Management:** `Admin\OrderController` for listing, viewing, and updating order statuses. `OrderPolicy` implemented. Admin Blade views for orders (`index`, `show` with status update form) created.
    *   **User & Tax Rate Management:** `Admin\UserController` for listing users and editing roles/status (with `UpdateUserRequest` and `UserPolicy`). `Admin\TaxRateController` for full CRUD of `TaxRate` models (with `Store/UpdateTaxRateRequest` and `TaxRatePolicy`). Example admin Blade views for these modules created.

**Key Achievements to Date:**
*   Local development environment (Sail) is fully operational, and database migrations are successful.
*   A comprehensive domain layer is in place with models, services, DTOs, and events for all core e-commerce and utility functions.
*   The public-facing frontend provides a rich user experience for browsing, product discovery, a scent quiz, and newsletter signup, closely matching the `sample_landing_page.html`.
*   Full user authentication (email/password via Breeze, social login via Socialite) and a complete user account management section (dashboard, order history, profile, address book) are functional.
*   The core e-commerce transactional flow is implemented: robust cart management, a Stripe-integrated checkout process, and automated order creation upon successful payment, including inventory updates and email notifications.
*   A functional admin panel allows administrators to manage products, categories, orders, users, and tax rates, with appropriate authorization.

The application is now a feature-rich platform, with most core functionalities for a v1.0 e-commerce site implemented.

## 3. Detailed File Breakdown (Cumulative - Focusing on Additions in Phases 4, 5, 6)

This section details significant files created or substantially modified during Phases 4, 5, and 6, building upon the foundation from Phases 1, 2, and 3. For brevity, files detailed in a previous status update (covering Phases 1-3) are not re-detailed unless significantly changed.

### 3.1. Configuration Files (`config/`)

*   **`config/auth.php`**
    *   **Purpose:** Authentication guards and user providers.
    *   **Key Change:** `providers.users.model` confirmed to be `App\Domain\UserManagement\Models\User::class`.
*   **`config/services.php`**
    *   **Purpose:** Credentials for third-party services.
    *   **Key Change:** `stripe` (key, secret, webhook secret), `google` (client_id, secret, redirect), `facebook` (client_id, secret, redirect) entries are now actively used by `StripeGateway` and `SocialLoginController`.
*   **`config/thescent.php`** (New content added for this phase)
    *   **Purpose:** Application-specific configuration.
    *   **New Settings for Admin/Orders:**
        ```php
        // config/thescent.php
        return [
            // ... (existing settings from Phase 1) ...
            'order_statuses' => [
                'pending_payment' => 'Pending Payment',
                'paid' => 'Paid',
                'processing' => 'Processing',
                'shipped' => 'Shipped',
                'delivered' => 'Delivered',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'refunded' => 'Refunded',
                'disputed' => 'Disputed',
                'payment_failed' => 'Payment Failed',
            ],
            'admin_roles' => [ // For user management in admin
                'user' => 'User',
                'staff' => 'Staff',
                'admin' => 'Admin',
            ],
            'user_statuses' => [ // For user management in admin
                'active' => 'Active',
                'inactive' => 'Inactive',
                'locked' => 'Locked',
            ],
        ];
        ```

### 3.2. Application Core (`app/`)

#### `app/Http/Middleware/`

*   **`EnsureUserIsAdmin.php`** (Generated in Phase 6.1)
    *   **Purpose:** Protects admin routes. Checks if `Auth::user()->role === 'admin'`.
    *   **Registered in `bootstrap/app.php`:**
        ```php
        // In bootstrap/app.php ->withMiddleware()
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            // ... other aliases
        ]);
        ```

#### `app/Domain/UserManagement/Services/`

*   **`AuthService.php`** (Expanded in Phase 4.3)
    *   **Purpose:** Handles Socialite authentication logic.
    *   **Key Method:** `findOrCreateUserFromSocialite(string $provider, SocialiteUser $socialiteUser): User`
        *   Checks if a user with the socialite email exists.
        *   If yes, logs them in.
        *   If no, creates a new user with details from socialite, sets email as verified, generates a random password, dispatches `UserRegisteredEvent`, and logs them in.
*   **`UserProfileService.php`** (Expanded in Phase 4.2)
    *   **Purpose:** Manages user profile and address updates.
    *   **Key Methods:**
        *   `updateUser(User $user, UserData $userData): User`: Updates user's name, email. Optionally updates password if provided in DTO.
        *   Address management methods (`storeAddress`, `updateAddress`, `destroyAddress`, `setDefaultAddress` - though these were placed directly in `AccountController` for simplicity in the generated code, they could be refactored here).

#### `app/Domain/CartManagement/Services/`

*   **`CartService.php`** (Fully Implemented in Phase 5.1)
    *   **Purpose:** Complete management of the shopping cart for both guests (session-based) and authenticated users (database-backed `cart_items` table).
    *   **Key Responsibilities & Methods:**
        *   `getCartIdentifierForStorage()`: Determines if to use `user_id` or `session_id`.
        *   `getQuery()`: Provides a base query for fetching cart items for the current context.
        *   `addItem(Product $product, int $quantity = 1)`: Adds/increments product quantity, checks stock.
        *   `updateItemQuantity(int $cartItemId, int $quantity)`: Updates quantity, removes if quantity is 0, checks stock.
        *   `removeItem(int $cartItemId)`: Removes an item.
        *   `getItems()`: Retrieves all cart items with loaded products.
        *   `getSubtotal()`: Calculates subtotal as a `MonetaryAmount`.
        *   `applyCoupon(string $couponCode)`: Validates and applies a coupon using `CouponService`, stores code in session.
        *   `getAppliedCoupon()`: Retrieves and re-validates the currently applied coupon.
        *   `removeCoupon()`: Clears coupon from session.
        *   `getDiscountAmount()`: Calculates discount as a `MonetaryAmount`.
        *   `getTotals()`: Returns an array of `MonetaryAmount` objects for subtotal, discount, shipping (placeholder), tax (placeholder), and grand total, plus the applied coupon.
        *   `getItemCount()`: Returns total number of items in the cart.
        *   `isEmpty()`: Checks if the cart is empty.
        *   `clearCart()`: Removes all items and the applied coupon.
        *   `mergeGuestCartToUser(User $user)`: Transfers guest cart items to a user's cart upon login.

#### `app/Domain/Checkout/Services/`

*   **`CheckoutService.php`** (Implemented in Phase 5.2)
    *   **Purpose:** Orchestrates the checkout process.
    *   **Key Methods:**
        *   `initiateCheckout(User $user = null, array $checkoutDataFromRequest = []): array`:
            *   Validates the cart is not empty.
            *   Calculates preliminary totals (subtotal, discount).
            *   Calls `PaymentService->createPaymentIntent()` to get a `client_secret` from Stripe.
            *   Prepares data for the checkout view (client secret, order summary).
            *   *Note: Shipping and final tax calculation would ideally happen here based on address before creating PaymentIntent.*
        *   `completeOrder(string $paymentIntentId, array $validatedCheckoutData, User $user = null, ?string $guestEmail = null): Order`:
            *   Intended to be called after successful client-side payment confirmation.
            *   Retrieves payment details via `PaymentService->getPaymentDetails()`.
            *   If payment is successful, gathers all necessary data (cart items from `CartService`, totals, shipping info from `$validatedCheckoutData`, user/guest details, applied coupon).
            *   Calls `OrderService->createOrderFromPaymentIntent()` to finalize the order.

#### `app/Domain/Payment/Gateways/`

*   **`StripeGateway.php`** (Implemented in Phase 5.2)
    *   **Purpose:** Implements `PaymentGateway` contract for Stripe.
    *   **Key Methods:**
        *   `createPaymentIntent(MonetaryAmount $amount, array $metadata, array $customerDetails)`: Creates a Stripe PaymentIntent.
        *   `getPaymentDetails(string $paymentIntentId)`: Retrieves a PaymentIntent from Stripe.
        *   `refund(string $paymentIntentId, MonetaryAmount $amount, string $reason)`: Creates a Stripe refund.
        *   `constructWebhookEvent(string $payload, string $signature, string $webhookSecret)`: Verifies and constructs a Stripe webhook event.

#### `app/Domain/Payment/Services/`

*   **`PaymentService.php`** (Implemented in Phase 5.2)
    *   **Purpose:** Facade for payment operations, delegates to the configured `PaymentGateway`.
    *   **Key Methods:** Mirrors the `PaymentGateway` interface.

#### `app/Domain/OrderManagement/Services/`

*   **`OrderService.php`** (Expanded in Phase 5.3 and 6.3)
    *   **Purpose:** Core logic for creating and managing orders.
    *   **Key Methods:**
        *   `createOrderFromPaymentIntent(...)`: (Implemented in 5.3) Complex method that:
            *   Creates `Order` and `OrderItem` records.
            *   Populates snapshots and all monetary fields.
            *   Calls `InventoryService->decreaseStockOnSale()` for each item.
            *   Calls `CartService->clearCart()`.
            *   Calls `CouponService->redeemCoupon()` if a coupon was used.
            *   Dispatches `OrderPlacedEvent`.
            *   All wrapped in a DB transaction.
        *   `updateOrderStatus(Order $order, string $newStatus, array $details = [])`: (Implemented in 6.3) Updates order status, adds tracking info if provided for 'shipped' status, dispatches `OrderStatusChangedEvent`.
*   **`OrderNumberService.php`** (Implemented in Phase 2.4)
    *   **Purpose:** Generates unique order numbers.
    *   **Key Method:** `generate()`: Creates `ORD-YYYYMMDD-XXXXXX` style numbers.

#### `app/Domain/Promotion/Services/`

*   **`CouponService.php`** (Implemented in Phase 2.5, used in Phase 5.1)
    *   **Purpose:** Validates and calculates coupon discounts.
    *   **Key Methods:** `findAndValidateCoupon()`, `calculateDiscount()`, `redeemCoupon()`.

#### `app/Domain/Utility/Services/`

*   **`NewsletterService.php`**: `subscribe()`, `unsubscribe()` methods implemented.
*   **`QuizService.php`**: `getRecommendations()` implemented with basic product filtering.
*   **`TaxService.php`**: `calculateTax()` implemented with placeholder/config-based logic.

#### `app/Http/Controllers/Auth/`

*   **`SocialLoginController.php`** (New - Phase 4.3)
    *   **Purpose:** Handles OAuth redirects and callbacks for social logins (Google, Facebook, etc.).
    *   **Key Methods:** `redirectToProvider(string $provider)`, `handleProviderCallback(string $provider)`. Uses `AuthService`.
*   **Breeze Controllers** (e.g., `AuthenticatedSessionController`, `RegisteredUserController`, etc.)
    *   **Purpose:** Handle standard email/password authentication flows.
    *   **Key Changes:** Assumed to be used as published by Breeze, with necessary checks to ensure they use the `App\Domain\UserManagement\Models\User` model (Breeze usually respects `config/auth.php`).

#### `app/Http/Controllers/Frontend/`

*   **`AccountController.php`** (New/Expanded - Phase 4.2)
    *   **Purpose:** Manages user account sections (dashboard, orders, profile, addresses).
    *   **Key Methods:** `dashboard()`, `orders()`, `showOrder(Order $order)`, `profileEdit()`, `profileUpdate(UpdateProfileRequest $request)`, `addresses()`, `createAddress()`, `storeAddress(StoreAddressRequest $request)`, `editAddress(Address $address)`, `updateAddress(UpdateAddressRequest $request, Address $address)`, `destroyAddress(Address $address)`, `setDefaultAddress(Address $address, Request $request)`.
    *   Uses `UserProfileService`, Form Requests, and Policies.
*   **`CartController.php`** (Expanded - Phase 5.1)
    *   **Purpose:** Handles web interactions with the shopping cart.
    *   **Key Methods:** `index()`, `store(Request $request, Product $product)`, `update(Request $request, int $cartItemId)` (for AJAX), `destroy(int $cartItemId)` (for AJAX), `applyCoupon(Request $request)`, `removeCoupon()`. Uses `CartService`.
*   **`CheckoutController.php`** (New - Phase 5.2)
    *   **Purpose:** Manages the frontend checkout process.
    *   **Key Methods (Planned):**
        *   `index()`: Displays the checkout page. Calls `CheckoutService->initiateCheckout()` to get Stripe `client_secret` and order summary, passes to view.
        *   `store(StoreCheckoutRequest $request)`: (Or `completePayment`) Handles form submission *after* client-side Stripe confirmation. Receives `payment_intent_id` and other form data. Calls `CheckoutService->completeOrder()`. Redirects to success page or shows errors.
        *   `successHandler(Request $request)`: (Or `handlePaymentReturn`) Handles redirects from Stripe after 3DS or other actions. Retrieves `payment_intent` status and proceeds to call `completeOrder` or show appropriate messages.
*   The other frontend controllers (`HomeController`, `ProductController`, `QuizController`, `NewsletterController`) were detailed in the previous status update covering Phase 3.

#### `app/Http/Controllers/Admin/` (New/Expanded - Phase 6)

*   **`DashboardController.php`**: `index()` fetches basic stats.
*   **`ProductController.php`**: Full CRUD using `StoreProductRequest`/`UpdateProductRequest`, `ProductPolicy`. Handles image uploads.
*   **`CategoryController.php`**: Full CRUD using `StoreCategoryRequest`/`UpdateCategoryRequest`, `CategoryPolicy`.
*   **`OrderController.php`**: `index()` (with search/filter), `show()` (detailed view), `update()` (for status changes using `OrderService`). Uses `OrderPolicy`.
*   **`UserController.php`**: `index()`, `edit()`, `update()` for managing user roles/status. Uses `UpdateUserRequest`, `UserPolicy`.
*   **`TaxRateController.php`**: Full CRUD for `TaxRate` model. Uses `StoreTaxRateRequest`/`UpdateTaxRateRequest`, `TaxRatePolicy`.

#### `app/Http/Controllers/Webhook/`

*   **`StripeWebhookController.php`** (New - Phase 5.3)
    *   **Purpose:** Handles incoming webhooks from Stripe.
    *   **Key Method:** `handleWebhook(Request $request)`: Verifies webhook signature using `PaymentService`. Based on event type (e.g., `payment_intent.succeeded`), dispatches `ProcessSuccessfulOrderJob`.
    *   **Route:** `POST /webhooks/stripe` (CSRF exempt).

#### `app/Http/Requests/` (New additions for Phases 4, 5, 6)
*   **Frontend:** `UpdateProfileRequest.php`, `StoreAddressRequest.php`, `UpdateAddressRequest.php`, `StoreCheckoutRequest.php`.
*   **Admin:** `UpdateUserRequest.php`, `StoreTaxRateRequest.php`, `UpdateTaxRateRequest.php`.
    *(`StoreProductRequest`, `UpdateProductRequest`, `StoreCategoryRequest`, `UpdateCategoryRequest` were also created/refined for Admin in Phase 6)*

#### `app/Jobs/`
*   **`ProcessSuccessfulOrderJob.php`** (New - Phase 5.3)
    *   **Purpose:** Handles order finalization after successful payment webhook. Queued job.
    *   **Key Method:** `handle(OrderService $orderService, PaymentService $paymentService, CartService $cartService)`:
        *   Retrieves necessary data (user, cart items - simplified currently, payment details).
        *   Calls `OrderService->createOrderFromPaymentIntent()`.
        *   Includes error logging and potentially checks if order already processed.

#### `app/Listeners/` (New additions for Phases 4, 5, 6)
*   **Order Listeners (for `OrderPlacedEvent`):**
    *   **`Order/UpdateInventoryOnOrderPlacedListener.php`**: Decreases stock using `InventoryService`.
    *   **`User/SendOrderConfirmationEmailListener.php`**: Queues `OrderConfirmationEmail` Mailable. (Path might be `Order/` too).
    *   **`Order/NotifyAdminOnNewOrderListener.php`**: (Placeholder - to be created) Sends notification to admin.
*   **Inventory Listeners (for `LowStockThresholdReachedEvent`):**
    *   **`Inventory/SendLowStockNotificationListener.php`**: (Placeholder - to be created) Notifies admin about low stock.
*   *(Listeners for `UserRegisteredEvent` and `OrderStatusChangedEvent` also defined structurally).*

#### `app/Mail/`
*   **`OrderConfirmationEmail.php`** (Expanded - Phase 5.3)
    *   Mailable class that takes an `Order` object and uses `emails.orders.confirmation.blade.php` (Markdown view).

#### `app/Policies/` (New additions for Phase 6)
*   **`ProductPolicy.php`**, **`CategoryPolicy.php`**, **`OrderPolicy.php`**, **`UserPolicy.php`**, **`AddressPolicy.php`**, **`TaxRatePolicy.php`**.
    *   Define authorization logic (viewAny, view, create, update, delete) for admin actions on these resources. Registered in `AuthServiceProvider`.

### 3.7. Resource Files (`resources/`)

#### `resources/js/`
*   **`checkout.js`** (New - Phase 5.2)
    *   **Purpose:** Handles Stripe.js Elements setup for the payment form on `checkout/index.blade.php`.
    *   **Key Logic:** Initializes Stripe, mounts Payment Element, handles payment confirmation (`stripe.confirmPayment`), displays error/success messages, and submits order data to backend upon successful client-side payment processing. Included as an Alpine.js data function (`checkoutFormHandler`) in the `checkout/index.blade.php` view.

#### `resources/views/auth/`
*   Files like `login.blade.php`, `register.blade.php`, `forgot-password.blade.php`, etc. (customized from Breeze output in Phase 4.1).
*   Includes `partials/social-logins.blade.php`.

#### `resources/views/frontend/account/` (New - Phase 4.2)
*   `dashboard.blade.php`: Displays welcome message, recent orders.
*   `orders.blade.php`: Lists user's orders with pagination.
*   `orders_show.blade.php`: Detailed view of a single order.
*   `profile-edit.blade.php`: Form to update user name, email, password (integrates Breeze's profile update forms if applicable, themed).
*   `addresses.blade.php`: Lists user's saved addresses, allows setting defaults, links to create/edit.
*   `address-create.blade.php` / `address-form.blade.php` (partial): Form to add a new address.
*   `address-edit.blade.php`: Form to edit an existing address.
*   `partials/sidebar.blade.php`: Common navigation sidebar for account pages.

#### `resources/views/frontend/cart/`
*   **`index.blade.php`** (New/Expanded - Phase 5.1)
    *   Displays cart items, quantities, prices.
    *   Allows updating quantities and removing items (via Alpine.js making AJAX calls to `CartController`).
    *   Shows subtotal, discount (if coupon applied), shipping (placeholder), tax (placeholder), grand total.
    *   Includes coupon application form.
    *   "Proceed to Checkout" button.

#### `resources/views/frontend/checkout/`
*   **`index.blade.php`** (New - Phase 5.2)
    *   Multi-step checkout form (simplified to single page for now).
    *   Collects shipping information.
    *   Includes Stripe Payment Element (`<div id="payment-element">`).
    *   Displays order summary.
    *   Uses Alpine.js (`checkoutFormHandler` from `checkout.js`) to manage Stripe interaction and form submission.
*   **`success.blade.php`** (New - Phase 5.3)
    *   Order confirmation page displayed after successful payment and order creation. Shows order number and thank you message.

#### `resources/views/emails/orders/`
*   **`confirmation.blade.php`** (New - Phase 5.3)
    *   Markdown email template for order confirmation. Displays order summary, items, totals, shipping address.

#### `resources/views/admin/` (New - Phase 6)
*   **`dashboard.blade.php`**: Admin dashboard UI with basic stats and links.
*   **`products/index.blade.php`**, `products/create.blade.php`, `products/edit.blade.php`: For product CRUD.
*   **`categories/index.blade.php`**, `categories/create.blade.php`, `categories/edit.blade.php`: For category CRUD.
*   **`orders/index.blade.php`**, `orders/show.blade.php`: For viewing orders and updating status.
*   **`users/index.blade.php`**, `users/edit.blade.php`: For managing users (roles, status).
*   **`tax_rates/index.blade.php`**, `tax_rates/create.blade.php`, `tax_rates/edit.blade.php`: For CRUD of tax rates.
    *   These views use the `x-layouts.admin-layout` and Tailwind CSS for a consistent admin interface. They include tables, forms, pagination, search/filter elements, and flash messages.

### 3.8. Route Files (`routes/`)

*   **`web.php`**:
    *   Expanded with routes for `AccountController` (dashboard, orders, profile, addresses) under `auth` and `verified` middleware.
    *   Expanded with routes for `CartController` (index, store, update, destroy, coupon apply/remove).
    *   Expanded with routes for `CheckoutController` (index, store/complete, success handler).
    *   Admin route group (`/admin`) defined with `auth`, `verified`, `admin` middleware, containing resource routes for all admin controllers (`DashboardController`, `ProductController`, `CategoryController`, `OrderController`, `UserController`, `TaxRateController`).
    *   Socialite routes (`/auth/{provider}/redirect`, `/auth/{provider}/callback`) added.
*   **`api.php`**: Basic structure for `/api/v1` in place, but full implementation of API endpoints (Step 7) is pending.
*   **`auth.php`**: (Generated by Breeze) Contains standard authentication routes (login, register, password reset, email verification).

## 4. Architectural Notes & Design Patterns (Reiteration & Elaboration)

*   **Domain-Driven Design (DDD) Influence:** The `app/Domain` directory is the cornerstone, with subdirectories for bounded contexts like `UserManagement`, `Catalog`, `OrderManagement`, `CartManagement`, `Payment`, `Promotion`, `Inventory`, and `Utility`. This promotes:
    *   **Modularity:** Each domain is self-contained with its models, services, DTOs, events, and exceptions.
    *   **Ubiquitous Language:** Class and method names reflect business terms.
    *   **Clear Boundaries:** Reduces coupling between different parts of the application.
*   **Service Layer:** Services (e.g., `CartService`, `OrderService`, `InventoryService`, `CouponService`, `PaymentService`) encapsulate core business logic, complex orchestrations, and interactions between multiple domain models or external services. Controllers delegate to these services.
*   **Repositories:** The `ProductRepositoryContract` and its `EloquentProductRepository` implementation in the Catalog domain showcase the repository pattern, abstracting data persistence. This pattern can be extended to other domains as complexity grows. For simpler domains, direct Eloquent usage in services might suffice initially.
*   **Value Objects (`MonetaryAmount`, `EmailAddress`, `FullName`):** Ensure data integrity, immutability, and provide domain-specific behavior for simple value types, reducing primitive obsession.
*   **Data Transfer Objects (DTOs with Spatie/Laravel-Data):** Used for structured data transfer, especially for service method inputs (from controllers/requests) and sometimes outputs. They provide type safety and clear contracts.
*   **Events & Listeners:** Extensively planned and partially implemented (e.g., `OrderPlacedEvent`, `UserRegisteredEvent`, `StockLevelChangedEvent`). This decouples actions like sending emails, updating inventory, or notifying admins from the primary operation, allowing for better maintainability and scalability (listeners can be queued).
*   **Form Requests:** Used for all significant form submissions (frontend and admin) to encapsulate validation logic and authorize requests, keeping controllers cleaner.
*   **Policies & Gates:** Used for authorization throughout the admin panel and for user-specific resources (e.g., a user viewing their own order).
*   **Blade Components & View Composers:** Frontend views are made modular and maintainable using Blade components (`ProductCard`, layouts) and view composers (`CategoryComposer`, `CartComposer`) to share common data.
*   **Alpine.js:** Provides targeted client-side interactivity for the Blade-rendered frontend without the overhead of a full SPA framework. Used for dark mode, quiz UI, cart updates (AJAX), newsletter submission, and other dynamic elements from `sample_landing_page.html`.

## 5. Onboarding Notes for New Developers

*   **Start with `README.md` and this document (`project_technical_status_update_document.md`):** They provide the project vision, architecture, and current state.
*   **Local Setup:** Use Laravel Sail. Follow `README.md` setup guide. Ensure `.env` is configured (copy from `.env.example`, run `sail artisan key:generate`).
*   **Key Directories:**
    *   `app/Domain/`: Core business logic. Understand the bounded contexts.
    *   `app/Http/Controllers/`: Separated into `Frontend`, `Admin`, `Auth`, `Api`, `Webhook`.
    *   `resources/views/`: Blade templates, especially `layouts`, `components`, `frontend`, `admin`.
    *   `routes/web.php` and `routes/api.php`: Entry points for requests.
*   **Workflow:**
    *   Run `./vendor/bin/sail up -d` to start services.
    *   Run `./vendor/bin/sail npm run dev` for frontend assets.
    *   Access the site at `http://localhost` (or `APP_URL`).
    *   Use `./vendor/bin/sail artisan ...` for all Artisan commands.
*   **Database:** Migrations in `database/migrations/` are the source of truth. GUI tools can connect to `127.0.0.1:3306` (user/pass from `.env`).
*   **Pusher Warnings:** These are normal during Sail operations if Pusher variables in `.env` are blank and `BROADCAST_DRIVER` is not `pusher`. They can generally be ignored.
*   **Code Style:** `laravel/pint` is used. Run `./vendor/bin/sail pint` before committing.

## 6. Guidance for QA Team

*   **Current Testable Areas (End-to-End Flow Examples):**
    1.  **User Registration & Login:** Via Breeze forms and Social Login (if configured).
    2.  **Landing Page & Product Browsing:** All sections of the landing page, navigation to product listing, category filtering, product detail page views.
    3.  **Scent Quiz:** Completion of the quiz and display of (currently basic) recommendations.
    4.  **Newsletter Subscription:** Form submission and success/error feedback.
    5.  **Shopping Cart Operations:** Adding products, updating quantities, removing items, viewing cart page, applying/removing coupons. (Test as guest and logged-in user).
    6.  **Checkout Process (up to payment submission):** Filling shipping details, seeing order summary, interacting with Stripe Elements payment form.
    7.  **Order Placement (with Stripe Test Cards):** Completing a payment with Stripe test cards, receiving an order confirmation email (check Mailpit at `http://localhost:8025`), verifying order in user's account section and admin panel.
    8.  **Inventory Update:** Check if product stock decreases after an order.
    9.  **User Account Management:** Viewing dashboard, order history, order details, editing profile (name, email, password), managing addresses.
    10. **Admin Panel Access:** Login as admin user.
    11. **Admin CRUD Operations:**
        *   Products: Create, view list (with search), edit, delete. Check image uploads.
        *   Categories: Create, view list, edit, delete.
        *   Orders: View list (with search/filter), view order details, update order status.
        *   Users: View list, edit user role/status.
        *   Tax Rates: Create, view list, edit, delete.
*   **Key Areas for Test Case Design:**
    *   All user flows mentioned above.
    *   Validation rules for all forms (registration, login, profile, addresses, newsletter, checkout, admin CRUD forms).
    *   Correct calculation of cart totals, including discounts, (placeholder) shipping, and (placeholder) taxes.
    *   Stock level logic: availability checks, decrement on order, backorder behavior.
    *   Authorization: Ensure only admins can access admin panel, users can only see/edit their own data.
    *   Responsiveness and UI consistency across frontend and admin panel.
    *   Dark mode functionality.

## 7. Conclusion & Recommendations for Next Steps

The project has made substantial progress, with the majority of core e-commerce functionalities and the admin panel now implemented up to Phase 6 of the execution plan. The foundational architecture is robust, and the application provides a user-friendly experience on the frontend and a functional management interface for administrators.

**Recommendations for Next Steps (Following the Execution Plan):**

1.  **Phase 7: API Development:**
    *   Fully implement all planned RESTful API endpoints in `app/Http/Controllers/Api/V1/` for products, categories, cart, checkout, orders, and authentication.
    *   Create and use API Resources (`app/Http/Resources/`) for all API responses.
    *   Implement robust API authentication and authorization using Laravel Sanctum.
    *   Consider API documentation using OpenAPI.

2.  **Phase 8: Testing, Refinement & Polish (Crucial Before Deployment):**
    *   **Write Comprehensive Tests:**
        *   **Unit Tests:** For all Domain services, complex model logic, value objects, DTOs.
        *   **Feature Tests:** For all web routes (frontend and admin) and all API endpoints. Cover happy paths, validation errors, authorization failures, and edge cases. Aim for high test coverage.
    *   **E2E Tests (Cypress):** Write tests for critical user flows (registration, login, product browsing, add-to-cart, full checkout, viewing orders, key admin tasks).
    *   **UI/UX Refinement:** Thoroughly review and polish all frontend and admin views for consistency, responsiveness, and usability based on feedback or further design iterations.
    *   **Accessibility (a11y) Audit:** Perform detailed accessibility testing using tools like axe-core, Lighthouse, and manual testing (keyboard navigation, screen readers).
    *   **Performance Optimization:**
        *   Profile the application to identify bottlenecks (Laravel Telescope, Debugbar).
        *   Optimize database queries (check for N+1, ensure proper indexing).
        *   Implement application-level caching where beneficial (e.g., for frequently accessed, rarely changing data).
        *   Ensure frontend assets are optimized via `npm run build`.

3.  **Phase 9: Deployment Preparation:**
    *   Finalize production `.env.example` and security hardening (CSP headers, etc.).
    *   Create/Refine production `Dockerfile` and `fly.toml` (for Fly.io) or deployment scripts for AWS Lightsail.
    *   Set up and test the CI/CD pipeline (GitHub Actions) for automated testing and deployment.

By systematically addressing these remaining phases, "The Scent" will evolve into a production-ready, robust, and delightful e-commerce platform.
