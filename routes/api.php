<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Protected routes for verified users
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});


// Route Kategori
Route::middleware('auth:sanctum')->group(function () {
    // Category Routes
    Route::apiResource('categories', CategoryController::class);

    // Post Routes
    Route::get('posts/published', [PostController::class, 'published']);
    Route::get('posts/category/{categoryId}', [PostController::class, 'byCategory']);
    Route::apiResource('posts', PostController::class);

    // Media/File Upload Routes
    Route::post('posts/{post}/featured-image', [PostController::class, 'uploadFeaturedImage'])
        ->name('posts.featured-image.upload');
    Route::post('posts/{post}/gallery', [PostController::class, 'uploadGalleryImages'])
        ->name('posts.gallery.upload');
});

// Public routes untuk konsumsi publik
Route::get('public/categories', [CategoryController::class, 'index']);
Route::get('public/categories/{category}', [CategoryController::class, 'show']);
Route::get('public/posts/published', [PostController::class, 'published']);
Route::get('public/posts/category/{categoryId}', [PostController::class, 'byCategory']);
Route::get('public/posts/{post}', [PostController::class, 'show']);

// routes/api.php

// Email Verification Routes
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
    ->middleware(['throttle:6,1'])
    ->name('verification.send');

