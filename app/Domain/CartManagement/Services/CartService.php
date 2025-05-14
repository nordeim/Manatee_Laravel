<?php

declare(strict_types=1);

namespace App\Domain\CartManagement\Services;

use App\Domain\Catalog\Models\Product;
use App\Domain\CartManagement\Models\CartItem;
use App\Domain\UserManagement\Models\User;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_CART_ID_KEY = 'cart_session_id';

    protected function getCartIdentifier(): ?string
    {
        if (auth()->check()) {
            return 'user_' . auth()->id(); // Or use user_id directly in queries
        }
        if (Session::has(self::SESSION_CART_ID_KEY)) {
            return Session::get(self::SESSION_CART_ID_KEY);
        }
        $sessionId = 'guest_' . Str::random(40);
        Session::put(self::SESSION_CART_ID_KEY, $sessionId);
        return $sessionId;
    }

    protected function getQuery()
    {
        $query = CartItem::query()->with('product');
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $sessionId = Session::get(self::SESSION_CART_ID_KEY);
            if ($sessionId) {
                $query->where('session_id', $sessionId);
            } else {
                // No active cart for guest if no session_id
                return CartItem::query()->whereRaw('1 = 0'); // Empty query
            }
        }
        return $query;
    }

    public function addItem(Product $product, int $quantity = 1): CartItem
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive.');
        }

        $cartItemQuery = $this->getQuery()->where('product_id', $product->id);
        $cartItem = $cartItemQuery->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            $attributes = [
                'product_id' => $product->id,
                'quantity' => $quantity,
            ];
            if (auth()->check()) {
                $attributes['user_id'] = auth()->id();
            } else {
                $attributes['session_id'] = $this->getCartIdentifier(); // Ensures session_id is created if not present
            }
            $cartItem = CartItem::create($attributes);
        }
        return $cartItem->load('product');
    }

    public function updateItemQuantity(int $cartItemId, int $quantity): ?CartItem
    {
        if ($quantity < 0) { // Allow 0 to remove
            throw new \InvalidArgumentException('Quantity cannot be negative.');
        }

        $cartItem = $this->getQuery()->find($cartItemId);
        if ($cartItem) {
            if ($quantity === 0) {
                $cartItem->delete();
                return null;
            }
            $cartItem->quantity = $quantity;
            $cartItem->save();
            return $cartItem->load('product');
        }
        return null;
    }

    public function removeItem(int $cartItemId): bool
    {
        $cartItem = $this->getQuery()->find($cartItemId);
        if ($cartItem) {
            return (bool) $cartItem->delete();
        }
        return false;
    }

    public function getItems(): Collection
    {
        return $this->getQuery()->get();
    }

    public function getSubtotal(): MonetaryAmount
    {
        $items = $this->getItems();
        $totalMinorAmount = 0;
        $currencyCode = config('thescent.default_currency', 'USD'); // Default currency

        if ($items->isNotEmpty()) {
            $currencyCode = $items->first()->product->currency_code; // Assume all items in cart have same currency for simplicity
        }

        foreach ($items as $item) {
            // Ensure currency codes match if handling multi-currency carts robustly
            if ($item->product->currency_code !== $currencyCode) {
                 // Handle or throw error for mixed currencies if not supported
            }
            $totalMinorAmount += $item->product->price_minor_amount * $item->quantity;
        }
        return new MonetaryAmount($totalMinorAmount, $currencyCode);
    }

    public function getItemCount(): int
    {
        return $this->getQuery()->sum('quantity');
    }

    public function clearCart(): void
    {
        $this->getQuery()->delete();
        // Optionally clear session ID if guest
        // if (!auth()->check()) {
        //     Session::forget(self::SESSION_CART_ID_KEY);
        // }
    }

    public function mergeGuestCartToUser(User $user): void
    {
        $guestSessionId = Session::get(self::SESSION_CART_ID_KEY);
        if (!$guestSessionId) {
            return;
        }

        $guestCartItems = CartItem::where('session_id', $guestSessionId)->get();

        foreach ($guestCartItems as $guestItem) {
            $userItem = CartItem::where('user_id', $user->id)
                                ->where('product_id', $guestItem->product_id)
                                // ->where('product_variant_id', $guestItem->product_variant_id) // If using variants
                                ->first();
            if ($userItem) {
                $userItem->quantity += $guestItem->quantity;
                $userItem->save();
                $guestItem->delete();
            } else {
                $guestItem->user_id = $user->id;
                $guestItem->session_id = null;
                $guestItem->save();
            }
        }
        Session::forget(self::SESSION_CART_ID_KEY);
    }
}
