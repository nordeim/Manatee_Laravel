<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
// Example:
// use App\View\Composers\CategoryComposer;
// use App\View\Composers\CartComposer;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Example view composers (uncomment and adapt when composers are created)
        // View::composer(['layouts.partials.frontend.navigation', 'frontend.products.index'], CategoryComposer::class);
        // View::composer('layouts.partials.frontend.navigation', CartComposer::class);
    }
}
