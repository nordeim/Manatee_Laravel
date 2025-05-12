# Execution Plan: The Scent E-commerce Platform

**Version:** 1.0
**Date:** May 13, 2024
**Project:** The Scent

## 1. Introduction

This document outlines the step-by-step execution plan for building "The Scent" e-commerce platform, based on the `technical_design_specification_document.md`. It breaks down the development process into logical phases and steps, specifying files to be created or modified, goals for each step, and a checklist for verification.

## 2. Overall Project Phases

1.  **Phase 0: Project Setup & Foundation (Completed by User)**
    *   Initial Laravel 12 installation.
    *   Basic `.env` and `composer.json` from `project_directory_structure.md`.
2.  **Phase 1: Core Laravel Configuration & Base Structure**
    *   Refine configurations, set up base layouts, implement core services.
3.  **Phase 2: Domain Modeling & Core Business Logic**
    *   Implement Domain entities, value objects, services, and repositories.
4.  **Phase 3: Frontend Implementation - Public Facing**
    *   Develop Blade views, Tailwind CSS styling, Alpine.js interactivity for user-facing sections.
5.  **Phase 4: Authentication & User Account Management**
    *   Implement registration, login, password reset, user profiles.
6.  **Phase 5: E-commerce Core - Catalog, Cart, Checkout, Orders**
    *   Implement product display, cart functionality, checkout process, order creation.
7.  **Phase 6: Admin Panel Implementation**
    *   Develop admin dashboard for managing products, orders, users.
8.  **Phase 7: API Development**
    *   Build out RESTful API endpoints.
9.  **Phase 8: Testing, Refinement & Polish**
    *   Write comprehensive tests, refine UI/UX, optimize performance.
10. **Phase 9: Deployment Preparation**
    *   Prepare for deployment (Dockerfiles, documentation).

## 3. Detailed Execution Steps

---

### Phase 1: Core Laravel Configuration & Base Structure

**Goal:** Establish a solid foundation with correct configurations, essential service providers, and base layouts.

#### Step 1.1: Environment & Core Configuration

*   **Files to Create/Modify:**
    *   `.env.example` (Update with all necessary variables)
    *   `.env` (Local setup, copy from updated `.env.example`)
    *   `config/app.php` (Review providers, aliases, app name)
    *   `config/database.php` (Ensure MySQL/MariaDB setup)
    *   `config/filesystems.php` (Basic public/local disk setup)
    *   `config/sanctum.php` (Default is usually fine)
    *   `config/services.php` (Prepare placeholders for Stripe, Mailgun, etc.)
    *   `config/thescent.php` (New - Create custom app config)
    *   `tailwind.config.js` (Setup based on design doc)
    *   `vite.config.js` (Ensure correct input/output paths)
    *   `postcss.config.js` (Ensure Tailwind/Autoprefixer are included)
    *   `composer.json` (Add initial set of core packages: `brick/money`, `spatie/laravel-data` (optional), `laravel/socialite` (optional), `stripe/stripe-php`).
    *   `package.json` (Ensure `tailwindcss`, `alpinejs`, `postcss`, `autoprefixer` are present).
*   **Objective:**
    *   All essential environment variables are defined.
    *   Core Laravel configurations are tailored for the project.
    *   Frontend build tools are correctly configured.
    *   Initial key composer packages are included.
*   **Checklist:**
    *   [ ] `.env.example` is comprehensive.
    *   [ ] Local `.env` allows the application to boot.
    *   [ ] `composer install` and `npm install` run without errors.
    *   [ ] `npm run dev` (Vite) starts and compiles basic assets.
    *   [ ] Custom `config/thescent.php` is created and readable.
    *   [ ] `tailwind.config.js` reflects the project's design system.

#### Step 1.2: Base Layouts & Frontend Assets

*   **Files to Create/Modify:**
    *   `resources/css/app.css` (Define root CSS variables, import Tailwind)
    *   `resources/js/app.js` (Initialize Alpine.js, basic structure for custom JS modules)
    *   `resources/js/bootstrap.js` (Review, usually fine as is)
    *   `resources/views/layouts/app.blade.php` (Main frontend layout based on `sample_landing_page.html`)
    *   `resources/views/layouts/partials/frontend/header.blade.php`
    *   `resources/views/layouts/partials/frontend/footer.blade.php`
    *   `resources/views/layouts/partials/frontend/navigation.blade.php`
    *   `resources/views/components/layouts/app-layout.blade.php` (If using anonymous Blade component for layout)
    *   `routes/web.php` (Add a basic route to test the layout, e.g., `/` pointing to a temporary view).
    *   `resources/views/frontend/home.blade.php` (Basic structure for landing page, extending `app.blade.php`).
*   **Objective:**
    *   A functional base layout for the frontend is in place.
    *   Core CSS (Tailwind, custom variables) and JS (Alpine) are loading.
    *   Dark mode toggle mechanism (JS and CSS) from `sample_landing_page.html` is integrated.
*   **Checklist:**
    *   [ ] Landing page route displays the base layout correctly.
    *   [ ] Header, footer, and basic navigation structure are visible.
    *   [ ] Tailwind CSS styles are applied.
    *   [ ] Alpine.js is functional.
    *   [ ] Dark mode toggle works.
    *   [ ] Console is free of major JS errors.

#### Step 1.3: Core Service Providers & Custom Casts

*   **Files to Create/Modify:**
    *   `app/Providers/DomainServiceProvider.php` (New - Create, register in `config/app.php`)
    *   `app/Providers/EventServiceProvider.php` (Prepare for event-listener mapping)
    *   `app/Providers/AuthServiceProvider.php` (Prepare for policy registration)
    *   `app/Casts/MonetaryAmountCast.php` (New - Implement custom cast)
    *   `app/Domain/Shared/ValueObjects/MonetaryAmount.php` (New - Implement basic VO)
*   **Objective:**
    *   Essential service providers are set up.
    *   Custom Eloquent cast for `MonetaryAmount` is ready for use in models.
*   **Checklist:**
    *   [ ] `DomainServiceProvider` is created and registered.
    *   [ ] `MonetaryAmountCast` and `MonetaryAmount` VO are implemented.
    *   [ ] No errors on application boot related to providers.

---

### Phase 2: Domain Modeling & Core Business Logic

**Goal:** Implement the core domain models, value objects, services, and repository contracts as defined in the design document. Focus on backend logic, not UI.

#### Step 2.1: User Management Domain

*   **Files to Create/Modify:**
    *   `app/Domain/UserManagement/Models/User.php` (Move from `app/Models/User.php`, adapt)
    *   `app/Domain/UserManagement/Models/Address.php` (New)
    *   `database/migrations/*_create_users_table.php` (Modify existing if needed for roles, status, address fields on user - though separate `addresses` table is preferred)
    *   `database/migrations/*_create_addresses_table.php` (New - For dedicated address model)
    *   `database/factories/UserFactory.php` (Update for new User model path and fields)
    *   `database/factories/AddressFactory.php` (New)
    *   `app/Domain/Shared/ValueObjects/EmailAddress.php` (New)
    *   `app/Domain/Shared/ValueObjects/FullName.php` (New)
    *   `app/Domain/UserManagement/DTOs/UserData.php` (New)
    *   `app/Domain/UserManagement/Services/UserProfileService.php` (New - basic structure)
    *   `app/Domain/UserManagement/Events/UserRegisteredEvent.php` (New)
    *   `app/Domain/UserManagement/Events/PasswordResetRequestedEvent.php` (New)
    *   `config/auth.php` (Update `providers.users.model` path)
*   **Objective:**
    *   User and Address models are implemented with relationships.
    *   Migrations create the necessary tables.
    *   Factories can generate test data.
    *   Basic DTOs and Event structures are in place.
*   **Checklist:**
    *   [ ] `php artisan migrate` runs successfully.
    *   [ ] User and Address factories work.
    *   [ ] Tinker can create and retrieve User/Address models.
    *   [ ] User model path in `config/auth.php` is correct.

#### Step 2.2: Catalog Domain

*   **Files to Create/Modify:**
    *   `app/Domain/Catalog/Models/Product.php` (New)
    *   `app/Domain/Catalog/Models/Category.php` (New)
    *   `app/Domain/Catalog/Models/ProductAttribute.php` (New)
    *   `database/migrations/*_create_categories_table.php` (New)
    *   `database/migrations/*_create_products_table.php` (New - using `MonetaryAmountCast`)
    *   `database/migrations/*_create_product_attributes_table.php` (New)
    *   `database/factories/CategoryFactory.php` (New)
    *   `database/factories/ProductFactory.php` (New)
    *   `database/factories/ProductAttributeFactory.php` (New)
    *   `app/Domain/Catalog/Contracts/ProductRepositoryContract.php` (New)
    *   `app/Infrastructure/Persistence/Eloquent/Repositories/EloquentProductRepository.php` (New)
    *   `app/Providers/DomainServiceProvider.php` (Bind `ProductRepositoryContract`)
    *   `app/Domain/Catalog/Services/ProductQueryService.php` (New - basic structure, method signatures)
    *   `app/Domain/Catalog/DTOs/ProductData.php` (New)
    *   `app/Domain/Catalog/Events/ProductViewedEvent.php` (New)
*   **Objective:**
    *   Product, Category, and ProductAttribute models are implemented with relationships.
    *   Migrations and factories are functional.
    *   Product repository pattern is established.
*   **Checklist:**
    *   [ ] `php artisan migrate` runs successfully.
    *   [ ] Product, Category, ProductAttribute factories work.
    *   [ ] `ProductRepositoryContract` and its Eloquent implementation are in place and bound.
    *   [ ] Tinker can use the repository to fetch product data.

#### Step 2.3: Inventory Domain

*   **Files to Create/Modify:**
    *   `app/Domain/Inventory/Models/InventoryMovement.php` (New)
    *   `database/migrations/*_create_inventory_movements_table.php` (New)
    *   `database/factories/InventoryMovementFactory.php` (New)
    *   `app/Domain/Inventory/Services/InventoryService.php` (New - implement `decreaseStock`, `increaseStock`, `getStockLevel` logic using `Product.stock_quantity` and `InventoryMovement` logging)
    *   `app/Domain/Inventory/Exceptions/InsufficientStockException.php` (New)
    *   `app/Domain/Inventory/Events/StockLevelChangedEvent.php` (New)
    *   `app/Domain/Inventory/Events/LowStockThresholdReachedEvent.php` (New)
*   **Objective:**
    *   Inventory movement tracking is implemented.
    *   Core inventory service logic for stock adjustments is functional.
*   **Checklist:**
    *   [ ] `php artisan migrate` runs successfully.
    *   [ ] `InventoryService` can correctly adjust product stock and log movements.
    *   [ ] `InsufficientStockException` is thrown appropriately.
    *   [ ] Unit tests for `InventoryService` pass.

#### Step 2.4: Order Management Domain

*   **Files to Create/Modify:**
    *   `app/Domain/OrderManagement/Models/Order.php` (New)
    *   `app/Domain/OrderManagement/Models/OrderItem.php` (New)
    *   `database/migrations/*_create_orders_table.php` (New)
    *   `database/migrations/*_create_order_items_table.php` (New)
    *   `database/factories/OrderFactory.php` (New)
    *   `database/factories/OrderItemFactory.php` (New)
    *   `app/Domain/OrderManagement/Services/OrderService.php` (New - basic structure, method signatures for `createOrder...`)
    *   `app/Domain/OrderManagement/Services/OrderNumberService.php` (New - implement generation logic)
    *   `app/Domain/OrderManagement/DTOs/OrderData.php` (New)
    *   `app/Domain/OrderManagement/Events/OrderPlacedEvent.php` (New)
    *   `app/Domain/OrderManagement/Events/OrderStatusChangedEvent.php` (New)
*   **Objective:**
    *   Order and OrderItem models are implemented with relationships.
    *   Migrations and factories are functional.
    *   Foundation for order creation logic is laid.
*   **Checklist:**
    *   [ ] `php artisan migrate` runs successfully.
    *   [ ] Order and OrderItem factories work.
    *   [ ] `OrderNumberService` generates unique order numbers.

#### Step 2.5: Cart Management, Checkout, Payment, Promotion Domains (Contracts & Basic Structures)

*   **Files to Create/Modify:**
    *   `app/Domain/CartManagement/Models/Cart.php` (New - if DB backed)
    *   `app/Domain/CartManagement/Models/CartItem.php` (New)
    *   `database/migrations/*_create_carts_table.php` (New - if DB backed)
    *   `database/migrations/*_create_cart_items_table.php` (New)
    *   `app/Domain/CartManagement/Services/CartService.php` (New - basic structure, session handling for guest cart)
    *   `app/Domain/Checkout/Services/CheckoutService.php` (New - basic structure)
    *   `app/Domain/Payment/Contracts/PaymentGateway.php` (New)
    *   `app/Domain/Payment/Gateways/StripeGateway.php` (New - basic structure, constructor for API key)
    *   `app/Domain/Payment/Services/PaymentService.php` (New - basic structure, inject `PaymentGateway`)
    *   `app/Domain/Promotion/Models/Coupon.php` (New - if implementing)
    *   `database/migrations/*_create_coupons_table.php` (New - if implementing)
    *   `app/Domain/Promotion/Services/CouponService.php` (New - basic structure)
*   **Objective:**
    *   Basic structures for cart, checkout, payment, and promotion domains are in place.
    *   Database tables for cart items (and coupons) are created.
*   **Checklist:**
    *   [ ] `php artisan migrate` runs successfully for cart and coupon tables.
    *   [ ] Basic `CartService` can add/retrieve items from session.
    *   [ ] Payment gateway contract and Stripe gateway structure are defined.

#### Step 2.6: Utility Domain (Models & Migrations)

*   **Files to Create/Modify:**
    *   `app/Domain/Utility/Models/QuizResult.php` (New)
    *   `app/Domain/Utility/Models/NewsletterSubscriber.php` (New)
    *   `app/Domain/Utility/Models/AuditLog.php` (New)
    *   `app/Domain/Utility/Models/EmailLog.php` (New)
    *   `app/Domain/Utility/Models/TaxRate.php` (New)
    *   `app/Domain/Utility/Models/TaxRateHistory.php` (New)
    *   `database/migrations/*_create_quiz_results_table.php` (New)
    *   `database/migrations/*_create_newsletter_subscribers_table.php` (New)
    *   `database/migrations/*_create_audit_log_table.php` (New)
    *   `database/migrations/*_create_email_log_table.php` (New)
    *   `database/migrations/*_create_tax_rates_table.php` (New)
    *   `database/migrations/*_create_tax_rate_history_table.php` (New)
    *   Corresponding Factories for these models.
    *   Basic Service structures (e.g., `TaxService.php`, `NewsletterService.php`).
*   **Objective:**
    *   All utility domain models and their database tables are created.
*   **Checklist:**
    *   [ ] `php artisan migrate` runs successfully for all utility tables.
    *   [ ] Factories for utility models are created.

---

### Phase 3: Frontend Implementation - Public Facing

**Goal:** Develop the user-facing frontend views and interactivity, translating the `sample_landing_page.html` and design document into Blade, Tailwind, and Alpine.js.

#### Step 3.1: Landing Page (Home)

*   **Files to Create/Modify:**
    *   `resources/views/frontend/home.blade.php` (Implement Hero, About, Product Highlights, Quiz Teaser, Testimonials, Newsletter sections based on `sample_landing_page.html`)
    *   `app/Http/Controllers/Frontend/HomeController.php` (New - method to return `home` view, fetch featured products via `ProductQueryService`)
    *   `routes/web.php` (Update `/` route)
    *   `resources/js/parallax-init.js` (Implement parallax from sample)
    *   `app/View/Components/Frontend/ProductCard.php` & `product-card.blade.php` (New)
    *   Relevant CSS in `resources/css/app.css` for unique section styles if not covered by Tailwind utilities.
*   **Objective:**
    *   The public landing page is visually complete and interactive as per the sample.
    *   Featured products are dynamically loaded.
*   **Checklist:**
    *   [ ] All sections from `sample_landing_page.html` are present on `home.blade.php`.
    *   [ ] Styling matches the sample.
    *   [ ] Parallax, mist animations, and other visual effects work.
    *   [ ] Featured products are displayed correctly.
    *   [ ] Page is responsive.

#### Step 3.2: Product Listing & Detail Pages

*   **Files to Create/Modify:**
    *   `resources/views/frontend/products/index.blade.php` (Product grid, filters, sorting)
    *   `resources/views/frontend/products/show.blade.php` (Product details, image gallery, add to cart button)
    *   `app/Http/Controllers/Frontend/ProductController.php` (New - `index` method with pagination/filtering using `ProductQueryService`, `show` method)
    *   `routes/web.php` (Add product routes)
    *   `app/View/Composers/CategoryComposer.php` (New - to provide categories for filtering)
    *   `app/Providers/AppServiceProvider.php` (Register `CategoryComposer`)
*   **Objective:**
    *   Users can browse products, view details, and see categories.
    *   Filtering and sorting on the product listing page (basic implementation).
*   **Checklist:**
    *   [ ] Product listing page displays products in a grid.
    *   [ ] Pagination works.
    *   [ ] Product detail page shows all relevant information.
    *   [ ] Category navigation/filtering is functional.
    *   [ ] Pages are responsive.

#### Step 3.3: Scent Quiz & Newsletter Subscription

*   **Files to Create/Modify:**
    *   `resources/views/frontend/quiz/show.blade.php` (Implement quiz UI from sample)
    *   `resources/js/scent-quiz.js` (Implement quiz logic from sample)
    *   `app/Http/Controllers/Frontend/QuizController.php` (New - `show` method, `submit` method to interact with `QuizService` and store `QuizResult`)
    *   `app/Domain/Utility/Services/QuizService.php` (Implement logic for recommendations)
    *   `routes/web.php` (Add quiz routes)
    *   `resources/views/partials/frontend/newsletter_form.blade.php` (Reusable newsletter form)
    *   `app/Http/Controllers/Frontend/NewsletterController.php` (New - `subscribe` method to interact with `NewsletterService`)
    *   `app/Domain/Utility/Services/NewsletterService.php` (Implement subscription logic)
    *   `app/Http/Requests/Frontend/NewsletterSubscriptionRequest.php` (New)
    *   `routes/web.php` (Add newsletter subscription route)
*   **Objective:**
    *   Scent quiz is interactive and provides recommendations (basic).
    *   Newsletter subscription form works.
*   **Checklist:**
    *   [ ] Quiz UI matches sample, steps work.
    *   [ ] Quiz submissions are saved (basic `QuizResult` model).
    *   [ ] Newsletter subscription saves email.
    *   [ ] Form validations are in place.

---

### Phase 4: Authentication & User Account Management

**Goal:** Implement secure user registration, login, password management, and basic account features.

#### Step 4.1: Authentication Scaffolding (Laravel Breeze/Fortify)

*   **Action:** Install and configure Laravel Breeze (Blade + Alpine stack) or Fortify.
    *   `composer require laravel/breeze --dev`
    *   `php artisan breeze:install blade` (or appropriate stack)
    *   `npm install && npm run dev`
    *   `php artisan migrate`
*   **Files to Create/Modify:**
    *   Auth views in `resources/views/auth/` (Customize styling to match The Scent theme)
    *   `app/Http/Controllers/Auth/` (Review and customize if needed)
    *   `routes/auth.php` (Generated by Breeze, include in `web.php`)
    *   Ensure `User` model path is correct if modified by Breeze.
*   **Objective:**
    *   Standard registration, login, password reset, email verification flows are functional.
*   **Checklist:**
    *   [ ] Users can register.
    *   [ ] Users can log in and log out.
    *   [ ] Password reset functionality works.
    *   [ ] Email verification flow works (if enabled).
    *   [ ] Auth pages match the site's theme.

#### Step 4.2: User Account Section

*   **Files to Create/Modify:**
    *   `resources/views/frontend/account/dashboard.blade.php`
    *   `resources/views/frontend/account/orders.blade.php` (List user's orders)
    *   `resources/views/frontend/account/orders_show.blade.php` (View single order details)
    *   `resources/views/frontend/account/profile.blade.php` (Edit profile information)
    *   `resources/views/frontend/account/addresses.blade.php` (Manage saved addresses - if implemented)
    *   `app/Http/Controllers/Frontend/AccountController.php` (Implement methods for dashboard, orders, profile updates using Domain services)
    *   `app/Http/Requests/Frontend/UpdateProfileRequest.php` (New)
    *   `routes/web.php` (Define authenticated account routes)
*   **Objective:**
    *   Authenticated users can access their dashboard, view order history, and update their profile.
*   **Checklist:**
    *   [ ] Account routes are protected by `auth` middleware.
    *   [ ] User can view their order history.
    *   [ ] User can update their basic profile information.

#### Step 4.3: Social Login (Optional, as per README)

*   **Action:** Install `laravel/socialite`. Configure providers (Google, Facebook) in `config/services.php` and `.env`.
*   **Files to Create/Modify:**
    *   `app/Domain/UserManagement/Services/AuthService.php` (Implement `redirectToProvider`, `handleProviderCallback` methods)
    *   `app/Http/Controllers/Auth/SocialLoginController.php` (New - to call `AuthService`)
    *   `routes/web.php` (Add routes for social login)
    *   Login/Register views (Add "Login with Google/Facebook" buttons)
*   **Objective:**
    *   Users can register/login using supported social media accounts.
*   **Checklist:**
    *   [ ] Social login buttons are present on auth pages.
    *   [ ] Users can successfully authenticate via configured social providers.
    *   [ ] Social accounts are correctly linked to user records.

---

### Phase 5: E-commerce Core - Catalog, Cart, Checkout, Orders

**Goal:** Implement the main e-commerce functionalities.

#### Step 5.1: Shopping Cart

*   **Files to Create/Modify:**
    *   `app/Domain/CartManagement/Services/CartService.php` (Fully implement add, update, remove, get totals, apply coupon logic)
    *   `app/Http/Controllers/Frontend/CartController.php` (Implement web routes to interact with `CartService`)
    *   `resources/views/frontend/cart/index.blade.php` (Display cart items, totals, update quantity, remove items, proceed to checkout)
    *   Layout views (Add cart icon with item count, using `CartComposer`)
    *   `app/View/Composers/CartComposer.php` (Implement logic to fetch cart summary)
    *   `app/Providers/AppServiceProvider.php` (Register `CartComposer`)
*   **Objective:**
    *   Full shopping cart functionality for guest and logged-in users.
*   **Checklist:**
    *   [ ] Products can be added to the cart.
    *   [ ] Cart item quantities can be updated.
    *   [ ] Cart items can be removed.
    *   [ ] Cart totals (subtotal, discounts, grand total) are calculated correctly.
    *   [ ] Cart persists for logged-in users (if DB-backed) and merges with session cart on login.
    *   [ ] Cart icon in header updates dynamically.

#### Step 5.2: Checkout Process

*   **Files to Create/Modify:**
    *   `app/Domain/Checkout/Services/CheckoutService.php` (Implement checkout steps: address, shipping, payment prep)
    *   `app/Domain/Payment/Services/PaymentService.php` (Implement `initiatePayment` using Stripe to create PaymentIntent)
    *   `app/Domain/Payment/Gateways/StripeGateway.php` (Implement PaymentIntent creation)
    *   `app/Http/Controllers/Frontend/CheckoutController.php` (Handle checkout form submissions, payment initiation)
    *   `app/Http/Requests/Frontend/StoreCheckoutRequest.php` (Validate checkout form data)
    *   `resources/views/frontend/checkout/index.blade.php` (Multi-step checkout form: shipping/billing address, payment details using Stripe Elements)
    *   `resources/js/checkout.js` (New - For Stripe Elements integration and payment submission)
    *   `app/Domain/Utility/Services/TaxService.php` (Implement basic tax calculation if applicable)
    *   `app/Domain/Promotion/Services/CouponService.php` (Integrate coupon application in checkout)
*   **Objective:**
    *   Users can proceed through the checkout process, enter addresses, and prepare for payment.
    *   Stripe Payment Intents are created.
*   **Checklist:**
    *   [ ] Checkout form collects necessary address and contact information.
    *   [ ] Stripe Elements are correctly integrated for card input.
    *   [ ] Taxes and shipping costs (basic placeholders for now) are displayed.
    *   [ ] Coupons can be applied.
    *   [ ] `PaymentIntent` is successfully created with Stripe.

#### Step 5.3: Order Creation & Post-Payment Handling

*   **Files to Create/Modify:**
    *   `app/Http/Controllers/Webhook/StripeWebhookController.php` (Implement webhook handling for `payment_intent.succeeded`)
    *   `app/Jobs/ProcessSuccessfulOrderJob.php` (New - To be dispatched by webhook controller)
    *   `app/Domain/OrderManagement/Services/OrderService.php` (Implement `createOrderFromPaymentIntent` or similar, called by the job. This will use `CartService` to get cart details, create `Order` & `OrderItem` records, clear cart, and dispatch `OrderPlacedEvent`)
    *   `app/Listeners/Order/UpdateInventoryOnOrderPlacedListener.php` (Implement using `InventoryService`)
    *   `app/Listeners/User/SendOrderConfirmationEmailListener.php` (Implement to queue `OrderConfirmationEmail`)
    *   `app/Mail/OrderConfirmationEmail.php` (Create mailable and Blade view for email)
    *   `resources/views/emails/orders/confirmation.blade.php` (New)
    *   `resources/views/frontend/checkout/success.blade.php` (Display order confirmation message)
*   **Objective:**
    *   Orders are successfully created in the database after confirmed payment.
    *   Inventory is updated.
    *   Customers receive an order confirmation email.
*   **Checklist:**
    *   [ ] Stripe webhook successfully triggers order creation job.
    *   [ ] `Order` and `OrderItem` records are correctly saved.
    *   [ ] Cart is cleared after successful order.
    *   [ ] `OrderPlacedEvent` is dispatched and listeners work:
        *   [ ] Inventory is decremented.
        *   [ ] Order confirmation email is sent (check Mailpit).
    *   [ ] User is redirected to a success page.

---

### Phase 6: Admin Panel Implementation

**Goal:** Develop a functional admin panel for managing core e-commerce data.

#### Step 6.1: Admin Base Layout & Dashboard

*   **Files to Create/Modify:**
    *   `resources/views/layouts/admin.blade.php` (Admin panel base layout: sidebar, header)
    *   `resources/views/admin/dashboard.blade.php` (Basic dashboard UI)
    *   `app/Http/Controllers/Admin/DashboardController.php` (Method for dashboard view, fetch basic stats)
    *   `app/Http/Middleware/EnsureUserIsAdmin.php` (Implement role check)
    *   `routes/web.php` (Define admin route group with `auth` and `admin` middleware)
*   **Objective:**
    *   Admin users can log in and access a basic, protected admin area.
*   **Checklist:**
    *   [ ] Admin routes are protected and accessible only by admin users.
    *   [ ] Admin base layout is functional.
    *   [ ] Basic dashboard page loads.

#### Step 6.2: Product & Category Management (Admin)

*   **Files to Create/Modify:**
    *   `app/Http/Controllers/Admin/ProductController.php` (Implement CRUD actions, using Domain services/repositories)
    *   `app/Http/Controllers/Admin/CategoryController.php` (Implement CRUD actions)
    *   `app/Http/Requests/Admin/StoreProductRequest.php` (New)
    *   `app/Http/Requests/Admin/UpdateProductRequest.php` (New)
    *   `app/Http/Requests/Admin/StoreCategoryRequest.php` (New)
    *   `resources/views/admin/products/` (index, create, edit .blade.php files)
    *   `resources/views/admin/categories/` (index, create, edit .blade.php files)
    *   `app/Policies/ProductPolicy.php`, `CategoryPolicy.php` (Implement rules for admin actions)
    *   Image upload handling in `ProductController` (potentially using `spatie/laravel-medialibrary` or storing to `public/storage` and linking in `Product` model).
*   **Objective:**
    *   Admins can create, read, update, and delete products and categories.
*   **Checklist:**
    *   [ ] Admin can list all products/categories.
    *   [ ] Admin can create new products/categories with validation.
    *   [ ] Admin can edit existing products/categories.
    *   [ ] Admin can delete products/categories.
    *   [ ] Product image uploads work.
    *   [ ] Authorization policies are enforced.

#### Step 6.3: Order Management (Admin)

*   **Files to Create/Modify:**
    *   `app/Http/Controllers/Admin/OrderController.php` (Implement `index`, `show`, `update` (for status changes))
    *   `resources/views/admin/orders/` (index, show .blade.php files)
    *   `app/Policies/OrderPolicy.php` (Implement rules for admin viewing/updating orders)
    *   `app/Domain/OrderManagement/Services/OrderService.php` (Add `updateOrderStatus` method, dispatch `OrderStatusChangedEvent`)
*   **Objective:**
    *   Admins can view orders and update their statuses.
*   **Checklist:**
    *   [ ] Admin can list all orders with filtering/sorting.
    *   [ ] Admin can view detailed order information.
    *   [ ] Admin can update order status (e.g., pending -> processing -> shipped).
    *   [ ] `OrderStatusChangedEvent` is dispatched (e.g., to send shipment notification).

#### Step 6.4: User Management & Tax Rate Management (Admin)

*   **Files to Create/Modify:**
    *   `app/Http/Controllers/Admin/UserController.php` (Implement `index`, `show`, `edit`, `update` for user roles/status)
    *   `resources/views/admin/users/` (index, edit .blade.php files)
    *   `app/Policies/UserPolicy.php` (Implement rules for admin managing users)
    *   `app/Http/Controllers/Admin/TaxRateController.php` (CRUD for `TaxRate` model)
    *   `resources/views/admin/tax_rates/` (index, create, edit .blade.php files)
*   **Objective:**
    *   Admins can manage basic user details and system tax rates.
*   **Checklist:**
    *   [ ] Admin can list users.
    *   [ ] Admin can edit user roles/status (with appropriate safeguards).
    *   [ ] Admin can CRUD tax rates.

---

### Phase 7: API Development

**Goal:** Implement RESTful API endpoints for potential external use or a decoupled frontend.

#### Step 7.1: API Authentication & Base Setup

*   **Files to Create/Modify:**
    *   `routes/api.php` (Structure for `/v1` prefix, Sanctum middleware)
    *   `app/Http/Controllers/Api/V1/AuthController.php` (Implement login, register, logout for API token generation using Sanctum)
    *   `app/Http/Requests/Api/V1/LoginRequest.php`, `RegisterRequest.php` (New)
    *   `app/Http/Resources/UserResource.php` (New)
*   **Objective:**
    *   API authentication via Sanctum tokens is functional.
*   **Checklist:**
    *   [ ] API users can register and receive a token.
    *   [ ] API users can log in and receive a token.
    *   [ ] Authenticated API users can log out (revoke token).
    *   [ ] Protected API routes require a valid token.

#### Step 7.2: Product & Cart API Endpoints

*   **Files to Create/Modify:**
    *   `app/Http/Controllers/Api/V1/ProductController.php` (Implement `index`, `show`)
    *   `app/Http/Controllers/Api/V1/CategoryController.php` (Implement `index`, `show`)
    *   `app/Http/Controllers/Api/V1/CartController.php` (Implement `index`, `storeItem`, `updateItem`, `destroyItem`)
    *   `app/Http/Resources/ProductResource.php`, `CategoryResource.php`, `CartResource.php` (New)
*   **Objective:**
    *   Public product/category data can be fetched via API.
    *   Authenticated users can manage their cart via API.
*   **Checklist:**
    *   [ ] Product and category listing/detail endpoints work.
    *   [ ] Cart API endpoints are functional and secure.
    *   [ ] API responses use the defined Resource structure.

#### Step 7.3: Checkout & Order API Endpoints

*   **Files to Create/Modify:**
    *   `app/Http/Controllers/Api/V1/CheckoutController.php` (Implement `prepare` for PaymentIntent, `complete` for order creation after client-side payment)
    *   `app/Http/Controllers/Api/V1/OrderController.php` (Implement `index`, `show` for user's orders)
    *   `app/Http/Resources/OrderResource.php` (New)
*   **Objective:**
    *   Checkout process can be driven via API.
    *   Users can retrieve their order history via API.
*   **Checklist:**
    *   [ ] API-driven checkout flow works.
    *   [ ] Users can fetch their orders via API.

---

### Phase 8: Testing, Refinement & Polish

**Goal:** Ensure application quality, stability, and a polished user experience.

#### Step 8.1: Unit & Feature Testing

*   **Action:** Write comprehensive Unit tests for Domain services, value objects, actions. Write Feature tests for all major web routes and API endpoints.
*   **Files to Create/Modify:**
    *   Numerous test files in `tests/Unit/` and `tests/Feature/`.
*   **Objective:**
    *   Achieve high, meaningful test coverage.
*   **Checklist:**
    *   [ ] All critical business logic is unit tested.
    *   [ ] All web routes and API endpoints have feature tests covering happy paths and error cases.
    *   [ ] `php artisan test` (or `pest`) runs successfully.
    *   [ ] Aim for >80% code coverage.

#### Step 8.2: E2E Testing (Cypress)

*   **Action:** Setup Cypress and write E2E tests for key user flows.
*   **Files to Create/Modify:**
    *   `cypress.config.js`
    *   Spec files in `cypress/e2e/` (e.g., `registration.cy.js`, `product_browsing.cy.js`, `checkout.cy.js`).
*   **Objective:**
    *   Key user journeys are validated in a real browser environment.
*   **Checklist:**
    *   [ ] Cypress is configured correctly.
    *   [ ] E2E tests for registration, login, adding to cart, and checkout pass.

#### Step 8.3: UI/UX Refinement & Accessibility

*   **Action:** Thoroughly review all frontend pages for responsiveness, visual consistency, and accessibility (WCAG AA target).
*   **Tools:** Browser developer tools, Lighthouse, axe-core, screen readers.
*   **Objective:**
    *   Polished, accessible, and responsive user interface.
*   **Checklist:**
    *   [ ] All pages are responsive across common device sizes.
    *   [ ] Consistent styling and branding.
    *   [ ] Good color contrast.
    *   [ ] Keyboard navigation is logical and complete.
    *   [ ] ARIA attributes are used correctly where needed.
    *   [ ] Major accessibility issues are addressed.

#### Step 8.4: Performance Optimization

*   **Action:** Identify and address performance bottlenecks.
    *   Run `php artisan optimize:clear`, then `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache` (in production-like environment).
    *   Optimize database queries (N+1 issues, add indexes). Use Laravel Debugbar/Telescope.
    *   Ensure frontend assets are minified (`npm run build`).
    *   Implement caching strategies (application cache, HTTP caching) where appropriate.
*   **Objective:**
    *   Application loads quickly and performs efficiently.
*   **Checklist:**
    *   [ ] Laravel caches are utilized.
    *   [ ] No obvious N+1 query problems.
    *   [ ] Frontend assets are optimized.
    *   [ ] Core Web Vitals are reasonable.

---

### Phase 9: Deployment Preparation

**Goal:** Prepare the application for deployment.

#### Step 9.1: Production Configuration & Security Hardening

*   **Files to Create/Modify:**
    *   Finalize `.env.example` for production.
    *   Review `config/app.php` (`APP_DEBUG=false`, `APP_ENV=production`).
    *   Implement security headers (CSP, HSTS via middleware or web server).
*   **Objective:**
    *   Application is configured securely for a production environment.
*   **Checklist:**
    *   [ ] `APP_DEBUG` is false for production.
    *   [ ] Strong `APP_KEY` is set.
    *   [ ] All necessary production environment variables are documented in `.env.example`.
    *   [ ] Security headers are configured.

#### Step 9.2: Dockerfile for Production (Fly.io) / Deployment Scripts

*   **Files to Create/Modify:**
    *   `Dockerfile` (If deploying to Fly.io or other container platforms, optimize for production: multi-stage build, non-root user, production web server like Caddy/Nginx + PHP-FPM).
    *   `fly.toml` (Configure for Fly.io deployment: release commands, processes).
    *   Deployment scripts (e.g., for Lightsail using Deployer or shell scripts).
*   **Objective:**
    *   A reliable and repeatable deployment process is established.
*   **Checklist:**
    *   [ ] Production Dockerfile is efficient and secure.
    *   [ ] `fly.toml` is correctly configured for release commands and processes.
    *   [ ] Deployment process is documented.

#### Step 9.3: CI/CD Pipeline Finalization

*   **Files to Create/Modify:**
    *   `.github/workflows/ci.yml` (Ensure it runs all tests, lints, and has a deployment step for main branch pushes).
*   **Objective:**
    *   Automated CI/CD pipeline for testing and deployment.
*   **Checklist:**
    *   [ ] CI pipeline runs successfully on pushes/PRs.
    *   [ ] CD pipeline deploys to staging/production on main branch merges.

---

This execution plan provides a structured approach to building "The Scent." Each step builds upon the previous, allowing for iterative development and testing. Flexibility will be needed, and some steps might overlap or be reordered based on development priorities and discoveries.
