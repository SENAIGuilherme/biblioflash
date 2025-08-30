<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\BookReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SystemSettingController;

// Public routes
Route::get('/', [HomeController::class, 'index']);
Route::get('/home', [HomeController::class, 'home'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');



Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');

// Books public routes
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// Categories public routes
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// Libraries public routes
Route::get('/libraries', [LibraryController::class, 'index'])->name('libraries.index');
Route::get('/libraries/{library}', [LibraryController::class, 'show'])->name('libraries.show');
Route::get('/libraries-map', [LibraryController::class, 'map'])->name('libraries.map');
Route::get('/api/libraries', [LibraryController::class, 'getLibraries'])->name('api.libraries');

// Authentication routes (manual definition)
Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);
Route::post('/cadastrar', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('cadastrar');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    
    // User profile
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [UserController::class, 'changePassword'])->name('profile.password');
    
    // Favorites
    Route::prefix('favorites')->name('favorites.')->group(function () {
        Route::get('/', [FavoriteController::class, 'index'])->name('index');
        Route::post('/{book}', [FavoriteController::class, 'store'])->name('store');
        Route::delete('/{book}', [FavoriteController::class, 'destroy'])->name('destroy');
        Route::post('/{book}/toggle', [FavoriteController::class, 'toggle'])->name('toggle');
        Route::delete('/', [FavoriteController::class, 'clear'])->name('clear');
        Route::get('/export', [FavoriteController::class, 'export'])->name('export');
        Route::post('/status', [FavoriteController::class, 'status'])->name('status');
    });
    
    // User loans and reservations
    Route::get('/my-loans', [LoanController::class, 'myLoans'])->name('loans.my');
    Route::get('/my-reservations', [ReservationController::class, 'myReservations'])->name('reservations.my');
    Route::get('/my-fines', [FineController::class, 'myFines'])->name('fines.my');
    Route::get('/my-reviews', [BookReviewController::class, 'myReviews'])->name('reviews.my');
    
    // Book reviews (user actions)
    Route::post('/books/{book}/reviews', [BookReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{bookReview}', [BookReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{bookReview}', [BookReviewController::class, 'destroy'])->name('reviews.destroy');
    
    // Reservations (user actions)
    Route::post('/books/{book}/reserve', [ReservationController::class, 'store'])->name('reservations.store');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'adminDashboard'])->name('dashboard');
    
    // Book registration
    Route::get('/books/register', [BookController::class, 'create'])->name('books.register');
    Route::post('/books/register', [BookController::class, 'store'])->name('books.store');
    
    // Books management
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/books/panel/rfid', [BookController::class, 'panel'])->name('books.panel');
    Route::resource('books', BookController::class)->except(['index', 'show', 'create', 'store']);
    Route::post('/books/{book}/toggle-favorite', [BookController::class, 'toggleFavorite'])->name('books.toggle-favorite');
    
    // Categories management
    Route::resource('categories', CategoryController::class)->except(['index', 'show']);
    Route::post('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    
    // Users management
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('/users/{user}/activity', [ActivityLogController::class, 'userLogs'])->name('users.activity');
    
    // Libraries management
    Route::resource('libraries', LibraryController::class)->except(['index', 'show']);
    Route::post('/libraries/{library}/toggle-status', [LibraryController::class, 'toggleStatus'])->name('libraries.toggle-status');
    
    // Loans management
    Route::resource('loans', LoanController::class);
    Route::post('/loans/{loan}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    Route::post('/loans/{loan}/renew', [LoanController::class, 'renew'])->name('loans.renew');
    Route::get('/loans/overdue', [LoanController::class, 'overdue'])->name('loans.overdue');
    
    // Reservations management
    Route::resource('reservations', ReservationController::class);
    Route::post('/reservations/{reservation}/attend', [ReservationController::class, 'attend'])->name('reservations.attend');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::get('/reservations/expired', [ReservationController::class, 'expired'])->name('reservations.expired');
    
    // Fines management
    Route::resource('fines', FineController::class)->only(['index', 'show']);
    Route::post('/fines/{fine}/pay', [FineController::class, 'pay'])->name('fines.pay');
    Route::post('/fines/{fine}/cancel', [FineController::class, 'cancel'])->name('fines.cancel');
    Route::get('/fines/financial-report', [FineController::class, 'financialReport'])->name('fines.financial-report');
    
    // Book reviews management
    Route::resource('reviews', BookReviewController::class)->only(['index', 'show']);
    Route::post('/reviews/{bookReview}/approve', [BookReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{bookReview}/reject', [BookReviewController::class, 'reject'])->name('reviews.reject');
    
    // Activity logs
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/{activityLog}', [ActivityLogController::class, 'show'])->name('show');
        Route::get('/statistics', [ActivityLogController::class, 'statistics'])->name('statistics');
        Route::get('/export', [ActivityLogController::class, 'export'])->name('export');
        Route::post('/cleanup', [ActivityLogController::class, 'cleanup'])->name('cleanup');
    });
    
    // System settings
    Route::resource('system-settings', SystemSettingController::class);
    Route::post('/system-settings/bulk-update', [SystemSettingController::class, 'bulkUpdate'])->name('system-settings.bulk-update');
    Route::get('/system-settings/export', [SystemSettingController::class, 'export'])->name('system-settings.export');
    Route::post('/system-settings/import', [SystemSettingController::class, 'import'])->name('system-settings.import');
});
