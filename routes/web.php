<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController; // Will be created in 3.2
use App\Http\Controllers\Frontend\QuizController; // Will be created in 3.3
use App\Http\Controllers\Frontend\NewsletterController; // Will be created in 3.3

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-us', [HomeController::class, 'about'])->name('about'); // Example, if separate about page
Route::get('/contact-us', [HomeController::class, 'contact'])->name('contact'); // Example

// Product Routes (will be expanded in Step 3.2)
Route::get('/shop', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Quiz Route (will be expanded in Step 3.3)
Route::get('/scent-quiz', [QuizController::class, 'show'])->name('quiz.show');
Route::post('/scent-quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');

// Newsletter Route (will be expanded in Step 3.3)
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');


// --- Standard Auth Routes (from Breeze/Fortify or placeholder) ---
if (!app()->routesAreCached()) {
    if (file_exists(__DIR__.'/auth.php')) {
        require __DIR__.'/auth.php';
    } else {
        Route::get('/login', function () { return 'Login Page Placeholder - Run Breeze Install'; })->name('login');
        Route::get('/register', function () { return 'Register Page Placeholder - Run Breeze Install'; })->name('register');
        Route::post('/logout', function () {
            // Placeholder logout logic if not using Breeze
            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect('/');
        })->name('logout');
    }
}

// Placeholder for account dashboard (Phase 4)
Route::get('/account/dashboard', function() {
    return 'Account Dashboard Placeholder - Requires Auth';
})->middleware(['auth'])->name('account.dashboard'); // Ensure auth middleware is setup
