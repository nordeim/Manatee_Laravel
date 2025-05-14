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
