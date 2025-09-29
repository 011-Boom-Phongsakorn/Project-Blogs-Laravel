<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    public function __construct()
    {
        // Middleware handled in routes
    }

    /**
     * Store a new comment
     */
    public function store(Request $request, $post): RedirectResponse
    {
        // Find post by ID since we're using ID in the route
        $post = Post::findOrFail($post);

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        Comment::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return redirect()->route('posts.show', $post)
            ->with('success', 'Comment added successfully!');
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $post = $comment->post;

        if (auth()->id() !== $comment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        return redirect()->route('posts.show', $post)
            ->with('success', 'Comment deleted successfully!');
    }
}
