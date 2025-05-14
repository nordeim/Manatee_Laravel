// resources/js/parallax-init.js
export default function initAboutParallax() {
    const parallaxImgContainer = document.getElementById('aboutParallaxImgContainer');
    const parallaxImg = document.getElementById('aboutParallaxImg');

    if (!parallaxImg || !parallaxImgContainer || !window.matchMedia('(pointer: fine)').matches || !window.matchMedia('(min-width: 901px)').matches) {
        if (parallaxImg) { // Reset if conditions not met but element exists
            parallaxImg.style.filter = 'blur(0) saturate(1) contrast(1)';
            parallaxImg.style.transform = 'translateY(0) scale(1) rotateZ(0deg)';
        }
        return;
    }

    let ticking = false;

    const handleScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const rect = parallaxImgContainer.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                const visibility = (windowHeight - rect.top) / (windowHeight + rect.height);
                const clampedVisibility = Math.max(0, Math.min(1, visibility));

                if (rect.bottom > 0 && rect.top < windowHeight) {
                    parallaxImgContainer.classList.add('scrolled'); // From sample CSS
                    const translateY = (clampedVisibility - 0.5) * -25; // Reduced effect
                    const scale = 1 + clampedVisibility * 0.03;
                    const rotateZ = (clampedVisibility - 0.5) * 3;
                    const blur = Math.max(0, 1.5 - clampedVisibility * 2.5);
                    const saturate = 1 + clampedVisibility * 0.05;

                    parallaxImg.style.transform = `translateY(${translateY}px) scale(${scale}) rotateZ(${rotateZ}deg)`;
                    parallaxImg.style.filter = `blur(${blur}px) saturate(${saturate})`;
                } else {
                    // Optionally reset when out of view
                    // parallaxImg.style.transform = 'translateY(0) scale(1) rotateZ(0deg)';
                    // parallaxImg.style.filter = 'blur(2px) saturate(1)';
                }
                ticking = false;
            });
            ticking = true;
        }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll(); // Initial check
}

// Call it in app.js or specific view
// initAboutParallax(); // This will be called in app.js if imported there
