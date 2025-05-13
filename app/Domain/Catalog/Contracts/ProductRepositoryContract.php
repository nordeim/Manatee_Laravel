<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Contracts;

use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Collection as EloquentCollection; // Eloquent specific
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryContract
{
    public function findById(int $id): ?Product;

    public function findBySlug(string $slug): ?Product;

    public function getFeatured(int $limit = 4): EloquentCollection;

    public function getAllPaginated(int $perPage = 12, array $filters = [], array $orderBy = ['created_at', 'desc']): LengthAwarePaginator;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): bool;
}
