<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Domain\Catalog\Services\ProductQueryService;
use App\Domain\Catalog\Models\Product; // For route model binding
use App\Domain\Catalog\Models\Category; // For category filtering

class ProductController extends Controller
{
    public function __construct(protected ProductQueryService $productQueryService)
    {
    }

    public function index(Request $request): View
    {
        $categorySlug = $request->query('category');
        $selectedCategory = null;
        if ($categorySlug) {
            $selectedCategory = Category::where('slug', $categorySlug)->first();
        }

        // Basic sorting, can be expanded
        $sortBy = $request->query('sort', 'created_at');
        $sortDirection = $request->query('direction', 'desc');
        $orderBy = [$sortBy, $sortDirection];

        $products = $this->productQueryService->getActivePaginatedProducts(
            perPage: config('thescent.products_per_page', 9),
            categorySlug: $categorySlug,
            orderBy: $orderBy
            // Add more filters from request here if needed
        );

        // Categories for filter sidebar (passed by ViewComposer later)
        // $categories = Category::whereNull('parent_id')->with('children')->get();

        return view('frontend.products.index', [
            'products' => $products,
            'selectedCategory' => $selectedCategory,
            // 'categories' => $categories, // Will be injected by CategoryComposer
        ]);
    }

    public function show(Product $product): View // Route model binding by slug
    {
        // Ensure product is active, or handle appropriately (e.g., 404)
        if (!$product->is_active) {
            abort(404);
        }
        $product->loadMissing('category', 'productAttributes'); // Eager load relations

        // Placeholder for related products
        $relatedProducts = $this->productQueryService->getFeaturedProducts(4)
            ->where('id', '!=', $product->id);


        return view('frontend.products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
