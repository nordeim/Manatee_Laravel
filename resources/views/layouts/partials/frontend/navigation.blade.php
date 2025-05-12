<nav class="sticky top-0 z-50 bg-white/85 dark:bg-scent-bg-dark/90 backdrop-blur-md shadow-sm border-b border-gray-200 dark:border-gray-700"
     x-data="{ open: false }">
    <div class="container-custom">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="text-2xl font-serif font-bold text-scent-primary dark:text-scent-primary-dark flex items-center">
                    <i class="fa-solid fa-feather-pointed mr-2 text-scent-accent dark:text-scent-accent-dark"></i>
                    {{ config('app.name', 'The Scent') }}
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-8">
                <a href="{{ route('home') }}" class="nav-link @if(request()->routeIs('home')) active @endif">Home</a>
                <a href="#about" class="nav-link">About</a> {{-- Update with route('about') later --}}
                <a href="#products" class="nav-link">Shop</a> {{-- Update with route('products.index') later --}}
                <a href="#finder" class="nav-link">Quiz</a> {{-- Update with route('quiz.show') later --}}
                <a href="#testimonials" class="nav-link">Reviews</a>
                <a href="#contact" class="nav-link">Contact</a> {{-- Could be part of footer or dedicated page --}}

                @guest
                    <a href="{{ route('login') }}" class="nav-link">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="nav-link">Register</a>
                    @endif
                @else
                    <a href="{{ route('account.dashboard') }}" class="nav-link">My Account</a> {{-- Create this route later --}}
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <a href="{{ route('logout') }}" class="nav-link"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            Logout
                        </a>
                    </form>
                @endguest

                <!-- Cart Icon -->
                <a href="#" class="nav-link relative" aria-label="Shopping Cart"> {{-- Update with route('cart.index') later --}}
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span x-show="$store.cart.count > 0"
                          x-text="$store.cart.count"
                          class="absolute -top-2 -right-2 bg-scent-cta text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                    </span>
                </a>

                <!-- Dark Mode Toggle -->
                <button @click="toggleDarkMode()"
                        class="ml-4 p-2 rounded-full text-scent-accent dark:text-scent-accent-dark hover:bg-scent-overlay dark:hover:bg-scent-overlay-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-scent-primary dark:focus:ring-scent-primary-dark transition-colors"
                        aria-label="Toggle theme">
                    <i class="fa-solid" :class="isDarkMode ? 'fa-sun' : 'fa-moon'"></i>
                </button>
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex items-center sm:hidden">
                <!-- Dark Mode Toggle (Mobile) -->
                 <button @click="toggleDarkMode()"
                        class="mr-2 p-2 rounded-full text-scent-accent dark:text-scent-accent-dark hover:bg-scent-overlay dark:hover:bg-scent-overlay-dark focus:outline-none"
                        aria-label="Toggle theme">
                    <i class="fa-solid" :class="isDarkMode ? 'fa-sun' : 'fa-moon'"></i>
                </button>
                <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-scent-text dark:text-scent-text-dark hover:text-scent-primary dark:hover:text-scent-primary-dark hover:bg-scent-overlay dark:hover:bg-scent-overlay-dark focus:outline-none focus:ring-2 focus:ring-inset focus:ring-scent-primary dark:focus:ring-scent-primary-dark"
                        aria-controls="mobile-menu" :aria-expanded="open.toString()">
                    <span class="sr-only">Open main menu</span>
                    <i class="fa-solid" :class="{ 'fa-times': open, 'fa-bars': !open }"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu, show/hide based on menu state. -->
    <div class="sm:hidden" id="mobile-menu" x-show="open" x-collapse>
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="mobile-nav-link @if(request()->routeIs('home')) active @endif">Home</a>
            <a href="#about" class="mobile-nav-link">About</a>
            <a href="#products" class="mobile-nav-link">Shop</a>
            <a href="#finder" class="mobile-nav-link">Quiz</a>
            <a href="#testimonials" class="mobile-nav-link">Reviews</a>
            <a href="#contact" class="mobile-nav-link">Contact</a>
            @guest
                <a href="{{ route('login') }}" class="mobile-nav-link">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="mobile-nav-link">Register</a>
                @endif
            @else
                <a href="#" class="mobile-nav-link">My Account</a> {{-- Update route --}}
                 <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" class="mobile-nav-link"
                       onclick="event.preventDefault(); this.closest('form').submit();">
                        Logout
                    </a>
                </form>
            @endguest
             <a href="#" class="mobile-nav-link">Cart (<span x-text="$store.cart.count"></span>)</a> {{-- Update route --}}
        </div>
    </div>
</nav>

@push('styles')
<style>
    .nav-link {
        @apply text-sm font-medium uppercase tracking-wider text-scent-text dark:text-scent-text-dark hover:text-scent-primary dark:hover:text-scent-primary-dark transition-colors;
    }
    .nav-link.active {
        @apply text-scent-primary dark:text-scent-primary-dark border-b-2 border-scent-accent dark:border-scent-accent-dark;
    }
    .mobile-nav-link {
        @apply block px-3 py-2 rounded-md text-base font-medium text-scent-text dark:text-scent-text-dark hover:bg-scent-overlay dark:hover:bg-scent-overlay-dark hover:text-scent-primary dark:hover:text-scent-primary-dark;
    }
     .mobile-nav-link.active {
        @apply bg-scent-overlay dark:bg-scent-overlay-dark text-scent-primary dark:text-scent-primary-dark;
    }
</style>
@endpush
