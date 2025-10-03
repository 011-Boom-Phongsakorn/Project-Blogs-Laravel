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
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('home');

// RSS Feeds
Route::get('/feed', [RssFeedController::class, 'index'])->name('feed.rss');
Route::get('/feed/user/{user}', [RssFeedController::class, 'user'])->name('feed.user');
Route::get('/feed/tag/{tag}', [RssFeedController::class, 'tag'])->name('feed.tag');

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

// Authenticated routes with rate limiting and email verification
Route::middleware(['auth', 'verified', 'throttle:60,1'])->group(function () {
    // My posts (must be before {slug} routes)
    Route::get('/my-posts', [PostController::class, 'myPosts'])->name('posts.mine');

    // Post management (stricter rate limit for write operations)
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store')->middleware('throttle:10,1');
    Route::get('/posts/{post:slug}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post:slug}', [PostController::class, 'update'])->name('posts.update')->middleware('throttle:10,1');
    Route::delete('/posts/{post:slug}', [PostController::class, 'destroy'])->name('posts.destroy')->middleware('throttle:10,1');

    // Social features (use ID for AJAX endpoints - explicitly bind by ID)
    Route::post('/posts/{post:id}/like', [LikeController::class, 'toggle'])->name('posts.like')->middleware('throttle:30,1');
    Route::post('/users/{user:id}/follow', [FollowController::class, 'toggle'])->name('users.follow')->middleware('throttle:30,1');
    Route::post('/posts/{post:id}/bookmark', [BookmarkController::class, 'toggle'])->name('posts.bookmark')->middleware('throttle:30,1');
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');

    // Comments
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store')->where('post', '[0-9]+')->middleware('throttle:20,1');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy')->middleware('throttle:20,1');

    // Image uploads (strict rate limit)
    Route::post('/upload/image', [ImageController::class, 'upload'])->name('upload.image')->middleware('throttle:10,1');
    Route::post('/upload/avatar', [ImageController::class, 'uploadAvatar'])->name('upload.avatar')->middleware('throttle:20,1');
    Route::delete('/upload/delete', [ImageController::class, 'delete'])->name('upload.delete')->middleware('throttle:10,1');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
