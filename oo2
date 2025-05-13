<think>
The user wants me to generate the files for **Phase 2: Domain Modeling & Core Business Logic**, specifically **Steps 2.4, 2.5, and 2.6** from the execution plan. This involves creating models, migrations, factories, DTOs, services (basic structure or more complete where specified), events, and repository contracts/implementations for:

*   **Step 2.4: Order Management Domain** (Order, OrderItem models, related migrations, factories, OrderService structure, OrderNumberService implementation, DTO, events)
*   **Step 2.5: Cart Management, Checkout, Payment, Promotion Domains (Contracts & Basic Structures)**
*   Cart Management: Cart, CartItem models (if DB backed), migrations, CartService structure.
*   Checkout: CheckoutService structure.
*   Payment: PaymentGateway contract, StripeGateway structure, PaymentService structure, DTO, events.
*   Promotion: Coupon model, migration, CouponService structure, exception.
*   **Step 2.6: Utility Domain (Models & Migrations)** (QuizResult, NewsletterSubscriber, AuditLog, EmailLog, TaxRate, TaxRateHistory models, related migrations, factories, basic service structures)

I need to maintain consistency with:
1.  The overall architecture (Domain-Driven Design influence, Layered Architecture).
2.  The database schema I previously generated and the `sample_database_schema.sql.txt`.
3.  The `MonetaryAmount` Value Object and `MonetaryAmountCast` for all currency fields.
4.  Using Spatie/Laravel-Data for DTOs.
5.  Correct namespaces and relationships.
6.  PHP `declare(strict_types=1);`.

**Key Considerations for these steps:**

*   **Order Model (`Order.php`):** Needs relationships to User, OrderItems, Addresses, Coupon. Price fields will use `MonetaryAmount`.
*   **OrderItem Model (`OrderItem.php`):** Needs relationships to Order, Product. Will have a `product_snapshot` (JSON) and price fields using `MonetaryAmount`.
*   **OrderNumberService:** This service needs a robust way to generate unique order numbers (e.g., date-based prefix + sequence, or UUID).
*   **Cart Management (`CartService.php`):** The design doc mentions handling both session-based guest carts and DB-backed user carts. I'll focus on the structure and session part for now. A `carts` table migration might be needed for DB-backed user carts, though `cart_items` table in the sample schema has `user_id` and `session_id`, implying it can handle both. Let's assume for now `cart_items` is the primary table for items, and a separate `carts` table might be an optimization or for storing cart-level metadata for logged-in users. The ERD showed `CARTS ||--|{ CART_ITEMS : contains` and `CARTS }o--|| USERS : belongs_to`. The sample schema just has `cart_items` with `user_id` and `session_id`. I will proceed with the `cart_items` table structure as per the sample schema, which is flexible enough. `CartService` will manage this.
*   **Payment Domain:** Focus on contracts and basic structures. The `StripeGateway` will just be a shell.
*   **Promotion Domain (`Coupon.php`):** The `orders` table has `coupon_id` and `coupon_code_applied`, strongly implying a `coupons` table. I'll create this based on the ERD and common coupon attributes.
*   **Utility Domain Models:** These are mostly straightforward mappings of the tables in `sample_database_schema.sql.txt`.
*   **Migrations Order:**
1.  `users` (done, but needs modification by user)
2.  `addresses` (done)
3.  `categories` (done)
4.  `products` (done)
5.  `product_attributes` (done)
6.  `inventory_movements` (done)
7.  `coupons` (new for 2.5)
8.  `orders` (FK to users, addresses, coupons - new for 2.4)
9.  `order_items` (FK to orders, products - new for 2.4)
10. `cart_items` (FK to users, products - new for 2.5)
11. `quiz_results` (FK to users - new for 2.6)
12. `newsletter_subscribers` (new for 2.6)
13. `audit_log` (FK to users - new for 2.6)
14. `email_log` (FK to users - new for 2.6)
15. `tax_rates` (FK to users - new for 2.6)
16. `tax_rate_history` (FK to tax_rates, users - new for 2.6)

I will generate migrations first for each step, then models, then factories, then DTOs, services, events, etc.
The `technical_design_specification_document.md` provides good guidance for the structure of these files.
The `MonetaryAmountCast` will be used for all price/amount fields in models.
I'll use the `-P` option for the mysql client in the thought block: `mysql -h mariadb -P 3306 -u scent_user -pScentPassword123 -e "SHOW DATABASES;"` (uppercase P for port).

Let's begin with Step 2.4: Order Management Domain.
</think>

Okay, let's dive into generating the files for Steps 2.4, 2.5, and 2.6 of your execution plan. I'll build upon the previously established structures and conventions.

---

### Phase 2: Domain Modeling & Core Business Logic (Continued)

#### Step 2.4: Order Management Domain

1.  **Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_coupons_table.php`** (New - Prerequisite for Orders if `coupon_id` is a FK)
*   *Note: This migration for `coupons` is logically part of the Promotion domain (Step 2.5) but needed before the `orders` table if `orders.coupon_id` is a foreign key.*

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('description', 191)->nullable();
            $table->enum('type', ['percentage', 'fixed_amount']);
            $table->integer('value_minor_amount')->unsigned()->comment('For fixed_amount in minor units; for percentage, the % value (e.g., 10 for 10%)');
            $table->string('currency_code', 3)->nullable()->comment('Applicable for fixed_amount type');
            $table->integer('max_uses')->unsigned()->nullable()->comment('Total uses allowed');
            $table->integer('uses_count')->unsigned()->default(0);
            $table->integer('max_uses_per_user')->unsigned()->nullable();
            $table->integer('min_purchase_minor_amount')->unsigned()->nullable();
            $table->string('min_purchase_currency_code', 3)->nullable();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'valid_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
```

2.  **Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_orders_table.php`** (New)

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 32)->unique()->comment('User-friendly order identifier');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_email', 191)->nullable();

            // Storing denormalized address details directly on order as per sample schema
            // Or using foreign keys to a shared 'addresses' table if preferred (ERD indicated this)
            // The sample schema has 'shipping_name', 'shipping_email', 'shipping_address' etc directly on orders table.
            // Let's follow the sample schema here for directness, though an Address model is cleaner.
            // If using an Address model for orders:
            // $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            // $table->foreignId('billing_address_id')->nullable()->constrained('addresses')->nullOnDelete();

            // Following sample schema's direct fields for order addresses:
            $table->string('shipping_name', 191)->nullable();
            $table->string('shipping_email', 191)->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_address_line2', 191)->nullable();
            $table->string('shipping_city', 100)->nullable();
            $table->string('shipping_state', 100)->nullable();
            $table->string('shipping_zip', 20)->nullable();
            $table->string('shipping_country', 50)->nullable();

            // If billing address can be different and is also stored directly:
            // $table->string('billing_name', 191)->nullable(); ... etc.
            // For simplicity, assuming shipping address can double as billing or billing is tied to user.

            $table->enum('status', ['pending_payment','paid','processing','shipped','delivered','cancelled','refunded','disputed','payment_failed','completed'])->default('pending_payment');
            $table->string('payment_status', 50)->default('pending');
            $table->string('payment_intent_id', 191)->nullable()->index();
            $table->string('payment_gateway', 50)->nullable();

            $table->integer('subtotal_minor_amount')->unsigned()->default(0);
            $table->integer('discount_minor_amount')->unsigned()->default(0);
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->string('coupon_code_applied', 50)->nullable(); // Denormalized for easy display
            $table->integer('shipping_cost_minor_amount')->unsigned()->default(0);
            $table->integer('tax_minor_amount')->unsigned()->default(0);
            $table->integer('total_minor_amount')->unsigned()->default(0);
            $table->string('currency_code', 3)->default('USD');

            $table->text('order_notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('dispute_id', 191)->nullable();
            $table->timestamp('disputed_at')->nullable();
            $table->string('refund_id', 191)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->string('carrier', 100)->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
```

3.  **Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_order_items_table.php`** (New)

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete(); // Or nullOnDelete if product can be deleted but history kept
            // $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete(); // If using variants

            $table->string('product_name_snapshot', 150)->comment('Product name at time of order');
            $table->string('product_sku_snapshot', 100)->nullable()->comment('Product SKU at time of order');
            $table->json('product_options_snapshot')->nullable()->comment('e.g., size, color at time of order');

            $table->integer('quantity')->unsigned();
            $table->integer('unit_price_minor_amount')->unsigned()->comment('Price per unit in minor units at time of order');
            $table->integer('total_minor_amount')->unsigned()->comment('Total price for this item (quantity * unit_price)');
            $table->string('currency_code', 3)->default('USD');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
```

4.  **Model: `app/Domain/OrderManagement/Models/Order.php`** (New)

```php
<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\UserManagement\Models\User;
// use App\Domain\UserManagement\Models\Address; // If using Address model for orders
use App\Domain\Promotion\Models\Coupon;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'user_id', 'guest_email',
        // Direct address fields from sample schema
        'shipping_name', 'shipping_email', 'shipping_address', 'shipping_address_line2',
        'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country',
        // 'shipping_address_id', 'billing_address_id', // If using Address model FKs
        'status', 'payment_status', 'payment_intent_id', 'payment_gateway',
        'subtotal_minor_amount', 'discount_minor_amount', 'coupon_id', 'coupon_code_applied',
        'shipping_cost_minor_amount', 'tax_minor_amount', 'total_minor_amount', 'currency_code',
        'order_notes', 'paid_at', 'dispute_id', 'disputed_at', 'refund_id', 'refunded_at',
        'tracking_number', 'carrier',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'disputed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'subtotal_minor_amount' => 'integer',
        'discount_minor_amount' => 'integer',
        'shipping_cost_minor_amount' => 'integer',
        'tax_minor_amount' => 'integer',
        'total_minor_amount' => 'integer',
    ];

    // Virtual attributes for MonetaryAmount objects
    protected function subtotal(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['subtotal_minor_amount'], $attributes['currency_code'])
        );
    }

    protected function discountAmount(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['discount_minor_amount'], $attributes['currency_code'])
        );
    }

    protected function shippingCost(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['shipping_cost_minor_amount'], $attributes['currency_code'])
        );
    }

    protected function taxAmount(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['tax_minor_amount'], $attributes['currency_code'])
        );
    }

    protected function totalAmount(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['total_minor_amount'], $attributes['currency_code'])
        );
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    // If using Address model for shipping/billing on orders:
    // public function shippingAddressResolved(): BelongsTo
    // {
    //     return $this->belongsTo(Address::class, 'shipping_address_id');
    // }
    // public function billingAddressResolved(): BelongsTo
    // {
    //     return $this->belongsTo(Address::class, 'billing_address_id');
    // }
}
```

5.  **Model: `app/Domain/OrderManagement/Models/OrderItem.php`** (New)

```php
<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\Catalog\Models\Product;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id',
        'product_name_snapshot', 'product_sku_snapshot', 'product_options_snapshot',
        'quantity', 'unit_price_minor_amount', 'total_minor_amount', 'currency_code',
    ];

    protected $casts = [
        'product_options_snapshot' => 'array',
        'quantity' => 'integer',
        'unit_price_minor_amount' => 'integer',
        'total_minor_amount' => 'integer',
    ];

    // Virtual attributes for MonetaryAmount objects
    protected function unitPrice(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['unit_price_minor_amount'], $attributes['currency_code'])
        );
    }

    protected function total(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['total_minor_amount'], $attributes['currency_code'])
        );
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
```

6.  **Factory: `database/factories/OrderFactory.php`** (New)
```php
<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\OrderManagement\Models\Order;
use App\Domain\UserManagement\Models\User;
// use App\Domain\UserManagement\Models\Address;
use App\Domain\Promotion\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->numberBetween(2000, 20000); // 20.00 - 200.00
        $shipping = fake()->numberBetween(500, 2000);
        $tax = (int)($subtotal * 0.07); // Example 7% tax
        $total = $subtotal + $shipping + $tax;

        return [
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'user_id' => User::factory(),
            'guest_email' => null, // Or fake()->safeEmail if user_id is null
            // 'shipping_address_id' => Address::factory()->shipping(), // If using Address model
            // 'billing_address_id' => Address::factory()->billing(),  // If using Address model
            'shipping_name' => fake()->name(),
            'shipping_email' => fake()->safeEmail(),
            'shipping_address' => fake()->streetAddress(),
            'shipping_city' => fake()->city(),
            'shipping_state' => fake()->stateAbbr(),
            'shipping_zip' => fake()->postcode(),
            'shipping_country' => fake()->countryCode(),

            'status' => fake()->randomElement(['pending_payment','paid','processing','shipped','delivered','completed']),
            'payment_status' => 'paid',
            'payment_intent_id' => 'pi_' . Str::random(24),
            'payment_gateway' => 'stripe',
            'subtotal_minor_amount' => $subtotal,
            'discount_minor_amount' => 0,
            // 'coupon_id' => fake()->optional()->randomElement(Coupon::pluck('id')->toArray()),
            'shipping_cost_minor_amount' => $shipping,
            'tax_minor_amount' => $tax,
            'total_minor_amount' => $total,
            'currency_code' => 'USD',
            'order_notes' => fake()->optional()->sentence(),
            'paid_at' => now(),
            'created_at' => fake()->dateTimeThisYear(),
            'updated_at' => now(),
        ];
    }

    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'guest_email' => fake()->safeEmail(),
        ]);
    }
}
```

7.  **Factory: `database/factories/OrderItemFactory.php`** (New)
```php
<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\OrderManagement\Models\OrderItem;
use App\Domain\OrderManagement\Models\Order;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $product = Product::factory()->create(); // Create a product for this item
        $quantity = fake()->numberBetween(1, 3);
        $unitPrice = $product->price_minor_amount; // Use product's actual price

        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'product_sku_snapshot' => $product->sku,
            'product_options_snapshot' => ['size' => $product->size, 'scent_profile' => $product->scent_profile],
            'quantity' => $quantity,
            'unit_price_minor_amount' => $unitPrice,
            'total_minor_amount' => $quantity * $unitPrice,
            'currency_code' => $product->currency_code,
        ];
    }
}
```

8.  **Service: `app/Domain/OrderManagement/Services/OrderService.php`** (New - Basic Structure)
```php
<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\Services;

use App\Domain\OrderManagement\Models\Order;
use App\Domain\OrderManagement\DTOs\OrderData; // You would create this DTO
use App\Domain\CartManagement\Services\CartService; // Assuming you'll use this
use App\Domain\Inventory\Services\InventoryService;
use App\Domain\Payment\Services\PaymentService;
use App\Domain\OrderManagement\Events\OrderPlacedEvent;
use App\Domain\OrderManagement\Events\OrderStatusChangedEvent;
use Illuminate\Support\Facades\DB;
use App\Domain\UserManagement\Models\User;

class OrderService
{
    public function __construct(
        protected OrderNumberService $orderNumberService,
        protected CartService $cartService,
        protected InventoryService $inventoryService,
        protected PaymentService $paymentService // Example
    ) {}

    public function createOrderFromCart(User $user = null, array $checkoutData, string $paymentIntentId, string $paymentGateway): Order
    {
        // This is a complex method, simplified here
        // 1. Get cart contents (from CartService)
        // 2. Validate cart items, stock (using InventoryService)
        // 3. Calculate totals (subtotal, tax, shipping, discount)
        // 4. Create Order record
        // 5. Create OrderItem records (with product snapshots)
        // 6. Decrease stock (using InventoryService)
        // 7. Clear cart (using CartService)
        // 8. Dispatch OrderPlacedEvent
        // For now, just a placeholder:
        // $order = Order::create([...]);
        // OrderPlacedEvent::dispatch($order);
        // return $order;
        throw new \LogicException("createOrderFromCart method not fully implemented.");
    }

    public function updateOrderStatus(Order $order, string $newStatus, array $details = []): Order
    {
        $oldStatus = $order->status;
        $order->status = $newStatus;

        // Add logic for specific status changes (e.g., setting shipped_at)
        if ($newStatus === 'shipped' && isset($details['tracking_number'])) {
            $order->tracking_number = $details['tracking_number'];
            $order->carrier = $details['carrier'] ?? null;
        }

        $order->save();

        OrderStatusChangedEvent::dispatch($order, $oldStatus, $details);
        return $order;
    }

    // ... other methods like processRefund, cancelOrder
}
```

9.  **Service: `app/Domain/OrderManagement/Services/OrderNumberService.php`** (New - Implemented)
```php
<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\Services;

use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderNumberService
{
    public function generate(): string
    {
        // Example: ORD-YYYYMMDD-XXXXXX (random alphanumeric)
        $prefix = 'ORD-';
        $datePart = Carbon::now()->format('Ymd');
        $randomPart = strtoupper(Str::random(6));

        // Could add a sequence number or check for uniqueness if high volume
        return $prefix . $datePart . '-' . $randomPart;
    }
}
```

10. **DTO: `app/Domain/OrderManagement/DTOs/OrderData.php`** (New - Spatie Laravel Data)
```php
<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use App\Domain\UserManagement\Models\User; // For typing
use App\Domain\OrderManagement\DTOs\OrderItemData; // Nested DTO
use Spatie\LaravelData\Attributes\DataCollectionOf;

class OrderData extends Data
{
    public function __construct(
        public string $order_number,
        public string $currency_code,
        public int $total_minor_amount,
        public int $subtotal_minor_amount,
        public string $status,
        public User|Optional|null $user, // If associated with a registered user
        public string|Optional|null $guest_email,
        #[DataCollectionOf(OrderItemData::class)]
        public array|Optional $items, // Array of OrderItemData
        public string|Optional|null $payment_intent_id,
        public string|Optional|null $payment_gateway,
        // ... include other relevant fields from the Order model for creation/update
        public int|Optional $discount_minor_amount,
        public int|Optional $shipping_cost_minor_amount,
        public int|Optional $tax_minor_amount,
        public string|Optional|null $coupon_code_applied,
        public string|Optional|null $shipping_name,
        // ... all shipping address fields
        // ... order notes, tracking etc.
    ) {}
}
```
*(Note: `OrderItemData` DTO would also be needed here for the items collection)*

**DTO: `app/Domain/OrderManagement/DTOs/OrderItemData.php`** (New - Spatie Laravel Data)
```php
<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class OrderItemData extends Data
{
    public function __construct(
        public int $product_id,
        public string $product_name_snapshot,
        public int $quantity,
        public int $unit_price_minor_amount,
        public int $total_minor_amount,
        public string $currency_code,
        public string|Optional|null $product_sku_snapshot,
        public array|Optional|null $product_options_snapshot
    ) {}
}
```

11. **Event: `app/Domain/OrderManagement/Events/OrderPlacedEvent.php`** (New)
```php
<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\Events;

use App\Domain\OrderManagement\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedEvent
{
    use Dispatchable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
```

12. **Event: `app/Domain/OrderManagement/Events/OrderStatusChangedEvent.php`** (New)
```php
<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\Events;

use App\Domain\OrderManagement\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChangedEvent
{
    use Dispatchable, SerializesModels;

    public Order $order;
    public string $oldStatus;
    public array $details; // Optional details about the change

    public function __construct(Order $order, string $oldStatus, array $details = [])
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->details = $details;
    }
}
```

---
#### Step 2.5: Cart Management, Checkout, Payment, Promotion Domains (Contracts & Basic Structures)

**Cart Management:**

1.  **Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_cart_items_table.php`** (New)

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('session_id', 191)->nullable()->comment('Used for guest carts');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            // $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete(); // If using variants
            $table->integer('quantity')->unsigned();
            $table->timestamps();

            // Ensure a product can only appear once per user cart or per session cart
            $table->unique(['user_id', 'product_id'/*, 'product_variant_id'*/], 'uq_cart_item_user_product');
            $table->unique(['session_id', 'product_id'/*, 'product_variant_id'*/], 'uq_cart_item_session_product');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
```

2.  **Model: `app/Domain/CartManagement/Models/CartItem.php`** (New)

```php
<?php

declare(strict_types=1);

namespace App\Domain\CartManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\UserManagement\Models\User;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        // 'product_variant_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // public function productVariant(): BelongsTo
    // {
    //     return $this->belongsTo(ProductVariant::class);
    // }
}
```

3.  **Factory: `database/factories/CartItemFactory.php`** (New)
```php
<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\CartManagement\Models\CartItem;
use App\Domain\UserManagement\Models\User;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        return [
            'user_id' => null, // Can be set specifically or left for guest
            'session_id' => Str::random(40), // For guest carts
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(1, 5),
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'session_id' => null,
        ]);
    }

    public function guest(?string $sessionId = null): static
    {
         return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'session_id' => $sessionId ?? Str::random(40),
        ]);
    }
}
```

4.  **Service: `app/Domain/CartManagement/Services/CartService.php`** (New - Basic Structure)

```php
<?php

declare(strict_types=1);

namespace App\Domain\CartManagement\Services;

use App\Domain\Catalog\Models\Product;
use App\Domain\CartManagement\Models\CartItem;
use App\Domain\UserManagement\Models\User;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_CART_ID_KEY = 'cart_session_id';

    protected function getCartIdentifier(): ?string
    {
        if (auth()->check()) {
            return 'user_' . auth()->id(); // Or use user_id directly in queries
        }
        if (Session::has(self::SESSION_CART_ID_KEY)) {
            return Session::get(self::SESSION_CART_ID_KEY);
        }
        $sessionId = 'guest_' . Str::random(40);
        Session::put(self::SESSION_CART_ID_KEY, $sessionId);
        return $sessionId;
    }

    protected function getQuery()
    {
        $query = CartItem::query()->with('product');
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $sessionId = Session::get(self::SESSION_CART_ID_KEY);
            if ($sessionId) {
                $query->where('session_id', $sessionId);
            } else {
                // No active cart for guest if no session_id
                return CartItem::query()->whereRaw('1 = 0'); // Empty query
            }
        }
        return $query;
    }

    public function addItem(Product $product, int $quantity = 1): CartItem
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        $cartItemQuery = $this->getQuery()->where('product_id', $product->id);
        $cartItem = $cartItemQuery->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            $attributes = [
                'product_id' => $product->id,
                'quantity' => $quantity,
            ];
            if (auth()->check()) {
                $attributes['user_id'] = auth()->id();
            } else {
                $attributes['session_id'] = $this->getCartIdentifier(); // Ensures session_id is created if not present
            }
            $cartItem = CartItem::create($attributes);
        }
        return $cartItem->load('product');
    }

    public function updateItemQuantity(int $cartItemId, int $quantity): ?CartItem
    {
        if ($quantity < 0) { // Allow 0 to remove
            throw new \InvalidArgumentException('Quantity cannot be negative.');
        }

        $cartItem = $this->getQuery()->find($cartItemId);
        if ($cartItem) {
            if ($quantity === 0) {
                $cartItem->delete();
                return null;
            }
            $cartItem->quantity = $quantity;
            $cartItem->save();
            return $cartItem->load('product');
        }
        return null;
    }

    public function removeItem(int $cartItemId): bool
    {
        $cartItem = $this->getQuery()->find($cartItemId);
        if ($cartItem) {
            return (bool) $cartItem->delete();
        }
        return false;
    }

    public function getItems(): Collection
    {
        return $this->getQuery()->get();
    }

    public function getSubtotal(): MonetaryAmount
    {
        $items = $this->getItems();
        $totalMinorAmount = 0;
        $currencyCode = config('thescent.default_currency', 'USD'); // Default currency

        if ($items->isNotEmpty()) {
            $currencyCode = $items->first()->product->currency_code; // Assume all items in cart have same currency for simplicity
        }

        foreach ($items as $item) {
            // Ensure currency codes match if handling multi-currency carts robustly
            if ($item->product->currency_code !== $currencyCode) {
                 // Handle or throw error for mixed currencies if not supported
            }
            $totalMinorAmount += $item->product->price_minor_amount * $item->quantity;
        }
        return new MonetaryAmount($totalMinorAmount, $currencyCode);
    }

    public function getItemCount(): int
    {
        return $this->getQuery()->sum('quantity');
    }

    public function clearCart(): void
    {
        $this->getQuery()->delete();
        // Optionally clear session ID if guest
        // if (!auth()->check()) {
        //     Session::forget(self::SESSION_CART_ID_KEY);
        // }
    }

    public function mergeGuestCartToUser(User $user): void
    {
        $guestSessionId = Session::get(self::SESSION_CART_ID_KEY);
        if (!$guestSessionId) {
            return;
        }

        $guestCartItems = CartItem::where('session_id', $guestSessionId)->get();

        foreach ($guestCartItems as $guestItem) {
            $userItem = CartItem::where('user_id', $user->id)
                                ->where('product_id', $guestItem->product_id)
                                // ->where('product_variant_id', $guestItem->product_variant_id) // If using variants
                                ->first();
            if ($userItem) {
                $userItem->quantity += $guestItem->quantity;
                $userItem->save();
                $guestItem->delete();
            } else {
                $guestItem->user_id = $user->id;
                $guestItem->session_id = null;
                $guestItem->save();
            }
        }
        Session::forget(self::SESSION_CART_ID_KEY);
    }
}
```

**Checkout Domain:**

5.  **Service: `app/Domain/Checkout/Services/CheckoutService.php`** (New - Basic Structure)
```php
<?php

declare(strict_types=1);

namespace App\Domain\Checkout\Services;

use App\Domain\CartManagement\Services\CartService;
use App\Domain\Payment\Services\PaymentService;
use App\Domain\OrderManagement\Services\OrderService;
use App\Domain\UserManagement\Models\User;
// ... other necessary DTOs and models

class CheckoutService
{
    public function __construct(
        protected CartService $cartService,
        protected PaymentService $paymentService,
        protected OrderService $orderService
        // ... other dependencies like ShippingService, TaxService
    ) {}

    public function initiateCheckout(User $user = null /* ... other params like address DTOs */): array
    {
        // 1. Validate cart (not empty, items in stock etc.)
        // 2. Calculate totals (subtotal, shipping, tax, final total)
        // 3. Create PaymentIntent via PaymentService
        // 4. Return data needed for frontend (e.g., client_secret, order summary)

        // Placeholder:
        $cartSubtotal = $this->cartService->getSubtotal();
        if ($cartSubtotal->getMinorAmount() === 0) {
            throw new \RuntimeException("Cart is empty.");
        }

        // $paymentIntentResult = $this->paymentService->createPaymentIntent($cartSubtotal /*, other details */);

        return [
            'client_secret' => 'pi_example_secret_from_stripe_or_other_gw', // $paymentIntentResult->getClientSecret(),
            'order_summary' => [
                'subtotal' => $cartSubtotal->format(),
                // ... other summary details
            ],
        ];
    }

    public function completeOrder(string $paymentIntentId /*, other relevant data */): \App\Domain\OrderManagement\Models\Order
    {
        // 1. Verify payment success with PaymentService (e.g., retrieve PaymentIntent)
        // 2. If payment successful, create the order using OrderService
        // 3. OrderService will handle inventory, notifications, clearing cart etc.
        // Placeholder:
        // $paymentDetails = $this->paymentService->getPaymentDetails($paymentIntentId);
        // if ($paymentDetails->isSuccessful()) {
        //     $user = auth()->user(); // or guest details
        //     $checkoutData = []; // from session or request
        //     return $this->orderService->createOrderFromCart($user, $checkoutData, $paymentIntentId, $paymentDetails->getGateway());
        // }
        throw new \LogicException("completeOrder method not fully implemented.");
    }
}
```

**Payment Domain:**

6.  **Contract: `app/Domain/Payment/Contracts/PaymentGateway.php`** (New)
```php
<?php

declare(strict_types=1);

namespace App\Domain\Payment\Contracts;

use App\Domain\Shared\ValueObjects\MonetaryAmount;
use App\Domain\Payment\DTOs\PaymentResultData;
use App\Domain\Payment\DTOs\PaymentIntentResultData;


interface PaymentGateway
{
    /**
     * Create a payment intent (or equivalent) for the given amount.
     */
    public function createPaymentIntent(MonetaryAmount $amount, array $metadata = [], array $customerDetails = []): PaymentIntentResultData;

    /**
     * Process a payment (e.g., capture a payment intent, charge a card directly).
     * This might not be used if using client-side confirmation with PaymentIntents.
     */
    // public function charge(MonetaryAmount $amount, string $paymentMethodId, array $customerDetails = []): PaymentResultData;

    /**
     * Retrieve payment details for a given payment identifier.
     */
    public function getPaymentDetails(string $paymentIdentifier): PaymentResultData;


    /**
     * Process a refund.
     */
    public function refund(string $paymentIdentifier, MonetaryAmount $amount = null, string $reason = null): PaymentResultData;

    /**
     * Construct and verify a webhook event.
     */
    public function constructWebhookEvent(string $payload, string $signature, string $webhookSecret): object; // Type depends on gateway SDK
}
```

7.  **Gateway Implementation: `app/Domain/Payment/Gateways/StripeGateway.php`** (New - Basic Structure)
```php
<?php

declare(strict_types=1);

namespace App\Domain\Payment\Gateways;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use App\Domain\Payment\DTOs\PaymentResultData;
use App\Domain\Payment\DTOs\PaymentIntentResultData;
use Stripe\StripeClient;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Stripe\Webhook;
use Stripe\Event as StripeEvent;

class StripeGateway implements PaymentGateway
{
    protected StripeClient $stripe;

    public function __construct(string $secretKey)
    {
        $this->stripe = new StripeClient($secretKey);
    }

    public function createPaymentIntent(MonetaryAmount $amount, array $metadata = [], array $customerDetails = []): PaymentIntentResultData
    {
        try {
            $params = [
                'amount' => $amount->getMinorAmount(),
                'currency' => strtolower($amount->getCurrencyCode()),
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => $metadata,
            ];
            // Add customer if details provided
            // if (!empty($customerDetails['email'])) { ... create/retrieve Stripe customer ... }

            $paymentIntent = $this->stripe->paymentIntents->create($params);

            return new PaymentIntentResultData(
                clientSecret: $paymentIntent->client_secret,
                paymentIntentId: $paymentIntent->id,
                status: $paymentIntent->status
            );
        } catch (ApiErrorException $e) {
            // Log error: $e->getMessage()
            throw new \RuntimeException("Stripe PaymentIntent creation failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function getPaymentDetails(string $paymentIntentId): PaymentResultData
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);
            return new PaymentResultData(
                id: $paymentIntent->id,
                status: $paymentIntent->status, // e.g., 'succeeded', 'requires_payment_method'
                isSuccess: $paymentIntent->status === PaymentIntent::STATUS_SUCCEEDED,
                amountMinor: $paymentIntent->amount,
                currency: strtoupper($paymentIntent->currency),
                gatewayResponse: $paymentIntent->toArray()
            );
        } catch (ApiErrorException $e) {
            throw new \RuntimeException("Failed to retrieve Stripe PaymentIntent: " . $e->getMessage(), 0, $e);
        }
    }


    public function refund(string $paymentIntentId, MonetaryAmount $amount = null, string $reason = null): PaymentResultData
    {
        try {
            $params = ['payment_intent' => $paymentIntentId];
            if ($amount) {
                $params['amount'] = $amount->getMinorAmount();
            }
            if ($reason) {
                $params['reason'] = $reason;
            }
            $refund = $this->stripe->refunds->create($params);
            return new PaymentResultData(
                id: $refund->id, // Refund ID
                status: $refund->status,
                isSuccess: $refund->status === 'succeeded',
                amountMinor: $refund->amount,
                currency: strtoupper($refund->currency),
                gatewayResponse: $refund->toArray(),
                originalPaymentId: $paymentIntentId
            );
        } catch (ApiErrorException $e) {
             throw new \RuntimeException("Stripe refund failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function constructWebhookEvent(string $payload, string $signature, string $webhookSecret): StripeEvent
    {
        return Webhook::constructEvent($payload, $signature, $webhookSecret);
    }
}
```

8.  **Service: `app/Domain/Payment/Services/PaymentService.php`** (New - Basic Structure)
```php
<?php

declare(strict_types=1);

namespace App\Domain\Payment\Services;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use App\Domain\Payment\DTOs\PaymentResultData;
use App\Domain\Payment\DTOs\PaymentIntentResultData;

class PaymentService
{
    public function __construct(protected PaymentGateway $gateway)
    {
    }

    public function createPaymentIntent(MonetaryAmount $amount, array $metadata = [], array $customerDetails = []): PaymentIntentResultData
    {
        return $this->gateway->createPaymentIntent($amount, $metadata, $customerDetails);
    }

    public function getPaymentDetails(string $paymentIdentifier): PaymentResultData
    {
        return $this->gateway->getPaymentDetails($paymentIdentifier);
    }

    public function processRefund(string $paymentIdentifier, MonetaryAmount $amount = null, string $reason = null): PaymentResultData
    {
        return $this->gateway->refund($paymentIdentifier, $amount, $reason);
    }

    public function handleWebhook(string $payload, string $signature, string $webhookSecret): object // Type depends on SDK
    {
        // Further processing (dispatching events etc.) would go here based on event type
        return $this->gateway->constructWebhookEvent($payload, $signature, $webhookSecret);
    }
}
```

9.  **DTO: `app/Domain/Payment/DTOs/PaymentResultData.php`** (New)
```php
<?php

declare(strict_types=1);

namespace App\Domain\Payment\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PaymentResultData extends Data
{
    public function __construct(
        public string $id, // Gateway's transaction/refund ID
        public string $status, // Gateway's status
        public bool $isSuccess,
        public int|Optional $amountMinor,
        public string|Optional $currency,
        public array|Optional $gatewayResponse, // Raw response from gateway
        public string|Optional $originalPaymentId, // For refunds
        public string|Optional $errorMessage,
    ) {}
}
```
10. **DTO: `app/Domain/Payment/DTOs/PaymentIntentResultData.php`** (New)
```php
<?php

declare(strict_types=1);

namespace App\Domain\Payment\DTOs;

use Spatie\LaravelData\Data;

class PaymentIntentResultData extends Data
{
    public function __construct(
        public string $clientSecret,
        public string $paymentIntentId,
        public string $status
    ) {}
}
```

11. **Events: `app/Domain/Payment/Events/PaymentSuccessfulEvent.php`, `PaymentFailedEvent.php`** (New)
```php
// app/Domain/Payment/Events/PaymentSuccessfulEvent.php
<?php declare(strict_types=1); namespace App\Domain\Payment\Events;
use App\Domain\Payment\DTOs\PaymentResultData; use Illuminate\Foundation\Events\Dispatchable; use Illuminate\Queue\SerializesModels;
class PaymentSuccessfulEvent { use Dispatchable, SerializesModels; public function __construct(public PaymentResultData $paymentResult, public ?int $orderId = null){} }

// app/Domain/Payment/Events/PaymentFailedEvent.php
<?php declare(strict_types=1); namespace App\Domain\Payment\Events;
use App\Domain\Payment\DTOs\PaymentResultData; use Illuminate\Foundation\Events\Dispatchable; use Illuminate\Queue\SerializesModels;
class PaymentFailedEvent { use Dispatchable, SerializesModels; public function __construct(public PaymentResultData $paymentResult, public ?int $orderId = null, public ?string $reason = null){} }
```

**Promotion Domain:**

12. **Model: `app/Domain/Promotion/Models/Coupon.php`** (New)
```php
<?php

declare(strict_types=1);

namespace App\Domain\Promotion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\Shared\ValueObjects\MonetaryAmount; // If needed for value or min_purchase
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'description', 'type', 'value_minor_amount', 'currency_code',
        'max_uses', 'uses_count', 'max_uses_per_user',
        'min_purchase_minor_amount', 'min_purchase_currency_code',
        'valid_from', 'valid_to', 'is_active',
    ];

    protected $casts = [
        'value_minor_amount' => 'integer',
        'max_uses' => 'integer',
        'uses_count' => 'integer',
        'max_uses_per_user' => 'integer',
        'min_purchase_minor_amount' => 'integer',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        if ($this->valid_from && Carbon::now()->lt($this->valid_from)) {
            return false;
        }
        if ($this->valid_to && Carbon::now()->gt($this->valid_to)) {
            return false;
        }
        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
            return false;
        }
        return true;
    }

    // Add methods for checking user specific limits, min purchase etc.
}
```

13. **Factory: `database/factories/CouponFactory.php`** (New)
```php
<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Promotion\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['percentage', 'fixed_amount']);
        $value = ($type === 'percentage') ? fake()->numberBetween(5, 50) : fake()->numberBetween(500, 5000); // 5-50% or $5-$50

        return [
            'code' => strtoupper(Str::random(8)),
            'description' => fake()->sentence,
            'type' => $type,
            'value_minor_amount' => $value,
            'currency_code' => ($type === 'fixed_amount') ? 'USD' : null,
            'max_uses' => fake()->optional(0.7, 1000)->numberBetween(100, 1000),
            'uses_count' => 0,
            'max_uses_per_user' => fake()->optional(0.5, 1)->numberBetween(1, 5),
            'min_purchase_minor_amount' => fake()->optional(0.6, 2000)->numberBetween(2000, 10000), // $20-$100
            'min_purchase_currency_code' => 'USD',
            'valid_from' => fake()->optional(0.8)->dateTimeBetween('-1 month', '+1 week'),
            'valid_to' => fake()->optional(0.8)->dateTimeBetween('+1 month', '+6 months'),
            'is_active' => true,
        ];
    }
}
```

14. **Service: `app/Domain/Promotion/Services/CouponService.php`** (New - Basic Structure)
```php
<?php

declare(strict_types=1);

namespace App\Domain\Promotion\Services;

use App\Domain\Promotion\Models\Coupon;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use App\Domain\UserManagement\Models\User;
use App\Domain\Promotion\Exceptions\InvalidCouponException;
// ... use Order model if checking user uses

class CouponService
{
    public function findAndValidateCoupon(string $code, MonetaryAmount $cartSubtotal, User $user = null): Coupon
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            throw new InvalidCouponException("Coupon '{$code}' not found.");
        }
        if (!$coupon->isValid()) {
            throw new InvalidCouponException("Coupon '{$code}' is no longer valid or has expired.");
        }

        // Check min purchase if applicable
        if ($coupon->min_purchase_minor_amount !== null) {
            if ($coupon->min_purchase_currency_code !== $cartSubtotal->getCurrencyCode()) {
                 throw new InvalidCouponException("Coupon '{$code}' currency mismatch for minimum purchase.");
            }
            if ($cartSubtotal->getMinorAmount() < $coupon->min_purchase_minor_amount) {
                $minPurchase = new MonetaryAmount($coupon->min_purchase_minor_amount, $coupon->min_purchase_currency_code);
                throw new InvalidCouponException("Cart subtotal does not meet minimum purchase of {$minPurchase->format()} for coupon '{$code}'.");
            }
        }

        // Check max uses per user (requires querying user's past orders with this coupon)
        if ($user && $coupon->max_uses_per_user !== null) {
            // $userUses = Order::where('user_id', $user->id)->where('coupon_id', $coupon->id)->count();
            // if ($userUses >= $coupon->max_uses_per_user) {
            //     throw new InvalidCouponException("Coupon '{$code}' usage limit exceeded for this user.");
            // }
        }

        return $coupon;
    }

    public function calculateDiscount(Coupon $coupon, MonetaryAmount $subtotal): MonetaryAmount
    {
        if ($coupon->type === 'percentage') {
            $discountAmount = (int) round(($subtotal->getMinorAmount() * $coupon->value_minor_amount) / 100);
            return new MonetaryAmount($discountAmount, $subtotal->getCurrencyCode());
        } elseif ($coupon->type === 'fixed_amount') {
            if ($coupon->currency_code !== $subtotal->getCurrencyCode()) {
                 throw new InvalidCouponException("Coupon '{$coupon->code}' currency mismatch with cart.");
            }
            // Ensure discount doesn't exceed subtotal
            $discountAmount = min($coupon->value_minor_amount, $subtotal->getMinorAmount());
            return new MonetaryAmount($discountAmount, $subtotal->getCurrencyCode());
        }
        return new MonetaryAmount(0, $subtotal->getCurrencyCode()); // Should not happen
    }

    public function redeemCoupon(Coupon $coupon): void
    {
        $coupon->increment('uses_count');
        // Consider atomicity if high concurrency
    }
}
```

15. **Exception: `app/Domain/Promotion/Exceptions/InvalidCouponException.php`** (New)
```php
<?php

declare(strict_types=1);

namespace App\Domain\Promotion\Exceptions;

use RuntimeException;

class InvalidCouponException extends RuntimeException
{
    //
}
```

---
#### Step 2.6: Utility Domain (Models & Migrations)

1.  **Migrations (Create one file per table, or group related ones):**
*   `database/migrations/YYYY_MM_DD_HHMMSS_create_quiz_results_table.php`
*   `database/migrations/YYYY_MM_DD_HHMMSS_create_newsletter_subscribers_table.php`
*   `database/migrations/YYYY_MM_DD_HHMMSS_create_audit_log_table.php`
*   `database/migrations/YYYY_MM_DD_HHMMSS_create_email_log_table.php`
*   `database/migrations/YYYY_MM_DD_HHMMSS_create_tax_rates_table.php`
*   `database/migrations/YYYY_MM_DD_HHMMSS_create_tax_rate_history_table.php`

*(Content for these migrations will directly map the SQL schema provided in `sample_database_schema.sql.txt`, ensuring correct column types, nullability, indexes, and foreign keys. I'll provide one example and then list the models.)*

**Example Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_quiz_results_table.php`**
```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id(); // From sample schema, it's int auto_increment
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email', 191)->nullable(); // Sample has 150, 191 is common Laravel default
            $table->text('answers'); // Assuming JSON or serialized data
            $table->text('recommendations')->nullable(); // Assuming JSON
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_results');
    }
};
```

2.  **Models (Create one file for each in `app/Domain/Utility/Models/`):**

*   **`app/Domain/Utility/Models/QuizResult.php`**
    ```php
    <?php declare(strict_types=1); namespace App\Domain\Utility\Models;
    use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Factories\HasFactory;
    use App\Domain\UserManagement\Models\User; use Illuminate\Database\Eloquent\Relations\BelongsTo;
    class QuizResult extends Model { use HasFactory;
    const UPDATED_AT = null; // Only created_at in sample
    protected $fillable = ['user_id', 'email', 'answers', 'recommendations', 'created_at'];
    protected $casts = ['answers' => 'array', 'recommendations' => 'array', 'created_at' => 'datetime'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }}
    ```

*   **`app/Domain/Utility/Models/NewsletterSubscriber.php`**
    ```php
    <?php declare(strict_types=1); namespace App\Domain\Utility\Models;
    use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Factories\HasFactory;
    class NewsletterSubscriber extends Model { use HasFactory;
    protected $fillable = ['email', 'subscribed_at', 'unsubscribed_at', 'token']; // Token for unsubscribe
    protected $casts = ['subscribed_at' => 'datetime', 'unsubscribed_at' => 'datetime']; }
    ```

*   **`app/Domain/Utility/Models/AuditLog.php`**
    ```php
    <?php declare(strict_types=1); namespace App\Domain\Utility\Models;
    use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Factories\HasFactory;
    use App\Domain\UserManagement\Models\User; use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\MorphTo;
    class AuditLog extends Model { use HasFactory;
    const UPDATED_AT = null;
    protected $fillable = ['user_id', 'action', 'auditable_type', 'auditable_id', 'old_values', 'new_values', 'ip_address', 'user_agent', 'details', 'created_at'];
    protected $casts = ['old_values' => 'array', 'new_values' => 'array', 'created_at' => 'datetime'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function auditable(): MorphTo { return $this->morphTo(); }}
    ```

*   **`app/Domain/Utility/Models/EmailLog.php`**
    ```php
    <?php declare(strict_types=1); namespace App\Domain\Utility\Models;
    use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Factories\HasFactory;
    use App\Domain\UserManagement\Models\User; use Illuminate\Database\Eloquent\Relations\BelongsTo;
    class EmailLog extends Model { use HasFactory;
    const UPDATED_AT = null; // Sample schema does not have updated_at for email_log
    protected $fillable = ['user_id', 'to_email', 'subject', 'email_type', 'status', 'mailer_error', 'sent_at', 'created_at'];
    protected $casts = ['sent_at' => 'datetime', 'created_at' => 'datetime'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }}
    ```

*   **`app/Domain/Utility/Models/TaxRate.php`**
    ```php
    <?php declare(strict_types=1); namespace App\Domain\Utility\Models;
    use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Factories\HasFactory;
    use App\Domain\UserManagement\Models\User; use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    class TaxRate extends Model { use HasFactory;
    protected $fillable = ['name', 'country_code', 'state_code', 'postal_code_pattern', 'city', 'rate_percentage', 'is_compound', 'priority', 'is_active', 'start_date', 'end_date', 'created_by_user_id'];
    protected $casts = ['rate_percentage' => 'decimal:4', 'is_compound' => 'boolean', 'is_active' => 'boolean', 'start_date' => 'date', 'end_date' => 'date', 'priority' => 'integer'];
    public function createdByUser(): BelongsTo { return $this->belongsTo(User::class, 'created_by_user_id'); }
    public function history(): HasMany { return $this->hasMany(TaxRateHistory::class); } }
    ```
    *Note: The SQL schema for `tax_rates` had `created_by` as nullable int. I've named the relation and FK `created_by_user_id` for clarity.*

*   **`app/Domain/Utility/Models/TaxRateHistory.php`**
    ```php
    <?php declare(strict_types=1); namespace App\Domain\Utility\Models;
    use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Factories\HasFactory;
    use App\Domain\UserManagement\Models\User; use App\Domain\Utility\Models\TaxRate;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    class TaxRateHistory extends Model { use HasFactory;
    const UPDATED_AT = null;
    protected $fillable = ['tax_rate_id', 'old_rate_percentage', 'new_rate_percentage', 'changed_by_user_id', 'created_at'];
    protected $casts = ['old_rate_percentage' => 'decimal:4', 'new_rate_percentage' => 'decimal:4', 'created_at' => 'datetime'];
    public function taxRate(): BelongsTo { return $this->belongsTo(TaxRate::class); }
    public function changedByUser(): BelongsTo { return $this->belongsTo(User::class, 'changed_by_user_id'); }}
    ```

3.  **Factories (Create one file for each model in `database/factories/`):**
*   `QuizResultFactory.php`, `NewsletterSubscriberFactory.php`, `AuditLogFactory.php`, `EmailLogFactory.php`, `TaxRateFactory.php`, `TaxRateHistoryFactory.php`.
*   These will use `fake()` helpers to generate appropriate data for each field.

4.  **Basic Service Structures (Create in `app/Domain/Utility/Services/`):**
*   `NewsletterService.php`, `QuizService.php`, `AuditLoggerService.php`, `TaxService.php`.
*   These will initially contain constructor and placeholder methods.

**Example Service: `app/Domain/Utility/Services/TaxService.php`**
```php
<?php declare(strict_types=1); namespace App\Domain\Utility\Services;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use App\Domain\Shared\ValueObjects\AddressDetail; // Assuming an Address VO for input
class TaxService {
    public function calculateTax(MonetaryAmount $amount, AddressDetail $shippingAddress /*, Product $product = null */): MonetaryAmount
    {
        // Placeholder:
        // 1. Find applicable TaxRate(s) based on address (country, state, zip) and possibly product type.
        // 2. Calculate tax. Consider compound taxes.
        // For now, return a zero tax amount or a fixed percentage from config.
        $taxRatePercentage = config('thescent.tax.default_rate_percentage', 0);
        $taxMinorAmount = (int) round(($amount->getMinorAmount() * $taxRatePercentage) / 100);
        return new MonetaryAmount($taxMinorAmount, $amount->getCurrencyCode());
    }
}
```

---

This completes the generation of files for Steps 2.4, 2.5, and 2.6. These files provide the core data structures and service shells for significant parts of the e-commerce functionality. The next phases would involve building out the controllers, frontend views, and fully implementing the business logic within these services. Remember to run `composer dump-autoload` after adding new classes and then run your migrations with `./vendor/bin/sail artisan migrate`.

