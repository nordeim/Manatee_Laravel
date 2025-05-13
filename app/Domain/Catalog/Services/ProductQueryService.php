<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Services;

use App\Domain\Catalog\Contracts\ProductRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use App\Domain\Catalog\Models\Product;

class ProductQueryService
{
    public function __construct(protected ProductRepositoryContract $productRepository)
    {
    }

    public function getActivePaginatedProducts(
        int $perPage = 12,
        ?string $categorySlug = null,
        array $orderBy = ['created_at', 'desc']
        // Add more filter params like price_min, price_max, search_term
    ): LengthAwarePaginator
    {
        $filters = [];
        if ($categorySlug) {
            $filters['category_slug'] = $categorySlug;
        }
        // Build filters array

        return $this->productRepository->getAllPaginated($perPage, $filters, $orderBy);
    }

    public function getFeaturedProducts(int $limit = 4): EloquentCollection
    {
        return $this->productRepository->getFeatured($limit);
    }

    public function findProductBySlug(string $slug): ?Product
    {
        return $this->productRepository->findBySlug($slug);
    }
}
