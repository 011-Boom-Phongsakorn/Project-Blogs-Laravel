<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');

// Public post routes
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/search', [PostController::class, 'search'])->name('posts.search');
Route::get('/api/search/suggestions', [PostController::class, 'suggestions'])->name('posts.suggestions');

// Posts create route (must be before {slug} route)
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create')->middleware('auth');

Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// User profiles
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/{user}/followers', [FollowController::class, 'followers'])->name('users.followers');
Route::get('/users/{user}/following', [FollowController::class, 'following'])->name('users.following');

// Tags
Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::get('/tags/{tag:name}', [TagController::class, 'show'])->name('tags.show');
Route::get('/api/tags/popular', [TagController::class, 'popular'])->name('tags.popular');
Route::get('/api/tags/search', [TagController::class, 'search'])->name('tags.search');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Post management
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post:slug}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post:slug}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post:slug}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Social features (use ID for AJAX endpoints - explicitly bind by ID)
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like')->where('post', '[0-9]+');
    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow')->where('user', '[0-9]+');
    Route::post('/posts/{post}/bookmark', [BookmarkController::class, 'toggle'])->name('posts.bookmark')->where('post', '[0-9]+');
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');

    // Comments
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store')->where('post', '[0-9]+');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Image uploads
    Route::post('/upload/image', [ImageController::class, 'upload'])->name('upload.image');
    Route::post('/upload/avatar', [ImageController::class, 'uploadAvatar'])->name('upload.avatar');
    Route::delete('/upload/delete', [ImageController::class, 'delete'])->name('upload.delete');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
