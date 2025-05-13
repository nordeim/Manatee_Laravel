<?php

declare(strict_types=1);

namespace App\Domain\Catalog\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProductAttributeData extends Data
{
    public function __construct(
        public string|Optional|null $scent_type,
        public string|Optional|null $mood_effect,
        public string|Optional|null $intensity_level
    ) {}
}
