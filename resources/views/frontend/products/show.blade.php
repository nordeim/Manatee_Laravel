<x-layouts.app-layout>
    <x-slot name="title">
        {{ $product->name }} - {{ config('app.name') }}
    </x-slot>

    <div class="bg-scent-bg dark:bg-scent-bg-dark py-12 sm:py-16">
        <div class="container-custom">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start">
                <!-- Product Image Gallery -->
                <div x-data="{ mainImage: '{{ $product->image_url ?: asset('images/product-placeholder.png') }}' }">
                    <div class="aspect-w-1 aspect-h-1 w-full rounded-lg overflow-hidden shadow-lg mb-4">
                        <img :src="mainImage" alt="{{ $product->name }}" class="w-full h-full object-cover object-center">
                    </div>
                    @if($product->gallery_images && count($product->gallery_images) > 0)
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                            <button @click="mainImage = '{{ $product->image_url }}'" class="aspect-w-1 aspect-h-1 rounded overflow-hidden focus:ring-2 ring-scent-primary dark:ring-scent-primary-dark">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }} thumbnail" class="w-full h-full object-cover">
                            </button>
                            @foreach($product->gallery_image_urls as $galleryImageUrl)
                                <button @click="mainImage = '{{ $galleryImageUrl }}'" class="aspect-w-1 aspect-h-1 rounded overflow-hidden focus:ring-2 ring-scent-primary dark:ring-scent-primary-dark">
                                    <img src="{{ $galleryImageUrl }}" alt="{{ $product->name }} gallery image" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div>
                    @if($product->category)
                        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-sm font-medium text-scent-primary dark:text-scent-primary-dark hover:text-scent-accent dark:hover:text-scent-accent-dark uppercase tracking-wider">
                            {{ $product->category->name }}
                        </a>
                    @endif
                    <h1 class="mt-2 text-3xl sm:text-4xl font-serif font-bold text-scent-primary dark:text-scent-primary-dark tracking-tight">
                        {{ $product->name }}
                    </h1>

                    <div class="mt-3">
                        <p class="text-3xl text-scent-accent dark:text-scent-accent-dark">{{ $product->price->format() }}</p>
                    </div>

                    @if($product->short_description)
                    <div class="mt-6">
                        <h3 class="sr-only">Description</h3>
                        <div class="space-y-6 text-base text-scent-text dark:text-scent-text-dark prose dark:prose-invert max-w-none">
                            <p>{{ $product->short_description }}</p>
                        </div>
                    </div>
                    @endif

                    <form class="mt-6"> {{-- Add to cart form will be implemented in Phase 5 --}}
                        {{-- Quantity selector --}}
                        <div class="mt-4">
                            <label for="quantity" class="block text-sm font-medium text-scent-text dark:text-scent-text-dark">Quantity</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1"
                                   class="mt-1 block w-20 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-scent-primary dark:focus:border-scent-primary-dark focus:ring-scent-primary dark:focus:ring-scent-primary-dark sm:text-sm">
                        </div>

                        <div class="mt-10 flex">
                            <button type="submit"
                                    class="btn btn-primary w-full sm:w-auto py-3 px-8 text-base">
                                Add to Cart
                            </button>
                            {{-- Add to wishlist button (Phase future) --}}
                        </div>
                    </form>

                    {{-- Product Attributes from product_attributes table --}}
                    @if($product->productAttributes)
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                        <h3 class="text-lg font-medium text-scent-primary dark:text-scent-primary-dark">Scent Profile</h3>
                        <div class="mt-4 prose prose-sm text-scent-text dark:text-scent-text-dark dark:prose-invert">
                            <ul class="list-disc list-inside space-y-1">
                                @if($product->productAttributes->scent_type)<li><strong>Type:</strong> {{ Str::title($product->productAttributes->scent_type) }}</li>@endif
                                @if($product->productAttributes->mood_effect)<li><strong>Mood:</strong> {{ Str::title($product->productAttributes->mood_effect) }}</li>@endif
                                @if($product->productAttributes->intensity_level)<li><strong>Intensity:</strong> {{ Str::title($product->productAttributes->intensity_level) }}</li>@endif
                            </ul>
                        </div>
                    </div>
                    @endif

                    @if($product->description)
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                        <h3 class="text-lg font-medium text-scent-primary dark:text-scent-primary-dark">Full Description</h3>
                        <div class="mt-4 prose prose-sm text-scent-text dark:text-scent-text-dark dark:prose-invert max-w-none">
                            {!! nl2br(e($product->description)) !!} {{-- Use nl2br(e()) for basic formatting if description is plain text --}}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Related Products Section --}}
            @if($relatedProducts && $relatedProducts->count() > 0)
            <div class="mt-16 sm:mt-24">
                <h2 class="text-2xl font-serif font-bold text-scent-primary dark:text-scent-primary-dark mb-8 text-center">
                    You Might Also Like
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                    @foreach($relatedProducts as $relatedProduct)
                        <x-frontend.product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layouts.app-layout>
