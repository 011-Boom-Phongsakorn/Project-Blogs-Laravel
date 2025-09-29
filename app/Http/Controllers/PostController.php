<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    public function __construct()
    {
        // Middleware is now handled in routes
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $posts = Post::published()
            ->with(['user', 'tags'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(12);

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post = Post::create($validated);

        // Handle tags
        if ($request->has('tags')) {
            $tags = TagController::findOrCreateTags($request->tags);
            $post->tags()->sync(collect($tags)->pluck('id'));
        }

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): View
    {
        $post->load(['user', 'tags', 'comments.user']);
        $post->loadCount(['likes', 'comments']);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($post->featured_image) {
                \Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post->update($validated);

        // Handle tags
        if ($request->has('tags')) {
            $tags = TagController::findOrCreateTags($request->tags);
            $post->tags()->sync(collect($tags)->pluck('id'));
        } else {
            $post->tags()->detach();
        }

        return redirect()->route('posts.show', $post)
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        // Delete associated images
        if ($post->featured_image) {
            \Storage::disk('public')->delete($post->featured_image);
        }
        if ($post->cover_image) {
            \Storage::disk('public')->delete($post->cover_image);
        }

        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }

    /**
     * Search posts by title and content with advanced filters.
     */
    public function search(Request $request): View
    {
        $query = $request->get('q');
        $tag = $request->get('tag');
        $author = $request->get('author');
        $sortBy = $request->get('sort', 'latest');

        // Start building the query
        $postsQuery = Post::published()->with(['user', 'tags'])->withCount(['likes', 'comments']);

        // Apply search query if provided
        if ($query) {
            $postsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%");
            });
        }

        // Filter by tag if provided
        if ($tag) {
            $postsQuery->whereHas('tags', function ($q) use ($tag) {
                $q->where('name', $tag);
            });
        }

        // Filter by author if provided
        if ($author) {
            $postsQuery->whereHas('user', function ($q) use ($author) {
                $q->where('name', 'like', "%{$author}%")
                  ->orWhere('email', 'like', "%{$author}%");
            });
        }

        // Apply sorting
        switch ($sortBy) {
            case 'oldest':
                $postsQuery->oldest();
                break;
            case 'popular':
                $postsQuery->orderBy('like_count', 'desc');
                break;
            case 'most_commented':
                $postsQuery->withCount('comments')->orderBy('comments_count', 'desc');
                break;
            case 'latest':
            default:
                $postsQuery->latest();
                break;
        }

        $posts = $postsQuery->paginate(12)->appends($request->query());

        // Get available tags for filter dropdown
        $availableTags = Tag::has('posts')->withCount('posts')->orderBy('posts_count', 'desc')->limit(20)->get();

        // Search statistics
        $totalResults = $posts->total();

        return view('posts.search', compact('posts', 'query', 'tag', 'author', 'sortBy', 'availableTags', 'totalResults'));
    }

    /**
     * Get search suggestions for autocomplete.
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $posts = Post::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->with('user')
            ->take(5)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'author' => 'by ' . $post->user->name,
                    'url' => route('posts.show', $post),
                ];
            });

        return response()->json($posts);
    }
}
