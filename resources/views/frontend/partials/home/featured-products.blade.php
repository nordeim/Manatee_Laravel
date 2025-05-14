@if($products && $products->count() > 0)
<section id="products" class="py-16 sm:py-24 bg-scent-bg-alt dark:bg-scent-bg-alt-dark">
    <div class="container-custom">
        <h2 class="text-3xl sm:text-4xl font-serif text-center text-scent-primary dark:text-scent-primary-dark mb-12 sm:mb-16">
            Artisanal Collections
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            @foreach($products as $product)
                <x-frontend.product-card :product="$product" />
            @endforeach
        </div>
        <div class="text-center mt-12">
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                View All Products
            </a>
        </div>
    </div>
</section>
@endif
