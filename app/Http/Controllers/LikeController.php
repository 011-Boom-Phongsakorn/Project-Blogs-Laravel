<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
    public function __construct()
    {
        // Middleware handled in routes
    }

    /**
     * Toggle like/unlike for a post
     */
    public function toggle(Request $request, $post): JsonResponse
    {
        $user = auth()->user();

        // Find post by ID since we're using ID in the route
        $post = Post::findOrFail($post);

        $existingLike = Like::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $post->decrementLikeCount();
            $liked = false;
        } else {
            // Like
            Like::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);
            $post->incrementLikeCount();
            $liked = true;
        }

        $post->refresh();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'like_count' => $post->like_count,
        ]);
    }
}
