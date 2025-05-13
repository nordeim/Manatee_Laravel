<think>
The user wants me to generate the files for **Phase 6: Admin Panel Implementation**, specifically **Steps 6.1 to 6.4** from the execution plan. This is a significant part and involves creating the backend administrative interface.

This phase includes:

*   **Step 6.1: Admin Base Layout & Dashboard**
    *   `resources/views/layouts/admin.blade.php` (Admin panel base layout)
    *   `resources/views/admin/dashboard.blade.php` (Basic dashboard UI)
    *   `app/Http/Controllers/Admin/DashboardController.php`
    *   `app/Http/Middleware/EnsureUserIsAdmin.php`
    *   Update `routes/web.php` for admin route group and dashboard.

*   **Step 6.2: Product & Category Management (Admin)**
    *   `app/Http/Controllers/Admin/ProductController.php` (CRUD actions)
    *   `app/Http/Controllers/Admin/CategoryController.php` (CRUD actions)
    *   `app/Http/Requests/Admin/StoreProductRequest.php` (already designed, will re-verify)
    *   `app/Http/Requests/Admin/UpdateProductRequest.php` (similar to store)
    *   `app/Http/Requests/Admin/StoreCategoryRequest.php` (New)
    *   `app/Http/Requests/Admin/UpdateCategoryRequest.php` (New)
    *   `resources/views/admin/products/*` (index, create, edit Blade files)
    *   `resources/views/admin/categories/*` (index, create, edit Blade files)
    *   `app/Policies/ProductPolicy.php`, `CategoryPolicy.php` (Implement admin rules)
    *   Image upload handling in `Admin\ProductController`.

*   **Step 6.3: Order Management (Admin)**
    *   `app/Http/Controllers/Admin/OrderController.php` (index, show, update status)
    *   `resources/views/admin/orders/*` (index, show Blade files)
    *   `app/Policies/OrderPolicy.php` (Implement admin rules)
    *   Update `app/Domain/OrderManagement/Services/OrderService.php` (ensure `updateOrderStatus` dispatches event).

*   **Step 6.4: User Management & Tax Rate Management (Admin)**
    *   `app/Http/Controllers/Admin/UserController.php` (index, show, edit, update roles/status)
    *   `resources/views/admin/users/*` (index, edit Blade files)
    *   `app/Policies/UserPolicy.php` (Implement admin rules)
    *   `app/Http/Controllers/Admin/TaxRateController.php` (CRUD for `TaxRate` model)
    *   `app/Http/Requests/Admin/StoreTaxRateRequest.php` (New)
    *   `app/Http/Requests/Admin/UpdateTaxRateRequest.php` (New)
    *   `resources/views/admin/tax_rates/*` (index, create, edit Blade files)

**Key Considerations for Admin Panel Implementation:**

1.  **Layout & Styling:** The admin panel will have a distinct layout (`admin.blade.php`) but should still utilize Tailwind CSS for styling. It will likely be a more functional, data-driven interface compared to the "mindful UX" of the frontend.
2.  **Authentication & Authorization:**
    *   All admin routes must be protected by `auth` middleware and the custom `EnsureUserIsAdmin` middleware.
    *   Model Policies (`ProductPolicy`, `OrderPolicy`, `UserPolicy`, `CategoryPolicy`) will be crucial for fine-grained access control within admin controllers.
3.  **CRUD Operations:** Controllers will handle standard CRUD. They will use Domain Services where complex business logic is involved, or interact with Eloquent models directly for simpler CRUD. Form Requests will handle validation.
4.  **Data Display:** Admin index pages will likely use tables to display data with pagination, sorting, and filtering capabilities.
5.  **Image Uploads (Products):** The `Admin\ProductController` needs to handle file uploads for product images. This could involve storing files locally (`storage/app/public/products`) and saving the path to the `Product` model, or integrating `spatie/laravel-medialibrary` if a more robust solution is desired (the design doc mentioned it as an option, but for now, I'll stick to simple path storage).
6.  **User Roles:** The `EnsureUserIsAdmin` middleware will check `Auth::user()->role === 'admin'`. The `Admin\UserController` will allow an admin to manage other users' roles (e.g., promote to 'staff' or 'admin', or demote).
7.  **Form Handling:** Blade views for create/edit forms will use Laravel's form helpers or standard HTML forms, with error display and old input repopulation.
8.  **Flash Messages:** Use session flash messages for success/error feedback after CRUD operations.
9.  **Consistency:** Ensure consistency with previously generated domain models, services, DTOs, and naming conventions.
10. **Simplicity for initial build:** Keep the admin UI clean and functional. Advanced features like charting on the dashboard can be added later. The initial dashboard might just show some counts.

**File Generation Strategy:**
I will proceed step-by-step (6.1, 6.2, 6.3, 6.4).

*   **6.1 Admin Base:** Middleware, layout, basic dashboard controller and view, route group.
*   **6.2 Product/Category Management:** Controllers, Form Requests, Policies, Blade views for CRUD.
*   **6.3 Order Management:** Controller, Policy, Blade views for viewing and updating order status.
*   **6.4 User/TaxRate Management:** Controllers, Form Requests, Policies, Blade views for CRUD.

I'll provide complete, working code for these components. The admin panel might look a bit spartan initially without heavy CSS styling beyond Tailwind's defaults, but it will be functional.
The `StoreProductRequest` and `UpdateProductRequest` were partially designed in the main design document; I will ensure they are complete.
I will need to create `EnsureUserIsAdmin` middleware.
I will also need to create the Policy files.
</think>

This is a pivotal phase where we build the administrative backbone of "The Scent." I will generate the complete and working versions of the files for **Phase 6: Admin Panel Implementation (Steps 6.1 to 6.4)**, ensuring consistency with all previously created components.

---

### Phase 6: Admin Panel Implementation

#### Step 6.1: Admin Base Layout & Dashboard

1.  **Middleware: `app/Http/Middleware/EnsureUserIsAdmin.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Symfony\Component\HttpFoundation\Response;

    class EnsureUserIsAdmin
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next): Response
        {
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                // Redirect them to the home page or a login page with an error
                // Or simply abort with a 403 Forbidden error
                // For simplicity, let's abort. In a real app, a redirect might be better.
                abort(403, 'Unauthorized action. Administrator access required.');
            }

            return $next($request);
        }
    }
    ```
    *   **Registration:** This middleware needs to be registered in `app/Http/Kernel.php`:
        ```php
        // In app/Http/Kernel.php
        protected $middlewareAliases = [ // Or $routeMiddleware if Laravel < 11
            // ... other aliases
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ];
        ```
        *(In Laravel 11+, route middleware aliases are typically defined in `bootstrap/app.php` using `$middleware->alias([...])`)*
        For Laravel 11+, modify `bootstrap/app.php`:
        ```php
        ->withMiddleware(function (Middleware $middleware) {
            $middleware->alias([
                'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
                // other aliases...
            ]);
            // $middleware->validateCsrfTokens(except: ['stripe/*']); // Example from L11 bootstrap
        })
        ```

2.  **Layout: `resources/views/layouts/admin.blade.php`** (New)

    ```php
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="darkModeHandler()" :class="{ 'dark': isDarkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Admin Dashboard' }} - {{ config('app.name', 'The Scent') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Raleway:wght@500;600&display=swap" rel="stylesheet">


        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        <style>
            /* Admin specific minimal styles */
            .admin-sidebar a.active {
                background-color: theme('colors.scent-primary/20');
                color: theme('colors.scent-primary');
                font-weight: 600;
            }
            html.dark .admin-sidebar a.active {
                background-color: theme('colors.scent-primary-dark/30');
                color: theme('colors.scent-primary-dark');
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
        <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Sidebar -->
            <aside class="w-64 bg-white dark:bg-gray-800 shadow-md hidden md:block">
                <div class="p-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold font-accent text-scent-primary dark:text-scent-primary-dark flex items-center">
                        <i class="fa-solid fa-feather-pointed mr-2 text-scent-accent dark:text-scent-accent-dark"></i>
                        The Scent Admin
                    </a>
                </div>
                <nav class="mt-4 admin-sidebar">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt fa-fw mr-3"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="fas fa-box-open fa-fw mr-3"></i> Products
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="fas fa-tags fa-fw mr-3"></i> Categories
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart fa-fw mr-3"></i> Orders
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users fa-fw mr-3"></i> Users
                    </a>
                    <a href="{{ route('admin.tax-rates.index') }}" class="flex items-center px-6 py-3 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors {{ request()->routeIs('admin.tax-rates.*') ? 'active' : '' }}">
                        <i class="fas fa-percent fa-fw mr-3"></i> Tax Rates
                    </a>
                    {{-- Add more admin links here --}}
                </nav>
            </aside>

            <!-- Main content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top bar -->
                <header class="bg-white dark:bg-gray-800 shadow-sm">
                    <div class="container-fluid mx-auto px-6 py-4 flex justify-between items-center">
                        <div>
                            {{-- Mobile sidebar toggle --}}
                            <button class="md:hidden text-gray-600 dark:text-gray-300 focus:outline-none">
                                <i class="fas fa-bars"></i>
                            </button>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button @click="toggleDarkMode()"
                                    class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none"
                                    aria-label="Toggle theme">
                                <i class="fa-solid" :class="isDarkMode ? 'fa-sun' : 'fa-moon'"></i>
                            </button>
                            <span class="text-gray-700 dark:text-gray-200">{{ Auth::user()->name ?? 'Admin' }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-gray-600 dark:text-gray-300 hover:text-scent-primary dark:hover:text-scent-primary-dark">
                                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                    @if (session('status') || session('success'))
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                            {{ session('status') ?: session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('modals')
        @stack('scripts')
    </body>
    </html>
    ```

3.  **Controller: `app/Http/Controllers/Admin/DashboardController.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\View\View;
    use App\Domain\OrderManagement\Models\Order;
    use App\Domain\UserManagement\Models\User;
    use App\Domain\Catalog\Models\Product;

    class DashboardController extends Controller
    {
        public function index(): View
        {
            // Fetch some basic stats for the dashboard
            $totalOrders = Order::count();
            $totalUsers = User::count();
            $totalProducts = Product::count();
            $recentOrders = Order::with('user')->latest()->take(5)->get();

            return view('admin.dashboard', [
                'totalOrders' => $totalOrders,
                'totalUsers' => $totalUsers,
                'totalProducts' => $totalProducts,
                'recentOrders' => $recentOrders,
            ]);
        }
    }
    ```

4.  **View: `resources/views/admin/dashboard.blade.php`** (New)

    ```php
    <x-layouts.admin-layout>
        <x-slot name="title">Admin Dashboard</x-slot>

        <h1 class="text-2xl font-semibold text-gray-700 dark:text-gray-200 mb-6">Dashboard</h1>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-scent-primary/20 dark:bg-scent-primary-dark/30 text-scent-primary dark:text-scent-primary-dark">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <div class="ms-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Total Orders</p>
                        <p class="text-2xl font-semibold text-gray-700 dark:text-gray-200">{{ $totalOrders }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-500/20 dark:bg-green-500/30 text-green-600 dark:text-green-400">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div class="ms-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Total Users</p>
                        <p class="text-2xl font-semibold text-gray-700 dark:text-gray-200">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-amber-500/20 dark:bg-amber-500/30 text-amber-600 dark:text-amber-400">
                        <i class="fas fa-box-open fa-2x"></i>
                    </div>
                    <div class="ms-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Total Products</p>
                        <p class="text-2xl font-semibold text-gray-700 dark:text-gray-200">{{ $totalProducts }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-4">Recent Orders</h2>
            @if($recentOrders->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Order #</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">View</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($recentOrders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $order->user ? $order->user->name : ($order->guest_email ?: 'Guest') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $order->totalAmount->format() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                 <span class="text-xs px-2 py-1 font-semibold leading-tight rounded-full
                                    @switch($order->status)
                                        @case('pending_payment') bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100 @break
                                        @case('processing') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100 @break
                                        @case('shipped') bg-sky-100 text-sky-800 dark:bg-sky-700 dark:text-sky-100 @break
                                        @case('delivered') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100 @break
                                        @case('completed') bg-emerald-100 text-emerald-800 dark:bg-emerald-700 dark:text-emerald-100 @break
                                        @case('cancelled') bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100 @break
                                        @default bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200
                                    @endswitch
                                ">{{ Str::title(str_replace('_', ' ', $order->status)) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-scent-primary dark:text-scent-primary-dark hover:underline">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500 dark:text-gray-400">No recent orders.</p>
            @endif
        </div>

    </x-layouts.admin-layout>
    ```

5.  **Route Update: `routes/web.php`** (Define admin route group and dashboard route)
    *   The previous `routes/web.php` already had a placeholder:
    ```php
    // Admin Routes (from previous step, now to be used)
    Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
        // Resource routes for products, categories, orders, users, tax-rates will be added in subsequent steps
        Route::resource('products', Admin\ProductController::class);
        Route::resource('categories', Admin\CategoryController::class);
        Route::resource('orders', Admin\OrderController::class)->only(['index', 'show', 'update']);
        Route::resource('users', Admin\UserController::class)->except(['create', 'store', 'destroy']); // Adjust allowed actions
        Route::resource('tax-rates', Admin\TaxRateController::class);
    });
    ```
    *Note: I've used `verified` middleware as well, which is good practice for admin areas. Ensure your `User` model implements `MustVerifyEmail` and email verification is set up if you use this.*

---
This covers Step 6.1. The next steps (6.2, 6.3, 6.4) involve creating the CRUD functionalities for Products, Categories, Orders, Users, and Tax Rates within this admin panel structure. I will generate those in subsequent responses as they are quite extensive. Each will include controllers, form requests, policies, and Blade views.

The files for **Step 6.2 (Product & Category Management)**, **Step 6.3 (Order Management)**, and **Step 6.4 (User & Tax Rate Management)** are numerous. I will generate them now, keeping them as complete as possible for this stage.

I will skip generating every single Blade view for CRUD for brevity here, but will provide the structure and example for one set (e.g., Products) and list the others that would follow the same pattern. The controllers and requests will be more complete.

---

#### Step 6.2: Product & Category Management (Admin)

1.  **Policy: `app/Policies/ProductPolicy.php`** (New)
    ```php
    <?php

    declare(strict_types=1);

    namespace App\Policies;

    use App\Domain\UserManagement\Models\User;
    use App\Domain\Catalog\Models\Product;

    class ProductPolicy
    {
        /**
         * Determine whether the user can view any models.
         */
        public function viewAny(User $user): bool
        {
            return $user->role === 'admin' || $user->role === 'staff'; // Example: staff can also view
        }

        /**
         * Determine whether the user can view the model.
         */
        public function view(User $user, Product $product): bool
        {
            return $user->role === 'admin' || $user->role === 'staff';
        }

        /**
         * Determine whether the user can create models.
         */
        public function create(User $user): bool
        {
            return $user->role === 'admin';
        }

        /**
         * Determine whether the user can update the model.
         */
        public function update(User $user, Product $product): bool
        {
            return $user->role === 'admin';
        }

        /**
         * Determine whether the user can delete the model.
         */
        public function delete(User $user, Product $product): bool
        {
            return $user->role === 'admin';
        }

        /**
         * Determine whether the user can restore the model.
         */
        public function restore(User $user, Product $product): bool
        {
            return $user->role === 'admin';
        }

        /**
         * Determine whether the user can permanently delete the model.
         */
        public function forceDelete(User $user, Product $product): bool
        {
            return $user->role === 'admin';
        }
    }
    ```

2.  **Policy: `app/Policies/CategoryPolicy.php`** (New - similar structure to `ProductPolicy`)
    ```php
    <?php declare(strict_types=1); namespace App\Policies;
    use App\Domain\UserManagement\Models\User; use App\Domain\Catalog\Models\Category;
    class CategoryPolicy {
        public function viewAny(User $user): bool { return $user->role === 'admin' || $user->role === 'staff'; }
        public function view(User $user, Category $category): bool { return $user->role === 'admin' || $user->role === 'staff'; }
        public function create(User $user): bool { return $user->role === 'admin'; }
        public function update(User $user, Category $category): bool { return $user->role === 'admin'; }
        public function delete(User $user, Category $category): bool { return $user->role === 'admin'; }
    }
    ```
    *(Register these in `AuthServiceProvider`)*

3.  **Form Request: `app/Http/Requests/Admin/StoreProductRequest.php`** (Re-verified and Completed)
    ```php
    <?php

    declare(strict_types=1);

    namespace App\Http\Requests\Admin;

    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Validation\Rule;
    use App\Domain\Catalog\Models\Category;

    class StoreProductRequest extends FormRequest
    {
        public function authorize(): bool
        {
            return $this->user()->can('create', \App\Domain\Catalog\Models\Product::class);
        }

        public function rules(): array
        {
            return [
                'name' => ['required', 'string', 'max:150'],
                'slug' => ['required', 'string', 'max:170', Rule::unique('products', 'slug')],
                'description' => ['nullable', 'string'],
                'short_description' => ['nullable', 'string', 'max:1000'], // Increased max
                'price_minor_amount' => ['required', 'integer', 'min:0'],
                'currency_code' => ['required', 'string', 'size:3'],
                'category_id' => ['nullable', 'integer', Rule::exists(Category::class, 'id')],
                'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($this->product?->id)],
                'stock_quantity' => ['required', 'integer', 'min:0'],
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // For file uploads
                'gallery_images' => ['nullable', 'array'],
                'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                'is_active' => ['sometimes', 'boolean'],
                'is_featured' => ['sometimes', 'boolean'],
                'backorder_allowed' => ['sometimes', 'boolean'],
                'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
                'reorder_point' => ['nullable', 'integer', 'min:0'],
                'size' => ['nullable', 'string', 'max:50'],
                'scent_profile' => ['nullable', 'string', 'max:191'],
                'origin' => ['nullable', 'string', 'max:100'],
                'benefits' => ['nullable', 'array'], // Assuming benefits are simple strings
                'benefits.*' => ['nullable', 'string', 'max:191'],
                'ingredients' => ['nullable', 'string'],
                'usage_instructions' => ['nullable', 'string'],
                'highlight_text' => ['nullable', 'string', 'max:50'],
                // Product Attributes
                'attributes.scent_type' => ['nullable', 'string', Rule::in(['floral','woody','citrus','oriental','fresh', 'herbal', 'spicy', 'sweet'])],
                'attributes.mood_effect' => ['nullable', 'string', Rule::in(['calming','energizing','focusing','balancing', 'uplifting', 'grounding'])],
                'attributes.intensity_level' => ['nullable', 'string', Rule::in(['light','medium','strong'])],
            ];
        }

        protected function prepareForValidation()
        {
            if ($this->has('name') && !$this->has('slug')) {
                $this->merge([
                    'slug' => \Illuminate\Support\Str::slug($this->name),
                ]);
            }
            $this->merge([
                'is_active' => $this->boolean('is_active'),
                'is_featured' => $this->boolean('is_featured'),
                'backorder_allowed' => $this->boolean('backorder_allowed'),
            ]);
        }
    }
    ```

4.  **Form Request: `app/Http/Requests/Admin/UpdateProductRequest.php`** (New)
    ```php
    <?php

    declare(strict_types=1);

    namespace App\Http\Requests\Admin;

    // This can often extend StoreProductRequest and override rules if needed,
    // especially for unique checks that need to ignore the current model.
    // For simplicity, it's largely the same but slug/sku ignore current product.
    use Illuminate\Validation\Rule;
    use App\Domain\Catalog\Models\Product;

    class UpdateProductRequest extends StoreProductRequest // Inherits rules and authorize
    {
        public function authorize(): bool
        {
            // Ensure product exists for unique rule, or use $this->route('product') which is a Product model instance
            $product = $this->route('product');
            return $this->user()->can('update', $product instanceof Product ? $product : null);
        }

        public function rules(): array
        {
            $productId = ($this->route('product') instanceof Product) ? $this->route('product')->id : null;

            $rules = parent::rules(); // Get rules from StoreProductRequest
            $rules['slug'] = ['required', 'string', 'max:170', Rule::unique('products', 'slug')->ignore($productId)];
            $rules['sku'] = ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($productId)];
            $rules['image'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']; // Image not required on update

            return $rules;
        }
    }
    ```

5.  **Controller: `app/Http/Controllers/Admin/ProductController.php`** (New)

    ```php
    <?php

    declare(strict_types=1);

    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use App\Domain\Catalog\Models\Product;
    use App\Domain\Catalog\Models\Category;
    use App\Domain\Catalog\Models\ProductAttribute;
    use App\Http\Requests\Admin\StoreProductRequest;
    use App\Http\Requests\Admin\UpdateProductRequest;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
    use Illuminate\View\View;
    use Illuminate\Http\RedirectResponse;

    class ProductController extends Controller
    {
        public function __construct()
        {
            // Apply policy to all resource methods
            $this->authorizeResource(Product::class, 'product');
        }

        public function index(Request $request): View
        {
            $query = Product::with('category')->latest();
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('sku', 'like', '%' . $request->search . '%');
            }
            $products = $query->paginate(15)->withQueryString();
            return view('admin.products.index', compact('products'));
        }

        public function create(): View
        {
            $categories = Category::orderBy('name')->pluck('name', 'id');
            return view('admin.products.create', compact('categories'));
        }

        public function store(StoreProductRequest $request): RedirectResponse
        {
            $data = $request->validated();
            $productAttributesData = $data['attributes'] ?? null;
            unset($data['attributes']);

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            // Handle gallery images if provided
            if ($request->hasFile('gallery_images')) {
                $galleryPaths = [];
                foreach ($request->file('gallery_images') as $file) {
                    $galleryPaths[] = $file->store('products/gallery', 'public');
                }
                $data['gallery_images'] = $galleryPaths;
            }

            $product = Product::create($data);

            if ($productAttributesData) {
                $product->productAttributes()->create($productAttributesData);
            }

            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        }

        public function show(Product $product): View
        {
            $product->load('category', 'productAttributes');
            return view('admin.products.show', compact('product'));
        }

        public function edit(Product $product): View
        {
            $categories = Category::orderBy('name')->pluck('name', 'id');
            $product->load('productAttributes');
            return view('admin.products.edit', compact('product', 'categories'));
        }

        public function update(UpdateProductRequest $request, Product $product): RedirectResponse
        {
            $data = $request->validated();
            $productAttributesData = $data['attributes'] ?? null;
            unset($data['attributes']);

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            // Handle gallery images update (more complex: add new, remove old, keep existing)
            // For simplicity, this example might just overwrite or append.
            // A better approach would involve managing individual gallery image records or using a media library package.
            if ($request->hasFile('gallery_images')) {
                // Potentially delete old gallery images
                // if ($product->gallery_images) {
                //     foreach ($product->gallery_images as $oldImage) Storage::disk('public')->delete($oldImage);
                // }
                $galleryPaths = $product->gallery_images ?? []; // Keep existing if not overwriting
                foreach ($request->file('gallery_images') as $file) {
                    $galleryPaths[] = $file->store('products/gallery', 'public');
                }
                $data['gallery_images'] = array_unique($galleryPaths); // Prevent duplicates if appending
            }


            $product->update($data);

            if ($productAttributesData) {
                $product->productAttributes()->updateOrCreate(['product_id' => $product->id], $productAttributesData);
            } else {
                $product->productAttributes()->delete(); // Remove attributes if none provided
            }

            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        }

        public function destroy(Product $product): RedirectResponse
        {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            if ($product->gallery_images) {
                foreach ($product->gallery_images as $galleryImage) {
                    Storage::disk('public')->delete($galleryImage);
                }
            }
            $product->delete();
            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
        }
    }
    ```

6.  **Form Requests for Categories:**
    *   **`app/Http/Requests/Admin/StoreCategoryRequest.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Requests\Admin;
    use Illuminate\Foundation\Http\FormRequest; use Illuminate\Validation\Rule;
    class StoreCategoryRequest extends FormRequest {
        public function authorize(): bool { return $this->user()->can('create', \App\Domain\Catalog\Models\Category::class); }
        public function rules(): array {
            return [
                'name' => ['required', 'string', 'max:100'],
                'slug' => ['required', 'string', 'max:120', Rule::unique('categories', 'slug')],
                'description' => ['nullable', 'string'],
                'parent_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            ];
        }
        protected function prepareForValidation() {
            if ($this->has('name') && !$this->has('slug')) {
                $this->merge(['slug' => \Illuminate\Support\Str::slug($this->name)]);
            }
        }
    }
    ```
    *   **`app/Http/Requests/Admin/UpdateCategoryRequest.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Requests\Admin;
    use Illuminate\Validation\Rule; use App\Domain\Catalog\Models\Category;
    class UpdateCategoryRequest extends StoreCategoryRequest {
        public function authorize(): bool {
            $category = $this->route('category');
            return $this->user()->can('update', $category instanceof Category ? $category : null);
        }
        public function rules(): array {
            $categoryId = ($this->route('category') instanceof Category) ? $this->route('category')->id : null;
            $rules = parent::rules();
            $rules['slug'] = ['required', 'string', 'max:120', Rule::unique('categories', 'slug')->ignore($categoryId)];
            return $rules;
        }
    }
    ```

7.  **Controller: `app/Http/Controllers/Admin/CategoryController.php`** (New)
    ```php
    <?php declare(strict_types=1); namespace App\Http\Controllers\Admin;
    use App\Http\Controllers\Controller; use App\Domain\Catalog\Models\Category;
    use App\Http\Requests\Admin\StoreCategoryRequest; use App\Http\Requests\Admin\UpdateCategoryRequest;
    use Illuminate\Http\Request; use Illuminate\View\View; use Illuminate\Http\RedirectResponse;
    class CategoryController extends Controller {
        public function __construct() { $this->authorizeResource(Category::class, 'category'); }
        public function index(Request $request): View {
            $query = Category::with('parent')->latest();
            if ($request->filled('search')) { $query->where('name', 'like', '%' . $request->search . '%'); }
            $categories = $query->paginate(15)->withQueryString();
            return view('admin.categories.index', compact('categories'));
        }
        public function create(): View {
            $parentCategories = Category::whereNull('parent_id')->orderBy('name')->pluck('name', 'id');
            return view('admin.categories.create', compact('parentCategories'));
        }
        public function store(StoreCategoryRequest $request): RedirectResponse {
            Category::create($request->validated());
            return redirect()->route('admin.categories.index')->with('success', 'Category created.');
        }
        public function edit(Category $category): View {
            $parentCategories = Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->pluck('name', 'id');
            return view('admin.categories.edit', compact('category', 'parentCategories'));
        }
        public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse {
            $category->update($request->validated());
            return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
        }
        public function destroy(Category $category): RedirectResponse {
            // Consider logic for child categories or products associated
            if ($category->products()->count() > 0 || $category->children()->count() > 0) {
                return back()->with('error', 'Cannot delete category with associated products or subcategories.');
            }
            $category->delete();
            return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
        }
    }
    ```

8.  **Admin Views for Products & Categories:**
    *   `resources/views/admin/products/index.blade.php`
    *   `resources/views/admin/products/create.blade.php`
    *   `resources/views/admin/products/edit.blade.php`
    *   `resources/views/admin/products/show.blade.php` (optional, or combine with edit)
    *   `resources/views/admin/categories/index.blade.php`
    *   `resources/views/admin/categories/create.blade.php`
    *   `resources/views/admin/categories/edit.blade.php`
    *   These views will use the `x-layouts.admin-layout`, Tailwind CSS for tables, forms, buttons, pagination links, search forms, and display validation errors and flash messages.
    *   **Example structure for `resources/views/admin/products/index.blade.php`:**

    ```php
    <x-layouts.admin-layout>
        <x-slot name="title">Manage Products</x-slot>

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">Products</h1>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary bg-scent-primary hover:bg-scent-primary/80 dark:bg-scent-primary-dark dark:hover:bg-scent-primary-dark/80 text-white">
                <i class="fas fa-plus mr-2"></i> Add Product
            </a>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.products.index') }}" class="mb-4">
            <div class="flex">
                <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}"
                       class="block w-full md:w-1/3 rounded-l-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scent-primary dark:focus:border-scent-primary-dark focus:ring-scent-primary dark:focus:ring-scent-primary-dark">
                <button type="submit" class="px-4 py-2 bg-scent-primary hover:bg-scent-primary/80 dark:bg-scent-primary-dark dark:hover:bg-scent-primary-dark/80 text-white rounded-r-md">Search</button>
            </div>
        </form>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow overflow-x-auto">
            @if($products->count())
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-10 w-10 rounded-md object-cover">
                                    @else
                                        <div class="h-10 w-10 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400 dark:text-gray-500">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $product->sku ?: 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $product->price->format() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $product->stock_quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($product->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">Active</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200">Edit</a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No products found.</p>
            @endif
        </div>
    </x-layouts.admin-layout>
    ```
    *(The create/edit forms for products would be more complex, involving all fields, file uploads, and potentially Alpine.js for dynamic interactions like adding gallery images.)*

---
This completes Step 6.1 and 6.2 (partially, focusing on controllers, requests, policies, and an example index view). The full set of CRUD views would be extensive but would follow the pattern shown. Next, I'll proceed with Order, User, and TaxRate management for the admin panel.

