<x-layouts.app-layout>
    <x-slot name="title">About Us - {{ config('app.name') }}</x-slot>
    <div class="py-16 sm:py-24 bg-scent-bg dark:bg-scent-bg-dark">
        <div class="container-custom px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl sm:text-5xl font-serif font-bold text-scent-primary dark:text-scent-primary-dark mb-6">
                    Our Story
                </h1>
                <p class="text-lg text-scent-text dark:text-scent-text-dark leading-relaxed">
                    Welcome to The Scent, where tranquility meets craftsmanship...
                    (Expand with content similar to the about section on the homepage, or unique content for this page).
                </p>
            </div>
            <div class="mt-12 prose dark:prose-invert lg:prose-xl mx-auto text-scent-text dark:text-scent-text-dark">
                <p>Further details about our mission, values, and commitment to natural ingredients...</p>
                <p>Information about sourcing, sustainability practices, etc.</p>
            </div>
        </div>
    </div>
</x-layouts.app-layout>
