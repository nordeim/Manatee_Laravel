import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
// Import other Alpine plugins if needed, e.g., focus, collapse
// import focus from '@alpinejs/focus'
// Alpine.plugin(focus)

// Import custom JS modules
import darkMode from './dark-mode';
// import ambientAudio from './ambient-audio'; // Example
// import parallaxInit from './parallax-init'; // Example
// import scentQuiz from './scent-quiz'; // Example

document.addEventListener('alpine:init', () => {
    Alpine.data('darkModeHandler', darkMode);
    // Alpine.data('ambientAudioHandler', ambientAudio);
    // Alpine.data('scentQuizHandler', scentQuiz);

    // Initialize other global Alpine components or data here if needed
    Alpine.store('cart', {
        count: 0,
        // other cart related reactive data
    });
});

Alpine.start();

// Initialize other non-Alpine JS modules
// parallaxInit(); // If it's a non-Alpine specific init

console.log('The Scent JS Initialized with Alpine.');
