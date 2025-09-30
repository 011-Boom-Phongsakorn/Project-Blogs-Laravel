<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    public function __construct()
    {
        // Middleware handled in routes
    }

    /**
     * Toggle bookmark/unbookmark for a post
     */
    public function toggle(Post $post): JsonResponse
    {
        $user = auth()->user();

        $existingBookmark = Bookmark::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existingBookmark) {
            // Remove bookmark
            $existingBookmark->delete();
            $bookmarked = false;
        } else {
            // Add bookmark
            Bookmark::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
            $bookmarked = true;
        }

        return response()->json([
            'success' => true,
            'bookmarked' => $bookmarked,
        ]);
    }

    /**
     * Show user's bookmarked posts
     */
    public function index(): View
    {
        $user = auth()->user();

        $bookmarkedPosts = $user->bookmarks()
            ->with(['user', 'tags'])
            ->withCount(['likes', 'comments'])
            ->latest('bookmarks.created_at')
            ->paginate(12);

        return view('bookmarks.index', compact('bookmarkedPosts'));
    }
}
