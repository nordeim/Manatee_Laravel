<x-layouts.app-layout>
    <x-slot name="title">
        {{ config('app.name', 'The Scent') }} — Home
    </x-slot>

    {{-- Hero Section (Simplified for now) --}}
    <section class="hero-section bg-scent-primary text-white h-[80vh] flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-5xl font-serif font-bold mb-4">Welcome to The Scent</h1>
            <p class="text-xl mb-8">Discover your perfect aroma.</p>
            <a href="#products" class="btn btn-primary text-lg">Explore Collections</a>
        </div>
    </section>

    {{-- Placeholder for other sections from sample_landing_page.html --}}
    <div class="container-custom py-12">
        <h2 class="text-3xl font-serif text-center mb-8">Our Story</h2>
        <p class="text-center">This is where the "About" section content will go.</p>

        <h2 class="text-3xl font-serif text-center mt-12 mb-8">Featured Products</h2>
        <p class="text-center">Product cards will be displayed here.</p>
        {{-- Example of using ProductCard component if it existed and data was passed --}}
        {{-- @foreach ($featuredProducts as $product)
            <x-frontend.product-card :product="$product" />
        @endforeach --}}
    </div>

</x-layouts.app-layout>
