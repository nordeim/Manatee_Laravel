<?php

declare(strict_types=1);

namespace App\View\Components\Frontend;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Domain\Catalog\Models\Product;

class ProductCard extends Component
{
    public Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function render(): View|Closure|string
    {
        return view('components.frontend.product-card');
    }
}
