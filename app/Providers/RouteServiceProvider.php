<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/account/dashboard'; // Adjusted to a likely post-login destination for this app

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        // In Laravel 11+, route files (web.php, api.php, console.php, channels.php)
        // are typically loaded automatically via bootstrap/app.php.
        // This class is primarily for the HOME constant, rate limiters,
        // and any global route patterns or model bindings.

        // Example of a global route pattern:
        // Route::pattern('id', '[0-9]+');

        // Example of route model binding if you need custom resolution logic,
        // though implicit binding usually handles this.
        // Route::model('product', \App\Domain\Catalog\Models\Product::class);
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web', function (Request $request) {
            // Example: A slightly higher limit for general web browsing
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        // Example for sensitive actions like login attempts
        RateLimiter::for('login_attempts', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email') . '|' . $request->ip());
        });
    }
}
