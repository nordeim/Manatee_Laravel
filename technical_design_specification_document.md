# Technical Design Specification Document: The Scent E-commerce Platform

**Version:** 1.0
**Date:** May 13, 2024
**Project:** The Scent (Based on Manatee_Laravel project structure)

## 1. Introduction and Goals

This document outlines the technical design for "The Scent," a premium aromatherapy e-commerce platform. It is based on the project vision, features, and architectural principles detailed in the `README.md` provided by the client, the existing Laravel 12 project structure (`project_directory_structure.md`), the target database schema (`sample_database_schema.sql.txt`), and UI/UX cues from `sample_landing_page.html`.

**Core Goals (reiterated from README.md):**

1.  **Flawless & Mindful User Experience:** Intuitive, responsive, accessible, mobile-first.
2.  **Robust & Reliable Backend:** Stable, secure, well-tested e-commerce platform.
3.  **Developer Ergonomics & Maintainability:** Clean, readable, testable codebase.
4.  **Future-Proof Architecture:** Modular, DDD-influenced design for extensibility.

This document will detail the file structure, key components, their responsibilities, interfaces, and illustrative code snippets necessary to realize this platform. It aims for a word count exceeding 6000 words through comprehensive descriptions.

## 2. Technology Stack Summary

The technology stack is pre-defined by the `README.md` and existing project files:

*   **Backend Framework:** Laravel 12
*   **PHP Version:** ^8.2 (targeting 8.3 features where appropriate)
*   **Database:** MariaDB 11.7 (MySQL compatible, using `mysql` driver)
*   **Frontend Core:** Blade, Tailwind CSS 3, Alpine.js
*   **Asset Bundling:** Vite
*   **Development Environment:** Docker via Laravel Sail
*   **Key Laravel Packages:** Sanctum (API Auth), Socialite (Social Login), Cashier (Stripe/Paddle integration - assumed for payments based on README intent), Fortify/Breeze (Auth scaffolding), Pint (Code Style), Pest (Testing).

## 3. Architectural Overview

The application will follow a **Layered Architecture** with strong influences from **Domain-Driven Design (DDD)**, as outlined in the `README.md`.

*   **Presentation Layer:** Client-facing (Browser - Blade/Tailwind/Alpine.js, API Clients)
*   **Web Application Layer (Laravel HTTP):** Routing, Controllers, Middleware, Form Requests, API Resources.
*   **Domain Layer (`app/Domain`):** Core business logic, Models, Services, Actions, Events, Value Objects, Repositories (Contracts). This is the heart of the customization.
*   **Infrastructure Layer:** Database interaction (Eloquent), external service integrations (Payment Gateways, Mail Services, File Storage), Queue implementations.
*   **Async Processing Layer:** Queue Workers for background tasks.

This design aims for:
*   **Separation of Concerns:** Each layer has distinct responsibilities.
*   **Testability:** Domain logic can be tested independently of framework concerns.
*   **Maintainability:** Clear structure makes the codebase easier to understand and modify.
*   **Modularity:** `app/Domain` will be organized into Bounded Contexts.

## 4. Detailed Directory & File Structure

This section details the planned directory structure and key files, expanding upon the standard Laravel layout and the `app/Domain` concept.

### 4.1. Root Directory

Standard Laravel structure. The existing `default.php` and root `index.php` are considered non-standard for Laravel and will be ignored in favor of `public/index.php` as the entry point. `manatee_laravel_full_backup.sql` is noted but `sample_database_schema.sql.txt` will be the source for schema design.

### 4.2. `app/` Directory

#### 4.2.1. `app/Console/Commands/`

Standard Laravel location for Artisan commands.

*   **`app/Console/Commands/Inspire.php`** (Existing)
    *   Purpose: Default Laravel inspire command.
*   **`app/Console/Commands/App/ImportLegacyDataCommand.php`** (New)
    *   Purpose: Example custom command for one-off tasks like importing data from a legacy system or the `manatee_laravel_full_backup.sql` if needed.
    *   Responsibilities: Parse input data, transform, and save using Domain services/repositories.
    *   Code Snippet (Illustrative):
        ```php
        namespace App\Console\Commands\App;

        use Illuminate\Console\Command;
        use App\Domain\Catalog\Services\ProductImportService; // Example

        class ImportLegacyDataCommand extends Command
        {
            protected $signature = 'app:import-legacy-data {source_file}';
            protected $description = 'Imports legacy product data from a specified file.';

            public function __construct(private ProductImportService $productImportService)
            {
                parent::__construct();
            }

            public function handle(): int
            {
                $sourceFile = $this->argument('source_file');
                if (!file_exists($sourceFile)) {
                    $this->error("Source file not found: {$sourceFile}");
                    return Command::FAILURE;
                }

                $this->info("Starting import from {$sourceFile}...");
                // $this->productImportService->importFromFile($sourceFile, $this->output); // Example call
                $this->info('Import completed successfully.');
                return Command::SUCCESS;
            }
        }
        ```
*   **`app/Console/Commands/App/UpdateProductStockCommand.php`** (New)
    *   Purpose: Command to update product stock levels, perhaps from an external feed or scheduled task.
    *   Responsibilities: Fetch stock data, validate, update inventory via `InventoryService`.
*   **`app/Console/Commands/App/GenerateSitemapCommand.php`** (New)
    *   Purpose: Generates the sitemap.xml file.
    *   Responsibilities: Uses `spatie/laravel-sitemap` or custom logic to crawl public routes and product pages. Invoked by scheduler.

#### 4.2.2. `app/Domain/` (Core of Custom Logic)

This is the central piece of the DDD-influenced architecture. Each subdirectory represents a Bounded Context.

##### 4.2.2.1. `app/Domain/Shared/`

For code shared across multiple Bounded Contexts.

*   **`app/Domain/Shared/ValueObjects/`**
    *   **`app/Domain/Shared/ValueObjects/MonetaryAmount.php`** (New)
        *   Purpose: Represents a monetary value with amount and currency. Immutable.
        *   Responsibilities: Handles currency formatting, arithmetic operations (addition, subtraction, multiplication by scalar), comparison. Internally might use `brick/money`.
        *   Interface: `__construct(int $minorAmount, string $currencyCode)`, `getAmount()`, `getCurrency()`, `isEqualTo(MonetaryAmount $other)`, `format()`.
        *   Code Snippet:
            ```php
            namespace App\Domain\Shared\ValueObjects;

            use Brick\Money\Money; // Example underlying library
            use Brick\Math\RoundingMode;

            readonly class MonetaryAmount
            {
                private Money $money;

                public function __construct(int $minorAmount, string $currencyCode)
                {
                    // Assuming currency code is validated elsewhere or by Brick/Money
                    $this->money = Money::ofMinor($minorAmount, $currencyCode);
                }

                public static function fromDecimal(string|float|int $amount, string $currencyCode): self
                {
                    return new self(Money::of($amount, $currencyCode)->getMinorAmount()->toInt(), $currencyCode);
                }

                public function getMinorAmount(): int { return $this->money->getMinorAmount()->toInt(); }
                public function getCurrencyCode(): string { return $this->money->getCurrency()->getCurrencyCode(); }
                public function getDecimalAmount(): string { return (string) $this->money->getAmount(); }

                public function add(MonetaryAmount $other): self
                {
                    // Ensure currency codes match before adding
                    if ($this->getCurrencyCode() !== $other->getCurrencyCode()) {
                        throw new \InvalidArgumentException('Cannot add amounts with different currencies.');
                    }
                    return new self($this->money->plus($other->money)->getMinorAmount()->toInt(), $this->getCurrencyCode());
                }

                public function isEqualTo(MonetaryAmount $other): bool
                {
                    return $this->money->isEqualTo($other->money);
                }

                public function format(string $locale = 'en_US'): string
                {
                    return $this->money->formatTo($locale);
                }
            }
            ```
    *   **`app/Domain/Shared/ValueObjects/EmailAddress.php`** (New)
        *   Purpose: Represents a validated email address. Immutable.
        *   Interface: `__construct(string $email)`, `getValue()`, `getDomain()`.
    *   **`app/Domain/Shared/ValueObjects/FullName.php`** (New)
        *   Purpose: Represents a person's full name (first, last, middle).
    *   **`app/Domain/Shared/ValueObjects/AddressDetail.php`** (New)
        *   Purpose: Represents a postal address. Immutable.
        *   Properties: `streetLine1`, `streetLine2`, `city`, `state`, `postalCode`, `countryCode`.
        *   Interface: `__construct(...)`, `getFullAddressString()`.

*   **`app/Domain/Shared/Contracts/Repository.php`** (New)
    *   Purpose: Base interface for repositories, defining common methods if any (e.g., `findById`, `save`, `delete`).
    *   This is optional; often specific repository interfaces are sufficient.

*   **`app/Domain/Shared/Events/DomainEvent.php`** (New - Abstract Base Class or Interface)
    *   Purpose: A base class or interface that all domain events can extend/implement. Might include common properties like `occurredAt`.

##### 4.2.2.2. `app/Domain/UserManagement/`

Handles users, authentication, authorization, profiles, addresses.

*   **`app/Domain/UserManagement/Models/User.php`** (Modify existing `app/Models/User.php`)
    *   Purpose: Eloquent model for users. Central to authentication and user data.
    *   Extends: `Illuminate\Foundation\Auth\User` as Authenticatable.
    *   Implements: `MustVerifyEmail` (optional).
    *   Uses: `HasApiTokens`, `Notifiable`, `HasRoles` (if using `spatie/laravel-permission` or similar).
    *   Relationships: `orders()`, `addresses()`, `cart()`, `quizResults()`, `auditLogs()`, `emailsSent()` (to `email_log`), `taxRatesCreated()`, `taxRateHistoriesChangedBy()`.
    *   Properties: Maps to `users` table columns from `sample_database_schema.sql.txt`.
    *   Casts: `email_verified_at` (datetime), `newsletter_subscribed` (boolean), `reset_token_expires_at` (datetime).
    *   Code Snippet (Relationship example):
        ```php
        namespace App\Domain\UserManagement\Models; // New path

        use Illuminate\Contracts\Auth\MustVerifyEmail;
        use Illuminate\Database\Eloquent\Factories\HasFactory;
        use Illuminate\Foundation\Auth\User as Authenticatable;
        use Illuminate\Notifications\Notifiable;
        use Laravel\Sanctum\HasApiTokens;
        use App\Domain\OrderManagement\Models\Order;
        use App\Domain\UserManagement\Models\Address; // Assuming Address model is in UserManagement
        // ... other uses

        class User extends Authenticatable implements MustVerifyEmail // Optional
        {
            use HasApiTokens, HasFactory, Notifiable; // Add HasRoles if using a package

            protected $fillable = [
                'name', 'email', 'password', 'role', 'status',
                'newsletter_subscribed', 'address_line1', 'address_line2',
                'city', 'state', 'postal_code', 'country',
            ];

            protected $hidden = ['password', 'remember_token', 'reset_token'];

            protected $casts = [
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
                'newsletter_subscribed' => 'boolean',
                'reset_token_expires_at' => 'datetime',
            ];

            public function orders()
            {
                return $this->hasMany(Order::class);
            }

            public function addresses()
            {
                // For saved addresses separate from order addresses
                return $this->hasMany(Address::class);
            }

            // Other relationships from schema:
            // quizResults, auditLogs, emailLogs, taxRates, taxRateHistory
            // cart (if persistent cart is tied to user)
        }
        ```
*   **`app/Domain/UserManagement/Models/Address.php`** (New)
    *   Purpose: Eloquent model for user-saved addresses (can also be used by orders).
    *   Relationships: `user()` (belongsTo).
    *   Properties: Maps to `users` table address columns (`address_line1` etc.) or a separate `addresses` table as per the ERD in the `README.md`. The sample schema has address fields on the `users` table *and* `orders` table, implying denormalization or multiple address concepts. The ERD suggests a dedicated `addresses` table, which is better. Let's assume a dedicated `addresses` table is preferred for normalization and reusability.
        ```sql
        -- Assuming an `addresses` table like this (based on ERD, not directly in sample_database_schema.sql.txt user table)
        CREATE TABLE `addresses` (
          `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `user_id` BIGINT UNSIGNED NULL, -- Nullable if used for guest order addresses stored persistently
          `address_line1` VARCHAR(255) NOT NULL,
          `address_line2` VARCHAR(255) NULL,
          `city` VARCHAR(100) NOT NULL,
          `state` VARCHAR(100) NULL,
          `postal_code` VARCHAR(20) NOT NULL,
          `country_code` CHAR(2) NOT NULL, -- ISO 3166-1 alpha-2
          `type` ENUM('billing', 'shipping', 'general') NULL,
          `is_default_shipping` BOOLEAN DEFAULT FALSE,
          `is_default_billing` BOOLEAN DEFAULT FALSE,
          `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
        );
        ```
    *   Code Snippet for `Address.php` model:
        ```php
        namespace App\Domain\UserManagement\Models;

        use Illuminate\Database\Eloquent\Model;
        use Illuminate\Database\Eloquent\Factories\HasFactory;
        use App\Domain\UserManagement\Models\User;

        class Address extends Model
        {
            use HasFactory;

            protected $fillable = [
                'user_id', 'address_line1', 'address_line2', 'city',
                'state', 'postal_code', 'country_code', 'type',
                'is_default_shipping', 'is_default_billing'
            ];

            protected $casts = [
                'is_default_shipping' => 'boolean',
                'is_default_billing' => 'boolean',
            ];

            public function user()
            {
                return $this->belongsTo(User::class);
            }

            public function getFormattedAddressAttribute(): string
            {
                // Logic to format address into a string
                $lines = [$this->address_line1];
                if ($this->address_line2) $lines[] = $this->address_line2;
                $lines[] = "{$this->city}, {$this->state} {$this->postal_code}";
                $lines[] = $this->country_code; // Or full country name from a lookup
                return implode("\n", array_filter($lines));
            }
        }
        ```

*   **`app/Domain/UserManagement/Services/AuthService.php`** (New)
    *   Purpose: Handles complex authentication logic beyond basic Laravel Auth.
    *   Responsibilities: Social login integration (interacts with Socialite), MFA setup/verification, API token management.
*   **`app/Domain/UserManagement/Services/UserProfileService.php`** (New)
    *   Purpose: Manages user profile updates, address management.
*   **`app/Domain/UserManagement/DTOs/UserData.php`** (New)
    *   Purpose: Data Transfer Object for user creation or updates. Used by services and form requests. From `spatie/laravel-data` or custom.
*   **`app/Domain/UserManagement/Events/UserRegisteredEvent.php`** (New)
    *   Purpose: Dispatched when a new user registers.
    *   Payload: `User $user`.
*   **`app/Domain/UserManagement/Events/PasswordResetRequestedEvent.php`** (New)
    *   Purpose: Dispatched when a user requests a password reset.

##### 4.2.2.3. `app/Domain/Catalog/`

Manages products, categories, variants, attributes, inventory.

*   **`app/Domain/Catalog/Models/Product.php`** (New)
    *   Purpose: Eloquent model for products.
    *   Relationships: `category()` (belongsTo `Category`), `variants()` (hasMany `ProductVariant`), `attributes()` (hasOne `ProductAttribute`), `tags()` (belongsToMany `Tag` - if tags implemented), `inventoryMovements()` (hasMany `InventoryMovement`), `orderItems()` (hasMany `OrderItem`).
    *   Properties: Maps to `products` table.
    *   Casts: `price` (MonetaryAmount custom cast, or Decimal cast), `gallery_images` (array/collection), `benefits` (array/collection), `is_featured` (boolean), `backorder_allowed` (boolean).
    *   Custom Casts: Needs a custom cast for `MonetaryAmount` (price) if using the Value Object.
    *   Code Snippet (Relationships):
        ```php
        namespace App\Domain\Catalog\Models;

        use Illuminate\Database\Eloquent\Model;
        use Illuminate\Database\Eloquent\Factories\HasFactory;
        use App\Domain\Shared\ValueObjects\MonetaryAmount; // If using custom cast
        use App\Casts\MonetaryAmountCast; // Example custom cast
        use App\Domain\Catalog\Models\Category;
        use App\Domain\Catalog\Models\ProductVariant;
        use App\Domain\Catalog\Models\ProductAttribute;
        use App\Domain\Inventory\Models\InventoryMovement;
        use App\Domain\OrderManagement\Models\OrderItem;

        class Product extends Model
        {
            use HasFactory;

            protected $fillable = [
                'name', 'description', 'short_description', 'image', 'gallery_images',
                'price', 'benefits', 'ingredients', 'usage_instructions', 'category_id',
                'is_featured', 'low_stock_threshold', 'reorder_point', 'backorder_allowed',
                'highlight_text', 'stock_quantity', 'size', 'scent_profile', 'origin', 'sku'
            ];

            protected $casts = [
                'price' => MonetaryAmountCast::class . ':USD', // Example currency
                'gallery_images' => 'array',
                'benefits' => 'array',
                'is_featured' => 'boolean',
                'backorder_allowed' => 'boolean',
                'stock_quantity' => 'integer', // This is on product, sample schema also has it. Consider if it should be sum of variants.
                'low_stock_threshold' => 'integer',
                'reorder_point' => 'integer',
            ];

            public function category()
            {
                return $this->belongsTo(Category::class);
            }

            public function variants()
            {
                // If product_variants table exists. The sample schema doesn't define it explicitly,
                // but the README ERD does. Assuming variants are a feature.
                // If no variants, then stock is directly on product.
                // The sample schema HAS `stock_quantity` on `products` table.
                // For now, let's assume product can have direct stock OR stock via variants.
                // return $this->hasMany(ProductVariant::class);
                return null; // Placeholder if variants are not yet in sample SQL schema directly.
            }

            public function productAttributes() // Renamed from attributes() to avoid conflict with Eloquent $attributes
            {
                return $this->hasOne(ProductAttribute::class);
            }

            public function inventoryMovements()
            {
                return $this->hasMany(InventoryMovement::class);
            }

            public function orderItems()
            {
                return $this->hasMany(OrderItem::class);
            }

            // Accessor for main image URL
            public function getImageUrlAttribute(): ?string
            {
                return $this->image ? asset('storage/' . $this->image) : null;
            }
        }
        ```
*   **`app/Domain/Catalog/Models/Category.php`** (New)
    *   Purpose: Eloquent model for product categories.
    *   Relationships: `products()` (hasMany `Product`). Parent/child self-referential relationships if hierarchical.
    *   Properties: Maps to `categories` table.
*   **`app/Domain/Catalog/Models/ProductAttribute.php`** (New)
    *   Purpose: Eloquent model for product-specific attributes (scent type, mood, intensity).
    *   Relationships: `product()` (belongsTo `Product`).
    *   Properties: Maps to `product_attributes` table. ENUMs will be simple string properties in PHP, validation in FormRequests/Services.
*   **`app/Domain/Catalog/Services/ProductQueryService.php`** (New)
    *   Purpose: Handles complex querying of products (filtering, sorting, searching).
    *   Responsibilities: Builds and executes Eloquent queries, potentially integrating with search engines like Algolia/Meilisearch in the future.
*   **`app/Domain/Catalog/Services/CategoryService.php`** (New)
    *   Purpose: Business logic for managing categories.
*   **`app/Domain/Catalog/DTOs/ProductData.php`** (New)
    *   Purpose: DTO for product creation/updates.
*   **`app/Domain/Catalog/Events/ProductViewedEvent.php`** (New)
    *   Purpose: Dispatched when a product detail page is viewed (for analytics, recommendations).

##### 4.2.2.4. `app/Domain/Inventory/`

Dedicated context for inventory, potentially separate from Catalog if logic is complex. The sample schema has `inventory_movements` table.

*   **`app/Domain/Inventory/Models/InventoryMovement.php`** (New)
    *   Purpose: Eloquent model to log all changes to inventory.
    *   Relationships: `product()` (belongsTo `Product`).
    *   Properties: Maps to `inventory_movements` table.
*   **`app/Domain/Inventory/Services/InventoryService.php`** (New)
    *   Purpose: Manages stock levels, handles stock adjustments, checks availability.
    *   Responsibilities: `decreaseStock(Product $product, int $quantity, string $type, ?int $referenceId)`, `increaseStock(...)`, `getStockLevel(Product $product)`. Interacts with `InventoryMovement`.
    *   Code Snippet (Illustrative method):
        ```php
        namespace App\Domain\Inventory\Services;

        use App\Domain\Catalog\Models\Product;
        use App\Domain\Inventory\Models\InventoryMovement;
        use Illuminate\Support\Facades\DB;
        use App\Domain\Inventory\Exceptions\InsufficientStockException;

        class InventoryService
        {
            public function decreaseStock(Product $product, int $quantity, string $type, ?int $referenceId = null, ?string $notes = null): void
            {
                if ($quantity <= 0) {
                    throw new \InvalidArgumentException('Quantity to decrease must be positive.');
                }

                DB::transaction(function () use ($product, $quantity, $type, $referenceId, $notes) {
                    // Refresh product model for pessimistic locking or fresh data
                    $product = Product::findOrFail($product->id); // Or $product->refresh();

                    if ($product->stock_quantity < $quantity && !$product->backorder_allowed) {
                        throw new InsufficientStockException("Not enough stock for product {$product->name}. Available: {$product->stock_quantity}, Requested: {$quantity}");
                    }

                    $product->stock_quantity -= $quantity;
                    $product->save();

                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'quantity_change' => -$quantity,
                        'type' => $type, // e.g., 'sale', 'adjustment'
                        'reference_id' => $referenceId, // e.g., order_id
                        'notes' => $notes,
                    ]);
                });
            }
            // ... other methods like increaseStock, getStockLevel
        }
        ```
*   **`app/Domain/Inventory/Events/StockLevelChangedEvent.php`** (New)
*   **`app/Domain/Inventory/Events/LowStockThresholdReachedEvent.php`** (New)
*   **`app/Domain/Inventory/Exceptions/InsufficientStockException.php`** (New)

##### 4.2.2.5. `app/Domain/OrderManagement/`

Handles orders, order items.

*   **`app/Domain/OrderManagement/Models/Order.php`** (New)
    *   Purpose: Eloquent model for orders.
    *   Relationships: `user()` (belongsTo `User`), `items()` (hasMany `OrderItem`), `billingAddress()` (belongsTo `Address` - assuming shared Address model), `shippingAddress()` (belongsTo `Address`), `coupon()` (belongsTo `Coupon` - if coupons are implemented).
    *   Properties: Maps to `orders` table from `sample_database_schema.sql.txt`.
    *   Casts: `subtotal`, `discount_amount`, `shipping_cost`, `tax_amount`, `total_amount` (MonetaryAmount custom cast or Decimal), `paid_at` (datetime), `disputed_at` (datetime), `refunded_at` (datetime).
*   **`app/Domain/OrderManagement/Models/OrderItem.php`** (New)
    *   Purpose: Eloquent model for items within an order.
    *   Relationships: `order()` (belongsTo `Order`), `product()` (belongsTo `Product` - for reference).
    *   Properties: Maps to `order_items` table.
    *   Casts: `price` (MonetaryAmount custom cast or Decimal).
*   **`app/Domain/OrderManagement/Services/OrderService.php`** (New)
    *   Purpose: Core logic for creating, updating, and managing orders.
    *   Responsibilities: `createOrderFromCart(...)`, `updateOrderStatus(...)`, `processRefund(...)`. Interacts with `InventoryService`, `PaymentService`.
*   **`app/Domain/OrderManagement/Services/OrderNumberService.php`** (New)
    *   Purpose: Generates unique, user-friendly order numbers.
*   **`app/Domain/OrderManagement/DTOs/OrderData.php`** (New)
*   **`app/Domain/OrderManagement/Events/OrderPlacedEvent.php`** (New)
*   **`app/Domain/OrderManagement/Events/OrderStatusChangedEvent.php`** (New)

##### 4.2.2.6. `app/Domain/CartManagement/`

Handles shopping cart logic.

*   **`app/Domain/CartManagement/Models/Cart.php`** (New - for persistent carts)
    *   Purpose: Eloquent model for user-specific persistent carts.
    *   Relationships: `user()` (belongsTo `User`), `items()` (hasMany `CartItem`).
    *   Schema: `cart_items` table in sample schema implies a cart concept. A `carts` table might be needed to link `user_id` to a set of `cart_items` if guest carts are purely session-based but user carts are DB-backed.
*   **`app/Domain/CartManagement/Models/CartItem.php`** (New)
    *   Purpose: Eloquent model for items in a persistent cart.
    *   Relationships: `cart()` (belongsTo `Cart`), `product()` (belongsTo `Product`).
    *   Properties: Maps to `cart_items` table.
*   **`app/Domain/CartManagement/Services/CartService.php`** (New)
    *   Purpose: Manages cart operations (add, update, remove items, calculate totals, apply coupons).
    *   Responsibilities: Handles both session-based guest carts and DB-backed user carts. Merges session cart to DB on login.
    *   Code Snippet (Conceptual):
        ```php
        namespace App\Domain\CartManagement\Services;

        use App\Domain\Catalog\Models\Product; // Or ProductVariant
        use Illuminate\Support\Facades\Session;
        // ...

        class CartService
        {
            private const SESSION_KEY = 'cart';

            public function addItem(Product $product, int $quantity = 1): void
            {
                // Logic to add item to session or DB-backed cart
                // Recalculate totals
            }

            public function getCartContents(): array // Or a Cart DTO
            {
                // Retrieve and structure cart items, totals, etc.
                return Session::get(self::SESSION_KEY, ['items' => [], 'total' => 0.00]);
            }

            // ... other methods: updateItemQuantity, removeItem, clearCart, getTotals
        }
        ```
*   **`app/Domain/CartManagement/DTOs/CartData.php`** (New - Represents the whole cart state)

##### 4.2.2.7. `app/Domain/Checkout/`

Orchestrates the checkout process.

*   **`app/Domain/Checkout/Services/CheckoutService.php`** (New)
    *   Purpose: Handles the multi-step checkout process.
    *   Responsibilities: Validates cart, creates temporary order/persists checkout state, interacts with `PaymentService` to initiate payment, finalizes order upon successful payment.
*   **`app/Domain/Checkout/DTOs/CheckoutData.php`** (New - For holding checkout form data)

##### 4.2.2.8. `app/Domain/Payment/`

Integrates with payment gateways.

*   **`app/Domain/Payment/Contracts/PaymentGateway.php`** (New - Interface)
    *   Purpose: Defines a common interface for different payment gateways.
    *   Methods: `charge(MonetaryAmount $amount, array $paymentDetails, array $customerDetails): PaymentResult`, `refund(...)`.
*   **`app/Domain/Payment/Gateways/StripeGateway.php`** (New - Implements `PaymentGateway`)
    *   Purpose: Stripe-specific payment processing logic.
    *   Uses: `stripe/stripe-php` SDK.
*   **`app/Domain/Payment/Services/PaymentService.php`** (New)
    *   Purpose: Facade for interacting with the configured payment gateway.
    *   Responsibilities: Initiates payments, processes webhooks, handles refunds. Uses the `PaymentGateway` interface.
*   **`app/Domain/Payment/DTOs/PaymentResult.php`** (New)
*   **`app/Domain/Payment/Events/PaymentSuccessfulEvent.php`** (New)
*   **`app/Domain/Payment/Events/PaymentFailedEvent.php`** (New)

##### 4.2.2.9. `app/Domain/Promotion/`

Handles discounts, coupons.

*   **`app/Domain/Promotion/Models/Coupon.php`** (New - if `orders.coupon_code` implies a richer coupon system than just a string)
    *   Purpose: Eloquent model for coupons/discounts.
    *   The `orders` table has `coupon_code` and `coupon_id`. This suggests a `coupons` table.
    *   Properties: `code`, `type` (percentage, fixed), `value`, `usage_limit`, `valid_from`, `valid_to`, `min_purchase`.
*   **`app/Domain/Promotion/Services/CouponService.php`** (New)
    *   Purpose: Validates and applies coupons to carts/orders.
*   **`app/Domain/Promotion/Exceptions/InvalidCouponException.php`** (New)

##### 4.2.2.10. `app/Domain/Utility/` (or `app/Domain/Miscellaneous/`)

For features like Quizzes, Newsletter, Audit Logging, Email Logging, Tax.

*   **`app/Domain/Utility/Models/QuizResult.php`** (New)
    *   Properties: Maps to `quiz_results` table.
*   **`app/Domain/Utility/Models/NewsletterSubscriber.php`** (New)
    *   Properties: Maps to `newsletter_subscribers` table.
*   **`app/Domain/Utility/Models/AuditLog.php`** (New)
    *   Properties: Maps to `audit_log` table.
*   **`app/Domain/Utility/Models/EmailLog.php`** (New)
    *   Properties: Maps to `email_log` table.
*   **`app/Domain/Utility/Models/TaxRate.php`** (New)
    *   Properties: Maps to `tax_rates` table.
*   **`app/Domain/Utility/Models/TaxRateHistory.php`** (New)
    *   Properties: Maps to `tax_rate_history` table.
*   **`app/Domain/Utility/Services/NewsletterService.php`** (New)
*   **`app/Domain/Utility/Services/QuizService.php`** (New)
*   **`app/Domain/Utility/Services/AuditLoggerService.php`** (New)
*   **`app/Domain/Utility/Services/TaxService.php`** (New)
    *   Purpose: Calculates taxes based on `TaxRate` models and shipping destination.

#### 4.2.3. `app/Http/Controllers/`

Standard Laravel controllers, kept thin.

*   **`app/Http/Controllers/Controller.php`** (Existing)
*   **`app/Http/Controllers/Frontend/`** (New Subdirectory)
    *   **`app/Http/Controllers/Frontend/HomeController.php`** (New) - For landing page, about, contact.
    *   **`app/Http/Controllers/Frontend/ProductController.php`** (New) - Product listings, product details.
    *   **`app/Http/Controllers/Frontend/CartController.php`** (New) - View cart, add/update/remove items (web routes).
    *   **`app/Http/Controllers/Frontend/CheckoutController.php`** (New) - Handles checkout steps.
    *   **`app/Http/Controllers/Frontend/AccountController.php`** (New) - User dashboard, order history, profile.
    *   **`app/Http/Controllers/Frontend/QuizController.php`** (New) - Handles scent quiz submission and results.
    *   **`app/Http/Controllers/Frontend/NewsletterController.php`** (New) - Handles newsletter subscription.
*   **`app/Http/Controllers/Auth/`** (Existing or from Breeze/Fortify)
    *   Standard Laravel authentication controllers. Will be customized or used as-is.
*   **`app/Http/Controllers/Admin/`** (New Subdirectory)
    *   **`app/Http/Controllers/Admin/DashboardController.php`** (New)
    *   **`app/Http/Controllers/Admin/ProductController.php`** (New) - CRUD for products.
    *   **`app/Http/Controllers/Admin/CategoryController.php`** (New) - CRUD for categories.
    *   **`app/Http/Controllers/Admin/OrderController.php`** (New) - Manage orders.
    *   **`app/Http/Controllers/Admin/UserController.php`** (New) - Manage users.
    *   **`app/Http/Controllers/Admin/TaxRateController.php`** (New) - Manage tax rates.
*   **`app/Http/Controllers/Api/V1/`** (New Subdirectory)
    *   API versions controllers, e.g., `ProductController`, `OrderController`, `AuthController` for API.
    *   These controllers will use API Resources for responses.
*   **`app/Http/Controllers/Webhook/`** (New Subdirectory)
    *   **`app/Http/Controllers/Webhook/StripeWebhookController.php`** (New)
    *   **`app/Http/Controllers/Webhook/PayPalWebhookController.php`** (New - if PayPal is used)

#### 4.2.4. `app/Http/Middleware/`

Custom middleware.

*   **`app/Http/Middleware/Authenticate.php`** (Existing)
*   **`app/Http/Middleware/EncryptCookies.php`** (Existing)
*   **`app/Http/Middleware/PreventRequestsDuringMaintenance.php`** (Existing)
*   **`app/Http/Middleware/TrimStrings.php`** (Existing)
*   **`app/Http/Middleware/TrustProxies.php`** (Existing)
*   **`app/Http/Middleware/ValidateSignature.php`** (Existing)
*   **`app/Http/Middleware/VerifyCsrfToken.php`** (Existing)
*   **`app/Http/Middleware/EnsureUserIsAdmin.php`** (New)
    *   Purpose: Protect admin routes. Checks if authenticated user has 'admin' role.
*   **`app/Http/Middleware/LogApiRequests.php`** (New - Optional)
    *   Purpose: Logs incoming API requests and responses for debugging/auditing.
*   **`app/Http/Middleware/SetLocale.php`** (New - for i18n)
    *   Purpose: Sets application locale based on user preference, URL segment, or session.

#### 4.2.5. `app/Http/Requests/`

Form Request classes for validation.

*   **`app/Http/Requests/Auth/LoginRequest.php`** (Existing or from Breeze/Fortify)
*   **`app/Http/Requests/Frontend/StoreCheckoutRequest.php`** (New)
*   **`app/Http/Requests/Admin/StoreProductRequest.php`** (New)
*   **`app/Http/Requests/Admin/UpdateProductRequest.php`** (New)
*   **`app/Http/Requests/Api/V1/StoreOrderRequest.php`** (New)
*   **`app/Http/Requests/Frontend/NewsletterSubscriptionRequest.php`** (New)
*   Code Snippet (`StoreProductRequest.php`):
    ```php
    namespace App\Http\Requests\Admin;

    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Validation\Rule;
    use App\Domain\Catalog\Models\Category; // For exists rule

    class StoreProductRequest extends FormRequest
    {
        public function authorize(): bool
        {
            return $this->user()->can('create_products'); // Example policy check or role check
        }

        public function rules(): array
        {
            return [
                'name' => ['required', 'string', 'max:150'],
                'slug' => ['required', 'string', 'max:200', Rule::unique('products', 'slug')],
                'description' => ['nullable', 'string'],
                'short_description' => ['nullable', 'string', 'max:500'],
                'price' => ['required', 'numeric', 'min:0'], // Assuming decimal input for price
                'category_id' => ['nullable', 'integer', Rule::exists(Category::class, 'id')],
                'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')],
                'stock_quantity' => ['required', 'integer', 'min:0'],
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // For file uploads
                // ... other fields from products table
            ];
        }
    }
    ```

#### 4.2.6. `app/Http/Resources/`

API Resource classes for transforming models/DTOs into JSON.

*   **`app/Http/Resources/UserResource.php`** (New)
*   **`app/Http/Resources/ProductResource.php`** (New)
*   **`app/Http/Resources/OrderResource.php`** (New)
*   **`app/Http/Resources/CategoryResource.php`** (New)
*   Code Snippet (`ProductResource.php`):
    ```php
    namespace App\Http\Resources;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\JsonResource;

    class ProductResource extends JsonResource
    {
        public function toArray(Request $request): array
        {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'short_description' => $this->short_description,
                'price' => $this->price, // Assuming price is already formatted or cast appropriately
                'image_url' => $this->image_url, // Accessor
                'gallery_images' => $this->gallery_images, // Cast to array
                'category' => CategoryResource::make($this->whenLoaded('category')),
                // 'attributes' => ProductAttributeResource::make($this->whenLoaded('productAttributes')),
                // 'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        }
    }
    ```

#### 4.2.7. `app/Jobs/`

Queued jobs.

*   **`app/Jobs/SendOrderConfirmationEmailJob.php`** (New)
*   **`app/Jobs/ProcessImageUploadJob.php`** (New - for resizing, watermarking product images)
*   **`app/Jobs/ProcessWebhookJob.php`** (New - generic base or specific ones like `ProcessStripeWebhookJob`)
*   **`app/Jobs/GenerateSalesReportJob.php`** (New - admin feature)

#### 4.2.8. `app/Listeners/`

Event listeners.

*   **`app/Listeners/User/SendWelcomeEmailListener.php`** (New - listens to `UserRegisteredEvent`)
*   **`app/Listeners/Order/UpdateInventoryOnOrderPlacedListener.php`** (New - listens to `OrderPlacedEvent`)
*   **`app/Listeners/Order/NotifyAdminOnNewOrderListener.php`** (New - listens to `OrderPlacedEvent`)
*   **`app/Listeners/Inventory/SendLowStockNotificationListener.php`** (New - listens to `LowStockThresholdReachedEvent`)
*   **`app/Listeners/LogSuccessfulLoginListener.php`** (New - listens to `Illuminate\Auth\Events\Login`)

#### 4.2.9. `app/Mail/`

Mailable classes.

*   **`app/Mail/WelcomeEmail.php`** (New)
*   **`app/Mail

#### 4.2.9. `app/Mail/` (Continued)

Mailable classes, typically used by listeners or services to send emails.

*   **`app/Mail/OrderConfirmationEmail.php`** (New)
    *   Purpose: Sends an order confirmation email to the customer after a successful order.
    *   Constructor: Accepts an `Order` model instance.
    *   View: Uses a Blade template (e.g., `resources/views/emails/orders/confirmation.blade.php`).
    *   Responsibilities: Gathers order data, passes it to the view, configures email subject, sender, recipient.
    *   Code Snippet:
        ```php
        namespace App\Mail;

        use App\Domain\OrderManagement\Models\Order;
        use Illuminate\Bus\Queueable;
        use Illuminate\Contracts\Queue\ShouldQueue;
        use Illuminate\Mail\Mailable;
        use Illuminate\Mail\Mailables\Content;
        use Illuminate\Mail\Mailables\Envelope;
        use Illuminate\Queue\SerializesModels;

        class OrderConfirmationEmail extends Mailable implements ShouldQueue
        {
            use Queueable, SerializesModels;

            public Order $order;

            public function __construct(Order $order)
            {
                $this->order = $order;
            }

            public function envelope(): Envelope
            {
                return new Envelope(
                    from: new \Illuminate\Mail\Mailables\Address(config('mail.from.address'), config('mail.from.name')),
                    to: $this->order->user_id ? $this->order->user->email : $this->order->guest_email,
                    subject: 'Your The Scent Order Confirmation #' . $this->order->order_number,
                );
            }

            public function content(): Content
            {
                return new Content(
                    markdown: 'emails.orders.confirmation', // Using Markdown for simplicity
                    with: [
                        'order' => $this->order,
                        'orderUrl' => route('account.orders.show', $this->order->id), // Example route
                    ],
                );
            }

            public function attachments(): array
            {
                // Optionally attach PDF invoice here
                return [];
            }
        }
        ```
*   **`app/Mail/PasswordResetEmail.php`** (New - or customize Laravel's default)
    *   Purpose: Sends the password reset link to the user.
    *   Constructor: Accepts user model and reset token.
*   **`app/Mail/AdminNewOrderNotificationEmail.php`** (New)
    *   Purpose: Notifies admin(s) when a new order is placed.
*   **`app/Mail/LowStockNotificationEmail.php`** (New)
    *   Purpose: Notifies admin(s) when product stock reaches a low threshold.
*   **`app/Mail/ShipmentNotificationEmail.php`** (New)
    *   Purpose: Notifies customer when their order has been shipped. Includes tracking info.

#### 4.2.10. `app/Models/` (Core Models - if not domain-specific)

This directory will hold Eloquent models that are truly application-wide or don't neatly fit into a specific domain context if `app/Domain/.../Models` is preferred. However, given the DDD approach, most models should reside within their respective domain contexts. The existing `app/Models/User.php` will be *moved* to `app/Domain/UserManagement/Models/User.php`. This directory might remain empty or hold very generic models (e.g., a generic `Setting` model if used).

For this project, we'll aim to place all primary models within `app/Domain/` subdirectories.
*   **Existing `app/Models/User.php`**: To be **moved and refactored** into `app/Domain/UserManagement/Models/User.php`.

#### 4.2.11. `app/Policies/`

Authorization policies to control user actions on resources.

*   **`app/Policies/ProductPolicy.php`** (New)
    *   Purpose: Defines authorization rules for product-related actions (view, create, update, delete).
    *   Methods: `viewAny(User $user)`, `view(?User $user, Product $product)`, `create(User $user)`, `update(User $user, Product $product)`, `delete(User $user, Product $product)`.
*   **`app/Policies/OrderPolicy.php`** (New)
    *   Purpose: Defines rules for order actions.
    *   Methods: `viewAny(User $user)`, `view(User $user, Order $order)`, `update(User $user, Order $order)` (e.g., for admin updating status).
*   **`app/Policies/CategoryPolicy.php`** (New)
*   **`app/Policies/UserPolicy.php`** (New)
    *   Purpose: Rules for user profile actions, admin managing users.
    *   Methods: `view(User $currentUser, User $targetUser)`, `update(User $currentUser, User $targetUser)`.
*   Registration: Policies must be registered in `App\Providers\AuthServiceProvider`.
    ```php
    // In AuthServiceProvider.php
    protected $policies = [
        Product::class => ProductPolicy::class,
        Order::class => OrderPolicy::class,
        Category::class => CategoryPolicy::class,
        User::class => UserPolicy::class,
        // ... other models
    ];
    ```

#### 4.2.12. `app/Providers/`

Service Providers register services, event listeners, policies, route model bindings, etc.

*   **`app/Providers/AppServiceProvider.php`** (Existing)
    *   Purpose: General application services bootstrapping.
    *   Modifications: Can be used to register custom bindings for Domain services/repositories if not done in dedicated providers.
*   **`app/Providers/AuthServiceProvider.php`** (Existing)
    *   Purpose: Registers authentication/authorization services, policies, gates.
    *   Modifications: Add model policies here (as shown above). Define Gates for specific permissions not tied to models.
*   **`app/Providers/BroadcastServiceProvider.php`** (Existing - if using broadcasting)
*   **`app/Providers/EventServiceProvider.php`** (Existing)
    *   Purpose: Registers event listeners.
    *   Modifications: Map domain events to their respective listeners.
        ```php
        // In EventServiceProvider.php
        protected $listen = [
            \App\Domain\UserManagement\Events\UserRegisteredEvent::class => [
                \App\Listeners\User\SendWelcomeEmailListener::class,
                // Potentially \App\Listeners\User\SubscribeToNewsletterListener::class,
            ],
            \App\Domain\OrderManagement\Events\OrderPlacedEvent::class => [
                \App\Listeners\Order\UpdateInventoryOnOrderPlacedListener::class,
                \App\Listeners\Order\NotifyAdminOnNewOrderListener::class,
                \App\Listeners\Order\SendOrderConfirmationEmailListener::class, // If not handled directly by OrderService
            ],
            // ... other events
        ];
        ```
*   **`app/Providers/RouteServiceProvider.php`** (Existing)
    *   Purpose: Configures routing, rate limiting, route model binding.
    *   Modifications: Ensure API routes are prefixed with `/api/v1`, define rate limiters.
*   **`app/Providers/DomainServiceProvider.php`** (New - Optional)
    *   Purpose: A dedicated provider to register bindings for services and repositories within the `app/Domain` layer. Helps keep `AppServiceProvider` cleaner.
    *   Responsibilities: Bind interfaces to concrete implementations (e.g., `ProductRepositoryContract` to `EloquentProductRepository`).
    *   Code Snippet:
        ```php
        namespace App\Providers;

        use Illuminate\Support\ServiceProvider;
        use App\Domain\Catalog\Contracts\ProductRepositoryContract;
        use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentProductRepository;
        // ... other contracts and implementations

        class DomainServiceProvider extends ServiceProvider
        {
            public function register(): void
            {
                // Catalog Bounded Context
                $this->app->bind(ProductRepositoryContract::class, EloquentProductRepository::class);
                // ... other Catalog bindings

                // OrderManagement Bounded Context
                // $this->app->bind(OrderRepositoryContract::class, EloquentOrderRepository::class);
                // ...
            }
        }
        ```
        *This provider would then be added to `config/app.php`'s `providers` array.*

#### 4.2.13. `app/Rules/`

Custom validation rules.

*   **`app/Rules/ValidSkuFormat.php`** (New)
    *   Purpose: Validates that a SKU follows a specific company format.
*   **`app/Rules/NotProfane.php`** (New)
    *   Purpose: Checks input (e.g., product reviews, user names) against a list of profane words.
*   **`app/Rules/StrongPassword.php`** (New - or use Laravel's built-in `Password` rule object)
    *   Purpose: Enforces password complexity requirements.

#### 4.2.14. `app/Casts/` (New Directory)

Custom Eloquent attribute casting.

*   **`app/Casts/MonetaryAmountCast.php`** (New)
    *   Purpose: Casts a database decimal/integer column (representing minor units) to a `MonetaryAmount` Value Object and back.
    *   Implements: `Illuminate\Contracts\Database\Eloquent\CastsAttributes`.
    *   Code Snippet:
        ```php
        namespace App\Casts;

        use App\Domain\Shared\ValueObjects\MonetaryAmount;
        use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
        use Illuminate\Database\Eloquent\Model;

        class MonetaryAmountCast implements CastsAttributes
        {
            protected string $currencyColumn; // Or a default currency

            // Allow specifying currency column or default currency via constructor
            public function __construct(?string $currencyColumn = null, protected string $defaultCurrency = 'USD')
            {
                $this->currencyColumn = $currencyColumn ?? 'currency_code'; // Default column name to look for
            }

            public function get(Model $model, string $key, mixed $value, array $attributes): ?MonetaryAmount
            {
                if ($value === null) {
                    return null;
                }
                // Determine currency, prefer specific column if available, else attribute, else default
                $currency = $attributes[$this->currencyColumn] ?? $model->{$this->currencyColumn} ?? $this->defaultCurrency;
                return new MonetaryAmount((int) $value, $currency);
            }

            public function set(Model $model, string $key, mixed $value, array $attributes): array
            {
                if ($value === null) {
                    return [$key => null];
                }
                if (!$value instanceof MonetaryAmount) {
                    throw new \InvalidArgumentException('The given value is not a MonetaryAmount instance.');
                }

                // Set both the amount (minor units) and currency if currencyColumn is defined
                $dataToSet = [$key => $value->getMinorAmount()];
                if (array_key_exists($this->currencyColumn, $attributes) || $model->isFillable($this->currencyColumn)) {
                     $dataToSet[$this->currencyColumn] = $value->getCurrencyCode();
                }
                return $dataToSet;
            }
        }
        ```
        *Usage in Model: `protected $casts = ['price_in_cents' => MonetaryAmountCast::class . ':USD', 'subtotal' => MonetaryAmountCast::class.':currency_column_name'];`*

#### 4.2.15. `app/View/Components/`

Custom Blade components for reusable UI elements.

*   **`app/View/Components/Frontend/ProductCard.php`** (New)
    *   Purpose: Renders a product card UI element.
    *   Constructor: Accepts `Product` model instance.
    *   View: `resources/views/components/frontend/product-card.blade.php`.
*   **`app/View/Components/Admin/StatisticWidget.php`** (New)
    *   Purpose: Renders a statistic widget for the admin dashboard.
*   **`app/View/Components/Shared/Modal.php`** (New)
*   **`app/View/Components/Form/Input.php`** (New)
*   **`app/View/Components/Layouts/AppLayout.php`** (New - for frontend main layout)
*   **`app/View/Components/Layouts/AdminLayout.php`** (New - for admin panel layout)

#### 4.2.16. `app/View/Composers/`

View Composers bind data to Blade views when they are rendered.

*   **`app/View/Composers/CategoryComposer.php`** (New)
    *   Purpose: Binds a list of all categories to views that need it (e.g., navigation, product filters).
    *   Registered in a Service Provider (e.g., `AppServiceProvider` or `ViewServiceProvider`).
*   **`app/View/Composers/CartComposer.php`** (New)
    *   Purpose: Binds cart summary (item count, total) to views like the main layout header.

#### 4.2.17. `app/Infrastructure/` (New Directory)

Contains implementations of domain contracts that interact with external systems.

*   **`app/Infrastructure/Persistence/Eloquent/Repositories/`**
    *   **`app/Infrastructure/Persistence/Eloquent/Repositories/EloquentProductRepository.php`** (New)
        *   Purpose: Implements `ProductRepositoryContract` using Eloquent.
        *   Code Snippet:
            ```php
            namespace App\Infrastructure\Persistence\Eloquent\Repositories;

            use App\Domain\Catalog\Contracts\ProductRepositoryContract;
            use App\Domain\Catalog\Models\Product;
            use Illuminate\Database\Eloquent\Collection;

            class EloquentProductRepository implements ProductRepositoryContract
            {
                public function findById(int $id): ?Product
                {
                    return Product::find($id);
                }

                public function findBySlug(string $slug): ?Product
                {
                    return Product::where('slug', $slug)->first();
                }

                public function getFeatured(int $limit = 4): Collection
                {
                    return Product::where('is_featured', true)->where('is_active', true)->take($limit)->get();
                }
                // ... other methods
            }
            ```
*   **`app/Infrastructure/PaymentGateways/Stripe/`**
    *   Contains specific classes for Stripe integration if `StripeGateway` becomes too complex.
*   **`app/Infrastructure/Search/Algolia/`** (Future)
    *   Algolia-specific search client implementations.

### 4.3. `bootstrap/` Directory

Standard Laravel directory. `app.php` is key. `providers.php` is new in Laravel 11+ for eager loading providers.

*   **`bootstrap/app.php`**: (Existing) Core application bootstrapping. Configures middleware, exception handling, service providers.
*   **`bootstrap/providers.php`**: (Existing) For auto-discovered and manually registered eager-loaded providers.

### 4.4. `config/` Directory

Application configuration files.

*   **`config/app.php`**: (Existing) Core app settings, timezone, locale, aliases, providers.
    *   Modifications: Add `DomainServiceProvider::class` if created. Update `APP_NAME` from `.env`.
*   **`config/auth.php`**: (Existing) Authentication guards and providers.
    *   Modifications: Ensure `providers.users.model` points to `App\Domain\UserManagement\Models\User::class`.
*   **`config/database.php`**: (Existing) Database connections.
    *   Modifications: Ensure `'mysql'` (for MariaDB) connection is correctly configured to use environment variables.
*   **`config/filesystems.php`**: (Existing) Configure disks (local, public, s3).
    *   Modifications: Setup 's3' disk if using AWS S3 for product images.
*   **`config/mail.php`**: (Existing) Mail driver settings.
*   **`config/queue.php`**: (Existing) Queue connection settings.
*   **`config/services.php`**: (Existing) Credentials for third-party services.
    *   Modifications: Add entries for Stripe, PayPal, Mailgun, Socialite providers.
        ```php
        // config/services.php
        'stripe' => [
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
            'webhook' => [
                'secret' => env('STRIPE_WEBHOOK_SECRET'),
                'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
            ],
        ],
        'mailgun' => [
            'domain' => env('MAILGUN_DOMAIN'),
            'secret' => env('MAILGUN_SECRET'),
            'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        ],
        // ... socialite providers (google, facebook etc.)
        ```
*   **`config/cors.php`**: (Existing) Cross-Origin Resource Sharing settings for API.
*   **`config/sanctum.php`**: (Existing) Laravel Sanctum configuration (API tokens, SPA auth).
*   **`config/thescent.php`** (New - Custom)
    *   Purpose: Application-specific configuration values for "The Scent".
    *   Examples: Default number of products per page, currency settings, feature flags (if not purely env-based).
    *   Code Snippet:
        ```php
        // config/thescent.php
        return [
            'products_per_page' => env('THESCENT_PRODUCTS_PER_PAGE', 12),
            'default_currency' => env('THESCENT_DEFAULT_CURRENCY', 'USD'),
            'low_stock_notification_email' => env('THESCENT_LOW_STOCK_EMAIL', 'admin@example.com'),
            'features' => [
                'reviews' => env('THESCENT_FEATURE_REVIEWS', true),
                'wishlist' => env('THESCENT_FEATURE_WISHLIST', false),
            ],
            // Tax settings if simple
            'tax' => [
                'default_rate_percentage' => env('THESCENT_DEFAULT_TAX_RATE', 0), // e.g., 7 for 7%
                'tax_based_on' => env('THESCENT_TAX_BASED_ON', 'shipping_address'), // shipping_address, billing_address
            ],
        ];
        ```

### 4.5. `database/` Directory

*   **`database/factories/`**: Model factories for seeding and testing.
    *   **`database/factories/UserFactory.php`**: (Existing) To be updated for `App\Domain\UserManagement\Models\User::class`.
    *   **`database/factories/ProductFactory.php`**: (New) For `App\Domain\Catalog\Models\Product::class`.
    *   **`database/factories/CategoryFactory.php`**: (New)
    *   **`database/factories/OrderFactory.php`**: (New)
    *   **`database/factories/AddressFactory.php`**: (New)
*   **`database/migrations/`**: Database schema migrations.
    *   Standard Laravel migrations for users, cache, jobs will exist.
    *   New migrations will be created based on `sample_database_schema.sql.txt` and the ERD from `README.md`. This includes:
        *   `create_categories_table.php`
        *   `create_products_table.php`
        *   `create_product_attributes_table.php`
        *   `create_inventory_movements_table.php`
        *   `create_addresses_table.php` (if making a dedicated addresses table, which is recommended over user table fields for orders)
        *   `create_orders_table.php`
        *   `create_order_items_table.php`
        *   `create_cart_items_table.php` (and `carts` table if user carts are DB backed)
        *   `create_quiz_results_table.php`
        *   `create_newsletter_subscribers_table.php`
        *   `create_audit_log_table.php`
        *   `create_email_log_table.php`
        *   `create_tax_rates_table.php`
        *   `create_tax_rate_history_table.php`
        *   `create_coupons_table.php` (if implementing coupons)
    *   Each migration file will define `up()` and `down()` methods using `Schema` builder.
    *   Example Migration Snippet (`create_products_table.php`):
        ```php
        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;

        return new class extends Migration
        {
            public function up(): void
            {
                Schema::create('products', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
                    $table->string('name', 150);
                    $table->string('slug', 200)->unique();
                    $table->text('description')->nullable();
                    $table->text('short_description')->nullable()->comment('Brief description for listings/previews');
                    $table->string('image')->nullable(); // Main image path
                    $table->json('gallery_images')->nullable()->comment('JSON array of additional image paths');
                    $table->unsignedInteger('price')->comment('Price in minor units (e.g., cents)'); // Storing as integer for MonetaryAmountCast
                    $table->string('currency_code', 3)->default('USD'); // To be used with price
                    $table->json('benefits')->nullable()->comment('Product benefits, stored as JSON array of strings');
                    $table->text('ingredients')->nullable()->comment('List of key ingredients');
                    $table->text('usage_instructions')->nullable()->comment('How to use the product');
                    $table->boolean('is_featured')->default(false);
                    $table->boolean('is_active')->default(true);
                    $table->integer('stock_quantity')->default(0);
                    $table->integer('low_stock_threshold')->default(20);
                    $table->integer('reorder_point')->default(30);
                    $table->boolean('backorder_allowed')->default(false)->comment('Allow purchase when stock_quantity <= 0');
                    $table->string('highlight_text', 50)->nullable();
                    $table->string('size', 50)->nullable()->comment('e.g., 10ml, 100g');
                    $table->string('scent_profile', 255)->nullable();
                    $table->string('origin', 100)->nullable();
                    $table->string('sku', 100)->nullable()->unique();
                    $table->timestamps();
                });
            }

            public function down(): void
            {
                Schema::dropIfExists('products');
            }
        };
        ```
*   **`database/seeders/`**: Database seeders.
    *   **`database/seeders/DatabaseSeeder.php`**: (Existing) Calls other seeders.
    *   **`database/seeders/UserSeeder.php`**: (New) Creates admin user, sample customers.
    *   **`database/seeders/CategorySeeder.php`**: (New)
    *   **`database/seeders/ProductSeeder.php`**: (New) Creates sample products using factories.
    *   **`database/seeders/OrderSeeder.php`**: (New) Creates sample orders for testing.
    *   **`database/seeders/TaxRateSeeder.php`**: (New) Seeds some default tax rates.

### 4.6. `public/` Directory

Web server document root.

*   **`public/index.php`**: (Existing) Laravel front controller. This is the correct entry point.
*   **`public/storage/`**: Symlink created by `php artisan storage:link` to `storage/app/public/`. For publicly accessible user uploads (e.g., product images if local driver).
*   **`public/build/`**: Contains compiled frontend assets (CSS, JS) by Vite. This directory is generated by `npm run build`.
*   **`public/favicons/`**: (New) Favicons for various platforms.
*   `sample_landing_page.html` and its assets will be integrated into Blade views and Vite's asset pipeline, not served directly from `public/` as standalone HTML.

### 4.7. `resources/` Directory

Frontend source files.

*   **`resources/css/`**
    *   **`resources/css/app.css`**: (Existing) Main CSS entry point. Imports Tailwind CSS. Custom CSS variables and base styles for light/dark mode will be defined here, referencing `sample_landing_page.html` styles.
        ```css
        /* resources/css/app.css */
        @import 'tailwindcss/base';
        @import 'tailwindcss/components';
        @import 'tailwindcss/utilities';

        :root {
          /* Light Mode Palette (from sample_landing_page.html) */
          --clr-bg: #f8f7f4;
          --clr-text: #333d41;
          --clr-primary: #2a7c8a;
          --clr-accent: #e0a86f;
          /* ... other light mode vars */
          --font-head: 'Cormorant Garamond', serif;
          --font-body: 'Montserrat', sans-serif;
          --font-accent: 'Raleway', sans-serif;
        }

        html.dark {
          /* Dark Mode Palette (from sample_landing_page.html) */
          --clr-bg: #1a202c;
          --clr-text: #e2e8f0;
          --clr-primary: #4fd1c5;
          --clr-accent: #f6ad55;
          /* ... other dark mode vars */
        }

        body {
          font-family: var(--font-body);
          background-color: var(--clr-bg);
          color: var(--clr-text);
          /* ... other base styles */
        }
        /* Other global styles adapted from sample_landing_page.html */
        ```
*   **`resources/js/`**
    *   **`resources/js/app.js`**: (Existing) Main JavaScript entry point. Imports Alpine.js, custom JS modules. Logic for dark mode toggle, ambient audio, parallax, quiz from `sample_landing_page.html` will be modularized here.
        ```javascript
        // resources/js/app.js
        import './bootstrap'; // Includes Axios typically

        import Alpine from 'alpinejs';
        window.Alpine = Alpine;
        Alpine.start();

        // Import custom JS modules
        import './dark-mode';
        import './ambient-audio';
        import './parallax-init';
        import './scent-quiz';
        import './mobile-nav'; // If needed
        import './form-handlers'; // For newsletter etc.

        console.log('The Scent JS initialized.');
        ```
    *   **`resources/js/bootstrap.js`**: (Existing) Sets up Axios, CSRF token.
    *   **`resources/js/dark-mode.js`**: (New) Contains the dark mode toggle logic from `sample_landing_page.html`.
    *   **`resources/js/ambient-audio.js`**: (New) Contains ambient audio toggle logic.
    *   **`resources/js/parallax-init.js`**: (New) Parallax effect initialization for elements.
    *   **`resources/js/scent-quiz.js`**: (New) Scent quiz logic.
*   **`resources/views/`**: Blade templates.
    *   **`resources/views/layouts/`**
        *   **`app.blade.php`**: Main frontend layout (header, footer, nav). Will adapt structure from `sample_landing_page.html`.
        *   **`admin.blade.php`**: Admin panel layout.
        *   **`guest.blade.php`**: Layout for auth pages (login, register).
        *   **`partials/frontend/header.blade.php`**: Frontend header.
        *   **`partials/frontend/footer.blade.php`**: Frontend footer.
        *   **`partials/frontend/navigation.blade.php`**: Frontend navigation.
    *   **`resources/views/components/`**: Blade components.
        *   (e.g., `frontend/product-card.blade.php`, `shared/modal.blade.php`)
    *   **`resources/views/frontend/`** (Directory for customer-facing pages)
        *   **`home.blade.php`**: Landing page content, adapting `sample_landing_page.html` sections (Hero, About, Products, Quiz, Testimonials, Newsletter).
        *   **`products/index.blade.php`**: Product listing page.
        *   **`products/show.blade.php`**: Product detail page.
        *   **`cart/index.blade.php`**: Shopping cart page.
        *   **`checkout/index.blade.php`**: Checkout page.
        *   **`checkout/success.blade.php`**: Order success page.
        *   **`account/dashboard.blade.php`**: User account dashboard.
        *   **`account/orders.blade.php`**: User order history.
        *   **`account/orders_show.blade.php`**: User view single order.
        *   **`account/profile.blade.php`**: User profile edit page.
        *   **`quiz/show.blade.php`**: Scent quiz page.
    *   **`resources/views/auth/`**: Authentication views (login, register, password reset). Provided by Breeze/Fortify.
    *   **`resources/views/admin/`**: Admin panel views.
        *   (e.g., `dashboard.blade.php`, `products/index.blade.php`, `products/edit.blade.php`)
    *   **`resources/views/emails/`**: Blade templates for emails.
        *   (e.g., `orders/confirmation.blade.php`, `auth/welcome.blade.php`)
    *   **`welcome.blade.php`**: (Existing) Default Laravel welcome page. Will be replaced by `frontend/home.blade.php`. The existing `resources/views/welcome.blade.php` (82KB) seems to be a direct copy of the sample landing page HTML and will be refactored into proper Blade components and layouts.
*   **`resources/lang/`**: Localization files.
    *   **`resources/lang/en/`**: English language strings.
        *   `en.json` or PHP array files (e.g., `validation.php`, `messages.php`).
    *   Additional locale directories (e.g., `es/`) for other languages.

### 4.8. `routes/` Directory

Route definitions.

*   **`routes/web.php`**: (Existing) Routes for web UI (frontend and admin). Authenticated routes, guest routes, admin routes grouped with middleware.
    ```php
    // routes/web.php
    use App\Http\Controllers\Frontend; // Assuming grouped controllers
    use App\Http\Controllers\Admin;
    use App\Http\Controllers\Auth; // Or Fortify routes

    // Frontend Routes
    Route::get('/', [Frontend\HomeController::class, 'index'])->name('home');
    Route::get('/about', [Frontend\HomeController::class, 'about'])->name('about');
    // ... other static pages

    Route::get('/products', [Frontend\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product:slug}', [Frontend\ProductController::class, 'show'])->name('products.show');
    // Category routes, Tag routes

    Route::get('/cart', [Frontend\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{productVariantId}', [Frontend\CartController::class, 'store'])->name('cart.store');
    // ... other cart actions (update, remove)

    Route::get('/checkout', [Frontend\CheckoutController::class, 'index'])->name('checkout.index')->middleware('auth.cart.notempty'); // Custom middleware
    Route::post('/checkout', [Frontend\CheckoutController::class, 'store'])->name('checkout.store')->middleware('auth');

    Route::get('/quiz', [Frontend\QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quiz', [Frontend\QuizController::class, 'submit'])->name('quiz.submit');

    Route::post('/newsletter/subscribe', [Frontend\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

    // Auth Routes (Laravel Fortify/Breeze usually handles this or provides routes to include)
    // Example: require __DIR__.'/auth.php';

    // Authenticated User Account Routes
    Route::middleware(['auth', 'verified'])->prefix('account')->name('account.')->group(function () {
        Route::get('/dashboard', [Frontend\AccountController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [Frontend\AccountController::class, 'orders'])->name('orders.index');
        Route::get('/orders/{order}', [Frontend\AccountController::class, 'showOrder'])->name('orders.show');
        Route::get('/profile', [Frontend\AccountController::class, 'profile'])->name('profile.edit');
        Route::put('/profile', [Frontend\AccountController::class, 'updateProfile'])->name('profile.update');
        // ... addresses, etc.
    });

    // Admin Routes
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () { // 'admin' is custom middleware
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', Admin\ProductController::class);
        Route::resource('categories', Admin\CategoryController::class);
        Route::resource('orders', Admin\OrderController::class)->only(['index', 'show', 'update']);
        Route::resource('users', Admin\UserController::class)->only(['index', 'show', 'edit', 'update']);
        Route::resource('tax-rates', Admin\TaxRateController::class);
        // ... other admin resources
    });
    ```
*   **`routes/api.php`**: (Existing) Routes for the RESTful API (versioned, e.g., `/v1/...`).
    ```php
    // routes/api.php
    use App\Http\Controllers\Api\V1; // Assuming grouped API controllers

    Route::prefix('v1')->name('api.v1.')->group(function () {
        // Public API routes
        Route::get('/products', [V1\ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product:slug}', [V1\ProductController::class, 'show'])->name('products.show');
        // ... categories, etc.

        // Auth API routes
        Route::post('/auth/login', [V1\AuthController::class, 'login'])->name('auth.login');
        Route_::post('/auth/register', [V1\AuthController::class, 'register'])->name('auth.register');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/auth/logout', [V1\AuthController::class, 'logout'])->name('auth.logout');
            Route::get('/user', [V1\AuthController::class, 'user'])->name('auth.user');

            // Cart API
            Route::get('/cart', [V1\CartController::class, 'index'])->name('cart.index');
            Route::post('/cart/items', [V1\CartController::class, 'storeItem'])->name('cart.items.store');
            // ... update, delete cart items

            // Checkout API
            Route::post('/checkout/prepare', [V1\CheckoutController::class, 'prepare'])->name('checkout.prepare');
            Route::post('/checkout/complete', [V1\CheckoutController::class, 'complete'])->name('checkout.complete'); // After client-side payment confirmation

            // Order API (for authenticated user)
            Route::get('/orders', [V1\OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [V1\OrderController::class, 'show'])->name('orders.show');
        });
    });

    // Webhook routes - outside auth middleware, often with specific CSRF exemption for webhooks
    Route::post('/webhooks/stripe', [App\Http\Controllers\Webhook\StripeWebhookController::class, 'handleWebhook'])->name('webhooks.stripe');
    ```
*   **`routes/console.php`**: (Existing) Artisan command closures/bindings.
*   **`routes/channels.php`**: (Existing) Event broadcasting channel authorization routes (if using Laravel Echo/WebSockets).

### 4.9. `storage/` Directory

Standard Laravel storage. Permissions are critical here. `storage/app/public` is for user-uploaded files that need to be publicly accessible (symlinked to `public/storage`).

### 4.10. `tests/` Directory

Test files.

*   **`tests/CreatesApplication.php`**: (Existing)
*   **`tests/TestCase.php`**: (Existing) Base test case class.
*   **`tests/Feature/`**: Feature tests.
    *   (e.g., `Auth/RegistrationTest.php`, `Frontend/ProductBrowsingTest.php`, `Admin/ProductManagementTest.php`)
*   **`tests/Unit/`**: Unit tests.
    *   (e.g., `Domain/Catalog/Models/ProductTest.php`, `Domain/Shared/ValueObjects/MonetaryAmountTest.php`)
*   **`tests/Browser/`**: (If using Laravel Dusk) Browser tests.
*   **`cypress/` directory at project root**: (If using Cypress) For E2E tests.

### 4.11. Root Files

*   **`.env`**: (Local) Local environment configuration. **NOT IN GIT.**
*   **`.env.example`**: (Existing) Template for `.env`. **IN GIT.**
    *   Modifications: Add all new environment variables needed (Stripe keys, Mailgun, custom app settings).
*   **`artisan`**: (Existing) Laravel command-line interface.
*   **`composer.json`**: (Existing) PHP dependencies.
    *   Modifications: Add `brick/money`, `spatie/laravel-data` (optional for DTOs), `spatie/laravel-sitemap`, `spatie/laravel-medialibrary` (if chosen for image handling), `stripe/stripe-php`, potentially `laravel/cashier`.
*   **`package.json`**: (Existing) Node.js dependencies.
    *   Modifications: `tailwindcss`, `alpinejs`, `postcss`, `autoprefixer` are standard. Add `font-awesome` (if using npm package) or ensure CDN link is in layout. Add `cypress` for E2E tests.
*   **`vite.config.js`**: (Existing) Vite configuration.
    *   Modifications: Ensure Tailwind CSS is processed correctly, paths are set up for `app.css` and `app.js`.
*   **`tailwind.config.js`**: (New, or modify if existing from Breeze/Jetstream) Tailwind CSS configuration.
    *   Purpose: Define custom theme (colors, fonts from `sample_landing_page.html`), plugins.
    *   Code Snippet:
        ```javascript
        // tailwind.config.js
        import defaultTheme from 'tailwindcss/defaultTheme';

        export default {
            content: [
                './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
                './storage/framework/views/*.php',
                './resources/views/**/*.blade.php',
                './resources/js/**/*.js', // If using JS to manipulate classes
            ],
            darkMode: 'class', // Enable class-based dark mode
            theme: {
                extend: {
                    colors: {
                        'scent-bg': 'var(--clr-bg)',
                        'scent-text': 'var(--clr-text)',
                        'scent-primary': 'var(--clr-primary)',
                        'scent-accent': 'var(--clr-accent)',
                        'scent-cta': 'var(--clr-cta)',
                        // Define other custom colors from sample_landing_page.html variables
                    },
                    fontFamily: {
                        sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
                        serif: ['Cormorant Garamond', ...defaultTheme.fontFamily.serif],
                        accent: ['Raleway', ...defaultTheme.fontFamily.sans],
                    },
                },
            },
            plugins: [
                require('@tailwindcss/forms'), // For basic form styling resets
                require('@tailwindcss/typography'), // For prose styling if needed
            ],
        };
        ```
*   **`pint.json`**: (Existing or New) Laravel Pint configuration for code style.
*   **`phpstan.neon`**: (New) PHPStan configuration for static analysis.
*   **`.gitignore`**: (Existing) Ensure `/.env`, `/public/build`, `/public/storage`, `/storage/*.key`, `node_modules/`, `vendor/` are ignored.

## 5. UI/UX Design Implementation Notes

The `sample_landing_page.html` provides a strong visual and interactive direction. This will be translated into Blade templates, Tailwind CSS, and Alpine.js as follows:

*   **Layout:** The main structure (header, hero, sections, footer, curve separators) will be in `resources/views/layouts/app.blade.php` and partials (`partials/frontend/*`).
*   **Styling:**
    *   CSS variables from `:root` and `body.dark` in `sample_landing_page.html` will be defined in `resources/css/app.css`.
    *   Tailwind utility classes will be used extensively in Blade templates to replicate the design. The `tailwind.config.js` will be customized with the color palette and fonts.
    *   Complex or repeated style patterns will be extracted into Tailwind CSS `@apply` directives for components in `app.css` or Blade components.
*   **Interactivity:**
    *   **Dark Mode Toggle:** Logic from `sample_landing_page.html`'s `<script>` tag will be moved to `resources/js/dark-mode.js` and initialized in `app.js`. An Alpine.js component will manage the `<html>` class and button icon.
    *   **Ambient Audio Toggle:** Logic moved to `resources/js/ambient-audio.js`, controlled by an Alpine.js component.
    *   **Parallax Hero/Image:** CSS-based parallax will be attempted first. If JS is needed, the logic from `sample_landing_page.html` will be refined in `resources/js/parallax-init.js`.
    *   **Mist Animation:** SVG and CSS animations will be directly embedded or included via Blade partials.
    *   **Scent Quiz:** The multi-step form logic will be managed by an Alpine.js component in `quiz/show.blade.php`, with JS logic in `resources/js/scent-quiz.js`. AJAX will be used for submitting answers and fetching results if needed (though simple step progression can be client-side).
    *   **Ingredient Markers:** Tooltip logic will be handled by Alpine.js or simple CSS `:hover`/`:focus-visible`.
    *   **Testimonial Fade-in:** Intersection Observer logic from `sample_landing_page.html` will be used.
    *   **Newsletter Form:** Basic HTML form, submission handled via Alpine.js and `fetch` to a backend route, as in the sample script.
*   **Responsive Design:** Tailwind's responsive prefixes (`sm:`, `md:`, `lg:`) will be used to match the media queries in `sample_landing_page.html`.
*   **Icons:** Font Awesome will be integrated, likely via CDN link in `app.blade.php` or by installing the npm package and importing into `app.css`.
*   **Blade Components:** Reusable UI elements like product cards, modals, form inputs, section headers will be extracted into Blade components (`resources/views/components/`).

## 6. API Design Summary (Recap from README)

*   **RESTful API:** Located under `/api/v1/`.
*   **Authentication:** Laravel Sanctum for token-based auth (`Authorization: Bearer <token>`).
*   **Responses:** JSON, using Laravel API Resources for consistent structure (data, links, meta).
*   **Key Endpoints (examples):**
    *   `GET /products`, `GET /products/{slug}`
    *   `POST /auth/login`, `POST /auth/register`, `POST /auth/logout` (Sanctum)
    *   `GET /cart`, `POST /cart/items`, `PUT /cart/items/{id}`, `DELETE /cart/items/{id}` (auth:sanctum)
    *   `POST /checkout/prepare`, `POST /checkout/complete` (auth:sanctum)
    *   `GET /orders`, `GET /orders/{id}` (auth:sanctum)
*   **Webhooks:**
    *   `POST /webhooks/stripe` (secured by signature verification).
*   **Rate Limiting:** Applied via Laravel's throttle middleware.
*   **Documentation:** OpenAPI spec (e.g., `docs/openapi.yaml`), generated or manually written.

## 7. Database Schema Mapping Summary (Recap from README & Sample SQL)

The database schema defined in `sample_database_schema.sql.txt` will be the primary source for migrations, augmented by the ERD in `README.md` where necessary (e.g., a dedicated `addresses` table, `coupons` table).

*   **Key Tables:** `users`, `products`, `categories`, `product_attributes`, `orders`, `order_items`, `cart_items`, `audit_log`, `email_log`, `inventory_movements`, `newsletter_subscribers`, `quiz_results`, `tax_rates`, `tax_rate_history`.
*   **Relationships:** Foreign keys will be established as per the schema.
*   **Data Types:** Will match the SQL schema (e.g., `VARCHAR`, `TEXT`, `INT`, `DECIMAL`, `TIMESTAMP`, `ENUM`, `JSON`). `DECIMAL` for currency is typically handled as integers (minor units) in application logic with custom casts for `MonetaryAmount` or string/float for direct Eloquent decimal casting. The sample schema `products.price` is `DECIMAL(10,2)`. We will adapt the `MonetaryAmountCast` or use Eloquent's decimal casting for this.
*   **Indexes:** Will be created on foreign keys and frequently queried columns.
*   The `manatee_laravel_full_backup.sql` implies some existing data or structure but will be superseded by new migrations based on `sample_database_schema.sql.txt` for a clean build.

## 8. Deployment Strategy (High-Level)

As per `README.md`, deployment to Fly.io or AWS Lightsail. This document focuses on application structure, but Dockerfiles and `fly.toml` configurations will be critical for Fly.io. For Lightsail, a more traditional LAMP stack setup with Supervisor for queues will be needed. Both will require setting environment variables securely.

## 9. Testing Strategy (High-Level)

*   **Unit Tests:** For Domain logic (Services, Value Objects, Actions) using Pest/PHPUnit. Mock dependencies.
*   **Feature Tests:** For API endpoints and web routes using Laravel's HTTP testing. Interact with an in-memory SQLite database or test database with transactions.
*   **E2E Tests:** Using Cypress for key user flows.
*   **Static Analysis:** PHPStan.
*   **Code Style:** Pint.
*   CI/CD via GitHub Actions will run linters, static analysis, and tests.

## 10. Conclusion

This technical design document provides a comprehensive blueprint for developing "The Scent" e-commerce platform. It emphasizes a modular, domain-centric architecture within the Laravel framework, aiming for a high-quality, maintainable, and extensible codebase. The detailed file structure and component descriptions should guide the development process effectively, translating the project's vision into a functional and elegant application. The next step is to create a detailed execution plan based on this design.

---
https://drive.google.com/file/d/149SQdFIsEyVGZtL5cHAjDSnFwfrufK4-/view?usp=sharing, https://aistudio.google.com/app/prompts?state=%7B%22ids%22:%5B%22191Xwb664N58hqofAV7tgOqIhw9Q_EXtW%22%5D,%22action%22:%22open%22,%22userId%22:%22103961307342447084491%22,%22resourceKeys%22:%7B%7D%7D&usp=sharing, https://drive.google.com/file/d/1JMi0sos9Z0VPmPtV_VUQdOiHP55dgoyM/view?usp=sharing, https://drive.google.com/file/d/1Ljou3KM9wH_n2IISZRUL5Usykg-a8yaZ/view?usp=sharing, https://drive.google.com/file/d/1UHbFpE2rY7aLNsT7R5-duOpJcgpWOoUh/view?usp=sharing, https://drive.google.com/file/d/1dppi2D-RTdYBal_YeLZxB4QHysdmQxpD/view?usp=sharing, https://drive.google.com/file/d/1nPblih0JaSTmarZmo2budApC3JXa64hP/view?usp=sharing
