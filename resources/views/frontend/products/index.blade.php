<x-layouts.app-layout>
    <x-slot name="title">
        Shop All Products - {{ config('app.name') }}
    </x-slot>

    <div class="bg-scent-bg dark:bg-scent-bg-dark py-12 sm:py-16">
        <div class="container-custom">
            <div class="mb-10 text-center">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-bold text-scent-primary dark:text-scent-primary-dark">
                    {{ $selectedCategory ? $selectedCategory->name : 'All Our Scents' }}
                </h1>
                @if($selectedCategory && $selectedCategory->description)
                    <p class="mt-4 text-lg text-scent-text dark:text-scent-text-dark max-w-2xl mx-auto">{{ $selectedCategory->description }}</p>
                @else
                    <p class="mt-4 text-lg text-scent-text dark:text-scent-text-dark max-w-2xl mx-auto">
                        Discover our curated collection of natural aromatherapy products.
                    </p>
                @endif
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Filters Sidebar (Example) -->
                <aside class="lg:w-1/4 xl:w-1/5">
                    <div class="p-6 bg-scent-bg-alt dark:bg-scent-bg-alt-dark rounded-lg shadow">
                        <h3 class="text-xl font-serif font-semibold text-scent-primary dark:text-scent-primary-dark mb-4">Categories</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('products.index') }}"
                                   class="block text-scent-text dark:text-scent-text-dark hover:text-scent-primary dark:hover:text-scent-primary-dark transition-colors {{ !request('category') ? 'font-bold text-scent-accent dark:text-scent-accent-dark' : '' }}">
                                    All Products
                                </a>
                            </li>
                            @if(isset($allCategories) && $allCategories->count())
                                @foreach($allCategories as $category)
                                    <li>
                                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                                           class="block text-scent-text dark:text-scent-text-dark hover:text-scent-primary dark:hover:text-scent-primary-dark transition-colors {{ request('category') === $category->slug ? 'font-bold text-scent-accent dark:text-scent-accent-dark' : '' }}">
                                            {{ $category->name }}
                                        </a>
                                        {{-- You can add subcategories here if needed --}}
                                    </li>
                                @endforeach
                            @endif
                        </ul>

                        {{-- Add more filters here: price range, scent type etc. --}}
                    </div>
                </aside>

                <!-- Product Grid -->
                <div class="lg:w-3/4 xl:w-4/5">
                    @if($products->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 sm:gap-8">
                            @foreach($products as $product)
                                <x-frontend.product-card :product="$product" />
                            @endforeach
                        </div>

                        <div class="mt-12">
                            {{ $products->links() }} {{-- Pagination --}}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fa-solid fa-box-open text-6xl text-scent-primary dark:text-scent-primary-dark opacity-50 mb-4"></i>
                            <p class="text-xl text-scent-text dark:text-scent-text-dark">No products found matching your criteria.</p>
                            <a href="{{ route('products.index') }}" class="mt-6 btn btn-primary">
                                Browse All Products
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app-layout>
