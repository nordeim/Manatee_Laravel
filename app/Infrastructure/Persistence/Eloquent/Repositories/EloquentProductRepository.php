<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Catalog\Contracts\ProductRepositoryContract;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepositoryContract
{
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::where('slug', $slug)->where('is_active', true)->first();
    }

    public function getFeatured(int $limit = 4): EloquentCollection
    {
        return Product::where('is_featured', true)
            ->where('is_active', true)
            ->take($limit)
            ->orderBy('updated_at', 'desc') // Or some other logic for featured
            ->get();
    }

    public function getAllPaginated(int $perPage = 12, array $filters = [], array $orderBy = ['created_at', 'desc']): LengthAwarePaginator
    {
        $query = Product::query()->where('is_active', true);

        if (isset($filters['category_slug'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('slug', $filters['category_slug']);
            });
        }
        // Add more filters: price range, search term, etc.

        return $query->orderBy($orderBy[0], $orderBy[1])->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }
}
