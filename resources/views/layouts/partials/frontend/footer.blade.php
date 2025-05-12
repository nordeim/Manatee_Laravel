<footer class="bg-[#1f2427] text-[#bdc8d3] pt-16">
    <div class="container-custom">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 pb-12">
            <!-- About Column -->
            <div class="footer-col">
                <h3 class="footer-heading"><i class="fa-solid fa-feather-pointed mr-2"></i>{{ config('app.name', 'The Scent') }}</h3>
                <p class="text-sm opacity-85 leading-relaxed">
                    {{ config('thescent.seo.default_description', 'Crafting premium aromatherapy and wellness products inspired by nature\'s harmony.') }}
                </p>
                <div class="mt-4 flex space-x-3">
                    <a href="{{ config('thescent.social_links.facebook', '#') }}" class="footer-social-link" aria-label="Facebook" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="{{ config('thescent.social_links.instagram', '#') }}" class="footer-social-link" aria-label="Instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="{{ config('thescent.social_links.pinterest', '#') }}" class="footer-social-link" aria-label="Pinterest" title="Pinterest"><i class="fab fa-pinterest-p"></i></a>
                    <a href="{{ config('thescent.social_links.twitter', '#') }}" class="footer-social-link" aria-label="Twitter" title="Twitter"><i class="fab fa-twitter"></i></a>
                </div>
            </div>

            <!-- Quick Links Column -->
            <div class="footer-col">
                <h3 class="footer-heading">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="footer-link">Shop Oils</a></li> {{-- Update route --}}
                    <li><a href="#" class="footer-link">Shop Soaps</a></li> {{-- Update route --}}
                    <li><a href="#" class="footer-link">Gift Sets</a></li>
                    <li><a href="#about" class="footer-link">Our Story</a></li> {{-- Update route --}}
                    <li><a href="#finder" class="footer-link">Scent Quiz</a></li> {{-- Update route --}}
                </ul>
            </div>

            <!-- Customer Care Column -->
            <div class="footer-col">
                <h3 class="footer-heading">Customer Care</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="footer-link">Contact Us</a></li>
                    <li><a href="#" class="footer-link">FAQs</a></li>
                    <li><a href="#" class="footer-link">Shipping & Returns</a></li>
                    <li><a href="#" class="footer-link">Track Order</a></li>
                    <li><a href="#" class="footer-link">Privacy Policy</a></li>
                </ul>
            </div>

            <!-- Get In Touch Column -->
            <div class="footer-col">
                <h3 class="footer-heading">Get In Touch</h3>
                <address class="not-italic space-y-3 text-sm opacity-90">
                    <p><i class="fas fa-map-marker-alt fa-fw text-scent-accent-dark mr-2"></i> {{ config('thescent.contact.address') }}</p>
                    <p><i class="fas fa-phone fa-fw text-scent-accent-dark mr-2"></i> <a href="tel:{{ str_replace(['(', ')', ' ', '-'], '', config('thescent.contact.phone')) }}" class="hover:text-scent-accent-dark">{{ config('thescent.contact.phone') }}</a></p>
                    <p><i class="fas fa-envelope fa-fw text-scent-accent-dark mr-2"></i> <a href="mailto:{{ config('thescent.contact.email') }}" class="hover:text-scent-accent-dark">{{ config('thescent.contact.email') }}</a></p>
                </address>
            </div>
        </div>
    </div>
    <div class="bg-[#15191b] py-6 text-center text-sm text-gray-400 border-t border-gray-700">
        <p>&copy; <span x-data="{ year: new Date().getFullYear() }" x-text="year"></span> {{ config('app.name', 'The Scent') }}. All Rights Reserved.</p>
        <div class="mt-2 payment-methods space-x-3">
            <i class="fab fa-cc-visa text-2xl text-gray-500 hover:text-white" title="Visa"></i>
            <i class="fab fa-cc-mastercard text-2xl text-gray-500 hover:text-white" title="Mastercard"></i>
            <i class="fab fa-cc-paypal text-2xl text-gray-500 hover:text-white" title="PayPal"></i>
            <i class="fab fa-cc-amex text-2xl text-gray-500 hover:text-white" title="American Express"></i>
        </div>
    </div>
</footer>

@push('styles')
<style>
    .footer-heading {
        @apply text-lg font-accent font-semibold text-scent-accent-dark mb-4 tracking-wider;
    }
    .footer-link {
        @apply text-sm text-gray-300 hover:text-scent-accent-dark transition-colors duration-150;
    }
    .footer-social-link {
        @apply text-gray-300 hover:text-scent-accent-dark text-xl transition-colors duration-150;
    }
</style>
@endpush
