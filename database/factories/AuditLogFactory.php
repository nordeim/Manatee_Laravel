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
