<section id="testimonials" class="testimonials-section py-16 sm:py-24 bg-scent-bg dark:bg-scent-bg-dark">
    <div class="container-custom px-4">
        <h2 class="text-3xl sm:text-4xl font-serif text-center text-scent-primary dark:text-scent-primary-dark mb-12 sm:mb-16">
            Hear From Our Community
        </h2>
        @if(!empty($testimonials) && count($testimonials) > 0)
            <div class="testimonials-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($testimonials as $testimonial)
                    <div class="testimonial-card bg-scent-bg-alt dark:bg-scent-bg-alt-dark p-6 sm:p-8 rounded-xl shadow-lg border-l-4 border-scent-accent dark:border-scent-accent-dark opacity-0 transition-all duration-700 ease-out" tabindex="0">
                        <div class="relative">
                            <p class="text-base sm:text-lg text-scent-text dark:text-scent-text-dark italic leading-relaxed mb-6 pl-10">
                                <span class="absolute left-0 top-0 text-5xl text-scent-primary dark:text-scent-primary-dark opacity-10 transform -translate-y-2">
                                    <i class="fa-solid fa-quote-left"></i>
                                </span>
                                {{ $testimonial['quote'] }}
                            </p>
                        </div>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-scent-soap dark:bg-scent-soap-dark flex items-center justify-center text-scent-primary dark:text-scent-primary-dark font-semibold text-lg">
                                {{ strtoupper(substr($testimonial['author'], 0, 1)) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-base font-semibold font-accent text-scent-primary dark:text-scent-primary-dark">{{ $testimonial['author'] }}</div>
                                <div class="text-scent-accent dark:text-scent-accent-dark text-sm">
                                    @for($i = 0; $i < $testimonial['stars']; $i++) <i class="fa-solid fa-star"></i> @endfor
                                    @for($i = $testimonial['stars']; $i < 5; $i++) <i class="fa-regular fa-star"></i> @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-scent-text dark:text-scent-text-dark">No testimonials yet. Be the first to share your experience!</p>
        @endif
    </div>
</section>
