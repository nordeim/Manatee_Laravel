<section id="about" class="about-section py-16 sm:py-24 bg-gradient-to-br from-[#eef7f7] to-scent-bg dark:from-slate-800 dark:to-scent-bg-dark relative overflow-hidden">
    <div class="container-custom px-4">
        <div class="about-content-wrap grid grid-cols-1 md:grid-cols-2 items-center gap-12">
            <div class="about-parallax-img relative md:order-1" id="aboutParallaxImgContainer">
                <img src="https://raw.githubusercontent.com/nordeim/The-Scent/refs/heads/main/images/scent4.jpg"
                     loading="lazy"
                     alt="Botanical Aromatic Ingredients with mortar and pestle"
                     id="aboutParallaxImg"
                     class="rounded-2xl w-full max-w-md mx-auto md:max-w-lg shadow-lg dark:shadow-scent-shadow-dark-lg"
                     style="filter: blur(var(--parallax-blur)); transition: filter 0.4s ease-out, transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
            </div>
            <div class="about-parallax-text relative z-[7] md:order-2 text-center md:text-left">
                <h2 class="text-3xl sm:text-4xl font-serif text-scent-primary dark:text-scent-primary-dark mb-6">
                    Nature's Wisdom, Bottled with Care
                </h2>
                <p class="text-base sm:text-lg text-scent-text dark:text-scent-text-dark mb-8 leading-relaxed max-w-lg mx-auto md:mx-0">
                    At <strong>The Scent</strong>, we believe in the restorative power of nature. We meticulously source the world's finest botanicals, blending ancient wisdom with modern expertise to create harmonious aromatherapy oils and soaps that nurture your mind, body, and spirit.
                </p>
                <div class="about-icons flex flex-col sm:flex-row items-center justify-center md:justify-start gap-x-8 gap-y-4 text-sm sm:text-base">
                    <span class="flex items-center gap-2 text-scent-primary dark:text-scent-primary-dark group">
                        <i class="fa-solid fa-earth-americas text-2xl text-scent-accent dark:text-scent-accent-dark group-hover:scale-110 transition-transform"></i>
                        <span class="font-accent">Globally Sourced</span>
                    </span>
                    <span class="flex items-center gap-2 text-scent-primary dark:text-scent-primary-dark group">
                        <i class="fa-solid fa-recycle text-2xl text-scent-accent dark:text-scent-accent-dark group-hover:scale-110 transition-transform"></i>
                        <span class="font-accent">Eco-Conscious</span>
                    </span>
                    <span class="flex items-center gap-2 text-scent-primary dark:text-scent-primary-dark group">
                        <i class="fa-solid fa-vial-circle-check text-2xl text-scent-accent dark:text-scent-accent-dark group-hover:scale-110 transition-transform"></i>
                        <span class="font-accent">Expert Formulated</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- Ingredient Map --}}
        <div class="ingredient-map-wrap mt-16 sm:mt-24 max-w-3xl mx-auto text-center">
            <div class="ingredient-map-title text-xl sm:text-2xl font-accent font-semibold text-scent-primary dark:text-scent-primary-dark mb-6">
                Discover the Origins of Our Signature Scents
            </div>
            <div class="ingredient-map relative w-full max-w-2xl mx-auto rounded-2xl shadow-md dark:shadow-scent-shadow-dark-base overflow-hidden"
                 x-data="{ focusedMarker: null }">
                <img src="https://raw.githubusercontent.com/nordeim/The-Scent-ideas/refs/heads/main/images/BlankMap-World-noborders.jpg"
                     loading="lazy" alt="World Map showing ingredient origins"
                     class="w-full block rounded-2xl opacity-90 dark:opacity-80" />

                @php
                    $markers = [
                        ['name' => 'Lavender', 'origin' => 'France', 'style' => 'left:43%;top:45%;', 'label_side' => 'right'],
                        ['name' => 'Cedarwood', 'origin' => 'Japan', 'style' => 'left:76.5%;top:50%;', 'label_side' => 'left'],
                        ['name' => 'Rosemary', 'origin' => 'Mediterranean', 'style' => 'left:48%;top:55%;', 'label_side' => 'right'],
                        ['name' => 'Orange', 'origin' => 'Brazil', 'style' => 'left:35%;top:73%;', 'label_side' => 'right'],
                    ];
                @endphp

                @foreach($markers as $index => $marker)
                <div class="ingredient-marker absolute w-7 h-7 transform -translate-x-1/2 -translate-y-1/2 cursor-pointer z-10 rounded-full group focus-within:scale-125 hover:scale-125 transition-transform duration-300"
                     style="{{ $marker['style'] }}"
                     tabindex="0"
                     aria-label="Ingredient from {{ $marker['origin'] }}: {{ $marker['name'] }}"
                     @focus="focusedMarker = {{ $index }}" @blur="focusedMarker = null"
                     @mouseenter="focusedMarker = {{ $index }}" @mouseleave="focusedMarker = null"
                     @keydown.enter.prevent="focusedMarker = (focusedMarker === {{ $index }} ? null : {{ $index }})"
                     @keydown.space.prevent="focusedMarker = (focusedMarker === {{ $index }} ? null : {{ $index }})">
                    <div class="marker-pulse absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 w-3 h-3 bg-scent-accent dark:bg-scent-accent-dark rounded-full shadow-[0_0_0_0_rgba(224,168,111,0.7)] dark:shadow-[0_0_0_0_rgba(246,173,85,0.7)]"
                         style="animation: markerPulse 1.8s infinite cubic-bezier(0.66, 0, 0, 1);"></div>
                    <div class="marker-label absolute top-1/2 -translate-y-1/2
                                {{ $marker['label_side'] === 'right' ? 'left-full ml-3' : 'right-full mr-3' }}
                                min-w-[90px] whitespace-nowrap bg-gray-800/95 text-white text-xs sm:text-sm p-2 px-3 rounded-lg shadow-lg
                                opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible
                                transition-all duration-200 ease-out"
                         :class="{ 'opacity-100 visible': focusedMarker === {{ $index }} }"
                         x-show="focusedMarker === {{ $index }}"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 {{ $marker['label_side'] === 'right' ? 'translate-x-[-5px]' : 'translate-x-[5px]' }}"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 {{ $marker['label_side'] === 'right' ? 'translate-x-[-5px]' : 'translate-x-[5px]' }}">
                        {{ $marker['name'] }}<small class="block text-xs opacity-80">{{ $marker['origin'] }}</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
