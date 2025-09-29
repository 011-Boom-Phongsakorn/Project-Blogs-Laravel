<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FollowController extends Controller
{
    public function __construct()
    {
        // Middleware handled in routes
    }

    /**
     * Toggle follow/unfollow for a user
     */
    public function toggle(Request $request, User $user): JsonResponse
    {
        $currentUser = auth()->user();

        if ($currentUser->id === $user->id) {
            return response()->json(['error' => 'Cannot follow yourself'], 400);
        }

        $existingFollow = Follow::where('follower_id', $currentUser->id)
            ->where('following_id', $user->id)
            ->first();

        if ($existingFollow) {
            // Unfollow
            $existingFollow->delete();
            $following = false;
        } else {
            // Follow
            Follow::create([
                'follower_id' => $currentUser->id,
                'following_id' => $user->id,
            ]);
            $following = true;
        }

        $followerCount = $user->followers()->count();

        return response()->json([
            'success' => true,
            'following' => $following,
            'followers_count' => $followerCount,
        ]);
    }

    /**
     * Show followers of a user
     */
    public function followers(User $user): View
    {
        $followers = $user->followers()
            ->withCount(['posts', 'followers'])
            ->paginate(20);

        return view('users.followers', compact('user', 'followers'));
    }

    /**
     * Show users that a user is following
     */
    public function following(User $user): View
    {
        $following = $user->following()
            ->withCount(['posts', 'followers'])
            ->paginate(20);

        return view('users.following', compact('user', 'following'));
    }
}
