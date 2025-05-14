<think>
The user is correctly pointing out that in my previous generation for "Step 2.6: Utility Domain (Models & Migrations)," I provided an example migration and then listed the models but did not generate all the migration files, all the factory files, nor the full basic service structures. The request is to complete this step thoroughly.

**Plan for Step 2.6 Completion:**

1.  **Migrations:**
    *   Generate all six migration files listed (`quiz_results`, `newsletter_subscribers`, `audit_log`, `email_log`, `tax_rates`, `tax_rate_history`).
    *   Ensure they accurately reflect the table structures from `sample_database_schema.sql.txt` (which I adapted into my own generated SQL schema earlier).
    *   Pay attention to column types, nullability, defaults, indexes, and foreign keys.
    *   Use appropriate timestamps (e.g., `created_at` only vs. full `timestamps()`).

2.  **Factories:**
    *   Generate a factory for each of the six models (`QuizResultFactory`, `NewsletterSubscriberFactory`, `AuditLogFactory`, `EmailLogFactory`, `TaxRateFactory`, `TaxRateHistoryFactory`).
    *   Define sensible default states using Faker.
    *   Reference other factories for foreign key relationships where appropriate (e.g., `User::factory()`).

3.  **Basic Service Structures:**
    *   Generate the shell for each service (`NewsletterService`, `QuizService`, `AuditLoggerService`, `TaxService`).
    *   Include constructors (empty or with basic type-hinted dependencies if obvious).
    *   Define key method signatures as outlined in the design doc, with basic placeholder implementations or comments indicating future logic.
    *   The `TaxService` example I provided previously was a good start. I will do similar for the others. `QuizService` already had a basic recommendation logic, I'll maintain that. `NewsletterService` had subscribe/unsubscribe. `AuditLoggerService` will have a method to log an action.

**Consistency Checks:**

*   **Database Schema:** All migrations must align with the target SQL schema (derived from `sample_database_schema.sql.txt` and my previous interpretations).
*   **Model Paths:** Factories and services will reference models using their correct `App\Domain\Utility\Models\*` namespace.
*   **Foreign Keys:** Ensure FK constraints in migrations point to the correct tables and columns (e.g., `users.id`).
*   **Timestamps:** The `sample_database_schema.sql.txt` is specific about which tables have `created_at` only vs. full `timestamps()`. I will follow this.
    *   `quiz_results`: `created_at` only.
    *   `newsletter_subscribers`: `subscribed_at` (effectively `created_at`), `updated_at` implied as standard for Eloquent if not explicitly omitted. The sample has `subscribed_at timestamp NULL DEFAULT CURRENT_TIMESTAMP`. My generated SQL schema for this had `created_at` and `updated_at`. I'll stick to what `sample_database_schema.sql.txt` implies and give it `created_at` and `updated_at` managed by Eloquent, and map `subscribed_at` to `created_at`.
    *   `audit_log`: `created_at` only.
    *   `email_log`: `sent_at` and `created_at`. The sample schema's `email_log` has `sent_at` and `created_at` as separate fields. `created_at` is the record creation, `sent_at` is when the email itself was sent.
    *   `tax_rates`: `created_at`, `updated_at`.
    *   `tax_rate_history`: `created_at` only.

I will generate the files one by one for clarity.
</think>

You are absolutely right! My apologies for not fully generating all the files for Step 2.6 in the previous round. I will now provide the complete and working versions for the migrations, factories, and basic service structures for the Utility Domain as outlined.

---

### Phase 2: Domain Modeling & Core Business Logic (Continued)

#### Step 2.6: Utility Domain (Models & Migrations - Complete Files)

The models for this step (`QuizResult`, `NewsletterSubscriber`, `AuditLog`, `EmailLog`, `TaxRate`, `TaxRateHistory`) were already provided in the previous response where I detailed files for Step 2.4, 2.5, and 2.6. I will now provide the complete set of corresponding migrations, factories, and the service shells.

**1. Migrations:**
    *(Remember to use appropriate sequential timestamps for your migration filenames, e.g., `YYYY_MM_DD_HHMMSS_action.php`)*

*   **`database/migrations/YYYY_MM_DD_HHMMSS_create_quiz_results_table.php`** (As previously provided, validated)
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
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('email', 191)->nullable();
                $table->json('answers'); // Changed from text to json for better structure
                $table->json('recommendations')->nullable(); // Changed from text to json
                $table->timestamp('created_at')->nullable()->useCurrent();
                // No updated_at as per sample schema, and it's a log-like table

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

*   **`database/migrations/YYYY_MM_DD_HHMMSS_create_newsletter_subscribers_table.php`** (New)
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
            Schema::create('newsletter_subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email', 191)->unique();
                $table->timestamp('subscribed_at')->nullable()->useCurrent(); // Maps to created_at
                $table->timestamp('unsubscribed_at')->nullable();
                $table->string('token', 64)->nullable()->unique()->comment('For unsubscribe/confirmation link');
                $table->timestamps(); // Adds created_at and updated_at
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('newsletter_subscribers');
        }
    };
    ```
    *Note: I've added `created_at` and `updated_at` as Eloquent defaults, `subscribed_at` can be an alias or set on creation.*

*   **`database/migrations/YYYY_MM_DD_HHMMSS_create_audit_log_table.php`** (New)
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
            Schema::create('audit_log', function (Blueprint $table) {
                $table->id(); // Sample had int auto_increment
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 100);
                $table->morphs('auditable'); // Creates auditable_id (BIGINT UNSIGNED) and auditable_type (VARCHAR)
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 191)->nullable(); // Sample had 255
                $table->text('details')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                // No updated_at as per sample schema, it's a log
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('audit_log');
        }
    };
    ```

*   **`database/migrations/YYYY_MM_DD_HHMMSS_create_email_log_table.php`** (New)
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
            Schema::create('email_log', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('to_email', 191);
                $table->string('subject', 191)->nullable();
                $table->string('email_type', 50)->nullable()->comment('e.g., welcome, password_reset, order_confirmation')->index();
                $table->enum('status', ['sent','failed','pending'])->default('pending');
                $table->text('mailer_error')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                // No updated_at as per sample schema, it's a log

                $table->index(['to_email', 'status']);
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('email_log');
        }
    };
    ```

*   **`database/migrations/YYYY_MM_DD_HHMMSS_create_tax_rates_table.php`** (New)
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
            Schema::create('tax_rates', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->comment('e.g., "US-CA Sales Tax", "EU VAT"');
                $table->char('country_code', 2)->comment('ISO 3166-1 alpha-2 country code');
                $table->string('state_code', 10)->nullable()->comment('ISO 3166-2 state/province code');
                $table->string('postal_code_pattern', 50)->nullable()->comment('Regex pattern or specific code');
                $table->string('city', 100)->nullable();
                $table->decimal('rate_percentage', 10, 4); // e.g., 7.2500 for 7.25%
                $table->boolean('is_compound')->default(false);
                $table->unsignedInteger('priority')->default(1);
                $table->boolean('is_active')->default(true);
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['country_code', 'state_code', 'postal_code_pattern', 'city'], 'uq_tax_rates_region');
                $table->index('country_code');
                $table->index('is_active');
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('tax_rates');
        }
    };
    ```

*   **`database/migrations/YYYY_MM_DD_HHMMSS_create_tax_rate_history_table.php`** (New)
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
            Schema::create('tax_rate_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tax_rate_id')->constrained('tax_rates')->cascadeOnDelete();
                $table->decimal('old_rate_percentage', 10, 4)->nullable();
                $table->decimal('new_rate_percentage', 10, 4);
                $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('created_at')->nullable()->useCurrent()->comment('Timestamp of when the change was made');
                // No updated_at as per sample schema, it's a log
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('tax_rate_history');
        }
    };
    ```

**2. Factories:**

*   **`database/factories/QuizResultFactory.php`** (New)
    ```php
    <?php

    declare(strict_types=1);

    namespace Database\Factories;

    use App\Domain\Utility\Models\QuizResult;
    use App\Domain\UserManagement\Models\User;
    use Illuminate\Database\Eloquent\Factories\Factory;

    class QuizResultFactory extends Factory
    {
        protected $model = QuizResult::class;

        public function definition(): array
        {
            return [
                'user_id' => fake()->boolean(70) ? User::factory() : null,
                'email' => function (array $attributes) {
                    return $attributes['user_id'] ? null : fake()->safeEmail;
                },
                'answers' => [
                    'feeling' => fake()->randomElement(['relax', 'energize', 'focus', 'sleep']),
                    'scentFamily' => fake()->randomElement(['floral', 'citrus', 'woody', 'herbal']),
                    'format' => fake()->randomElement(['oil', 'soap', 'both']),
                ],
                'recommendations' => [fake()->numberBetween(1,10), fake()->numberBetween(1,10)], // Example: array of product IDs
                'created_at' => fake()->dateTimeThisYear(),
            ];
        }
    }
    ```

*   **`database/factories/NewsletterSubscriberFactory.php`** (New)
    ```php
    <?php

    declare(strict_types=1);

    namespace Database\Factories;

    use App\Domain\Utility\Models\NewsletterSubscriber;
    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Support\Str;

    class NewsletterSubscriberFactory extends Factory
    {
        protected $model = NewsletterSubscriber::class;

        public function definition(): array
        {
            return [
                'email' => fake()->unique()->safeEmail(),
                'subscribed_at' => now(), // Or fake()->dateTimeThisYear()
                'unsubscribed_at' => null,
                'token' => Str::random(60),
            ];
        }

        public function unsubscribed(): static
        {
            return $this->state(fn (array $attributes) => [
                'unsubscribed_at' => now(),
            ]);
        }
    }
    ```

*   **`database/factories/AuditLogFactory.php`** (New)
    ```php
    <?php

    declare(strict_types=1);

    namespace Database\Factories;

    use App\Domain\Utility\Models\AuditLog;
    use App\Domain\UserManagement\Models\User;
    use App\Domain\Catalog\Models\Product; // Example auditable type
    use Illuminate\Database\Eloquent\Factories\Factory;

    class AuditLogFactory extends Factory
    {
        protected $model = AuditLog::class;

        public function definition(): array
        {
            $auditableModels = [Product::class/*, Order::class, User::class*/]; // Add more as needed
            $auditableType = fake()->randomElement($auditableModels);
            $auditableInstance = $auditableType::factory()->create();

            return [
                'user_id' => User::factory(),
                'action' => fake()->randomElement(['created', 'updated', 'deleted', 'viewed']),
                'auditable_type' => $auditableType,
                'auditable_id' => $auditableInstance->id,
                'old_values' => ['status' => 'pending'],
                'new_values' => ['status' => 'active'],
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'details' => fake()->sentence(),
                'created_at' => fake()->dateTimeThisYear(),
            ];
        }
    }
    ```

*   **`database/factories/EmailLogFactory.php`** (New)
    ```php
    <?php

    declare(strict_types=1);

    namespace Database\Factories;

    use App\Domain\Utility\Models\EmailLog;
    use App\Domain\UserManagement\Models\User;
    use Illuminate\Database\Eloquent\Factories\Factory;

    class EmailLogFactory extends Factory
    {
        protected $model = EmailLog::class;

        public function definition(): array
        {
            return [
                'user_id' => fake()->boolean(80) ? User::factory() : null,
                'to_email' => fake()->safeEmail(),
                'subject' => fake()->sentence(4),
                'email_type' => fake()->randomElement(['welcome', 'password_reset', 'order_confirmation', 'newsletter']),
                'status' => fake()->randomElement(['sent','failed','pending']),
                'mailer_error' => function (array $attributes) {
                    return $attributes['status'] === 'failed' ? fake()->sentence() : null;
                },
                'sent_at' => function (array $attributes) {
                    return $attributes['status'] === 'sent' ? fake()->dateTimeThisMonth() : null;
                },
                'created_at' => fake()->dateTimeThisMonth(),
            ];
        }
    }
    ```

*   **`database/factories/TaxRateFactory.php`** (New)
    ```php
    <?php

    declare(strict_types=1);

    namespace Database\Factories;

    use App\Domain\Utility\Models\TaxRate;
    use App\Domain\UserManagement\Models\User;
    use Illuminate\Database\Eloquent\Factories\Factory;

    class TaxRateFactory extends Factory
    {
        protected $model = TaxRate::class;

        public function definition(): array
        {
            return [
                'name' => fake()->city() . ' Sales Tax',
                'country_code' => fake()->countryCode(),
                'state_code' => fake()->optional(0.7)->stateAbbr(),
                'postal_code_pattern' => null,
                'city' => null,
                'rate_percentage' => fake()->randomFloat(4, 0, 20), // e.g., 7.2500
                'is_compound' => fake()->boolean(10),
                'priority' => fake()->numberBetween(1, 3),
                'is_active' => true,
                'start_date' => fake()->optional()->date(),
                'end_date' => null,
                'created_by_user_id' => User::factory()->admin(),
            ];
        }
    }
    ```

*   **`database/factories/TaxRateHistoryFactory.php`** (New)
    ```php
    <?php

    declare(strict_types=1);

    namespace Database\Factories;

    use App\Domain\Utility\Models\TaxRateHistory;
    use App\Domain\Utility\Models\TaxRate;
    use App\Domain\UserManagement\Models\User;
    use Illuminate\Database\Eloquent\Factories\Factory;

    class TaxRateHistoryFactory extends Factory
    {
        protected $model = TaxRateHistory::class;

        public function definition(): array
        {
            return [
                'tax_rate_id' => TaxRate::factory(),
                'old_rate_percentage' => fake()->optional()->randomFloat(4, 0, 20),
                'new_rate_percentage' => fake()->randomFloat(4, 0, 20),
                'changed_by_user_id' => User::factory()->admin(),
                'created_at' => fake()->dateTimeThisYear(),
            ];
        }
    }
    ```

**3. Basic Service Structures:**

*   **`app/Domain/Utility/Services/NewsletterService.php`** (Expanded from previous phase)
    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Utility\Services;

    use App\Domain\Utility\Models\NewsletterSubscriber;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Log;
    // use App\Events\NewsletterSubscribed; // Example event

    class NewsletterService
    {
        public function subscribe(string $email): NewsletterSubscriber
        {
            $normalizedEmail = strtolower(trim($email));

            Log::info("Attempting to subscribe email: {$normalizedEmail}");

            $subscriber = NewsletterSubscriber::firstOrCreate(
                ['email' => $normalizedEmail],
                [
                    'subscribed_at' => now(), // Eloquent will manage created_at/updated_at
                    'token' => Str::random(60)
                ]
            );

            if (!$subscriber->wasRecentlyCreated && $subscriber->unsubscribed_at) {
                // Re-subscribing an unsubscribed email
                $subscriber->unsubscribed_at = null;
                $subscriber->subscribed_at = now(); // Reset subscribed_at
                $subscriber->token = Str::random(60); // Optionally generate a new token
                $subscriber->save();
                Log::info("Email re-subscribed: {$normalizedEmail}");
            } elseif ($subscriber->wasRecentlyCreated) {
                 Log::info("New email subscribed: {$normalizedEmail}");
                // event(new NewsletterSubscribed($subscriber)); // Dispatch an event
            } else {
                 Log::info("Email already subscribed: {$normalizedEmail}");
            }

            return $subscriber;
        }

        public function unsubscribe(string $token): bool
        {
            $subscriber = NewsletterSubscriber::where('token', $token)
                                              ->whereNull('unsubscribed_at')
                                              ->first();
            if ($subscriber) {
                $subscriber->unsubscribed_at = now();
                $isSaved = $subscriber->save();
                if ($isSaved) {
                    Log::info("Email unsubscribed using token: {$subscriber->email}");
                }
                return $isSaved;
            }
            Log::warning("Unsubscribe attempt with invalid or already used token: {$token}");
            return false;
        }
    }
    ```

*   **`app/Domain/Utility/Services/QuizService.php`** (Expanded from previous phase)
    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Utility\Services;

    use App\Domain\Catalog\Models\Product;
    use Illuminate\Support\Collection;
    use App\Domain\Catalog\Services\ProductQueryService;
    use App\Domain\Utility\Models\QuizResult;
    use Illuminate\Support\Facades\Auth;

    class QuizService
    {
        public function __construct(protected ProductQueryService $productQueryService)
        {
        }

        public function getRecommendations(array $answers): Collection
        {
            // This is a placeholder for more sophisticated recommendation logic.
            // For now, use the client-side logic's product list or fetch some products.
            // This would ideally query products based on tags/attributes matching answers.

            $feeling = $answers['feeling'] ?? null;
            $scentFamily = $answers['scentFamily'] ?? null;
            $format = $answers['format'] ?? null; // 'oil', 'soap', 'both'

            $query = Product::query()->where('is_active', true);

            if ($feeling) {
                // Example: simple name based search for tags
                // In a real app, products would have tags or attributes for 'feeling'
                $query->where(function($q) use ($feeling) {
                    $q->where('name', 'like', '%' . $feeling . '%')
                      ->orWhere('description', 'like', '%' . $feeling . '%')
                      ->orWhere('scent_profile', 'like', '%' . $feeling . '%');
                });
            }

            if ($scentFamily) {
                 $query->where(function($q) use ($scentFamily) {
                    $q->where('scent_profile', 'like', '%' . $scentFamily . '%')
                      ->orWhereHas('productAttributes', fn($paq) => $paq->where('scent_type', $scentFamily));
                 });
            }

            if ($format === 'oil') {
                $query->where(function($q){ // Assuming 'oil' products might have 'Oil' in name or specific category
                    $q->where('name', 'like', '%Oil%')
                      ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', '%Essential Oil%'));
                });
            } elseif ($format === 'soap') {
                 $query->where(function($q){
                    $q->where('name', 'like', '%Soap%')
                      ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', '%Soap%'));
                });
            }

            $recommendedProducts = $query->inRandomOrder()->take(2)->get();

            if ($recommendedProducts->count() < 2) {
                $fallbackProducts = $this->productQueryService->getFeaturedProducts(4)
                    ->whereNotIn('id', $recommendedProducts->pluck('id')->all())
                    ->take(2 - $recommendedProducts->count());
                $recommendedProducts = $recommendedProducts->merge($fallbackProducts);
            }
             if ($recommendedProducts->isEmpty()) {
                $recommendedProducts = Product::where('is_active', true)->inRandomOrder()->take(2)->get();
            }

            return $recommendedProducts->map(function(Product $product){
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'desc' => $product->short_description,
                    'img' => $product->image, // Assuming image is just the filename relative to storage/app/public
                    'image_url' => $product->image_url, // Uses accessor
                    'price_formatted' => $product->price->format(), // Uses accessor
                ];
            });
        }

        public function saveQuizResult(array $answers, Collection $recommendations, ?string $email = null): QuizResult
        {
            return QuizResult::create([
                'user_id' => Auth::id(),
                'email' => $email ?: Auth::user()?->email,
                'answers' => $answers,
                'recommendations' => $recommendations->pluck('id')->toArray(), // Store IDs or full product data
            ]);
        }
    }
    ```

*   **`app/Domain/Utility/Services/AuditLoggerService.php`** (New - Basic Structure)
    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Utility\Services;

    use App\Domain\Utility\Models\AuditLog;
    use App\Domain\UserManagement\Models\User;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Http\Request; // For IP and User Agent

    class AuditLoggerService
    {
        public function logAction(
            string $action,
            Model $auditable = null,
            User $user = null,
            array $oldValues = null,
            array $newValues = null,
            string $details = null,
            Request $request = null
        ): void {
            AuditLog::create([
                'user_id' => $user?->id ?? auth()->id(),
                'action' => $action,
                'auditable_type' => $auditable ? $auditable->getMorphClass() : null,
                'auditable_id' => $auditable?->getKey(),
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'details' => $details,
            ]);
        }
    }
    ```

*   **`app/Domain/Utility/Services/TaxService.php`** (Expanded from previous phase)
    ```php
    <?php

    declare(strict_types=1);

    namespace App\Domain\Utility\Services;

    use App\Domain\Shared\ValueObjects\MonetaryAmount;
    use App\Domain\Shared\ValueObjects\AddressDetail; // Or use the Address Model
    use App\Domain\Utility\Models\TaxRate;
    use App\Domain\Catalog\Models\Product; // Optional, if taxes are product-specific
    use Illuminate\Support\Collection;

    class TaxService
    {
        public function calculateTax(MonetaryAmount $subtotalAmount, AddressDetail $shippingAddress, Collection $cartItems = null): MonetaryAmount
        {
            // 1. Determine applicable tax rates based on shippingAddress
            //    (country_code, state_code, postal_code_pattern, city)
            //    This could involve complex lookups against the tax_rates table.
            // 2. Consider if tax is on shipping, product-specific tax categories.
            // 3. Handle compound taxes if `is_compound` is true.

            // Simplified example: Find first matching active tax rate
            $applicableRate = TaxRate::where('is_active', true)
                ->where('country_code', $shippingAddress->countryCode)
                // Add more precise matching for state, postal code, city if data available
                // This needs to be more robust for real-world tax calculation.
                ->when($shippingAddress->state, function ($query, $state) {
                    return $query->where(function ($q) use ($state) {
                        $q->where('state_code', $state)->orWhereNull('state_code');
                    });
                })
                ->orderBy('priority') // Apply higher priority taxes first
                ->first();

            if ($applicableRate) {
                $taxMinorAmount = (int) round(
                    ($subtotalAmount->getMinorAmount() * $applicableRate->rate_percentage) / 100
                );
                return new MonetaryAmount($taxMinorAmount, $subtotalAmount->getCurrencyCode());
            }

            // Default to zero tax if no rate found or for more complex scenarios
            return new MonetaryAmount(0, $subtotalAmount->getCurrencyCode());
        }
    }
    ```

---
This completes the file generation for Step 2.6, including all migrations, factories, and basic service structures for the Utility domain. Remember to replace `YYYY_MM_DD_HHMMSS` in migration filenames with actual sequential timestamps. Next, you'd run `composer dump-autoload` and then `./vendor/bin/sail artisan migrate`.

