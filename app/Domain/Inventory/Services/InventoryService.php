<?php

declare(strict_types=1);

namespace App\Domain\Inventory\Services;

use App\Domain\Catalog\Models\Product;
use App\Domain\Inventory\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use App\Domain\Inventory\Exceptions\InsufficientStockException;
use App\Domain\Inventory\Events\StockLevelChangedEvent;
use App\Domain\Inventory\Events\LowStockThresholdReachedEvent;
use Throwable;

class InventoryService
{
    public function adjustStock(
        Product $product,
        int $quantityChange,
        string $type,
        ?int $referenceId = null,
        ?string $notes = null
    ): InventoryMovement {
        if ($quantityChange === 0) {
            throw new \InvalidArgumentException('Quantity change cannot be zero.');
        }

        return DB::transaction(function () use ($product, $quantityChange, $type, $referenceId, $notes) {
            // Lock the product row for update to prevent race conditions
            $product = Product::lockForUpdate()->findOrFail($product->id);

            $newStockLevel = $product->stock_quantity + $quantityChange;

            if ($newStockLevel < 0 && !$product->backorder_allowed) {
                throw new InsufficientStockException(
                    "Not enough stock for product ID {$product->id} ({$product->name}). Available: {$product->stock_quantity}, Requested change: {$quantityChange}"
                );
            }

            $product->stock_quantity = $newStockLevel;
            $product->save();

            $movement = InventoryMovement::create([
                'product_id' => $product->id,
                'quantity_change' => $quantityChange,
                'type' => $type,
                'reference_id' => $referenceId,
                'notes' => $notes,
            ]);

            StockLevelChangedEvent::dispatch($product, $quantityChange, $type, $referenceId);

            if ($product->stock_quantity <= $product->low_stock_threshold && $quantityChange < 0) {
                LowStockThresholdReachedEvent::dispatch($product);
            }

            return $movement;
        });
    }

    public function decreaseStockOnSale(Product $product, int $quantity, int $orderId): InventoryMovement
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity to decrease must be positive for a sale.');
        }
        return $this->adjustStock($product, -$quantity, 'sale', $orderId, "Order #{$orderId}");
    }

    public function increaseStockOnReturn(Product $product, int $quantity, int $orderId): InventoryMovement
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity to increase must be positive for a return.');
        }
        return $this->adjustStock($product, $quantity, 'return', $orderId, "Return for Order #{$orderId}");
    }

    public function restockProduct(Product $product, int $quantity, ?string $notes = 'Product restock'): InventoryMovement
    {
         if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity to restock must be positive.');
        }
        return $this->adjustStock($product, $quantity, 'restock', null, $notes);
    }


    public function getStockLevel(Product $product): int
    {
        // Could also query movements table for sum, but product.stock_quantity should be authoritative
        return $product->stock_quantity;
    }

    public function isStockAvailable(Product $product, int $requestedQuantity): bool
    {
        if ($requestedQuantity <= 0) {
            return true; // Requesting zero or negative quantity is always "available" in a sense
        }
        return $product->stock_quantity >= $requestedQuantity || $product->backorder_allowed;
    }
}
