<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    /**
     * Display a listing of all tags
     */
    public function index(): View
    {
        $tags = Tag::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->orderBy('name')
            ->paginate(20);

        return view('tags.index', compact('tags'));
    }

    /**
     * Show posts for a specific tag
     */
    public function show(Tag $tag): View
    {
        $posts = $tag->posts()
            ->published()
            ->with(['user', 'tags'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(12);

        return view('tags.show', compact('tag', 'posts'));
    }

    /**
     * Get popular tags for autocomplete/suggestions
     */
    public function popular(): JsonResponse
    {
        $tags = Tag::withCount('posts')
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->limit(20)
            ->get(['name', 'slug', 'posts_count']);

        return response()->json($tags);
    }

    /**
     * Search tags by name
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $tags = Tag::where('name', 'LIKE', "%{$query}%")
            ->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit(10)
            ->get(['name', 'slug', 'posts_count']);

        return response()->json($tags);
    }

    /**
     * Create or find tags from comma-separated string
     */
    public static function findOrCreateTags(string $tagString): array
    {
        $tagNames = array_map('trim', explode(',', $tagString));
        $tagNames = array_filter($tagNames); // Remove empty values

        $tags = [];
        foreach ($tagNames as $tagName) {
            if (strlen($tagName) > 0 && strlen($tagName) <= 50) {
                $tag = Tag::firstOrCreate([
                    'name' => $tagName,
                    'slug' => \Illuminate\Support\Str::slug($tagName)
                ]);
                $tags[] = $tag;
            }
        }

        return $tags;
    }
}