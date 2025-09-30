<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class RssFeedController extends Controller
{
    /**
     * Generate RSS feed for published posts
     */
    public function index(): Response
    {
        $posts = Post::published()
            ->with(['user', 'tags'])
            ->latest()
            ->take(20)
            ->get();

        $rss = view('feed.rss', compact('posts'))->render();

        return response($rss, 200)
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }

    /**
     * Generate RSS feed for specific user's posts
     */
    public function user($userId): Response
    {
        $posts = Post::where('user_id', $userId)
            ->published()
            ->with(['user', 'tags'])
            ->latest()
            ->take(20)
            ->get();

        if ($posts->isEmpty()) {
            abort(404);
        }

        $user = $posts->first()->user;
        $rss = view('feed.rss', compact('posts', 'user'))->render();

        return response($rss, 200)
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }

    /**
     * Generate RSS feed for specific tag
     */
    public function tag($tagName): Response
    {
        $posts = Post::published()
            ->whereHas('tags', function ($query) use ($tagName) {
                $query->where('name', $tagName);
            })
            ->with(['user', 'tags'])
            ->latest()
            ->take(20)
            ->get();

        if ($posts->isEmpty()) {
            abort(404);
        }

        $tag = $tagName;
        $rss = view('feed.rss', compact('posts', 'tag'))->render();

        return response($rss, 200)
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
