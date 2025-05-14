<x-layouts.app-layout>
    <x-slot name="title">
        Scent Quiz - Discover Your Perfect Aroma - {{ config('app.name') }}
    </x-slot>

    <section id="finder" class="finder-section py-16 sm:py-24 bg-gradient-to-r from-teal-50 via-amber-50 to-rose-50 dark:from-gray-800 dark:via-slate-800 dark:to-neutral-800">
        <div class="container-custom">
            <div class="quiz-wrap max-w-2xl mx-auto p-6 sm:p-10 bg-white/90 dark:bg-scent-bg-alt-dark/95 backdrop-blur-sm shadow-xl rounded-2xl"
                 x-data="scentQuizHandler()">
                <div class="quiz-headline text-center text-2xl sm:text-3xl font-serif text-scent-primary dark:text-scent-primary-dark mb-8">
                    Discover Your Perfect Scent
                </div>

                <form id="scentQuizForm" class="multi-quiz mt-7 relative" @submit.prevent>
                    {{-- Step 1: Feeling --}}
                    <div class="quiz-step" data-step="1" x-show="currentStep === 1" x-transition.opacity.duration.500ms>
                        <div class="quiz-q text-center text-xl font-semibold font-serif text-scent-text dark:text-scent-text-dark mb-6">
                            <i class="fa-solid fa-lightbulb mr-2 text-scent-accent dark:text-scent-accent-dark"></i> How do you want to feel?
                        </div>
                        <div class="quiz-options grid grid-cols-2 sm:grid-cols-2 gap-3 sm:gap-4 mb-8">
                            <button type="button" @click="nextStep('feeling', 'relax')" class="quiz-opt">
                                <i class="fa-solid fa-couch"></i> Relaxed
                            </button>
                            <button type="button" @click="nextStep('feeling', 'energize')" class="quiz-opt">
                                <i class="fa-solid fa-bolt"></i> Energized
                            </button>
                            <button type="button" @click="nextStep('feeling', 'focus')" class="quiz-opt">
                                <i class="fa-solid fa-crosshairs"></i> Focused
                            </button>
                            <button type="button" @click="nextStep('feeling', 'sleep')" class="quiz-opt">
                                <i class="fa-solid fa-bed"></i> Sleepy
                            </button>
                        </div>
                    </div>

                    {{-- Step 2: Scent Family --}}
                    <div class="quiz-step" data-step="2" x-show="currentStep === 2" x-transition.opacity.duration.500ms>
                        <div class="quiz-q text-center text-xl font-semibold font-serif text-scent-text dark:text-scent-text-dark mb-6">
                            <i class="fa-solid fa-leaf mr-2 text-scent-accent dark:text-scent-accent-dark"></i> Which scent family calls to you?
                        </div>
                        <div class="quiz-options grid grid-cols-2 sm:grid-cols-2 gap-3 sm:gap-4 mb-8">
                            <button type="button" @click="nextStep('scentFamily', 'floral')" class="quiz-opt">
                                <i class="fa-solid fa-fan"></i> Floral
                            </button>
                            <button type="button" @click="nextStep('scentFamily', 'citrus')" class="quiz-opt">
                                <i class="fa-solid fa-lemon"></i> Citrus
                            </button>
                            <button type="button" @click="nextStep('scentFamily', 'woody')" class="quiz-opt">
                                <i class="fa-solid fa-tree"></i> Woody
                            </button>
                            <button type="button" @click="nextStep('scentFamily', 'herbal')" class="quiz-opt">
                                <i class="fa-solid fa-mortar-pestle"></i> Herbal
                            </button>
                        </div>
                    </div>

                    {{-- Step 3: Format --}}
                    <div class="quiz-step" data-step="3" x-show="currentStep === 3" x-transition.opacity.duration.500ms>
                        <div class="quiz-q text-center text-xl font-semibold font-serif text-scent-text dark:text-scent-text-dark mb-6">
                            <i class="fa-solid fa-spray-can-sparkles mr-2 text-scent-accent dark:text-scent-accent-dark"></i> Choose your preferred format:
                        </div>
                        <div class="quiz-options grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-8">
                            <button type="button" @click="nextStep('format', 'oil')" class="quiz-opt">
                                <i class="fa-solid fa-bottle-droplet"></i> Essential Oil
                            </button>
                            <button type="button" @click="nextStep('format', 'soap')" class="quiz-opt">
                                <i class="fa-solid fa-soap"></i> Artisan Soap
                            </button>
                            <button type="button" @click="nextStep('format', 'both')" class="quiz-opt sm:col-span-3">
                                <i class="fa-solid fa-boxes-stacked"></i> Surprise Me!
                            </button>
                        </div>
                    </div>

                    <div class="quiz-progress text-center text-sm text-scent-primary dark:text-scent-primary-dark opacity-80 mb-4 font-accent"
                         x-text="quizStepNumText"
                         x-show="currentStep <= totalSteps && !isLoadingResult">
                    </div>
                </form>

                {{-- Quiz Result Area --}}
                <div class="quiz-result text-center pt-6 min-h-[200px]" id="quizResultDisplay" style="display: none;" x-show="currentStep > totalSteps || isLoadingResult" x-transition.opacity.duration.700ms>
                    <div x-show="isLoadingResult" class="text-center py-10">
                        <i class="fa-solid fa-spinner fa-spin text-4xl text-scent-primary dark:text-scent-primary-dark"></i>
                        <p class="mt-4 text-scent-text dark:text-scent-text-dark">Finding your perfect scents...</p>
                    </div>

                    <div x-show="!isLoadingResult && productRecommendations.length > 0">
                        <h4 class="text-2xl font-serif font-bold text-scent-accent dark:text-scent-accent-dark mb-6">Your Aroma Profile Suggests:</h4>
                        <div class="products-grid grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-md mx-auto mb-8">
                            <template x-for="product in productRecommendations" :key="product.id">
                                <div class="product-card group bg-scent-bg dark:bg-scent-bg-alt-dark rounded-lg shadow-md hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 border-2 border-transparent hover:border-scent-accent dark:hover:border-scent-accent-dark overflow-hidden flex flex-col">
                                    <a :href="'/product/' + product.id" class="block"> {{-- Update to actual product URL later --}}
                                        <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden">
                                            <img :src="'https://raw.githubusercontent.com/nordeim/The-Scent/refs/heads/main/images/' + product.img"
                                                 :alt="product.name"
                                                 class="w-full h-full object-cover object-center group-hover:opacity-90 group-hover:scale-105 transition-all duration-300 ease-in-out">
                                        </div>
                                    </a>
                                    <div class="p-4 text-center flex flex-col flex-grow">
                                        <h3 class="text-lg font-serif font-bold text-scent-primary dark:text-scent-primary-dark mb-1" x-text="product.name"></h3>
                                        <p class="text-xs text-scent-text dark:text-scent-text-dark opacity-80 mb-2 flex-grow" x-text="product.desc"></p>
                                        <a :href="'/product/' + product.id"
                                           class="mt-auto self-center inline-flex items-center gap-1 text-xs font-accent font-semibold uppercase text-scent-cta dark:text-scent-cta-dark hover:text-scent-cta-hover dark:hover:text-scent-cta-dark-hover">
                                            View Product <i class="fa-solid fa-arrow-right text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div x-show="!isLoadingResult && productRecommendations.length === 0">
                        <p class="text-scent-text dark:text-scent-text-dark mb-6">We couldn't find specific recommendations right now, but explore our full collection!</p>
                    </div>

                    <a href="{{ route('products.index') }}" class="cta-main btn btn-primary text-base mt-8">
                        <i class="fa-solid fa-gift mr-2"></i> Shop All Collections
                    </a>
                </div>
            </div>
        </div>
    </section>
    @push('styles')
    <style>
        .quiz-opt {
            @apply w-full bg-scent-accent dark:bg-scent-accent-dark text-white dark:text-scent-bg-dark border-none rounded-full font-accent text-sm sm:text-base font-semibold py-3 px-4 shadow-md hover:bg-scent-cta dark:hover:bg-scent-cta-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-scent-cta dark:focus:ring-scent-cta-dark transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 hover:scale-[1.02] flex items-center justify-center gap-2;
        }
        .quiz-opt i { @apply opacity-90; }
    </style>
    @endpush
</x-layouts.app-layout>
