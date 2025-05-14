<section id="finder" class="finder-section py-16 sm:py-24 bg-gradient-to-br from-teal-50 via-amber-50 to-rose-50 dark:from-gray-800 dark:via-slate-800 dark:to-neutral-800">
    <div class="container-custom px-4">
        <div class="quiz-wrap max-w-3xl mx-auto p-8 sm:p-12 text-center bg-white/80 dark:bg-scent-bg-alt-dark/90 backdrop-blur-md shadow-xl rounded-2xl">
            <i class="fa-solid fa-flask-vial text-5xl text-scent-accent dark:text-scent-accent-dark mb-6"></i>
            <h2 class="text-3xl sm:text-4xl font-serif text-scent-primary dark:text-scent-primary-dark mb-4">
                Find Your Signature Scent
            </h2>
            <p class="text-lg text-scent-text dark:text-scent-text-dark mb-8 max-w-xl mx-auto">
                Not sure where to start? Our interactive quiz will guide you to the perfect aromatherapy blend based on your mood and preferences.
            </p>
            <a href="{{ route('quiz.show') }}" class="btn btn-primary text-lg py-3 px-8 rounded-full font-accent font-semibold shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105 group">
                Take The Quiz <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>
</section>
