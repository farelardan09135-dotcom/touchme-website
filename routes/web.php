<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\TowerController;


// Public routes
Route::get('/', function () {
    return view('auth.login');
});

// Guest routes - untuk yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

// Logout route - untuk yang sudah login
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// User routes - semua authenticated user bisa akses
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes - hanya untuk admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // News Management
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('/news', [NewsController::class, 'store'])->name('news.store');
    Route::delete('/news/{id}', [NewsController::class, 'destroy'])->name('news.destroy');
    
    // Import News
    Route::get('/news/import', [NewsController::class, 'importForm'])->name('news.import.form');
    Route::post('/news/import', [NewsController::class, 'import'])->name('news.import');
});

// Product Routes
// Menjadi:
Route::get('/products/tower', [TowerController::class, 'index'])->name('products.tower');
Route::get('/products/tower/{slug}', [TowerController::class, 'show'])->name('products.tower.detail');

Route::get('/products/power', [ProductController::class, 'power'])->name('products.power');
Route::get('/products/fiber-optic', [ProductController::class, 'fiber'])->name('products.fiber');
Route::get('/products/managed-service', [ProductController::class, 'managedService'])->name('products.managed-service');
Route::get('/products/administration', [ProductController::class, 'administration'])->name('products.administration');

// Division Route
Route::get('/divisions/{slug}', [DivisionController::class, 'show'])->name('divisions.show');

// Feed Routes (harus login)
Route::middleware(['auth'])->group(function () {
    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
    Route::post('/feed', [FeedController::class, 'store'])->name('feed.store');
    Route::post('/feed/{post}/like', [FeedController::class, 'toggleLike'])->name('feed.like');
    Route::post('/feed/{post}/comment', [FeedController::class, 'storeComment'])->name('feed.comment');
    Route::delete('/feed/{post}', [FeedController::class, 'destroy'])->name('feed.destroy');
});
require __DIR__.'/auth.php';