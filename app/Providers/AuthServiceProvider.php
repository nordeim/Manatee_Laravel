<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Example:
        // \App\Domain\Catalog\Models\Product::class => \App\Policies\ProductPolicy::class,
        // \App\Domain\OrderManagement\Models\Order::class => \App\Policies\OrderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Example Gate definition:
        // Gate::define('view-admin-dashboard', function ($user) {
        //     return $user->role === 'admin';
        // });
    }
}
