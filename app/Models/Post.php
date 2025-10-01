<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class Post extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::created(function () {
            Cache::forget('popular_tags');
        });

        static::updated(function () {
            Cache::forget('popular_tags');
        });

        static::deleted(function ($post) {
            Cache::forget('popular_tags');

            // Delete all notifications related to this post
            \DB::table('notifications')
                ->where('data->post_slug', $post->slug)
                ->delete();
        });
    }

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'cover_image',
        'featured_image',
        'featured_image_alt',
        'status',
        'like_count',
    ];

    protected $casts = [
        'status' => 'string',
        'like_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function bookmarkedBy()
    {
        return $this->belongsToMany(User::class, 'bookmarks');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function incrementLikeCount()
    {
        $this->increment('like_count');
    }

    public function decrementLikeCount()
    {
        $this->decrement('like_count');
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value) . '-' . Str::random(8);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getFeaturedImageUrlAttribute()
    {
        if (!$this->featured_image) return null;

        // If it's already a full URL, return as is
        if (str_starts_with($this->featured_image, 'http')) {
            return $this->featured_image;
        }

        return asset('storage/' . $this->featured_image);
    }

    public function getCoverImageUrlAttribute()
    {
        if (!$this->cover_image) return null;

        // If it's already a full URL, return as is
        if (str_starts_with($this->cover_image, 'http')) {
            return $this->cover_image;
        }

        return asset('storage/' . $this->cover_image);
    }

    public function hasImage()
    {
        return !empty($this->featured_image) || !empty($this->cover_image);
    }

    public function getImageUrl()
    {
        return $this->featured_image_url ?? $this->cover_image_url;
    }

    public function getImageAlt()
    {
        return $this->featured_image_alt ?? $this->title;
    }

    public function isBookmarkedBy($userId)
    {
        if (!$userId) return false;
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }

    public function isLikedBy($userId)
    {
        if (!$userId) return false;
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Calculate reading time based on content word count
     * Average reading speed: 200 words per minute
     */
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200);

        if ($minutes < 1) {
            return '1 min read';
        }

        return $minutes . ' min read';
    }

    /**
     * Get related posts based on shared tags
     */
    public function relatedPosts($limit = 3)
    {
        $posts = collect();

        // Try to get posts with shared tags first
        if ($this->tags->isNotEmpty()) {
            $tagIds = $this->tags->pluck('id');

            $posts = Post::whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tag_id', $tagIds);
            })
            ->where('id', '!=', $this->id)
            ->published()
            ->with(['user', 'tags'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->take($limit)
            ->get();
        }

        // If not enough posts with shared tags, try same author
        if ($posts->count() < $limit) {
            $authorPosts = Post::where('user_id', $this->user_id)
                ->where('id', '!=', $this->id)
                ->published()
                ->with(['user', 'tags'])
                ->withCount(['likes', 'comments'])
                ->latest()
                ->take($limit - $posts->count())
                ->get();

            $posts = $posts->merge($authorPosts);
        }

        // If still not enough, get latest posts from anyone
        if ($posts->count() < $limit) {
            $latestPosts = Post::where('id', '!=', $this->id)
                ->published()
                ->with(['user', 'tags'])
                ->withCount(['likes', 'comments'])
                ->latest()
                ->take($limit - $posts->count())
                ->get();

            $posts = $posts->merge($latestPosts);
        }

        return $posts->unique('id')->take($limit);
    }
}
