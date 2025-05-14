<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Domain\Catalog\Services\ProductQueryService;

class HomeController extends Controller
{
    public function __construct(protected ProductQueryService $productQueryService)
    {
    }

    public function index(): View
    {
        // Fetch a few featured products to display on the homepage
        $featuredProducts = $this->productQueryService->getFeaturedProducts(4);

        // Fetch some testimonials (placeholder for now, will come from DB later)
        $testimonials = [
            [
                'quote' => "The Serenity Blend Oil is pure magic in a bottle. It's become an essential part of my evening wind-down routine. Truly calming.",
                'author' => 'Sarah L.',
                'stars' => 5,
            ],
            [
                'quote' => "I was skeptical about focus oils, but the Focus Flow Blend genuinely helps clear my head during long workdays. The scent is refreshing, not overpowering.",
                'author' => 'Michael T.',
                'stars' => 5,
            ],
            [
                'quote' => "These soaps are divine! The Citrus Burst leaves my skin feeling soft and smells incredible. Plus, they look beautiful in my bathroom.",
                'author' => 'Emma R.',
                'stars' => 5,
            ]
        ];

        return view('frontend.home', [
            'featuredProducts' => $featuredProducts,
            'testimonialsData' => $testimonials, // Renamed to avoid conflict with section ID
        ]);
    }

    public function about(): View // Placeholder for a dedicated about page if needed
    {
        return view('frontend.static.about'); // Example: resources/views/frontend/static/about.blade.php
    }

    public function contact(): View // Placeholder
    {
        return view('frontend.static.contact');
    }
}
