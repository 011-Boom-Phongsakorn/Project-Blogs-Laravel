<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    public $timestamps = false; // เพราะ table มีเฉพาะ created_at

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    protected $dates = [
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
