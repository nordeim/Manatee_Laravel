# Project Technical Status Update: The Scent E-commerce Platform

**Document Version:** 1.0
**Date:** May 13, 2024
**Project Version:** Target v1.0 (In Development)
**Based on Execution Plan Steps Completed:** 1.1-1.3, 2.1-2.6, 3.1-3.3

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
*   **PHP Version:** ^8.2
*   **Database:** MariaDB (via `mariadb:11` Docker image)
*   **Frontend Core:** Blade, Tailwind CSS 3, Alpine.js
*   **Asset Bundling:** Vite
*   **Development Environment:** Docker via Laravel Sail
*   **Key Supporting Packages:** `brick/money` (monetary values), `spatie/laravel-data` (DTOs), Laravel Sanctum (API Auth), Laravel Socialite (Social Logins - planned), Laravel Breeze (Auth Scaffolding - planned), Stripe PHP SDK.

This document details the work accomplished so far, focusing on the foundational setup, core domain modeling, and initial public-facing frontend implementation.

## 2. Current Development Status

Development has progressed through the initial phases of the execution plan, establishing a solid foundation for the application.

**Completed Phases/Steps from Execution Plan:**

*   **Phase 1: Core Laravel Configuration & Base Structure (Steps 1.1 - 1.3)**
    *   **1.1 Environment & Core Configuration:** `.env.example` updated, core Laravel `config/*.php` files reviewed and tailored, custom `config/thescent.php` created, frontend build tools (Tailwind, Vite, PostCSS) configured. Key Composer and NPM packages added.
    *   **1.2 Base Layouts & Frontend Assets:** Main frontend Blade layout (`app-layout.blade.php`) and essential partials (navigation, footer) created, adapting structure from `sample_landing_page.html`. Core CSS (`app.css` with Tailwind and custom variables) and JS (`app.js` with Alpine.js and custom module structure) implemented. Dark mode toggle is functional.
    *   **1.3 Core Service Providers & Custom Casts:** `DomainServiceProvider` and `ViewServiceProvider` created and registered. `MonetaryAmount` Value Object and `MonetaryAmountCast` for Eloquent models implemented.

*   **Phase 2: Domain Modeling & Core Business Logic (Steps 2.1 - 2.6)**
    *   **2.1 User Management Domain:** `User` and `Address` models created with relationships. Migrations and factories for these are in place. `EmailAddress` and `FullName` Value Objects created. Basic `UserData` DTO and `UserProfileService` structure defined. `UserRegisteredEvent` and `PasswordResetRequestedEvent` created. `config/auth.php` updated.
    *   **2.2 Catalog Domain:** `Product`, `Category`, `ProductAttribute` models created with relationships. Migrations and factories are functional. `ProductRepositoryContract` and `EloquentProductRepository` implemented and bound. Basic `ProductQueryService` structure, `ProductData` DTO, and `ProductViewedEvent` created.
    *   **2.3 Inventory Domain:** `InventoryMovement` model, migration, and factory created. `InventoryService` implemented with core stock adjustment logic (decrease, increase, check availability) and `InventoryMovement` logging. `InsufficientStockException`, `StockLevelChangedEvent`, and `LowStockThresholdReachedEvent` created.
    *   **2.4 Order Management Domain:** `Order` and `OrderItem` models implemented with relationships and `MonetaryAmount` usage. `OrderNumberService` implemented. Migrations and factories are functional. Basic `OrderService` structure, `OrderData` DTO, `OrderPlacedEvent`, and `OrderStatusChangedEvent` created.
    *   **2.5 Cart, Checkout, Payment, Promotion Domains (Contracts & Basic Structures):**
        *   **Cart:** `CartItem` model and migration created. `CartService` implemented with session-based guest cart and DB-backed user cart logic (add, update, remove, get items, subtotal, merge).
        *   **Checkout:** Basic `CheckoutService` structure defined.
        *   **Payment:** `PaymentGateway` contract, basic `StripeGateway` structure (with `createPaymentIntent`, `getPaymentDetails`, `refund`, `constructWebhookEvent`), `PaymentService` structure, `PaymentResultData`, `PaymentIntentResultData` DTOs, and payment events created.
        *   **Promotion:** `Coupon` model and migration created based on ERD. Basic `CouponService` (with validation and discount calculation logic) and `InvalidCouponException` created.
    *   **2.6 Utility Domain (Models & Migrations):** `QuizResult`, `NewsletterSubscriber`, `AuditLog`, `EmailLog`, `TaxRate`, `TaxRateHistory` models, their corresponding migrations, and factories created. Basic service structures (`NewsletterService`, `QuizService`, `AuditLoggerService`, `TaxService`) defined.

*   **Phase 3: Frontend Implementation - Public Facing (Steps 3.1 - 3.3)**
    *   **3.1 Landing Page (Home):** `HomeController` created to serve `frontend.home.blade.php`. This view now includes partials for Hero, About, Featured Products, Quiz Finder teaser, Testimonials, and Newsletter sections, adapting structure and styling from `sample_landing_page.html`. `ProductCard` Blade component created. Basic JS for parallax and ambient audio integrated.
    *   **3.2 Product Listing & Detail Pages:** `ProductController` created with `index` (paginated, basic filtering) and `show` methods. `frontend.products.index.blade.php` and `frontend.products.show.blade.php` views created. `CategoryComposer` implemented and registered to provide categories to views.
    *   **3.3 Scent Quiz & Newsletter Subscription:** `QuizController` and basic `QuizService` for handling quiz logic and placeholder recommendations. `frontend.quiz.show.blade.php` with Alpine.js for multi-step UI created. `resources/js/scent-quiz.js` contains client-side quiz logic. `NewsletterController`, `NewsletterService`, and `NewsletterSubscriptionRequest` created for handling newsletter sign-ups via an AJAX form partial.

**Key Achievements:**
*   Local development environment (Sail) is operational.
*   Core database schema is migrated.
*   Domain models for key e-commerce entities are in place.
*   Foundational services and repositories are structured.
*   The public-facing landing page, product listing/detail pages, and initial quiz/newsletter functionality are implemented at a structural and basic interactive level.

## 3. Detailed File Breakdown

This section provides details on the key files created or significantly modified so far.

### 3.1. Configuration Files (`config/`)

*   **`config/app.php`**
    *   **Purpose:** Core application settings.
    *   **Key Changes:**
        *   `'name' => env('APP_NAME', 'The Scent')`
        *   `providers` array updated to include `App\Providers\DomainServiceProvider::class` and `App\Providers\ViewServiceProvider::class`. Explicit registrations for `FortifyServiceProvider` and `JetstreamServiceProvider` were removed as Breeze (the chosen auth starter) handles its needs. `RouteServiceProvider` is kept.
    *   **Code Snippet (providers array):**
        ```php
        'providers' => ServiceProvider::defaultProviders()->merge([
            // ... standard providers ...
            App\Providers\AppServiceProvider::class,
            App\Providers\AuthServiceProvider::class,
            App\Providers\EventServiceProvider::class,
            App\Providers\RouteServiceProvider::class, // Standard, useful for HOME const & rate limiters
            App\Providers\DomainServiceProvider::class, // Custom for domain bindings
            App\Providers\ViewServiceProvider::class,   // Custom for view composers
        ])->toArray(),
        ```

*   **`config/auth.php`**
    *   **Purpose:** Authentication guards and user providers.
    *   **Key Changes:** `providers.users.model` updated to `App\Domain\UserManagement\Models\User::class`.
    *   **Code Snippet:**
        ```php
        'providers' => [
            'users' => [
                'driver' => 'eloquent',
                'model' => App\Domain\UserManagement\Models\User::class,
            ],
        ],
        ```

*   **`config/database.php`**
    *   **Purpose:** Database connection settings.
    *   **Key Changes:** Ensured `'mysql'` connection is configured for Sail's `mariadb` service using environment variables (`DB_HOST=mariadb`, etc.).

*   **`config/filesystems.php`**
    *   **Purpose:** Defines file storage disks.
    *   **Key Changes:** Standard setup; `public` disk URL uses `APP_URL`. Ready for `s3` configuration if needed.

*   **`config/services.php`**
    *   **Purpose:** Stores API keys and settings for third-party services.
    *   **Key Changes:** Placeholders added for `stripe`, `mailgun` (or other mailers), and socialite providers (`google`, `facebook`).
    *   **Code Snippet (Stripe example):**
        ```php
        'stripe' => [
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
            'webhook' => [
                'secret' => env('STRIPE_WEBHOOK_SECRET'),
                'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
            ],
        ],
        ```

*   **`config/thescent.php`** (New)
    *   **Purpose:** Custom application-specific configurations.
    *   **Key Settings:** `products_per_page`, `default_currency`, notification emails, feature flags, SEO defaults, contact info, social links.
    *   **Code Snippet (example):**
        ```php
        return [
            'products_per_page' => env('THESCENT_PRODUCTS_PER_PAGE', 12),
            'default_currency' => env('THESCENT_DEFAULT_CURRENCY', 'USD'),
            'notifications' => [
                'admin_email' => env('THESCENT_ADMIN_EMAIL', 'admin@example.com'),
            ],
            // ... more settings
        ];
        ```

*   **`config/sanctum.php`**
    *   **Purpose:** Configuration for Laravel Sanctum (API token authentication, SPA CSRF protection).
    *   **Key Changes:** Standard file provided. `stateful` domains configured to include `localhost` and common development ports. `guard` defaults to `['web']`.

### 3.2. Application Core (`app/`)

#### 3.2.1. Service Providers (`app/Providers/`)

*   **`AppServiceProvider.php`**
    *   **Purpose:** General application bootstrapping.
    *   **Key Methods:** `register()`, `boot()`. Currently mostly standard.

*   **`AuthServiceProvider.php`**
    *   **Purpose:** Registers authentication/authorization services, model policies, and gates.
    *   **Key Methods:** `boot()`. The `$policies` array is prepared for model policies.
    *   **Code Snippet (policies structure):**
        ```php
        protected $policies = [
            // Example: \App\Domain\Catalog\Models\Product::class => \App\Policies\ProductPolicy::class,
        ];
        ```

*   **`EventServiceProvider.php`**
    *   **Purpose:** Maps events to their listeners.
    *   **Key Property:** `$listen` array.
    *   **Code Snippet (example mapping):**
        ```php
        protected $listen = [
            \App\Domain\UserManagement\Events\UserRegisteredEvent::class => [
                \App\Listeners\User\SendWelcomeEmailListener::class, // To be created
            ],
            \App\Domain\OrderManagement\Events\OrderPlacedEvent::class => [
                \App\Listeners\Order\UpdateInventoryOnOrderPlacedListener::class, // To be created
            ],
        ];
        ```

*   **`RouteServiceProvider.php`**
    *   **Purpose:** Configures route model bindings, pattern filters, rate limiting, and defines the `HOME` constant for post-authentication redirection.
    *   **Key Methods:** `boot()`, `configureRateLimiting()`.
    *   **Code Snippet (rate limiter example):**
        ```php
        protected function configureRateLimiting(): void
        {
            RateLimiter::for('api', function (Request $request) {
                return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
            });
        }
        ```

*   **`DomainServiceProvider.php`** (New)
    *   **Purpose:** Dedicated provider for binding interfaces from the Domain layer to their concrete implementations in the Infrastructure layer (primarily repositories).
    *   **Key Methods:** `register()`.
    *   **Code Snippet (example binding):**
        ```php
        public function register(): void
        {
            $this->app->bind(
                \App\Domain\Catalog\Contracts\ProductRepositoryContract::class,
                \App\Infrastructure\Persistence\Eloquent\Repositories\EloquentProductRepository::class
            );
        }
        ```

*   **`ViewServiceProvider.php`** (New)
    *   **Purpose:** Registers view composers to share data with specific Blade views.
    *   **Key Methods:** `boot()`.
    *   **Code Snippet (example composer registration):**
        ```php
        public function boot(): void
        {
            View::composer(
                ['frontend.products.index', 'layouts.partials.frontend.navigation'], // Views to receive data
                \App\View\Composers\CategoryComposer::class
            );
        }
        ```

#### 3.2.2. Custom Casts (`app/Casts/`)

*   **`MonetaryAmountCast.php`**
    *   **Purpose:** Custom Eloquent cast to convert database integer/currency columns into a `MonetaryAmount` Value Object and vice-versa.
    *   **Key Methods:** `get()`, `set()`.
    *   **Usage in Models:**
        ```php
        // In a Model with `price_minor_amount` and `currency_code` columns
        // This creates a virtual 'price' attribute
        protected function price(): \Illuminate\Database\Eloquent\Casts\Attribute
        {
            return \Illuminate\Database\Eloquent\Casts\Attribute::make(
                get: fn ($value, $attributes) => new \App\Domain\Shared\ValueObjects\MonetaryAmount(
                    (int) ($attributes['price_minor_amount'] ?? 0),
                    (string) ($attributes['currency_code'] ?? config('thescent.default_currency', 'USD'))
                ),
                set: fn (\App\Domain\Shared\ValueObjects\MonetaryAmount $value) => [
                    'price_minor_amount' => $value->getMinorAmount(),
                    'currency_code' => $value->getCurrencyCode(),
                ]
            );
        }
        ```

#### 3.2.3. Domain Layer (`app/Domain/`)

This is the core of the application's business logic, organized by Bounded Contexts.

##### `app/Domain/Shared/ValueObjects/`

*   **`MonetaryAmount.php`**
    *   **Purpose:** Immutable representation of a monetary value with amount (in minor units) and currency code. Uses `brick/money` internally.
    *   **Key Methods:** `__construct(int $minorAmount, string $currencyCode)`, `fromDecimal(...)`, `getMinorAmount()`, `getCurrencyCode()`, `getDecimalAmount()`, `add()`, `isEqualTo()`, `format()`.
*   **`EmailAddress.php`**
    *   **Purpose:** Validated, immutable email address object.
    *   **Key Methods:** `__construct(string $email)`, `getValue()`, `getDomainPart()`.
*   **`FullName.php`**
    *   **Purpose:** Represents a person's full name.
    *   **Key Methods:** `__construct(string $firstName, ?string $middleName, string $lastName)`, `getFullName()`.

##### `app/Domain/UserManagement/`

*   **Models:**
    *   **`User.php`**: Eloquent model for users. Extends `Authenticatable`. Includes relationships to Orders, Addresses, QuizResults, AuditLogs, EmailLogs, TaxRates. Defines fillable attributes, hidden attributes, and casts (e.g., `password` to `hashed`, `newsletter_subscribed` to `boolean`).
    *   **`Address.php`**: Eloquent model for postal addresses. Belongs to `User`. Contains address fields, casts, and a formatted address accessor.
*   **DTOs:**
    *   **`UserData.php`**: (Spatie/Laravel-Data) DTO for creating/updating user data.
*   **Services:**
    *   **`UserProfileService.php`**: Basic structure for `updateUser()` and `createUser()` methods.
*   **Events:**
    *   **`UserRegisteredEvent.php`**: Dispatched on new user registration. Contains the `User` object.
    *   **`PasswordResetRequestedEvent.php`**: Dispatched on password reset request. Contains `User` and reset token.

##### `app/Domain/Catalog/`

*   **Models:**
    *   **`Category.php`**: Eloquent model for product categories. Self-referential parent/child relationships.
    *   **`Product.php`**: Eloquent model for products. Relationships to Category, ProductAttributes, InventoryMovements, OrderItems. Uses the `MonetaryAmount` system for its `price` via an accessor/mutator pair for `price_minor_amount` and `currency_code`. Contains image URL accessors.
    *   **`ProductAttribute.php`**: Eloquent model for specific product attributes (scent_type, mood_effect, intensity_level). Belongs to `Product`.
*   **Contracts:**
    *   **`ProductRepositoryContract.php`**: Interface defining methods for product data access (e.g., `findById`, `findBySlug`, `getFeatured`, `getAllPaginated`).
*   **DTOs:**
    *   **`ProductData.php`**: (Spatie/Laravel-Data) DTO for product creation/update, including nested `ProductAttributeData`.
    *   **`ProductAttributeData.php`**: (Spatie/Laravel-Data) DTO for product attributes.
*   **Services:**
    *   **`ProductQueryService.php`**: Uses `ProductRepositoryContract` to fetch products (paginated, featured, by slug). Basic filtering by category slug implemented.
*   **Events:**
    *   **`ProductViewedEvent.php`**: For tracking product views.

##### `app/Domain/Inventory/`

*   **Models:**
    *   **`InventoryMovement.php`**: Eloquent model logging stock changes. Belongs to `Product`. `UPDATED_AT` is disabled.
*   **Services:**
    *   **`InventoryService.php`**: Implemented methods `adjustStock`, `decreaseStockOnSale`, `increaseStockOnReturn`, `restockProduct`, `getStockLevel`, `isStockAvailable`. Uses database transactions and pessimistic locking for stock adjustments. Dispatches `StockLevelChangedEvent` and `LowStockThresholdReachedEvent`.
*   **Exceptions:**
    *   **`InsufficientStockException.php`**: Custom exception for stock issues.
*   **Events:**
    *   **`StockLevelChangedEvent.php`**: Dispatched when stock level changes.
    *   **`LowStockThresholdReachedEvent.php`**: Dispatched when stock drops to or below threshold.

##### `app/Domain/OrderManagement/`

*   **Models:**
    *   **`Order.php`**: Eloquent model for orders. Relationships to User, OrderItems, Coupon. Uses `MonetaryAmount` system for price fields (subtotal, discount, shipping, tax, total) via accessors. Address details are currently direct fields as per sample schema.
    *   **`OrderItem.php`**: Eloquent model for items in an order. Relationships to Order, Product. Includes `product_snapshot` (JSON) and uses `MonetaryAmount` for price fields.
*   **Services:**
    *   **`OrderNumberService.php`**: Implemented `generate()` method for unique, formatted order numbers (e.g., `ORD-YYYYMMDD-XXXXXX`).
    *   **`OrderService.php`**: Basic structure for `createOrderFromCart()` (complex logic placeholder) and `updateOrderStatus()`.
*   **DTOs:**
    *   **`OrderData.php`**: (Spatie/Laravel-Data) DTO for order data, includes a collection of `OrderItemData`.
    *   **`OrderItemData.php`**: (Spatie/Laravel-Data) DTO for order item details.
*   **Events:**
    *   **`OrderPlacedEvent.php`**: Dispatched when an order is successfully placed.
    *   **`OrderStatusChangedEvent.php`**: Dispatched when an order's status changes.

##### `app/Domain/CartManagement/`

*   **Models:**
    *   **`CartItem.php`**: Eloquent model for items in the cart. Can belong to a `User` or be identified by `session_id` for guests. Relates to `Product`.
*   **Services:**
    *   **`CartService.php`**: Implemented to manage cart operations: `addItem`, `updateItemQuantity`, `removeItem`, `getItems`, `getSubtotal` (returns `MonetaryAmount`), `getItemCount`, `clearCart`, `mergeGuestCartToUser`. Handles both session-based guest identification and user-ID based cart item storage.

##### `app/Domain/Checkout/`

*   **Services:**
    *   **`CheckoutService.php`**: Basic structure for `initiateCheckout()` (calculates totals, placeholder for PaymentIntent creation) and `completeOrder()` (placeholder for verifying payment and creating order).

##### `app/Domain/Payment/`

*   **Contracts:**
    *   **`PaymentGateway.php`**: Interface defining methods for payment operations (`createPaymentIntent`, `getPaymentDetails`, `refund`, `constructWebhookEvent`).
*   **Gateways:**
    *   **`StripeGateway.php`**: Basic implementation of `PaymentGateway` for Stripe. Includes methods for creating PaymentIntents, retrieving payment details, processing refunds, and constructing webhook events using the Stripe PHP SDK.
*   **Services:**
    *   **`PaymentService.php`**: Facade that uses the `PaymentGateway` contract to perform payment operations.
*   **DTOs:**
    *   **`PaymentResultData.php`**: DTO for representing the result of a payment or refund operation.
    *   **`PaymentIntentResultData.php`**: DTO for payment intent creation results (client secret, ID, status).
*   **Events:**
    *   `PaymentSuccessfulEvent.php`, `PaymentFailedEvent.php`.

##### `app/Domain/Promotion/`

*   **Models:**
    *   **`Coupon.php`**: Eloquent model for discount coupons. Includes attributes for code, type, value, usage limits, validity dates, active status. Contains an `isValid()` method.
*   **Services:**
    *   **`CouponService.php`**: Implemented `findAndValidateCoupon()` and `calculateDiscount()` methods. Basic `redeemCoupon()` method.
*   **Exceptions:**
    *   **`InvalidCouponException.php`**.

##### `app/Domain/Utility/`

*   **Models:** `QuizResult.php`, `NewsletterSubscriber.php`, `AuditLog.php`, `EmailLog.php`, `TaxRate.php`, `TaxRateHistory.php`. All are Eloquent models mapping to their respective tables from `sample_database_schema.sql.txt`, with appropriate fillable attributes, casts, and relationships.
*   **Services:** Basic structures for `NewsletterService.php` (with `subscribe`/`unsubscribe`), `QuizService.php` (with placeholder `getRecommendations`), `AuditLoggerService.php` (placeholder), and `TaxService.php` (with placeholder `calculateTax`).

#### 3.2.4. HTTP Layer (`app/Http/`)

*   **Controllers (`app/Http/Controllers/Frontend/`)**
    *   **`HomeController.php`**: `index()` method fetches featured products (via `ProductQueryService`) and dummy testimonials, passes them to `frontend.home.blade.php`. Placeholder `about()` and `contact()` methods.
    *   **`ProductController.php`**:
        *   `index()`: Fetches paginated active products using `ProductQueryService`, supports basic category filtering from request query. Passes data to `frontend.products.index.blade.php`.
        *   `show(Product $product)`: Uses route model binding to fetch a product by slug. Ensures product is active. Loads category and attributes. Fetches some related products. Passes data to `frontend.products.show.blade.php`.
    *   **`QuizController.php`**:
        *   `show()`: Returns `frontend.quiz.show.blade.php`.
        *   `submit(Request $request)`: Validates quiz answers, calls `QuizService->getRecommendations()`, saves `QuizResult`, returns recommendations as JSON.
    *   **`NewsletterController.php`**:
        *   `subscribe(NewsletterSubscriptionRequest $request)`: Calls `NewsletterService->subscribe()` and returns JSON response.
*   **Form Requests (`app/Http/Requests/Frontend/`)**
    *   **`NewsletterSubscriptionRequest.php`**: Defines validation rules (`required`, `email`, `max`) and custom messages for newsletter email input.
*   **View Composers (`app/View/Composers/`)**
    *   **`CategoryComposer.php`**: Fetches root categories (cached) and makes them available as `$allCategories` to specified views (product index, navigation, footer). Registered in `ViewServiceProvider`.

#### 3.2.5. Infrastructure Layer (`app/Infrastructure/`)

*   **Persistence (`app/Infrastructure/Persistence/Eloquent/Repositories/`)**
    *   **`EloquentProductRepository.php`**: Implements `ProductRepositoryContract`. Provides Eloquent-based implementations for finding products, getting featured products, paginating all active products (with basic category filtering), and CRUD operations.

#### 3.2.6. Mail (`app/Mail/`)
*   (Files like `OrderConfirmationEmail.php` generated in design spec, but full implementation and listener setup is for a later phase).

### 3.3. Database Files (`database/`)

*   **Migrations:**
    *   New migrations created for: `addresses`, `categories`, `products`, `product_attributes`, `inventory_movements`, `coupons`, `orders`, `order_items`, `cart_items`, `quiz_results`, `newsletter_subscribers`, `audit_log`, `email_log`, `tax_rates`, `tax_rate_history`.
    *   These migrations define table structures based on the provided SQL schema and ERD, including column types, nullability, indexes, and foreign key constraints. Prices are stored as `integer('..._minor_amount')` and `string('currency_code')`.
*   **Factories:**
    *   New factories created for all new domain models: `AddressFactory`, `CategoryFactory`, `ProductFactory`, `ProductAttributeFactory`, `InventoryMovementFactory`, `CouponFactory`, `OrderFactory`, `OrderItemFactory`, `CartItemFactory`, and utility models.
    *   `UserFactory` updated for new User model path and attributes.
    *   Factories generate realistic placeholder data for development and testing.

### 3.4. Resource Files (`resources/`)

*   **CSS (`resources/css/app.css`):**
    *   Imports Tailwind CSS.
    *   Defines CSS custom variables for light and dark mode color palettes and fonts based on `sample_landing_page.html`.
    *   Includes basic global styles for `body`, headings, links, and a `.container-custom` utility.
*   **JavaScript (`resources/js/`):**
    *   **`app.js`**: Main JS entry point. Initializes Alpine.js and imports custom modules like `darkModeHandler`, `ambientAudioHandler`, `scentQuizHandler`. Calls `initAboutParallax()`.
    *   **`bootstrap.js`**: Standard Laravel setup for Axios.
    *   **`dark-mode.js`**: Alpine.js component logic for managing dark/light theme toggle and persistence in `localStorage`.
    *   **`ambient-audio.js`**: Alpine.js component logic for the ambient audio toggle.
    *   **`parallax-init.js`**: Standalone function to initialize parallax effect for the "About" section image, based on scroll position.
    *   **`scent-quiz.js`**: Alpine.js component logic for the multi-step scent quiz UI, handling step progression and basic client-side result display (with a note to switch to backend processing).
*   **Views (`resources/views/`):**
    *   **Layouts (`layouts/` and `components/layouts/`):**
        *   `components/layouts/app-layout.blade.php`: Main frontend layout component. Includes HTML structure, head (meta, title, fonts, CSS/JS Vite links), body, slots for page-specific header and content, and partials for navigation and footer. Integrates dark mode Alpine component.
        *   `layouts/partials/frontend/navigation.blade.php`: Frontend navigation bar with logo, desktop/mobile links, auth links, cart icon (with Alpine store for count), and dark mode toggle.
        *   `layouts/partials/frontend/footer.blade.php`: Frontend footer with company info, quick links, customer care, contact details, social links, and payment methods icons.
    *   **Components (`components/frontend/`):**
        *   `product-card.blade.php`: Blade component to display a single product card with image, name, short description, price, and link to detail page.
    *   **Frontend Pages (`frontend/`):**
        *   `home.blade.php`: Landing page. Includes Blade partials for Hero, About, Featured Products (using `ProductCard` component), Finder (quiz teaser), Testimonials, Newsletter sections, adapting structure from `sample_landing_page.html`. Also includes floating shop button and ambient audio toggle.
        *   `products/index.blade.php`: Product listing page. Displays a grid of products (using `ProductCard`), pagination links, and a sidebar for category filtering (data provided by `CategoryComposer`).
        *   `products/show.blade.php`: Product detail page. Shows main product image, gallery thumbnails (with Alpine for switching main image), product name, price, descriptions, attributes. Includes a placeholder add-to-cart form and related products section.
        *   `quiz/show.blade.php`: Scent quiz page. Implements the multi-step quiz UI using Alpine.js (`scentQuizHandler`) with sections for each question and dynamically displayed recommendations.
    *   **Partials (`frontend/partials/`):**
        *   `home/hero.blade.php`, `home/featured-products.blade.php`, etc., created to modularize the `home.blade.php` content based on sections from `sample_landing_page.html`.
        *   `newsletter_form.blade.php`: Reusable newsletter subscription form with Alpine.js for AJAX submission and feedback.
        *   Curve separator SVGs (`curves/curve1.blade.php`, etc.) to replicate section transitions from `sample_landing_page.html`.

### 3.5. Route Files (`routes/`)

*   **`web.php`**:
    *   Defines routes for `HomeController` (`/`, `/about-us`, `/contact-us`).
    *   Defines routes for `ProductController` (`/shop`, `/product/{product:slug}`).
    *   Defines routes for `QuizController` (`/scent-quiz`, `/scent-quiz/submit`).
    *   Defines route for `NewsletterController` (`/newsletter/subscribe`).
    *   Includes placeholder auth routes and an authenticated `/account/dashboard` route.
*   **`api.php`**: Basic structure for `/api/v1` created but detailed endpoint implementation is for a later phase.

### 3.6. Root Project Files

*   **`composer.json`**: Updated with necessary packages like `brick/money`, `spatie/laravel-data`, `laravel/socialite`, `stripe/stripe-php`. Dependency conflicts previously resolved.
*   **`package.json`**: Includes `alpinejs`, `tailwindcss`, and other frontend build dependencies.
*   **`tailwind.config.js`**: Configured with custom color palette, fonts (from `sample_landing_page.html` CSS variables), and necessary plugins. `darkMode: 'class'` enabled.
*   **`vite.config.js`**, **`postcss.config.js`**: Standard Laravel configurations.

## 4. Architectural Notes & Design Patterns

*   **Layered Architecture & DDD Influence:** The `app/Domain/` structure is central, separating business logic into contexts (UserManagement, Catalog, Inventory, OrderManagement, etc.). This promotes modularity and testability.
*   **Service Layer:** Services (e.g., `InventoryService`, `ProductQueryService`, `OrderNumberService`, `NewsletterService`) encapsulate business logic, keeping controllers thin.
*   **Repository Pattern:** `ProductRepositoryContract` and `EloquentProductRepository` demonstrate this pattern for data abstraction in the Catalog domain.
*   **Value Objects:** `MonetaryAmount`, `EmailAddress`, `FullName` are used for type safety and encapsulating domain-specific data types. `MonetaryAmountCast` facilitates their use with Eloquent.
*   **Data Transfer Objects (DTOs):** Spatie/Laravel-Data is used for DTOs (e.g., `UserData`, `ProductData`, `OrderData`) to provide structured data transfer between layers.
*   **Events:** Domain events (e.g., `UserRegisteredEvent`, `OrderPlacedEvent`, `StockLevelChangedEvent`) are defined for decoupling application components. Listeners will handle side effects.
*   **Blade Components & Partials:** Used extensively in the frontend to create reusable UI elements and modularize views, enhancing maintainability and consistency with the `sample_landing_page.html` design.
*   **Alpine.js:** Leveraged for client-side interactivity (dark mode, quiz steps, newsletter submission, ambient audio) directly within Blade templates, keeping JS footprint minimal for these tasks.

## 5. Onboarding Notes for New Developers

*   **Understand the `.env` file:** This is crucial for local setup. Copy from `.env.example`.
*   **Laravel Sail:** All development should happen within the Sail environment. Use `./vendor/bin/sail <command>` for Artisan, Composer, NPM, etc.
*   **Directory Structure:** Familiarize yourself with the standard Laravel structure and especially the `app/Domain/` organization for business logic.
*   **Frontend:** Review `resources/css/app.css` (Tailwind setup, CSS vars), `resources/js/app.js` (Alpine setup, custom modules), and `tailwind.config.js`. Blade layouts are in `resources/views/components/layouts/` and `resources/views/layouts/`.
*   **Database:** Migrations are the source of truth for the schema. Use seeders and factories for test data. Connect to MariaDB via Sail or a GUI tool using `127.0.0.1:3306` and credentials from `.env`.
*   **Key Concepts:** Review Laravel MVC, Service Layer, Repositories (where used), DTOs, and Value Objects. The `technical_design_specification_document.md` provides more detail.
*   **Git Workflow:** Follow the branching strategy outlined in `README.md` (Gitflow variant).
*   **Pusher Warnings:** The numerous Pusher warnings during Sail commands are currently cosmetic as Pusher broadcasting is not actively configured (`BROADCAST_DRIVER=log`). They can be ignored for now.

## 6. Guidance for QA Team

*   **Areas Ready for Initial Review/Test Case Design:**
    *   **Landing Page (`/`):** Visuals, responsiveness, dark mode, hero animation (mist), parallax on "About" image (if JS implemented), client-side ambient audio toggle. All links should point to valid (even if placeholder) pages.
    *   **Product Listing Page (`/shop`):** Display of products, basic category filtering (links), pagination.
    *   **Product Detail Page (`/product/{slug}`):** Display of product information, image gallery interaction, display of attributes. Add-to-cart functionality is not yet implemented.
    *   **Scent Quiz (`/scent-quiz`):** Multi-step UI functionality, client-side progression through steps, display of (currently placeholder/simple logic) recommendations.
    *   **Newsletter Subscription:** Form submission via AJAX, success/error messages (client-side for now, backend validation through `NewsletterSubscriptionRequest`). Emails are saved to `newsletter_subscribers` table.
*   **Key Functionalities Implemented (Backend Foundation):**
    *   Database schema for users, products, categories, orders, inventory, cart, and utility tables.
    *   Basic CRUD capability can be tested via Tinker for models if direct UI isn't ready (e.g., creating a Product with attributes).
    *   `InventoryService` logic for stock adjustment (can be tested via Tinker or unit tests).
    *   `OrderNumberService` generation.
    *   `MonetaryAmount` handling for prices.
*   **What's NOT Ready for Full E2E Testing Yet:**
    *   Full user authentication (registration, login flows beyond basic Breeze setup).
    *   Shopping cart "add to cart" functionality and full cart management.
    *   Checkout process and payment integration.
    *   Order creation backend logic triggered by payments.
    *   Admin panel functionality.
    *   API endpoints (beyond basic structure).

## 7. Conclusion & Recommendations for Next Steps

Significant progress has been made in setting up the project foundation, modeling the core domains, and implementing the initial public-facing frontend elements. The application now has a runnable base with a visual landing page, product display capabilities, and interactive elements like the scent quiz.

**Recommendations for Next Steps (Following the Execution Plan):**

1.  **Phase 4: Authentication & User Account Management:**
    *   Fully install and configure Laravel Breeze: `php artisan breeze:install blade` (or chosen stack), then `npm install && npm run dev`.
    *   Customize Breeze views to match "The Scent" theme.
    *   Implement the full user account section (dashboard, order history, profile management) by building out `AccountController` and related views.
    *   Implement Social Login functionality if prioritized.

2.  **Phase 5: E-commerce Core - Catalog, Cart, Checkout, Orders (Backend Focus):**
    *   Implement "Add to Cart" functionality on product pages, connecting to `CartService`.
    *   Fully implement `CartService` for DB-backed user carts and robust total calculations (including coupons via `CouponService`).
    *   Develop the `CheckoutController` and `CheckoutService` to handle address submission and prepare for payment.
    *   Integrate `StripeGateway` fully within `PaymentService` to handle `PaymentIntent` creation and webhook verification.
    *   Complete the `OrderService` to create orders from verified payments, trigger inventory updates, and send confirmation emails (via Mailable classes and queued jobs).

3.  **Admin Panel (Phase 6 - Backend for CRUD):**
    *   Start implementing the backend logic for Admin controllers (Products, Categories, Orders, Users) to perform CRUD operations using the domain services and repositories.

4.  **Testing (Ongoing - Phase 8):**
    *   Begin writing Feature tests for the implemented frontend routes and controller actions.
    *   Write Unit tests for the business logic within services (e.g., `QuizService` recommendation logic, `CouponService` validation).

By focusing on these areas, the core e-commerce functionality can be built out, leading towards a testable and deployable version 1.0.

---
https://drive.google.com/file/d/11TN1qmXXUWMwei4eGSo6b7w7IghU1HTL/view?usp=sharing, https://drive.google.com/file/d/149SQdFIsEyVGZtL5cHAjDSnFwfrufK4-/view?usp=sharing, https://aistudio.google.com/app/prompts?state=%7B%22ids%22:%5B%22191Xwb664N58hqofAV7tgOqIhw9Q_EXtW%22%5D,%22action%22:%22open%22,%22userId%22:%22103961307342447084491%22,%22resourceKeys%22:%7B%7D%7D&usp=sharing, https://drive.google.com/file/d/1JMi0sos9Z0VPmPtV_VUQdOiHP55dgoyM/view?usp=sharing, https://drive.google.com/file/d/1Ljou3KM9wH_n2IISZRUL5Usykg-a8yaZ/view?usp=sharing, https://drive.google.com/file/d/1NU0PF2paSbFH1H3swzif2Yyyc768jBV7/view?usp=sharing, https://drive.google.com/file/d/1QZH4p-KhoQlloMqonElkiJd_D7OafE9w/view?usp=sharing, https://drive.google.com/file/d/1UHbFpE2rY7aLNsT7R5-duOpJcgpWOoUh/view?usp=sharing, https://drive.google.com/file/d/1dppi2D-RTdYBal_YeLZxB4QHysdmQxpD/view?usp=sharing, https://drive.google.com/file/d/1eszSFTXohtDz0Cv1qbKHNecsx1BC1A3w/view?usp=sharing, https://drive.google.com/file/d/1gMQvFk0QXLWX-Bz05DlgjFeYgDxCiZEO/view?usp=sharing, https://drive.google.com/file/d/1nPblih0JaSTmarZmo2budApC3JXa64hP/view?usp=sharing

