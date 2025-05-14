<header class="hero relative min-h-screen w-full flex flex-col items-center justify-center overflow-hidden isolate bg-[#111518]" id="hero">
    <video class="hero-bg-video absolute inset-0 w-full h-full object-cover z-0 opacity-90"
           style="filter: blur(1px) brightness(0.85) contrast(1.1);"
           autoplay muted loop playsinline
           poster="https://raw.githubusercontent.com/nordeim/The-Scent/refs/heads/main/images/scent5.jpg">
        <source src="https://raw.githubusercontent.com/nordeim/The-Scent/refs/heads/main/videos/aroma.mp4" type="video/mp4">
        <img class="hidden" src="https://raw.githubusercontent.com/nordeim/The-Scent/refs/heads/main/images/scent5.jpg" alt="Calm Aroma Visual">
    </video>
    <div class="absolute inset-0 bg-gradient-to-b from-black/10 via-black/30 to-black/60 z-[1]"></div>

    {{-- Mist Trails SVG (from sample) --}}
    <div class="mist-trails absolute inset-0 z-[2] pointer-events-none mix-blend-screen opacity-80" aria-hidden="true">
       <svg width="100%" height="100%" viewBox="0 0 1400 700" preserveAspectRatio="xMidYMid slice" fill="none" xmlns="http://www.w3.org/2000/svg">
         <path class="mist-path stroke-[var(--mist-light)] dark:stroke-[var(--mist-dark)] fill-none stroke-[18px] opacity-80 stroke-linecap-round stroke-linejoin-round blur-[1.5px]" style="stroke-dasharray: 4400; stroke-dashoffset: 4400; animation: mistTrail 18s linear infinite alternate;" d="M-100,550 Q250,400 550,580 T900,420 Q1150,250 1500,400" />
         <path class="mist-path2 stroke-[var(--mist-light)] dark:stroke-[var(--mist-dark)] fill-none stroke-[12px] opacity-65 stroke-linecap-round stroke-linejoin-round blur-[1.5px]" style="stroke-dasharray: 4000; stroke-dashoffset: 4000; animation: mistTrail2 25s linear infinite alternate;" d="M-100,680 Q400,620 800,650 T1500,600" />
       </svg>
    </div>

    <div class="hero-content relative z-10 max-w-3xl text-center px-4 sm:px-6 lg:px-8 mt-[5vh]">
        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold font-serif text-white mb-6 tracking-wider" style="text-shadow: 0 4px 20px rgba(0,0,0,0.4);">
            Crafted Aromas, Pure Well-being
        </h1>
        <p class="text-lg sm:text-xl lg:text-2xl text-gray-200 mb-10 max-w-xl mx-auto leading-relaxed" style="text-shadow: 0 2px 10px rgba(0,0,0,0.3);">
            Experience the essence of nature with our handcrafted aromatherapy blends, sourced globally and formulated with passion.
        </p>
        <a href="{{ route('products.index') }}" class="cta-main btn btn-primary text-lg sm:text-xl py-4 px-10 sm:px-12 rounded-full font-accent font-semibold shadow-lg hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105 group">
            <i class="fa-solid fa-bag-shopping mr-3 group-hover:animate-pulse"></i> Explore Collections
        </a>
    </div>
</header>
@push('styles')
<style>
    /* Specific styles for mist animation if not in app.css already */
    @keyframes mistTrail { 0% { stroke-dashoffset: 4400; opacity: .5; } 50% { opacity: .9; } 100% { stroke-dashoffset: 0; opacity: .5; } }
    @keyframes mistTrail2 { 0% { stroke-dashoffset: 0; opacity: 0.3; } 50% { opacity: .7; } 100% { stroke-dashoffset: 4000; opacity: .3; } }
</style>
@endpush
