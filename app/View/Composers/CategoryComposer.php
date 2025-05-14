<?php

declare(strict_types=1);

namespace App\View\Composers;

use Illuminate\View\View;
use App\Domain\Catalog\Models\Category; // Assuming your Category model path

class CategoryComposer
{
    protected $categories;

    public function __construct()
    {
        // Cache this query for better performance
        $this->categories = \Illuminate\Support\Facades\Cache::remember('all_root_categories', now()->addHour(), function () {
            return Category::whereNull('parent_id')
                           // ->with('children') // Eager load children if displaying hierarchical menu
                           ->orderBy('name')
                           ->get();
        });
    }

    public function compose(View $view): void
    {
        $view->with('allCategories', $this->categories);
    }
}
