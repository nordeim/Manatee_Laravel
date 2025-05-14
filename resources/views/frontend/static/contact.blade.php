<x-layouts.app-layout>
    <x-slot name="title">Contact Us - {{ config('app.name') }}</x-slot>

    <div class="py-16 sm:py-24 bg-scent-bg dark:bg-scent-bg-dark">
        <div class="container-custom px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl sm:text-5xl font-serif font-bold text-scent-primary dark:text-scent-primary-dark mb-6">
                    Get In Touch
                </h1>
                <p class="text-lg text-scent-text dark:text-scent-text-dark leading-relaxed mb-10">
                    We'd love to hear from you! Whether you have a question about our products, an order, or just want to share your experience, feel free to reach out.
                </p>
            </div>

            <div class="max-w-xl mx-auto bg-scent-bg-alt dark:bg-scent-bg-alt-dark p-8 rounded-lg shadow-xl">
                {{-- Basic Contact Form (Does not submit yet, just UI) --}}
                <form action="#" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-y-6 gap-x-8">
                        <div>
                            <label for="contact_name" class="block text-sm font-medium text-scent-text dark:text-scent-text-dark">Full Name</label>
                            <input type="text" name="contact_name" id="contact_name" autocomplete="name" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scent-primary dark:focus:border-scent-primary-dark focus:ring-scent-primary dark:focus:ring-scent-primary-dark">
                        </div>
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-scent-text dark:text-scent-text-dark">Email</label>
                            <input type="email" name="contact_email" id="contact_email" autocomplete="email" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scent-primary dark:focus:border-scent-primary-dark focus:ring-scent-primary dark:focus:ring-scent-primary-dark">
                        </div>
                        <div>
                            <label for="contact_message" class="block text-sm font-medium text-scent-text dark:text-scent-text-dark">Message</label>
                            <textarea id="contact_message" name="contact_message" rows="4" required class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-scent-primary dark:focus:border-scent-primary-dark focus:ring-scent-primary dark:focus:ring-scent-primary-dark"></textarea>
                        </div>
                    </div>
                    <div class="mt-8">
                        <button type="submit" class="w-full btn btn-primary">
                            Send Message
                        </button>
                    </div>
                </form>
                <div class="mt-10 text-center text-scent-text dark:text-scent-text-dark">
                    <p class="mb-2">You can also reach us at:</p>
                    <p><i class="fas fa-envelope fa-fw mr-2"></i> <a href="mailto:{{ config('thescent.contact.email') }}" class="hover:underline">{{ config('thescent.contact.email') }}</a></p>
                    <p><i class="fas fa-phone fa-fw mr-2"></i> <a href="tel:{{ str_replace(['(', ')', ' ', '-'], '', config('thescent.contact.phone')) }}" class="hover:underline">{{ config('thescent.contact.phone') }}</a></p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app-layout>
