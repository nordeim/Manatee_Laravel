// resources/js/testimonial-observer.js
export default function initTestimonialObserver() {
    const testimonials = document.querySelectorAll('.testimonial-card');
    if (!testimonials.length) return;
    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0) scale(1)';
                obs.unobserve(entry.target);
            }
        });
    }, {
        rootMargin: '0px 0px -100px 0px',
        threshold: 0.1
    });
    testimonials.forEach(testimonial => {
        testimonial.style.opacity = '0';
        testimonial.style.transform = 'translateY(30px) scale(0.98)';
        testimonial.style.transition = 'opacity 0.7s ease-out, transform 0.7s ease-out';
        observer.observe(testimonial);
    });
}
