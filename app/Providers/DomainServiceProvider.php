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
