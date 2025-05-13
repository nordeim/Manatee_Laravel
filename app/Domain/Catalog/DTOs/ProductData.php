<?php

declare(strict_types=1);

namespace App\Domain\Catalog\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Illuminate\Http\UploadedFile; // For image uploads

class ProductData extends Data
{
    public function __construct(
        public string $name,
        public string $slug,
        public int $price_minor_amount, // Store as minor units
        public string $currency_code,
        public int $stock_quantity,
        public string|Optional $description,
        public string|Optional $short_description,
        public UploadedFile|Optional|null $image, // For new image upload
        public string|Optional|null $existing_image_path, // To keep existing image or remove it
        public array|Optional $gallery_images, // Array of UploadedFile or paths
        public array|Optional $benefits,
        public string|Optional $ingredients,
        public string|Optional $usage_instructions,
        public int|Optional|null $category_id,
        public bool|Optional $is_featured,
        public bool|Optional $is_active,
        public int|Optional $low_stock_threshold,
        public int|Optional $reorder_point,
        public bool|Optional $backorder_allowed,
        public string|Optional $highlight_text,
        public string|Optional $size,
        public string|Optional $scent_profile,
        public string|Optional $origin,
        public string|Optional|null $sku,
        // For ProductAttributeData
        public ProductAttributeData|Optional|null $attributes
    ) {}
}
