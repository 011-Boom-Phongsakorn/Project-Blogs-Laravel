<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

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
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }

    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
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
}
