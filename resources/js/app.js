// resources/js/app.js
import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
window.Alpine = Alpine;
Alpine.plugin(collapse);

// Import custom JS modules/Alpine components
import darkModeHandler from './dark-mode';
import ambientAudioHandler from './ambient-audio';
import scentQuizHandler from './scent-quiz';
import initAboutParallax from './parallax-init';
import initTestimonialObserver from './testimonial-observer'; // New

document.addEventListener('alpine:init', () => {
    Alpine.data('darkModeHandler', darkModeHandler);
    Alpine.data('ambientAudioHandler', ambientAudioHandler);
    Alpine.data('scentQuizHandler', scentQuizHandler);
    // Newsletter form Alpine component is defined inline in the newsletter_form.blade.php partial
});

Alpine.start();

// Initialize non-Alpine specific JS modules on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('aboutParallaxImgContainer')) {
        initAboutParallax();
    }
    if (document.querySelector('.testimonial-card')) { // Check if testimonials exist
        initTestimonialObserver();
    }
    console.log('The Scent JS Initialized with Alpine and custom modules.');
});
