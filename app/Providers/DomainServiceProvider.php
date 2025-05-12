<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Example:
// use App\Domain\Catalog\Contracts\ProductRepositoryContract;
// use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentProductRepository;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Example binding (uncomment and adapt when repositories are created)
        // $this->app->bind(
        //     ProductRepositoryContract::class,
        //     EloquentProductRepository::class
        // );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
