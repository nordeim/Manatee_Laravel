<x-layouts.app-layout>
    <x-slot name="title">
        {{ config('thescent.seo.default_title', 'The Scent - Home') }}
    </x-slot>

    {{-- HERO SECTION --}}
    @include('frontend.partials.home.hero')

    {{-- CURVE SEPARATOR 1 --}}
    @include('frontend.partials.curves.curve1')

    {{-- ABOUT SECTION --}}
    @include('frontend.partials.home.about')

    {{-- CURVE SEPARATOR 2 --}}
    @include('frontend.partials.curves.curve2')

    {{-- FEATURED PRODUCTS SECTION --}}
    @include('frontend.partials.home.featured-products', ['products' => $featuredProducts])

    {{-- CURVE SEPARATOR 3 --}}
    @include('frontend.partials.curves.curve3')

    {{-- SCENT QUIZ FINDER SECTION --}}
    @include('frontend.partials.home.finder')

    {{-- CURVE SEPARATOR 4 --}}
    @include('frontend.partials.curves.curve4')

    {{-- TESTIMONIALS SECTION --}}
    @include('frontend.partials.home.testimonials', ['testimonials' => $testimonialsData])

    {{-- CURVE SEPARATOR 5 --}}
    @include('frontend.partials.curves.curve5')

    {{-- NEWSLETTER SECTION --}}
    @include('frontend.partials.home.newsletter')

    {{-- Floating Shop Now Button (from sample_landing_page.html) --}}
    <a href="{{ route('products.index') }}" class="shop-now fixed bottom-7 right-7 z-[1091] bg-scent-cta dark:bg-scent-cta-dark text-white text-base font-semibold font-accent rounded-full py-4 px-7 shadow-lg hover:bg-scent-cta-hover dark:hover:bg-scent-cta-dark-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-scent-cta dark:focus:ring-scent-cta-dark transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105 flex items-center gap-3" aria-label="Shop Now">
        <i class="fa-solid fa-store text-lg"></i> Shop Now
    </a>

    {{-- Ambient Audio Toggle (from sample_landing_page.html) --}}
    <div class="ambient-audio-toggle fixed md:bottom-8 md:right-36 top-20 sm:top-auto right-7 z-[1010] bg-white/60 dark:bg-scent-bg-alt-dark/70 backdrop-blur-md rounded-full shadow-md"
         x-data="ambientAudioHandler()" title="Toggle Ambient Sound" role="region" aria-label="Toggle ambient sound">
        <audio id="scentAudio" loop src="https://cdn.pixabay.com/audio/2022/10/16/audio_12bac8f711.mp3"></audio>
        <button @click="toggleAudio()"
                class="p-3 rounded-full text-scent-accent dark:text-scent-accent-dark hover:bg-scent-overlay dark:hover:bg-scent-overlay-dark focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-scent-primary dark:focus:ring-scent-primary-dark transition-colors w-10 h-10 flex items-center justify-center"
                aria-label="Toggle ambient scent sound">
            <i class="fa-solid text-lg" :class="isAudioOn ? 'fa-volume-high' : 'fa-volume-xmark'"></i>
        </button>
    </div>

</x-layouts.app-layout>
