<think>
The user wants me to generate the files for **Phase 7: API Development**, specifically **Steps 7.1, 7.2, and 7.3** from the execution plan. This phase focuses on building out the RESTful API endpoints.

This involves:

*   **Step 7.1: API Authentication & Base Setup**
    *   Update `routes/api.php` (structure for `/v1` prefix, Sanctum middleware).
    *   Implement `app/Http/Controllers/Api/V1/AuthController.php` (login, register, logout for API token generation using Sanctum).
    *   Create `app/Http/Requests/Api/V1/LoginRequest.php`, `RegisterRequest.php`.
    *   Create `app/Http/Resources/UserResource.php`.

*   **Step 7.2: Product & Cart API Endpoints**
    *   Implement `app/Http/Controllers/Api/V1/ProductController.php` (`index`, `show`).
    *   Implement `app/Http/Controllers/Api/V1/CategoryController.php` (`index`, `show`).
    *   Implement `app/Http/Controllers/Api/V1/CartController.php` (`index`, `storeItem`, `updateItem`, `destroyItem`).
    *   Create `app/Http/Resources/ProductResource.php`, `CategoryResource.php`, `CartResource.php` (and `CartItemResource`).

*   **Step 7.3: Checkout & Order API Endpoints**
    *   Implement `app/Http/Controllers/Api/V1/CheckoutController.php` (`prepare` for PaymentIntent, `complete` for order creation).
    *   Implement `app/Http/Controllers/Api/V1/OrderController.php` (`index`, `show` for user's orders).
    *   Create `app/Http/Resources/OrderResource.php` (and `OrderItemResource`).

**Key Considerations for API Implementation:**

1.  **Consistency with Domain Layer:** API controllers will primarily be thin layers, delegating business logic to the already created Domain Services (e.g., `ProductQueryService`, `CartService`, `CheckoutService`, `OrderService`, `AuthService`).
2.  **Authentication:** Laravel Sanctum will be used for API token authentication. Routes needing authentication will use the `auth:sanctum` middleware.
3.  **API Resources:** All data returned by the API will be formatted using Laravel API Resources for consistency and to control data exposure (e.g., `ProductResource`, `UserResource`, `OrderResource`).
4.  **Validation:** API requests will use Form Request classes (e.g., `Api\V1\LoginRequest`) for validation.
5.  **Error Handling:** API will return appropriate HTTP status codes for errors (e.g., 400, 401, 403, 404, 422, 500) with JSON error responses.
6.  **Versioning:** All API routes will be prefixed with `/api/v1/`.
7.  **Rate Limiting:** The `RouteServiceProvider` already has a basic `'api'` rate limiter defined. This will apply.
8.  **Data Exposure:** API Resources are crucial for ensuring only necessary data is exposed. For example, user resource should not expose password hashes.
9.  **Cart Management via API:** Needs careful handling of guest vs. authenticated user carts. Sanctum handles authenticated users. For guest carts via API, a device identifier or a session-like mechanism might be needed if Sanctum's SPA session support isn't used (which is more for first-party SPAs). For pure token-based API, guest cart management is trickier without a session. I will assume for now that API cart management is primarily for authenticated users. If guest cart management via API is needed, a separate discussion on strategy (e.g., passing a unique device ID) would be required. For now, `CartController` (API) will assume authenticated users.
10. **Checkout via API:** This will also assume an authenticated user. The `CheckoutController` will prepare PaymentIntents and allow order completion.
11. **Monetary Values:** API Resources should format `MonetaryAmount` objects appropriately (e.g., decimal string and currency code).

**File Generation Strategy:**
I will go step-by-step (7.1, 7.2, 7.3) and generate the necessary files for each.

*   **7.1 API Auth:** `AuthController`, `LoginRequest`, `RegisterRequest`, `UserResource`, and `routes/api.php` setup.
*   **7.2 Product & Cart API:** `ProductController`, `CategoryController`, `CartController`, and their respective Resources and Form Requests (if any complex input for cart).
*   **7.3 Checkout & Order API:** `CheckoutController`, `OrderController`, and their Resources.

This phase focuses on exposing the existing backend logic via a well-defined API.
The `routes/api.php` will need significant updates.
I'll ensure API Resources load necessary relationships (`whenLoaded`).
</think>

Okay, let's build out the API for "The Scent"! This phase will focus on creating the RESTful API endpoints as outlined in **Phase 7 (Steps 7.1 to 7.3)** of your execution plan. We'll ensure these endpoints are secure, well-structured, and leverage the domain services we've already built.

---

### Phase 7: API Development

#### Step 7.1: API Authentication & Base Setup

1.  **Routes: `routes/api.php`** (Updated for V1 structure and Auth)
    *   This file will define all `/api/v1/...` routes.

    ```php
    <?php

    declare(strict_types=1);

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Api\V1\AuthController;
    use App\Http\Controllers\Api\V1\ProductController as V1ProductController;
    use App\Http\Controllers\Api\V1\CategoryController as V1CategoryController;
    use App\Http\Controllers\Api\V1\CartController as V1CartController;
    use App\Http\Controllers\Api\V1\CheckoutController as V1CheckoutController;
    use App\Http\Controllers\Api\V1\OrderController as V1OrderController;

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider and all of them will
    | be assigned to the "api" middleware group. Make something great!
    |
    */

    Route::prefix('v1')->name('api.v1.')->group(function () {
        // Authentication Routes
        Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

        // Publicly Accessible Routes (Products, Categories)
        Route::get('/products', [V1ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product:slug}', [V1ProductController::class, 'show'])->name('products.show');
        Route::get('/categories', [V1CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/{category:slug}', [V1CategoryController::class, 'show'])->name('categories.show');

        // Authenticated Routes (Protected by Sanctum)
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::get('/user', [AuthController::class, 'user'])->name('auth.user');

            // Cart Management API
            Route::prefix('cart')->name('cart.')->group(function () {
                Route::get('/', [V1CartController::class, 'index'])->name('index');
                Route::post('/items', [V1CartController::class, 'storeItem'])->name('items.store');
                Route::put('/items/{cartItemId}', [V1CartController::class, 'updateItem'])->name('items.update');
                Route::delete('/items/{cartItemId}', [V1CartController::class, 'destroyItem'])->name('items.destroy');
                Route::post('/coupon', [V1CartController::class, 'applyCoupon'])->name('coupon.apply');
                Route::delete('/coupon', [V1CartController::class, 'removeCoupon'])->name('coupon.remove');
            });

            // Checkout API
            Route::prefix('checkout')->name('checkout.')->group(function () {
                Route::post('/prepare', [V1CheckoutController::class, 'prepare'])->name('prepare');
                Route::post('/complete', [V1CheckoutController::class, 'complete'])->name('complete');
            });

            // User Orders API
            Route::prefix('orders')->name('orders.')->group(function() {
                Route::get('/', [V1OrderController::class, 'index'])->name('index');
                Route::get('/{order}', [V1OrderController::class, 'show'])->name('show'); // Uses route model binding by ID
            });

            // User Profile & Address Management API (Examples)
            // Route::get('/profile', [V1UserProfileController::class, 'show'])->name('profile.show');
            // Route::put('/profile', [V1UserProfileController::class, 'update'])->name('profile.update');
            // Route::apiResource('/addresses', V1AddressController::class);
        });
    });

    // Webhook routes are typically defined in routes/web.php or a dedicated webhook file,
    // as they often require CSRF exemption and use different auth (signature verification).
    // If you want a Stripe webhook under /api, ensure CSRF is handled.
    // Route::post('/webhooks/stripe', [\App\Http\Controllers\Webhook\StripeWebhookController::class, 'handleWebhook'])->name('api.webhooks.stripe');
    ```

2.  **Controller: `app/Http/Controllers/Api/V1/AuthController.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Http\Controllers\Api\V1;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use App\Domain\UserManagement\Models\User;
    use App\Http\Requests\Api\V1\LoginRequest; // To be created
    use App\Http\Requests\Api\V1\RegisterRequest; // To be created
    use App\Http\Resources\UserResource; // To be created
    use Illuminate\Http\JsonResponse;
    use App\Domain\UserManagement\Events\UserRegisteredEvent;

    class AuthController extends Controller
    {
        public function register(RegisterRequest $request): JsonResponse
        {
            $validated = $request->validated();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'user', // Default role for API registration
            ]);

            event(new UserRegisteredEvent($user));

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully.',
                'user' => UserResource::make($user),
                'token' => $token,
            ], 201);
        }

        public function login(LoginRequest $request): JsonResponse
        {
            $credentials = $request->validated();

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials.'], 401);
            }

            /** @var User $user */
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Logged in successfully.',
                'user' => UserResource::make($user),
                'token' => $token,
            ]);
        }

        public function logout(Request $request): JsonResponse
        {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully.']);
        }

        public function user(Request $request): UserResource
        {
            return UserResource::make($request->user());
        }
    }
    ```

3.  **Form Request: `app/Http/Requests/Api/V1/LoginRequest.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Http\Requests\Api\V1;

    use Illuminate\Foundation\Http\FormRequest;

    class LoginRequest extends FormRequest
    {
        public function authorize(): bool
        {
            return true;
        }

        public function rules(): array
        {
            return [
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
                'device_name' => ['sometimes', 'string', 'max:255'], // Optional for naming the token
            ];
        }
    }
    ```

4.  **Form Request: `app/Http/Requests/Api/V1/RegisterRequest.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Http\Requests\Api\V1;

    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Validation\Rules\Password;

    class RegisterRequest extends FormRequest
    {
        public function authorize(): bool
        {
            return true;
        }

        public function rules(): array
        {
            return [
                'name' => ['required', 'string', 'max:191'],
                'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ];
        }
    }
    ```

5.  **API Resource: `app/Http/Resources/UserResource.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Http\Resources; // Standard location, or App\Http\Resources\Api\V1 if preferred

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\JsonResource;

    class UserResource extends JsonResource
    {
        /**
         * Transform the resource into an array.
         *
         * @return array<string, mixed>
         */
        public function toArray(Request $request): array
        {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'status' => $this->status,
                'email_verified_at' => $this->email_verified_at?->toIso8601String(),
                'newsletter_subscribed' => $this->newsletter_subscribed,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
                // Include default addresses if needed and loaded
                // 'default_shipping_address' => AddressResource::make($this->whenLoaded('defaultShippingAddress')),
                // 'default_billing_address' => AddressResource::make($this->whenLoaded('defaultBillingAddress')),
            ];
        }
    }
    ```

---
#### Step 7.2: Product & Cart API Endpoints

1.  **API Resource: `app/Http/Resources/CategoryResource.php`** (New)

    ```php
    <?php declare(strict_types=1); namespace App\Http\Resources;
    use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource;
    class CategoryResource extends JsonResource {
        public function toArray(Request $request): array {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'parent_id' => $this->parent_id,
                // 'parent' => CategoryResource::make($this->whenLoaded('parent')),
                // 'children' => CategoryResource::collection($this->whenLoaded('children')),
                'created_at' => $this->created_at->toIso8601String(),
            ];
        }
    }
    ```

2.  **API Resource: `app/Http/Resources/ProductAttributeResource.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Resources;
    use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource;
    class ProductAttributeResource extends JsonResource {
        public function toArray(Request $request): array {
            return [
                'id' => $this->id,
                'product_id' => $this->product_id,
                'scent_type' => $this->scent_type,
                'mood_effect' => $this->mood_effect,
                'intensity_level' => $this->intensity_level,
            ];
        }
    }
    ```

3.  **API Resource: `app/Http/Resources/ProductResource.php`** (Expanded/Refined from previous design)

    ```php
    <?php declare(strict_types=1); namespace App\Http\Resources;
    use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource;
    class ProductResource extends JsonResource {
        public function toArray(Request $request): array {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'short_description' => $this->short_description,
                'price' => [ // Using the MonetaryAmount accessor
                    'amount_minor' => $this->price->getMinorAmount(),
                    'amount_decimal' => $this->price->getDecimalAmount(),
                    'currency_code' => $this->price->getCurrencyCode(),
                    'formatted' => $this->price->format(),
                ],
                'sku' => $this->sku,
                'stock_quantity' => $this->stock_quantity,
                'is_active' => $this->is_active,
                'is_featured' => $this->is_featured,
                'backorder_allowed' => $this->backorder_allowed,
                'image_url' => $this->image_url, // Accessor
                'gallery_image_urls' => $this->gallery_image_urls, // Accessor
                'category' => CategoryResource::make($this->whenLoaded('category')),
                'attributes' => ProductAttributeResource::make($this->whenLoaded('productAttributes')),
                'size' => $this->size,
                'scent_profile' => $this->scent_profile,
                'origin' => $this->origin,
                'benefits' => $this->benefits, // Cast to array
                'ingredients' => $this->ingredients,
                'usage_instructions' => $this->usage_instructions,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ];
        }
    }
    ```

4.  **Controller: `app/Http/Controllers/Api/V1/ProductController.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Controllers\Api\V1;
    use App\Http\Controllers\Controller; use Illuminate\Http\Request;
    use App\Domain\Catalog\Services\ProductQueryService; use App\Domain\Catalog\Models\Product;
    use App\Http\Resources\ProductResource; use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
    class ProductController extends Controller {
        public function __construct(protected ProductQueryService $productQueryService){}
        public function index(Request $request): AnonymousResourceCollection {
            // Add request validation for filters/sorting if needed
            $products = $this->productQueryService->getActivePaginatedProducts(
                $request->input('per_page', config('thescent.products_per_page', 12)),
                $request->input('category'), // category slug
                [$request->input('sort_by', 'created_at'), $request->input('sort_direction', 'desc')]
                // Add more filters as needed
            );
            return ProductResource::collection($products);
        }
        public function show(Product $product): ProductResource { // Route model binding by slug
            if (!$product->is_active) { abort(404, 'Product not found or not available.'); }
            $product->loadMissing('category', 'productAttributes');
            return ProductResource::make($product);
        }
    }
    ```

5.  **Controller: `app/Http/Controllers/Api/V1/CategoryController.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Controllers\Api\V1;
    use App\Http\Controllers\Controller; use App\Domain\Catalog\Models\Category;
    use App\Http\Resources\CategoryResource; use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
    class CategoryController extends Controller {
        public function index(): AnonymousResourceCollection {
            $categories = Category::whereNull('parent_id')->orderBy('name')->get(); // Example: top-level categories
            return CategoryResource::collection($categories);
        }
        public function show(Category $category): CategoryResource { // Route model binding by slug
            $category->loadMissing('children', 'products'); // Example eager loads
            return CategoryResource::make($category);
        }
    }
    ```

6.  **API Resource: `app/Http/Resources/CartItemResource.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Resources;
    use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource;
    class CartItemResource extends JsonResource {
        public function toArray(Request $request): array {
            return [
                'id' => $this->id,
                'product' => ProductResource::make($this->whenLoaded('product')),
                'quantity' => $this->quantity,
                'item_total' => [ // Calculate item total if not directly on model
                    'amount_minor' => $this->product->price->getMinorAmount() * $this->quantity,
                    'currency_code' => $this->product->price->getCurrencyCode(),
                    'formatted' => (new \App\Domain\Shared\ValueObjects\MonetaryAmount(
                        $this->product->price->getMinorAmount() * $this->quantity,
                        $this->product->price->getCurrencyCode()
                    ))->format(),
                ],
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ];
        }
    }
    ```

7.  **API Resource: `app/Http/Resources/CartResource.php`** (New - Represents the whole cart)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Resources;
    use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource;
    class CartResource extends JsonResource { // This resource wraps the output of CartService->getTotals() and items
        public function toArray(Request $request): array {
            // The $this->resource here is expected to be an array like:
            // ['items' => Collection<CartItem>, 'totals' => array from CartService->getTotals()]
            $totals = $this->resource['totals'];
            $coupon = $totals['applied_coupon'] ? [
                'code' => $totals['applied_coupon']->code,
                'description' => $totals['applied_coupon']->description,
                'type' => $totals['applied_coupon']->type,
                'value_minor_amount' => $totals['applied_coupon']->value_minor_amount,
            ] : null;

            return [
                'items' => CartItemResource::collection($this->resource['items']),
                'item_count' => $this->resource['item_count'],
                'subtotal' => [
                    'amount_minor' => $totals['subtotal']->getMinorAmount(),
                    'currency_code' => $totals['subtotal']->getCurrencyCode(),
                    'formatted' => $totals['subtotal']->format(),
                ],
                'discount' => [
                    'amount_minor' => $totals['discount']->getMinorAmount(),
                    'currency_code' => $totals['discount']->getCurrencyCode(),
                    'formatted' => $totals['discount']->format(),
                ],
                'shipping_cost' => [
                    'amount_minor' => $totals['shipping']->getMinorAmount(),
                    'currency_code' => $totals['shipping']->getCurrencyCode(),
                    'formatted' => $totals['shipping']->format(),
                ],
                'tax' => [
                    'amount_minor' => $totals['tax']->getMinorAmount(),
                    'currency_code' => $totals['tax']->getCurrencyCode(),
                    'formatted' => $totals['tax']->format(),
                ],
                'grand_total' => [
                    'amount_minor' => $totals['grand_total']->getMinorAmount(),
                    'currency_code' => $totals['grand_total']->getCurrencyCode(),
                    'formatted' => $totals['grand_total']->format(),
                ],
                'applied_coupon' => $coupon,
            ];
        }
    }
    ```

8.  **Controller: `app/Http/Controllers/Api/V1/CartController.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Controllers\Api\V1;
    use App\Http\Controllers\Controller; use Illuminate\Http\Request;
    use App\Domain\CartManagement\Services\CartService; use App\Domain\Catalog\Models\Product;
    use App\Domain\Promotion\Exceptions\InvalidCouponException;
    use App\Http\Resources\CartResource; use App\Http\Resources\CartItemResource;
    use Illuminate\Http\JsonResponse; use Illuminate\Validation\Rule;
    class CartController extends Controller {
        public function __construct(protected CartService $cartService){
             $this->middleware('auth:sanctum'); // All cart actions require auth for API
        }
        public function index(): CartResource {
            return CartResource::make([
                'items' => $this->cartService->getItems(),
                'totals' => $this->cartService->getTotals(),
                'item_count' => $this->cartService->getItemCount()
            ]);
        }
        public function storeItem(Request $request): JsonResponse {
            $validated = $request->validate([
                'product_id' => ['required', 'integer', Rule::exists(Product::class, 'id')],
                'quantity' => ['sometimes', 'integer', 'min:1', 'max:100'], // Max quantity example
            ]);
            $product = Product::findOrFail($validated['product_id']);
            $quantity = $validated['quantity'] ?? 1;
            try {
                $cartItem = $this->cartService->addItem($product, $quantity);
                return CartItemResource::make($cartItem)
                    ->additional(['message' => "{$product->name} added to cart."])
                    ->response()->setStatusCode(201);
            } catch (\App\Domain\Inventory\Exceptions\InsufficientStockException $e) {
                return response()->json(['message' => $e->getMessage()], 422);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Could not add item to cart.'], 500);
            }
        }
        public function updateItem(Request $request, int $cartItemId): JsonResponse {
            $validated = $request->validate(['quantity' => ['required', 'integer', 'min:0']]);
            try {
                $cartItem = $this->cartService->updateItemQuantity($cartItemId, $validated['quantity']);
                if ($cartItem) {
                    return CartItemResource::make($cartItem)
                        ->additional(['message' => 'Cart item updated.'])->response();
                }
                return response()->json([
                    'message' => 'Item removed from cart.',
                    'cart' => CartResource::make([
                        'items' => $this->cartService->getItems(),
                        'totals' => $this->cartService->getTotals(),
                        'item_count' => $this->cartService->getItemCount()
                    ]),
                ]);
            } catch (\App\Domain\Inventory\Exceptions\InsufficientStockException $e) {
                return response()->json(['message' => $e->getMessage()], 422);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Could not update cart item.'], 500);
            }
        }
        public function destroyItem(int $cartItemId): JsonResponse {
            if ($this->cartService->removeItem($cartItemId)) {
                return response()->json([
                    'message' => 'Item removed from cart.',
                     'cart' => CartResource::make([
                        'items' => $this->cartService->getItems(),
                        'totals' => $this->cartService->getTotals(),
                        'item_count' => $this->cartService->getItemCount()
                    ]),
                ]);
            }
            return response()->json(['message' => 'Item not found in cart or could not be removed.'], 404);
        }
        public function applyCoupon(Request $request): JsonResponse {
            $validated = $request->validate(['coupon_code' => 'required|string|max:50']);
            try {
                $coupon = $this->cartService->applyCoupon($validated['coupon_code']);
                return response()->json([
                    'message' => "Coupon '{$coupon->code}' applied successfully.",
                    'cart' => CartResource::make([
                        'items' => $this->cartService->getItems(),
                        'totals' => $this->cartService->getTotals(),
                        'item_count' => $this->cartService->getItemCount()
                    ]),
                ]);
            } catch (InvalidCouponException $e) {
                return response()->json(['message' => $e->getMessage()], 422);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Could not apply coupon.'], 500);
            }
        }
        public function removeCoupon(): JsonResponse {
            $this->cartService->removeCoupon();
            return response()->json([
                'message' => 'Coupon removed.',
                'cart' => CartResource::make([
                    'items' => $this->cartService->getItems(),
                    'totals' => $this->cartService->getTotals(),
                    'item_count' => $this->cartService->getItemCount()
                ]),
            ]);
        }
    }
    ```

---
#### Step 7.3: Checkout & Order API Endpoints

1.  **API Resource: `app/Http/Resources/OrderItemResource.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Resources;
    use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource;
    class OrderItemResource extends JsonResource {
        public function toArray(Request $request): array {
            return [
                'id' => $this->id,
                'product_id' => $this->product_id,
                'product_name_snapshot' => $this->product_name_snapshot,
                'product_sku_snapshot' => $this->product_sku_snapshot,
                'product_options_snapshot' => $this->product_options_snapshot,
                'quantity' => $this->quantity,
                'unit_price' => [
                    'amount_minor' => $this->unitPrice->getMinorAmount(), // Using accessor
                    'currency_code' => $this->unitPrice->getCurrencyCode(),
                    'formatted' => $this->unitPrice->format(),
                ],
                'total' => [
                    'amount_minor' => $this->total->getMinorAmount(), // Using accessor
                    'currency_code' => $this->total->getCurrencyCode(),
                    'formatted' => $this->total->format(),
                ],
            ];
        }
    }
    ```

2.  **API Resource: `app/Http/Resources/OrderResource.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Resources;
    use Illuminate\Http\Request; use Illuminate\Http\Resources\Json\JsonResource;
    class OrderResource extends JsonResource {
        public function toArray(Request $request): array {
            return [
                'id' => $this->id,
                'order_number' => $this->order_number,
                'user' => UserResource::make($this->whenLoaded('user')),
                'guest_email' => $this->guest_email,
                // Shipping Address - simplified, assumes direct fields on Order model
                'shipping_address' => [
                    'name' => $this->shipping_name, 'email' => $this->shipping_email,
                    'address1' => $this->shipping_address, 'address2' => $this->shipping_address_line2,
                    'city' => $this->shipping_city, 'state' => $this->shipping_state,
                    'zip' => $this->shipping_zip, 'country' => $this->shipping_country,
                ],
                'status' => $this->status,
                'payment_status' => $this->payment_status,
                'payment_gateway' => $this->payment_gateway,
                'payment_intent_id' => $this->payment_intent_id,
                'subtotal' => $this->subtotal->format(), // Using MonetaryAmount accessor
                'discount_amount' => $this->discountAmount->format(),
                'coupon_code_applied' => $this->coupon_code_applied,
                'shipping_cost' => $this->shippingCost->format(),
                'tax_amount' => $this->taxAmount->format(),
                'total_amount' => $this->totalAmount->format(),
                'currency_code' => $this->currency_code,
                'order_notes' => $this->order_notes,
                'paid_at' => $this->paid_at?->toIso8601String(),
                'tracking_number' => $this->tracking_number,
                'carrier' => $this->carrier,
                'items' => OrderItemResource::collection($this->whenLoaded('items')),
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ];
        }
    }
    ```

3.  **Controller: `app/Http/Controllers/Api/V1/CheckoutController.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Controllers\Api\V1;
    use App\Http\Controllers\Controller; use Illuminate\Http\Request;
    use App\Domain\Checkout\Services\CheckoutService; use App\Http\Requests\Api\V1\StoreCheckoutApiRequest; // New request
    use App\Domain\UserManagement\Models\User; use Illuminate\Http\JsonResponse;
    use Illuminate\Support\Facades\Auth; use App\Http\Resources\OrderResource;
    class CheckoutController extends Controller {
        public function __construct(protected CheckoutService $checkoutService){
            $this->middleware('auth:sanctum');
        }
        public function prepare(Request $request): JsonResponse {
            // This API endpoint assumes cart data is implicitly managed for the authenticated user by CartService
            // Or could accept cart items in request for a stateless cart approach
            try {
                /** @var User $user */
                $user = $request->user();
                // $shippingAddressData = $request->input('shipping_address', []); // Example if address is passed
                $checkoutInitData = $this->checkoutService->initiateCheckout($user /*, $shippingAddressData */);
                return response()->json($checkoutInitData);
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }
        public function complete(StoreCheckoutApiRequest $request): JsonResponse|OrderResource {
            /** @var User $user */
            $user = $request->user();
            $validatedData = $request->validated();
            try {
                $order = $this->checkoutService->completeOrder(
                    $validatedData['payment_intent_id'],
                    $validatedData, // Contains shipping address etc.
                    $user
                );
                return OrderResource::make($order)->additional(['message' => 'Order placed successfully.']);
            } catch (\Exception $e) {
                // Log::error('API Checkout complete error: ' . $e->getMessage());
                return response()->json(['message' => 'Failed to complete order: ' . $e->getMessage()], 500);
            }
        }
    }
    ```

4.  **Form Request: `app/Http/Requests/Api/V1/StoreCheckoutApiRequest.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Requests\Api\V1;
    use Illuminate\Foundation\Http\FormRequest;
    class StoreCheckoutApiRequest extends FormRequest {
        public function authorize(): bool { return $this->user() != null; } // Must be authenticated
        public function rules(): array {
            return [
                'payment_intent_id' => ['required', 'string'], // From client-side Stripe confirmation
                // Shipping address details (required for the order)
                'shipping_name' => ['required', 'string', 'max:191'],
                'shipping_address_line1' => ['required', 'string', 'max:191'],
                'shipping_address_line2' => ['nullable', 'string', 'max:191'],
                'shipping_city' => ['required', 'string', 'max:100'],
                'shipping_state' => ['required', 'string', 'max:100'],
                'shipping_postal_code' => ['required', 'string', 'max:20'],
                'shipping_country_code' => ['required', 'string', 'size:2'],
                'shipping_phone' => ['nullable', 'string', 'max:30'],
                'customer_notes' => ['nullable', 'string', 'max:1000'],
                // Billing address if different
            ];
        }
    }
    ```

5.  **Controller: `app/Http/Controllers/Api/V1/OrderController.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Controllers\Api\V1;
    use App\Http\Controllers\Controller; use Illuminate\Http\Request;
    use App\Domain\OrderManagement\Models\Order; use App\Http\Resources\OrderResource;
    use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
    class OrderController extends Controller {
        public function __construct() { $this->middleware('auth:sanctum'); }
        public function index(Request $request): AnonymousResourceCollection {
            /** @var \App\Domain\UserManagement\Models\User $user */
            $user = $request->user();
            $orders = $user->orders()->with(['items.product'])->latest()->paginate(10);
            return OrderResource::collection($orders);
        }
        public function show(Request $request, Order $order): OrderResource {
            /** @var \App\Domain\UserManagement\Models\User $user */
            $user = $request->user();
            // Authorize: Ensure the authenticated user owns this order
            if ($order->user_id !== $user->id) {
                abort(403, 'You are not authorized to view this order.');
            }
            $order->loadMissing(['items.product', 'user' /*, 'shippingAddressResolved'*/]);
            return OrderResource::make($order);
        }
    }
    ```

---
This completes the generation of files for API Development (Steps 7.1, 7.2, 7.3). These controllers provide the API layer over the domain services. The API resources ensure consistent JSON responses. Next steps from the execution plan would be robust testing (Phase 8) and then deployment preparation (Phase 9).

---
https://drive.google.com/file/d/1-Z3R3XaYO-mE0jKkAITBi4XJoTRm75fh/view?usp=sharing, https://drive.google.com/file/d/11TN1qmXXUWMwei4eGSo6b7w7IghU1HTL/view?usp=sharing, https://drive.google.com/file/d/13YMUodSoUQTGda21CAFUN-uvRBDOg2v0/view?usp=sharing, https://drive.google.com/file/d/149SQdFIsEyVGZtL5cHAjDSnFwfrufK4-/view?usp=sharing, https://drive.google.com/file/d/16LkILDN3LEo2wnccNd1Usnc2knXmcrAA/view?usp=sharing, https://aistudio.google.com/app/prompts?state=%7B%22ids%22:%5B%22191Xwb664N58hqofAV7tgOqIhw9Q_EXtW%22%5D,%22action%22:%22open%22,%22userId%22:%22103961307342447084491%22,%22resourceKeys%22:%7B%7D%7D&usp=sharing, https://drive.google.com/file/d/1JMi0sos9Z0VPmPtV_VUQdOiHP55dgoyM/view?usp=sharing, https://drive.google.com/file/d/1JmeU56zj-mdBzJciPH1CqEBUPh0nFDI6/view?usp=sharing, https://drive.google.com/file/d/1Ljou3KM9wH_n2IISZRUL5Usykg-a8yaZ/view?usp=sharing, https://drive.google.com/file/d/1NU0PF2paSbFH1H3swzif2Yyyc768jBV7/view?usp=sharing, https://drive.google.com/file/d/1OKIe2QIK161cS-Si4ATMzcPm3H1e_rhp/view?usp=sharing, https://drive.google.com/file/d/1QZH4p-KhoQlloMqonElkiJd_D7OafE9w/view?usp=sharing, https://drive.google.com/file/d/1UHbFpE2rY7aLNsT7R5-duOpJcgpWOoUh/view?usp=sharing, https://drive.google.com/file/d/1ZkX3SrAwn8mmBkyrU6ZaK7JsxzZfkEkR/view?usp=sharing, https://drive.google.com/file/d/1dppi2D-RTdYBal_YeLZxB4QHysdmQxpD/view?usp=sharing, https://drive.google.com/file/d/1eszSFTXohtDz0Cv1qbKHNecsx1BC1A3w/view?usp=sharing, https://drive.google.com/file/d/1gMQvFk0QXLWX-Bz05DlgjFeYgDxCiZEO/view?usp=sharing, https://drive.google.com/file/d/1nPblih0JaSTmarZmo2budApC3JXa64hP/view?usp=sharing, https://drive.google.com/file/d/1ns3qMlPug-efcmdH-urdxBXr8-y-2toZ/view?usp=sharing

