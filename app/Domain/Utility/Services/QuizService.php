<?php

declare(strict_types=1);

namespace App\Domain\Utility\Services;

use App\Domain\Catalog\Models\Product;
use Illuminate\Support\Collection;
use App\Domain\Catalog\Services\ProductQueryService;
use App\Domain\Utility\Models\QuizResult;
use Illuminate\Support\Facades\Auth;

class QuizService
{
    public function __construct(protected ProductQueryService $productQueryService)
    {
    }

    public function getRecommendations(array $answers): Collection
    {
        // This is a placeholder for more sophisticated recommendation logic.
        // For now, use the client-side logic's product list or fetch some products.
        // This would ideally query products based on tags/attributes matching answers.

        $feeling = $answers['feeling'] ?? null;
        $scentFamily = $answers['scentFamily'] ?? null;
        $format = $answers['format'] ?? null; // 'oil', 'soap', 'both'

        $query = Product::query()->where('is_active', true);

        if ($feeling) {
            // Example: simple name based search for tags
            // In a real app, products would have tags or attributes for 'feeling'
            $query->where(function($q) use ($feeling) {
                $q->where('name', 'like', '%' . $feeling . '%')
                  ->orWhere('description', 'like', '%' . $feeling . '%')
                  ->orWhere('scent_profile', 'like', '%' . $feeling . '%');
            });
        }

        if ($scentFamily) {
             $query->where(function($q) use ($scentFamily) {
                $q->where('scent_profile', 'like', '%' . $scentFamily . '%')
                  ->orWhereHas('productAttributes', fn($paq) => $paq->where('scent_type', $scentFamily));
             });
        }

        if ($format === 'oil') {
            $query->where(function($q){ // Assuming 'oil' products might have 'Oil' in name or specific category
                $q->where('name', 'like', '%Oil%')
                  ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', '%Essential Oil%'));
            });
        } elseif ($format === 'soap') {
             $query->where(function($q){
                $q->where('name', 'like', '%Soap%')
                  ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', '%Soap%'));
            });
        }

        $recommendedProducts = $query->inRandomOrder()->take(2)->get();

        if ($recommendedProducts->count() < 2) {
            $fallbackProducts = $this->productQueryService->getFeaturedProducts(4)
                ->whereNotIn('id', $recommendedProducts->pluck('id')->all())
                ->take(2 - $recommendedProducts->count());
            $recommendedProducts = $recommendedProducts->merge($fallbackProducts);
        }
         if ($recommendedProducts->isEmpty()) {
            $recommendedProducts = Product::where('is_active', true)->inRandomOrder()->take(2)->get();
        }

        return $recommendedProducts->map(function(Product $product){
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'desc' => $product->short_description,
                'img' => $product->image, // Assuming image is just the filename relative to storage/app/public
                'image_url' => $product->image_url, // Uses accessor
                'price_formatted' => $product->price->format(), // Uses accessor
            ];
        });
    }

    public function saveQuizResult(array $answers, Collection $recommendations, ?string $email = null): QuizResult
    {
        return QuizResult::create([
            'user_id' => Auth::id(),
            'email' => $email ?: Auth::user()?->email,
            'answers' => $answers,
            'recommendations' => $recommendations->pluck('id')->toArray(), // Store IDs or full product data
        ]);
    }
}
