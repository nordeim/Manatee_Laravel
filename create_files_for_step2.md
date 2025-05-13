<think>
The user wants me to generate the files for **Phase 2: Domain Modeling & Core Business Logic**, specifically **Steps 2.1, 2.2, and 2.3** from the execution plan. This involves creating models, migrations, factories, DTOs, services (basic structure), events, and repository contracts/implementations for:
    *   **Step 2.1: User Management Domain** (User, Address models, related migrations, factories, DTO, service structure, events, config update)
    *   **Step 2.2: Catalog Domain** (Product, Category, ProductAttribute models, related migrations, factories, repository, service structure, DTO, events)
    *   **Step 2.3: Inventory Domain** (InventoryMovement model, related migration, factory, InventoryService implementation, exceptions, events)

I need to be meticulous and ensure:
1.  Correct namespaces as per the `technical_design_specification_document.md`.
2.  Relationships in Eloquent models are correctly defined based on the SQL schema I generated previously and the ERD.
3.  Migrations accurately reflect the SQL schema.
4.  Factories provide sensible default data.
5.  DTOs, Service (structures), Events, and Exceptions are created as planned.
6.  The `MonetaryAmountCast` and `MonetaryAmount` Value Object (created in Step 1.3) will be used where appropriate (e.g., for `price` fields).
7.  Remember that the `sample_database_schema.sql.txt` is the primary schema source, augmented by the ERD for things like the dedicated `addresses` table. The SQL schema I generated in a previous step should be my main reference for table structures.

**Constraint Re-check based on last successful `composer install` and locked versions:**
*   Laravel Framework: `v11.44.7`
*   PHP: `^8.2`
*   Brick/Money: `0.8.1`
*   Spatie/Laravel-Data: `4.15.1` (if I decide to use it for DTOs, it's available)

**Planning for Monetary Values:**
The database schema I generated uses `INTEGER UNSIGNED` for `_minor_amount` columns and a `VARCHAR(3)` for `currency_code`. The `MonetaryAmountCast` is designed to work with this. I need to ensure models like `Product` and `Order` use this cast for their price fields.
Example from `Product` model in design doc: `price_minor_amount` and `currency_code` columns.
The cast was: `protected $casts = ['price' => MonetaryAmountCast::class . ':USD'];` but it should be more like:
`protected $casts = ['price' => MonetaryAmountCast::class . ':price_minor_amount,currency_code'];` if the cast needs to know which columns to read from/write to, or simply if the model has `price_minor_amount` and `currency_code` attributes, the cast on a virtual `price` attribute can handle it. My `MonetaryAmountCast` was designed to take a currency column or a default currency. Let's refine its usage. A common pattern is to have the cast operate on a single column (e.g., `price_in_cents`) and assume a currency or fetch it from another model attribute.

Let's re-evaluate the `MonetaryAmountCast`. It currently expects the value being cast (`$value`) to be the minor amount. The currency is resolved via `$this->currencyColumnOrCode`, which can be a column name or a static currency.
So, if a model has `price_minor_units` and `currency`, the cast would be on `price_object` (a virtual attribute) like:
`protected $casts = ['price_object' => MonetaryAmountCast::class . ':currency'];`
And then `get()` would use `$attributes['price_minor_units']` and `$attributes['currency']`.
`set()` would set `$attributes['price_minor_units']` and `$attributes['currency']`.

Alternatively, and more simply, if the model has `price_in_cents` and `currency_code` columns:
```php
// In Product.php
protected $casts = [
    'price_details' => MonetaryAmountCast::class . ':cents=price_in_cents,currency=currency_code', // Custom cast syntax
];
// Or, even better, a cast that handles multiple attributes:
// protected $casts = [ 'price' => MoneyCast::class ]; // where MoneyCast reads amount_column and currency_column

// Simpler: if the `price` *column itself* stores minor units.
// Product table has `price_minor_amount` and `currency_code`.
// So a virtual `price` attribute can be cast:
protected function price(): Attribute {
    return Attribute::make(
        get: fn ($value, $attributes) => new MonetaryAmount($attributes['price_minor_amount'], $attributes['currency_code']),
        set: fn (MonetaryAmount $value, $attributes) => [
            'price_minor_amount' => $value->getMinorAmount(),
            'currency_code' => $value->getCurrencyCode(),
        ]
    );
}
// Then, no explicit cast in $casts array is needed for 'price' if it's an accessor/mutator.
// Or, if 'price' is a real column intended to store the MonetaryAmount object representation (e.g. JSON),
// then the cast is on 'price'.
// The previous `MonetaryAmountCast` was designed to cast a *single* attribute that stores the minor amount,
// and it *infers* or is *told* where the currency is.

Let's stick to the plan: the `Product` table has `price_minor_amount` and `currency_code`.
The `MonetaryAmountCast` I provided previously was:
`public function get(Model $model, string $key, mixed $value, array $attributes): ?MonetaryAmount` where `$value` is the content of the attribute `$key` (e.g., `price_minor_amount`).
And `public function set(Model $model, string $key, mixed $value, array $attributes): array` where `$value` is the `MonetaryAmount` object.

So if we cast `price_minor_amount`:
`protected $casts = ['price_minor_amount' => MonetaryAmountCast::class . ':currency_code'];`
This would mean the `get` method receives the integer from `price_minor_amount` as `$value`. It uses `$this->currencyColumnOrCode` (which is 'currency_code') to fetch currency from `$attributes['currency_code']`. This works.
The `set` method receives a `MonetaryAmount` object as `$value`. It returns `[$key => $value->getMinorAmount()]` (which updates `price_minor_amount`) and `[$this->currencyColumnOrCode => $value->getCurrencyCode()]` (which updates `currency_code`). This also works.

Okay, the cast should work as designed.

**Using Spatie/Laravel-Data for DTOs:**
The design doc mentioned `spatie/laravel-data` as optional. It's in `composer.json`. I will use it for DTOs as it's clean and powerful.

**Migration Dependencies:**
I need to generate migrations in an order that respects foreign key constraints.
1.  `users`
2.  `addresses` (FK to users)
3.  `categories`
4.  `products` (FK to categories)
5.  `product_attributes` (FK to products)
6.  `inventory_movements` (FK to products)

**File Generation Strategy:**
For each step (2.1, 2.2, 2.3):
1.  Create/Modify Migrations first.
2.  Create/Modify Models (including relationships, casts, fillable, etc.).
3.  Create/Modify Factories.
4.  Create DTOs.
5.  Create Service basic structures.
6.  Create Event basic structures.
7.  Create Exception basic structures.
8.  Create Repository Contracts and Eloquent Implementations (for Catalog).
9.  Update `config/auth.php` (for User model path).
10. Update `DomainServiceProvider.php` (for repository bindings).

This is a lot of files. I will generate them sequentially.
I will ensure to use the exact SQL schema I provided previously as the basis for migrations and model properties.
The user wants "complete and working version for each of the project code and config files". "Working" here means syntactically correct and fulfilling the structural/relational design. Full business logic in services will be more iterative.

One small detail: the `users` table migration. Laravel's default `create_users_table` exists. I need to modify it rather than create a new one, or instruct the user to do so, ensuring it aligns with the final `users` table design. The schema I provided includes a `users` table definition that matches the `sample_database_schema.sql.txt` plus the default address foreign keys. The standard Laravel migrations for `password_reset_tokens`, `failed_jobs`, `cache`, `jobs` are also relevant. The prompt asks for files in 2.1 to 2.3. Standard Laravel migrations are from `0001_01_01_...` so they come first. I will generate the new migrations for my domain models. The user's output showed `0001_01_01_000000_create_users_table`, `0001_01_01_000001_create_cache_table`, `0001_01_01_000002_create_jobs_table` already ran. So, my new migrations will have later timestamps.

Let's assume the `0001_01_01_000000_create_users_table.php` migration needs to be *modified* to match the full `users` table definition from my provided SQL schema (role, status, default address IDs etc.). I will generate the *content* of this modified migration. Similarly for `password_reset_tokens` and `failed_jobs` if they deviate from Laravel defaults and are part of "my" schema. The schema I provided for standard Laravel tables `password_reset_tokens`, `failed_jobs`, `sessions`, `cache` are fairly standard. The critical ones are the domain-specific tables.
For now, I will focus on generating the *new* migrations based on the `execution_plan.md` for steps 2.1, 2.2, 2.3. The user can adapt their existing `create_users_table` migration.

I need to ensure all generated PHP files have the strict types declaration: `declare(strict_types=1);`

Let's start.
</think>

Absolutely! We're at an exciting stage now that the core environment and dependencies are stable. Let's proceed with generating the files for **Phase 2: Domain Modeling & Core Business Logic**, specifically **Steps 2.1, 2.2, and 2.3** as outlined in your execution plan.

I will generate the complete, working (syntactically correct and fulfilling the design) versions of these files.

---

### Phase 2: Domain Modeling & Core Business Logic

#### Step 2.1: User Management Domain

**Files to Create/Modify:**

1.  **Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_addresses_table.php`** (New)
    *   *Note: You should adjust the timestamp `YYYY_MM_DD_HHMMSS` to be sequential after your existing migrations. The existing `0001_01_01_000000_create_users_table.php` migration will need to be manually updated by you to include the `role`, `status`, `newsletter_subscribed`, `reset_token`, `reset_token_expires_at`, `default_shipping_address_id`, and `default_billing_address_id` columns as per the final SQL schema I provided, and to add foreign key constraints for the default address IDs *after* this `addresses` table migration has run, or make them nullable and add constraints in a separate later migration.*

    ```php
    <?php

    declare(strict_types=1);

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('addresses', function (Blueprint $table) {
                $table->id(); // Alias for $table->bigIncrements('id')
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('first_name', 191)->nullable();
                $table->string('last_name', 191)->nullable();
                $table->string('company', 191)->nullable();
                $table->string('address_line1', 191);
                $table->string('address_line2', 191)->nullable();
                $table->string('city', 100);
                $table->string('state', 100)->nullable()->comment('State / Province / Region');
                $table->string('postal_code', 20);
                $table->char('country_code', 2)->comment('ISO 3166-1 alpha-2 country code');
                $table->string('phone', 30)->nullable();
                $table->enum('type', ['billing', 'shipping', 'general'])->nullable()->default('general');
                $table->boolean('is_default_shipping')->default(false);
                $table->boolean('is_default_billing')->default(false);
                $table->timestamps();
            });

            // It's good practice to add foreign keys for default_shipping_address_id and default_billing_address_id
            // on the users table AFTER the addresses table is created.
            // This can be done in a separate migration or by modifying the users table migration
            // if it runs after this one (which it typically doesn't).
            // For now, assuming users table is modified separately to include these columns and then:
            // Schema::table('users', function (Blueprint $table) {
            //     $table->foreign('default_shipping_address_id')->references('id')->on('addresses')->nullOnDelete();
            //     $table->foreign('default_billing_address_id')->references('id')->on('addresses')->nullOnDelete();
            // });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            // Schema::table('users', function (Blueprint $table) {
            //     $table->dropForeign(['default_shipping_address_id']);
            //     $table->dropForeign(['default_billing_address_id']);
            // });
            Schema::dropIfExists('addresses');
        }
    };
    ```

2.  **Model: `app/Domain/UserManagement/Models/User.php`**
    *   *(You will need to MOVE your existing `app/Models/User.php` to this path and then modify it. Ensure the class declaration at the top is `class User extends Authenticatable implements MustVerifyEmail` if you want email verification)*

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\UserManagement\Models;

    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Laravel\Sanctum\HasApiTokens;
    use App\Domain\OrderManagement\Models\Order;
    use App\Domain\UserManagement\Models\Address;
    use App\Domain\Utility\Models\QuizResult;
    use App\Domain\Utility\Models\AuditLog;
    use App\Domain\Utility\Models\EmailLog;
    use App\Domain\Utility\Models\TaxRate; // For created_by_user_id
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    class User extends Authenticatable implements MustVerifyEmail // Add MustVerifyEmail if you require it
    {
        use HasApiTokens, HasFactory, Notifiable;

        /**
         * The attributes that are mass assignable.
         *
         * @var array<int, string>
         */
        protected $fillable = [
            'name',
            'email',
            'password',
            'role',
            'status',
            'newsletter_subscribed',
            'default_shipping_address_id', // Added
            'default_billing_address_id',  // Added
        ];

        /**
         * The attributes that should be hidden for serialization.
         *
         * @var array<int, string>
         */
        protected $hidden = [
            'password',
            'remember_token',
            'reset_token', // From sample schema
        ];

        /**
         * The attributes that should be cast.
         *
         * @var array<string, string>
         */
        protected $casts = [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'newsletter_subscribed' => 'boolean',
            'reset_token_expires_at' => 'datetime', // From sample schema
        ];

        public function orders(): HasMany
        {
            return $this->hasMany(Order::class);
        }

        public function addresses(): HasMany
        {
            return $this->hasMany(Address::class);
        }

        public function defaultShippingAddress(): BelongsTo
        {
            return $this->belongsTo(Address::class, 'default_shipping_address_id');
        }

        public function defaultBillingAddress(): BelongsTo
        {
            return $this->belongsTo(Address::class, 'default_billing_address_id');
        }

        public function quizResults(): HasMany
        {
            return $this->hasMany(QuizResult::class);
        }

        public function auditLogs(): HasMany
        {
            // Assuming audit_log user_id can be null if action is system generated
            return $this->hasMany(AuditLog::class, 'user_id');
        }

        public function emailLogs(): HasMany
        {
            return $this->hasMany(EmailLog::class, 'user_id');
        }

        public function taxRatesCreated(): HasMany
        {
            return $this->hasMany(TaxRate::class, 'created_by_user_id');
        }

        // Add other relationships as needed from the schema, e.g., for cart if persistent
    }
    ```

3.  **Model: `app/Domain/UserManagement/Models/Address.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\UserManagement\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use App\Domain\UserManagement\Models\User;

    class Address extends Model
    {
        use HasFactory;

        protected $fillable = [
            'user_id',
            'first_name',
            'last_name',
            'company',
            'address_line1',
            'address_line2',
            'city',
            'state',
            'postal_code',
            'country_code',
            'phone',
            'type',
            'is_default_shipping',
            'is_default_billing'
        ];

        protected $casts = [
            'is_default_shipping' => 'boolean',
            'is_default_billing' => 'boolean',
        ];

        public function user(): BelongsTo
        {
            return $this->belongsTo(User::class);
        }

        public function getFormattedLinesAttribute(): array
        {
            $lines = [];
            if ($this->first_name || $this->last_name) {
                $lines[] = trim($this->first_name . ' ' . $this->last_name);
            }
            if ($this->company) {
                $lines[] = $this->company;
            }
            $lines[] = $this->address_line1;
            if ($this->address_line2) {
                $lines[] = $this->address_line2;
            }
            $lines[] = trim("{$this->city}, {$this->state} {$this->postal_code}");
            $lines[] = $this->country_code; // Consider converting to full country name using a helper
            return array_filter($lines);
        }

        public function getFormattedHtmlAttribute(): string
        {
            return implode('<br>', $this->getFormattedLinesAttribute());
        }

        public function __toString(): string
        {
            return implode("\n", $this->getFormattedLinesAttribute());
        }
    }
    ```

4.  **Factory: `database/factories/UserFactory.php`** (Modify existing)
    *   Ensure the model points to the new location `App\Domain\UserManagement\Models\User::class`.
    *   Add new fields like `role`, `status`, etc.

    ```php
    <?php

    declare(strict_types=1);

    namespace Database\Factories;

    use App\Domain\UserManagement\Models\User; // Updated path
    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Str;

    /**
     * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\UserManagement\Models\User>
     */
    class UserFactory extends Factory
    {
        /**
         * The current password being used by the factory.
         */
        protected static ?string $password;

        /**
         * The name of the factory's corresponding model.
         *
         * @var string
         */
        protected $model = User::class; // Ensure this points to your User model

        /**
         * Define the model's default state.
         *
         * @return array<string, mixed>
         */
        public function definition(): array
        {
            return [
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => static::$password ??= Hash::make('password'),
                'remember_token' => Str::random(10),
                'role' => 'user', // Default role
                'status' => 'active', // Default status
                'newsletter_subscribed' => fake()->boolean(30), // 30% chance of being true
                // 'default_shipping_address_id' => null, // Typically set after address creation
                // 'default_billing_address_id' => null,
            ];
        }

        /**
         * Indicate that the model's email address should be unverified.
         */
        public function unverified(): static
        {
            return $this->state(fn (array $attributes) => [
                'email_verified_at' => null,
            ]);
        }

        /**
         * Indicate that the user is an admin.
         */
        public function admin(): static
        {
            return $this->state(fn (array $attributes) => [
                'role' => 'admin',
            ]);
        }
    }
    ```

5.  **Factory: `database/factories/AddressFactory.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace Database\Factories;

    use App\Domain\UserManagement\Models\Address;
    use App\Domain\UserManagement\Models\User;
    use Illuminate\Database\Eloquent\Factories\Factory;

    class AddressFactory extends Factory
    {
        protected $model = Address::class;

        public function definition(): array
        {
            return [
                'user_id' => User::factory(), // Or null if allowing guest addresses
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'company' => fake()->optional()->company(),
                'address_line1' => fake()->streetAddress(),
                'address_line2' => fake()->optional()->secondaryAddress(),
                'city' => fake()->city(),
                'state' => fake()->stateAbbr(),
                'postal_code' => fake()->postcode(),
                'country_code' => fake()->countryCode(), // e.g., 'US'
                'phone' => fake()->optional()->phoneNumber(),
                'type' => fake()->randomElement(['billing', 'shipping', 'general', null]),
                'is_default_shipping' => false,
                'is_default_billing' => false,
            ];
        }

        public function forUser(User $user): static
        {
            return $this->state(fn (array $attributes) => [
                'user_id' => $user->id,
            ]);
        }

        public function shipping(): static
        {
            return $this->state(fn (array $attributes) => [
                'type' => 'shipping',
            ]);
        }

        public function billing(): static
        {
            return $this->state(fn (array $attributes) => [
                'type' => 'billing',
            ]);
        }

        public function defaultShipping(): static
        {
            return $this->shipping()->state(fn (array $attributes) => [
                'is_default_shipping' => true,
            ]);
        }

        public function defaultBilling(): static
        {
            return $this->billing()->state(fn (array $attributes) => [
                'is_default_billing' => true,
            ]);
        }
    }
    ```

6.  **Value Object: `app/Domain/Shared/ValueObjects/EmailAddress.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Shared\ValueObjects;

    use InvalidArgumentException;
    use Stringable;

    readonly class EmailAddress implements Stringable
    {
        public string $value;
        public string $localPart;
        public string $domainPart;

        public function __construct(string $email)
        {
            $email = trim(strtolower($email));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Invalid email address format: {$email}");
            }
            $this->value = $email;
            [$this->localPart, $this->domainPart] = explode('@', $this->value, 2);
        }

        public function getValue(): string
        {
            return $this->value;
        }

        public function getLocalPart(): string
        {
            return $this->localPart;
        }

        public function getDomainPart(): string
        {
            return $this->domainPart;
        }

        public function isEqualTo(self $other): bool
        {
            return $this->value === $other->value;
        }

        public function __toString(): string
        {
            return $this->value;
        }
    }
    ```

7.  **Value Object: `app/Domain/Shared/ValueObjects/FullName.php`** (New)
    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Shared\ValueObjects;

    use Stringable;

    readonly class FullName implements Stringable
    {
        public function __construct(
            public string $firstName,
            public ?string $middleName = null,
            public string $lastName
        ) {
        }

        public function getFullName(): string
        {
            return trim($this->firstName . ($this->middleName ? ' ' . $this->middleName : '') . ' ' . $this->lastName);
        }

        public function __toString(): string
        {
            return $this->getFullName();
        }
    }
    ```

8.  **DTO: `app/Domain/UserManagement/DTOs/UserData.php`** (New - Using Spatie Laravel Data)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\UserManagement\DTOs;

    use Spatie\LaravelData\Data;
    use Spatie\LaravelData\Optional;

    class UserData extends Data
    {
        public function __construct(
            public string $name,
            public string $email,
            public string|Optional $password, // Optional for updates
            public string|Optional $role,
            public string|Optional $status,
            public bool|Optional $newsletter_subscribed,
            // Add address fields if creating user with address directly, or handle addresses separately
            // public string|Optional $address_line1,
            // ...
        ) {}

        public static function rules(): array
        {
            // Basic rules, more specific rules in FormRequests
            return [
                'name' => ['required', 'string', 'max:191'],
                'email' => ['required', 'email', 'max:191'],
                // Password rules would differ for creation vs update
            ];
        }
    }
    ```

9.  **Service: `app/Domain/UserManagement/Services/UserProfileService.php`** (New - Basic Structure)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\UserManagement\Services;

    use App\Domain\UserManagement\Models\User;
    use App\Domain\UserManagement\DTOs\UserData;
    use Illuminate\Support\Facades\Hash;

    class UserProfileService
    {
        public function updateUser(User $user, UserData $userData): User
        {
            $updateData = [
                'name' => $userData->name,
                'email' => $userData->email,
            ];

            if (!($userData->password instanceof \Spatie\LaravelData\Optional) && $userData->password) {
                $updateData['password'] = Hash::make($userData->password);
            }
            if (!($userData->newsletter_subscribed instanceof \Spatie\LaravelData\Optional)) {
                $updateData['newsletter_subscribed'] = $userData->newsletter_subscribed;
            }
            // Add other updatable fields based on UserData

            $user->update($updateData);
            return $user->refresh();
        }

        public function createUser(UserData $userData): User
        {
            return User::create([
                'name' => $userData->name,
                'email' => $userData->email,
                'password' => Hash::make($userData->password), // Assuming password is required for creation
                'role' => $userData->role instanceof \Spatie\LaravelData\Optional ? 'user' : $userData->role,
                'status' => $userData->status instanceof \Spatie\LaravelData\Optional ? 'active' : $userData->status,
                'newsletter_subscribed' => $userData->newsletter_subscribed instanceof \Spatie\LaravelData\Optional ? false : $userData->newsletter_subscribed,
            ]);
        }

        // Methods for managing addresses can go here or in a dedicated AddressService
    }
    ```

10. **Event: `app/Domain/UserManagement/Events/UserRegisteredEvent.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\UserManagement\Events;

    use App\Domain\UserManagement\Models\User;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Queue\SerializesModels;

    class UserRegisteredEvent
    {
        use Dispatchable, InteractsWithSockets, SerializesModels;

        public User $user;

        public function __construct(User $user)
        {
            $this->user = $user;
        }
    }
    ```

11. **Event: `app/Domain/UserManagement/Events/PasswordResetRequestedEvent.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\UserManagement\Events;

    use App\Domain\UserManagement\Models\User;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Queue\SerializesModels;

    class PasswordResetRequestedEvent
    {
        use Dispatchable, InteractsWithSockets, SerializesModels;

        public User $user;
        public string $token;

        public function __construct(User $user, string $token)
        {
            $this->user = $user;
            $this->token = $token;
        }
    }
    ```

12. **Config Update: `config/auth.php`**
    *   Ensure `providers.users.model` points to the new User model path.

    ```php
    // In config/auth.php
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Domain\UserManagement\Models\User::class, // <<<< UPDATE THIS
        ],
        // ...
    ],
    ```

---
#### Step 2.2: Catalog Domain

1.  **Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_categories_table.php`** (New)

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
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('slug', 120)->unique();
                $table->text('description')->nullable();
                $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('categories');
        }
    };
    ```

2.  **Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_products_table.php`** (New)

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
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
                $table->string('name', 150);
                $table->string('slug', 170)->unique();
                $table->text('description')->nullable();
                $table->text('short_description')->nullable()->comment('Brief description for listings/previews');
                $table->string('image', 191)->nullable()->comment('Path to main image');
                $table->json('gallery_images')->nullable()->comment('JSON array of additional image paths');
                $table->integer('price_minor_amount')->unsigned()->comment('Price in minor units (e.g., cents)');
                $table->string('currency_code', 3)->default('USD');
                $table->json('benefits')->nullable()->comment('Product benefits, stored as JSON array of strings');
                $table->text('ingredients')->nullable()->comment('List of key ingredients');
                $table->text('usage_instructions')->nullable()->comment('How to use the product');
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('stock_quantity')->default(0);
                $table->integer('low_stock_threshold')->unsigned()->default(20);
                $table->integer('reorder_point')->unsigned()->default(30);
                $table->boolean('backorder_allowed')->default(false)->comment('Allow purchase when stock_quantity <= 0');
                $table->string('highlight_text', 50)->nullable();
                $table->string('size', 50)->nullable()->comment('e.g., 10ml, 100g');
                $table->string('scent_profile', 191)->nullable();
                $table->string('origin', 100)->nullable();
                $table->string('sku', 100)->nullable()->unique();
                $table->timestamps();

                $table->index(['is_active', 'is_featured']);
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('products');
        }
    };
    ```

3.  **Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_product_attributes_table.php`** (New)

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
            Schema::create('product_attributes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->enum('scent_type', ['floral','woody','citrus','oriental','fresh', 'herbal', 'spicy', 'sweet'])->nullable();
                $table->enum('mood_effect', ['calming','energizing','focusing','balancing', 'uplifting', 'grounding'])->nullable();
                $table->enum('intensity_level', ['light','medium','strong'])->nullable();
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('product_attributes');
        }
    };
    ```

4.  **Model: `app/Domain/Catalog/Models/Category.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Catalog\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    class Category extends Model
    {
        use HasFactory;

        protected $fillable = [
            'name',
            'slug',
            'description',
            'parent_id',
        ];

        public function products(): HasMany
        {
            return $this->hasMany(Product::class);
        }

        public function parent(): BelongsTo
        {
            return $this->belongsTo(Category::class, 'parent_id');
        }

        public function children(): HasMany
        {
            return $this->hasMany(Category::class, 'parent_id');
        }
    }
    ```

5.  **Model: `app/Domain/Catalog/Models/Product.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Catalog\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use App\Domain\Shared\ValueObjects\MonetaryAmount;
    use App\Casts\MonetaryAmountCast; // This was the plan for the cast
    use App\Domain\Catalog\Models\Category;
    use App\Domain\Catalog\Models\ProductAttribute;
    use App\Domain\Inventory\Models\InventoryMovement;
    use App\Domain\OrderManagement\Models\OrderItem;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasOne;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute; // Eloquent's Attribute class

    class Product extends Model
    {
        use HasFactory;

        protected $fillable = [
            'name', 'slug', 'description', 'short_description', 'image', 'gallery_images',
            'price_minor_amount', 'currency_code', // Storing these directly
            'benefits', 'ingredients', 'usage_instructions', 'category_id',
            'is_featured', 'is_active', 'stock_quantity', 'low_stock_threshold',
            'reorder_point', 'backorder_allowed', 'highlight_text', 'size',
            'scent_profile', 'origin', 'sku',
        ];

        protected $casts = [
            'gallery_images' => 'array',
            'benefits' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'backorder_allowed' => 'boolean',
            'price_minor_amount' => 'integer',
            'stock_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'reorder_point' => 'integer',
        ];

        // Virtual 'price' attribute using MonetaryAmount
        protected function price(): EloquentAttribute
        {
            return EloquentAttribute::make(
                get: fn ($value, $attributes) => new MonetaryAmount(
                    (int) ($attributes['price_minor_amount'] ?? 0),
                    (string) ($attributes['currency_code'] ?? config('thescent.default_currency', 'USD'))
                ),
                set: fn (MonetaryAmount $value) => [
                    'price_minor_amount' => $value->getMinorAmount(),
                    'currency_code' => $value->getCurrencyCode(),
                ]
            );
        }

        public function category(): BelongsTo
        {
            return $this->belongsTo(Category::class);
        }

        public function productAttributes(): HasOne // Renamed to avoid Eloquent attribute conflict
        {
            return $this->hasOne(ProductAttribute::class);
        }

        public function inventoryMovements(): HasMany
        {
            return $this->hasMany(InventoryMovement::class);
        }

        public function orderItems(): HasMany
        {
            return $this->hasMany(OrderItem::class);
        }

        public function getImageUrlAttribute(): ?string
        {
            return $this->image ? asset('storage/' . $this->image) : asset('images/placeholder.png'); // Added placeholder
        }

        public function getGalleryImageUrlsAttribute(): array
        {
            $gallery = $this->gallery_images ?? [];
            return array_map(fn($imagePath) => asset('storage/' . $imagePath), $gallery);
        }
    }
    ```

6.  **Model: `app/Domain/Catalog/Models/ProductAttribute.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Catalog\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    class ProductAttribute extends Model
    {
        use HasFactory;

        protected $table = 'product_attributes'; // Explicitly define table name

        protected $fillable = [
            'product_id',
            'scent_type',
            'mood_effect',
            'intensity_level',
        ];

        // No casts needed for enums if they are stored as strings and handled by DB

        public function product(): BelongsTo
        {
            return $this->belongsTo(Product::class);
        }
    }
    ```

7.  **Factory: `database/factories/CategoryFactory.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace Database\Factories;

    use App\Domain\Catalog\Models\Category;
    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Str;

    class CategoryFactory extends Factory
    {
        protected $model = Category::class;

        public function definition(): array
        {
            $name = fake()->unique()->words(rand(1, 3), true);
            return [
                'name' => Str::title($name),
                'slug' => Str::slug($name),
                'description' => fake()->optional()->sentence(),
                'parent_id' => null, // Or Category::factory() for subcategories
            ];
        }
    }
    ```

8.  **Factory: `database/factories/ProductFactory.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace Database\Factories;

    use App\Domain\Catalog\Models\Product;
    use App\Domain\Catalog\Models\Category;
    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Str;

    class ProductFactory extends Factory
    {
        protected $model = Product::class;

        public function definition(): array
        {
            $name = fake()->words(rand(2, 4), true);
            return [
                'name' => Str::title($name),
                'slug' => Str::slug($name) . '-' . Str::random(4), // Ensure uniqueness
                'description' => fake()->paragraphs(3, true),
                'short_description' => fake()->sentence(15),
                // 'image' => 'products/sample-' . rand(1, 10) . '.jpg', // Placeholder for image path
                'price_minor_amount' => fake()->numberBetween(1000, 10000), // e.g., $10.00 to $100.00
                'currency_code' => 'USD',
                'category_id' => Category::factory(),
                'is_featured' => fake()->boolean(20),
                'is_active' => true,
                'stock_quantity' => fake()->numberBetween(0, 200),
                'low_stock_threshold' => 20,
                'reorder_point' => 30,
                'backorder_allowed' => fake()->boolean(10),
                'sku' => strtoupper(Str::random(3) . '-' . fake()->randomNumber(5, true)),
                'size' => fake()->randomElement(['10ml', '30ml', '50ml', '100g', '200g', null]),
                'scent_profile' => fake()->words(rand(2,3), true),
                'origin' => fake()->optional()->country(),
            ];
        }
    }
    ```

9.  **Factory: `database/factories/ProductAttributeFactory.php`** (New)
    ```php
    <?php

    declare(strict_types=1);

    namespace Database\Factories;

    use App\Domain\Catalog\Models\ProductAttribute;
    use App\Domain\Catalog\Models\Product;
    use Illuminate\Database\Eloquent\Factories\Factory;

    class ProductAttributeFactory extends Factory
    {
        protected $model = ProductAttribute::class;

        public function definition(): array
        {
            return [
                'product_id' => Product::factory(),
                'scent_type' => fake()->randomElement(['floral','woody','citrus','oriental','fresh', 'herbal', 'spicy', 'sweet', null]),
                'mood_effect' => fake()->randomElement(['calming','energizing','focusing','balancing', 'uplifting', 'grounding', null]),
                'intensity_level' => fake()->randomElement(['light','medium','strong', null]),
            ];
        }
    }
    ```

10. **Repository Contract: `app/Domain/Catalog/Contracts/ProductRepositoryContract.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Catalog\Contracts;

    use App\Domain\Catalog\Models\Product;
    use Illuminate\Database\Eloquent\Collection as EloquentCollection; // Eloquent specific
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;

    interface ProductRepositoryContract
    {
        public function findById(int $id): ?Product;

        public function findBySlug(string $slug): ?Product;

        public function getFeatured(int $limit = 4): EloquentCollection;

        public function getAllPaginated(int $perPage = 12, array $filters = [], array $orderBy = ['created_at', 'desc']): LengthAwarePaginator;

        public function create(array $data): Product;

        public function update(Product $product, array $data): Product;

        public function delete(Product $product): bool;
    }
    ```

11. **Repository Implementation: `app/Infrastructure/Persistence/Eloquent/Repositories/EloquentProductRepository.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Infrastructure\Persistence\Eloquent\Repositories;

    use App\Domain\Catalog\Contracts\ProductRepositoryContract;
    use App\Domain\Catalog\Models\Product;
    use Illuminate\Database\Eloquent\Collection as EloquentCollection;
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;

    class EloquentProductRepository implements ProductRepositoryContract
    {
        public function findById(int $id): ?Product
        {
            return Product::find($id);
        }

        public function findBySlug(string $slug): ?Product
        {
            return Product::where('slug', $slug)->where('is_active', true)->first();
        }

        public function getFeatured(int $limit = 4): EloquentCollection
        {
            return Product::where('is_featured', true)
                ->where('is_active', true)
                ->take($limit)
                ->orderBy('updated_at', 'desc') // Or some other logic for featured
                ->get();
        }

        public function getAllPaginated(int $perPage = 12, array $filters = [], array $orderBy = ['created_at', 'desc']): LengthAwarePaginator
        {
            $query = Product::query()->where('is_active', true);

            if (isset($filters['category_slug'])) {
                $query->whereHas('category', function ($q) use ($filters) {
                    $q->where('slug', $filters['category_slug']);
                });
            }
            // Add more filters: price range, search term, etc.

            return $query->orderBy($orderBy[0], $orderBy[1])->paginate($perPage);
        }

        public function create(array $data): Product
        {
            return Product::create($data);
        }

        public function update(Product $product, array $data): Product
        {
            $product->update($data);
            return $product->fresh();
        }

        public function delete(Product $product): bool
        {
            return (bool) $product->delete();
        }
    }
    ```

12. **Provider Update: `app/Providers/DomainServiceProvider.php`** (Modify)

    ```php
    <?php

    declare(strict_types=1);

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
            $this->app->bind(
                ProductRepositoryContract::class,
                EloquentProductRepository::class
            );
            // ... other Catalog bindings

            // OrderManagement Bounded Context
            // $this->app->bind(OrderRepositoryContract::class, EloquentOrderRepository::class);
            // ...
        }

        public function boot(): void
        {
            //
        }
    }
    ```

13. **Service: `app/Domain/Catalog/Services/ProductQueryService.php`** (New - Basic Structure)
    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Catalog\Services;

    use App\Domain\Catalog\Contracts\ProductRepositoryContract;
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;
    use Illuminate\Database\Eloquent\Collection as EloquentCollection;
    use App\Domain\Catalog\Models\Product;

    class ProductQueryService
    {
        public function __construct(protected ProductRepositoryContract $productRepository)
        {
        }

        public function getActivePaginatedProducts(
            int $perPage = 12,
            ?string $categorySlug = null,
            array $orderBy = ['created_at', 'desc']
            // Add more filter params like price_min, price_max, search_term
        ): LengthAwarePaginator
        {
            $filters = [];
            if ($categorySlug) {
                $filters['category_slug'] = $categorySlug;
            }
            // Build filters array

            return $this->productRepository->getAllPaginated($perPage, $filters, $orderBy);
        }

        public function getFeaturedProducts(int $limit = 4): EloquentCollection
        {
            return $this->productRepository->getFeatured($limit);
        }

        public function findProductBySlug(string $slug): ?Product
        {
            return $this->productRepository->findBySlug($slug);
        }
    }
    ```

14. **DTO: `app/Domain/Catalog/DTOs/ProductData.php`** (New - Spatie Laravel Data)
    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Catalog\DTOs;

    use Spatie\LaravelData\Data;
    use Spatie\LaravelData\Optional;
    use Illuminate\Http\UploadedFile; // For image uploads

    class ProductData extends Data
    {
        public function __construct(
            public string $name,
            public string $slug,
            public int $price_minor_amount, // Store as minor units
            public string $currency_code,
            public int $stock_quantity,
            public string|Optional $description,
            public string|Optional $short_description,
            public UploadedFile|Optional|null $image, // For new image upload
            public string|Optional|null $existing_image_path, // To keep existing image or remove it
            public array|Optional $gallery_images, // Array of UploadedFile or paths
            public array|Optional $benefits,
            public string|Optional $ingredients,
            public string|Optional $usage_instructions,
            public int|Optional|null $category_id,
            public bool|Optional $is_featured,
            public bool|Optional $is_active,
            public int|Optional $low_stock_threshold,
            public int|Optional $reorder_point,
            public bool|Optional $backorder_allowed,
            public string|Optional $highlight_text,
            public string|Optional $size,
            public string|Optional $scent_profile,
            public string|Optional $origin,
            public string|Optional|null $sku,
            // For ProductAttributeData
            public ProductAttributeData|Optional|null $attributes
        ) {}
    }
    ```
    *(Note: `ProductAttributeData` DTO would also be needed if passing attributes nested)*

    **DTO: `app/Domain/Catalog/DTOs/ProductAttributeData.php`** (New - Spatie Laravel Data)
    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Catalog\DTOs;

    use Spatie\LaravelData\Data;
    use Spatie\LaravelData\Optional;

    class ProductAttributeData extends Data
    {
        public function __construct(
            public string|Optional|null $scent_type,
            public string|Optional|null $mood_effect,
            public string|Optional|null $intensity_level
        ) {}
    }
    ```

15. **Event: `app/Domain/Catalog/Events/ProductViewedEvent.php`** (New)
    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Catalog\Events;

    use App\Domain\Catalog\Models\Product;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Queue\SerializesModels;

    class ProductViewedEvent
    {
        use Dispatchable, SerializesModels;

        public Product $product;
        public ?int $userId; // Optional user ID if logged in

        public function __construct(Product $product, ?int $userId = null)
        {
            $this->product = $product;
            $this->userId = $userId;
        }
    }
    ```

---
#### Step 2.3: Inventory Domain

1.  **Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_inventory_movements_table.php`** (New)

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
            Schema::create('inventory_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('quantity_change')->comment('Positive for stock in, negative for stock out');
                $table->enum('type', ['sale', 'restock', 'return', 'adjustment', 'initial']);
                $table->unsignedBigInteger('reference_id')->nullable()->comment('e.g., order_id for sale, purchase_order_id for restock');
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent(); // No updated_at for immutable log

                $table->index(['product_id', 'created_at']);
                $table->index('type');
                $table->index('reference_id'); // If you query by reference
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('inventory_movements');
        }
    };
    ```

2.  **Model: `app/Domain/Inventory/Models/InventoryMovement.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Inventory\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use App\Domain\Catalog\Models\Product;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    class InventoryMovement extends Model
    {
        use HasFactory;

        protected $fillable = [
            'product_id',
            'quantity_change',
            'type',
            'reference_id',
            'notes',
        ];

        /**
         * Indicates if the model should be timestamped.
         * Only 'created_at' is used for this log-like table.
         *
         * @var bool
         */
        public $timestamps = false; // Set to true if you want both created_at and updated_at auto-managed by Eloquent

        // If $timestamps = false, and you want created_at to be managed by Eloquent on creation:
        // const CREATED_AT = 'created_at';
        // const UPDATED_AT = null; // No updated_at column

        // However, the migration defines created_at with default CURRENT_TIMESTAMP,
        // so Eloquent's auto-timestamping might not be strictly necessary if $timestamps = false.
        // If $timestamps = true (default), Eloquent will manage both. Let's keep it simple with true.
        // public $timestamps = true; // (Corrected - easier)

        protected $casts = [
            'quantity_change' => 'integer',
        ];


        public function product(): BelongsTo
        {
            return $this->belongsTo(Product::class);
        }
    }
    ```
    *Correction:* For simplicity and standard Eloquent behavior, it's better to let Eloquent manage both `created_at` and `updated_at` if `updated_at` is desired. If only `created_at` is needed and to be auto-managed by the DB, then `$timestamps = false` and ensure the migration sets `created_at` appropriately. The provided SQL schema for `inventory_movements` *only* has `created_at`. So, let's adjust the model to reflect that:

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Inventory\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use App\Domain\Catalog\Models\Product;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    class InventoryMovement extends Model
    {
        use HasFactory;

        const UPDATED_AT = null; // No updated_at column

        protected $fillable = [
            'product_id',
            'quantity_change',
            'type',
            'reference_id',
            'notes',
            'created_at', // Allow mass assignment if setting manually
        ];

        protected $casts = [
            'quantity_change' => 'integer',
            'created_at' => 'datetime',
        ];

        public function product(): BelongsTo
        {
            return $this->belongsTo(Product::class);
        }
    }
    ```

3.  **Factory: `database/factories/InventoryMovementFactory.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace Database\Factories;

    use App\Domain\Inventory\Models\InventoryMovement;
    use App\Domain\Catalog\Models\Product;
    use Illuminate\Database\Eloquent\Factories\Factory;

    class InventoryMovementFactory extends Factory
    {
        protected $model = InventoryMovement::class;

        public function definition(): array
        {
            $type = fake()->randomElement(['sale', 'restock', 'return', 'adjustment', 'initial']);
            $quantityChange = match ($type) {
                'sale', 'adjustment' => fake()->numberBetween(-100, -1), // Adjustment can be negative
                'return', 'restock', 'initial' => fake()->numberBetween(1, 100),
                default => fake()->numberBetween(-50,50),
            };

            return [
                'product_id' => Product::factory(),
                'quantity_change' => $quantityChange,
                'type' => $type,
                'reference_id' => $type === 'sale' ? fake()->optional()->randomNumber(5) : null, // e.g. order ID
                'notes' => fake()->optional()->sentence(),
                'created_at' => fake()->dateTimeThisYear(),
            ];
        }
    }
    ```

4.  **Service: `app/Domain/Inventory/Services/InventoryService.php`** (New - Implemented)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Inventory\Services;

    use App\Domain\Catalog\Models\Product;
    use App\Domain\Inventory\Models\InventoryMovement;
    use Illuminate\Support\Facades\DB;
    use App\Domain\Inventory\Exceptions\InsufficientStockException;
    use App\Domain\Inventory\Events\StockLevelChangedEvent;
    use App\Domain\Inventory\Events\LowStockThresholdReachedEvent;
    use Throwable;

    class InventoryService
    {
        public function adjustStock(
            Product $product,
            int $quantityChange,
            string $type,
            ?int $referenceId = null,
            ?string $notes = null
        ): InventoryMovement {
            if ($quantityChange === 0) {
                throw new \InvalidArgumentException('Quantity change cannot be zero.');
            }

            return DB::transaction(function () use ($product, $quantityChange, $type, $referenceId, $notes) {
                // Lock the product row for update to prevent race conditions
                $product = Product::lockForUpdate()->findOrFail($product->id);

                $newStockLevel = $product->stock_quantity + $quantityChange;

                if ($newStockLevel < 0 && !$product->backorder_allowed) {
                    throw new InsufficientStockException(
                        "Not enough stock for product ID {$product->id} ({$product->name}). Available: {$product->stock_quantity}, Requested change: {$quantityChange}"
                    );
                }

                $product->stock_quantity = $newStockLevel;
                $product->save();

                $movement = InventoryMovement::create([
                    'product_id' => $product->id,
                    'quantity_change' => $quantityChange,
                    'type' => $type,
                    'reference_id' => $referenceId,
                    'notes' => $notes,
                ]);

                StockLevelChangedEvent::dispatch($product, $quantityChange, $type, $referenceId);

                if ($product->stock_quantity <= $product->low_stock_threshold && $quantityChange < 0) {
                    LowStockThresholdReachedEvent::dispatch($product);
                }

                return $movement;
            });
        }

        public function decreaseStockOnSale(Product $product, int $quantity, int $orderId): InventoryMovement
        {
            if ($quantity <= 0) {
                throw new \InvalidArgumentException('Quantity to decrease must be positive for a sale.');
            }
            return $this->adjustStock($product, -$quantity, 'sale', $orderId, "Order #{$orderId}");
        }

        public function increaseStockOnReturn(Product $product, int $quantity, int $orderId): InventoryMovement
        {
            if ($quantity <= 0) {
                throw new \InvalidArgumentException('Quantity to increase must be positive for a return.');
            }
            return $this->adjustStock($product, $quantity, 'return', $orderId, "Return for Order #{$orderId}");
        }

        public function restockProduct(Product $product, int $quantity, ?string $notes = 'Product restock'): InventoryMovement
        {
             if ($quantity <= 0) {
                throw new \InvalidArgumentException('Quantity to restock must be positive.');
            }
            return $this->adjustStock($product, $quantity, 'restock', null, $notes);
        }


        public function getStockLevel(Product $product): int
        {
            // Could also query movements table for sum, but product.stock_quantity should be authoritative
            return $product->stock_quantity;
        }

        public function isStockAvailable(Product $product, int $requestedQuantity): bool
        {
            if ($requestedQuantity <= 0) {
                return true; // Requesting zero or negative quantity is always "available" in a sense
            }
            return $product->stock_quantity >= $requestedQuantity || $product->backorder_allowed;
        }
    }
    ```

5.  **Exception: `app/Domain/Inventory/Exceptions/InsufficientStockException.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Inventory\Exceptions;

    use RuntimeException;

    class InsufficientStockException extends RuntimeException
    {
        // You can add custom properties or methods if needed
    }
    ```

6.  **Event: `app/Domain/Inventory/Events/StockLevelChangedEvent.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Inventory\Events;

    use App\Domain\Catalog\Models\Product;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Queue\SerializesModels;

    class StockLevelChangedEvent
    {
        use Dispatchable, SerializesModels;

        public Product $product;
        public int $quantityChange;
        public string $type;
        public ?int $referenceId;

        public function __construct(Product $product, int $quantityChange, string $type, ?int $referenceId = null)
        {
            $this->product = $product;
            $this->quantityChange = $quantityChange;
            $this->type = $type;
            $this->referenceId = $referenceId;
        }
    }
    ```

7.  **Event: `app/Domain/Inventory/Events/LowStockThresholdReachedEvent.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Inventory\Events;

    use App\Domain\Catalog\Models\Product;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Queue\SerializesModels;

    class LowStockThresholdReachedEvent
    {
        use Dispatchable, SerializesModels;

        public Product $product;

        public function __construct(Product $product)
        {
            $this->product = $product;
        }
    }
    ```

---

This completes the generation of files for Steps 2.1, 2.2, and 2.3. Remember to create the migration files with appropriate timestamps in their filenames so they run in the correct order. After adding these files, you would typically run `composer dump-autoload` and then `./vendor/bin/sail artisan migrate`. Then you can start building out the controller logic and frontend views that use these domain components.
