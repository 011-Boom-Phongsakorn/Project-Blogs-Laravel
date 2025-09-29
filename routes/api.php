<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Post;

Route::middleware('web')->group(function () {
    Route::get('/search', function (Request $request) {
        $query = $request->get('q');
        $limit = min($request->get('limit', 10), 20); // Max 20 results

        if (empty($query) || strlen($query) < 2) {
            return response()->json(['posts' => []]);
        }

        $posts = Post::published()
            ->with('user:id,name')
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%");
            })
            ->select(['id', 'slug', 'title', 'excerpt', 'user_id', 'created_at'])
            ->orderByRaw('CASE WHEN title LIKE ? THEN 1 ELSE 2 END', ["%{$query}%"])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'slug' => $post->slug,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 100),
                    'author_name' => $post->user->name,
                    'created_at' => $post->created_at->toISOString(),
                ];
            });

        return response()->json(['posts' => $posts]);
    });
});