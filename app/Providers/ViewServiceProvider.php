<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\View\Composers\CategoryComposer;
use App\View\Composers\CartComposer; // Assuming this will be created later

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Views that need categories for filtering/navigation
        View::composer(
            ['frontend.products.index', 'layouts.partials.frontend.navigation', 'layouts.partials.frontend.footer'], // Add other views as needed
            CategoryComposer::class
        );

        // Example for Cart Composer (will be implemented in Cart phase)
        // View::composer(['layouts.partials.frontend.navigation'], CartComposer::class);
    }
}
