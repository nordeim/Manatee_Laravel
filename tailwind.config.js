import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/View/Components/**/*.php', // For Blade components if classes are used there
    ],

    darkMode: 'class', // Enable class-based dark mode

    theme: {
        extend: {
            colors: {
                // Light Mode Palette (from sample_landing_page.html CSS variables)
                'scent-bg': 'var(--clr-bg, #f8f7f4)',
                'scent-bg-alt': 'var(--clr-bg-alt, #ffffff)',
                'scent-text': 'var(--clr-text, #333d41)',
                'scent-primary': 'var(--clr-primary, #2a7c8a)',
                'scent-accent': 'var(--clr-accent, #e0a86f)',
                'scent-soap': 'var(--clr-soap, #c8dace)',
                'scent-cta': 'var(--clr-cta, #ff7b4f)',
                'scent-cta-hover': 'var(--clr-cta-hover, #ff6a3d)',
                'scent-overlay': 'var(--clr-overlay, rgba(42, 124, 138, 0.06))',

                // Dark Mode Palette (from sample_landing_page.html CSS variables)
                'scent-bg-dark': 'var(--clr-bg-dark, #1a202c)',
                'scent-bg-alt-dark': 'var(--clr-bg-alt-dark, #2d3748)',
                'scent-text-dark': 'var(--clr-text-dark, #e2e8f0)',
                'scent-primary-dark': 'var(--clr-primary-dark, #4fd1c5)',
                'scent-accent-dark': 'var(--clr-accent-dark, #f6ad55)',
                'scent-soap-dark': 'var(--clr-soap-dark, #4a5568)',
                'scent-cta-dark': 'var(--clr-cta-dark, #ff8c69)',
                'scent-cta-dark-hover': 'var(--clr-cta-dark-hover, #ff7043)',
                'scent-overlay-dark': 'var(--clr-overlay-dark, rgba(79, 209, 197, 0.1))',
            },
            fontFamily: {
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans], // Body font
                serif: ['Cormorant Garamond', ...defaultTheme.fontFamily.serif], // Headings
                accent: ['Raleway', ...defaultTheme.fontFamily.sans], // Accent font
            },
            // Add custom spacing, boxShadow, etc. based on CSS variables if needed
            // e.g., boxShadow: { base: 'var(--shadow-base)', lg: 'var(--shadow-lg)'}
        },
    },

    plugins: [forms, typography],
};
