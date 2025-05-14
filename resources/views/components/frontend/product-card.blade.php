@props(['product'])

<div class="product-card group bg-scent-bg-alt dark:bg-scent-bg-alt-dark rounded-lg shadow-md hover:shadow-xl dark:hover:shadow-scent-shadow-dark-lg transition-all duration-300 ease-in-out transform hover:-translate-y-1 border-2 border-transparent hover:border-scent-accent dark:hover:border-scent-accent-dark overflow-hidden flex flex-col"
     tabindex="0" role="article" aria-labelledby="product-name-{{ $product->id }}">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden">
            <img src="{{ $product->image_url ?: asset('images/product-placeholder.png') }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover object-center group-hover:opacity-90 group-hover:scale-105 transition-all duration-300 ease-in-out">
        </div>
    </a>
    <div class="p-6 text-center flex flex-col flex-grow">
        <div class="flex-grow">
            <h3 id="product-name-{{ $product->id }}" class="text-xl font-serif font-bold text-scent-primary dark:text-scent-primary-dark mb-2">
                <a href="{{ route('products.show', $product->slug) }}" class="hover:underline">
                    {{ $product->name }}
                </a>
            </h3>
            @if($product->short_description)
            <p class="text-sm text-scent-text dark:text-scent-text-dark opacity-80 mb-3 line-clamp-2">
                {{ $product->short_description }}
            </p>
            @endif
            <p class="text-lg font-semibold text-scent-accent dark:text-scent-accent-dark mb-4">
                {{ $product->price->format() }}
            </p>
        </div>
        <a href="{{ route('products.show', $product->slug) }}"
           class="mt-auto self-center inline-flex items-center gap-2 text-sm font-accent font-semibold uppercase text-scent-cta dark:text-scent-cta-dark hover:text-scent-cta-hover dark:hover:text-scent-cta-dark-hover border-b-2 border-transparent hover:border-scent-cta dark:hover:border-scent-cta-dark transition-colors duration-150 group-hover:tracking-wider">
            View Details <i class="fa-solid fa-arrow-right transform group-hover:translate-x-1 transition-transform duration-150"></i>
        </a>
    </div>
</div>
