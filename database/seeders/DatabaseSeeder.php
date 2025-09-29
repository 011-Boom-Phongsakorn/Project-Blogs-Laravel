<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\Like;
use App\Models\Follow;
use App\Models\Bookmark;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 10 users
        $users = User::factory(10)->create();

        // Create a test user
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'bio' => 'This is a test user account for the blog system.',
        ]);

        // Create 10 tags
        $tags = Tag::factory(10)->create();

        // Create 30 posts
        $posts = Post::factory(30)->create([
            'user_id' => function () use ($users) {
                return $users->random()->id;
            }
        ]);

        // Attach random tags to posts
        $posts->each(function ($post) use ($tags) {
            $post->tags()->attach(
                $tags->random(rand(1, 4))->pluck('id')->toArray()
            );
        });

        // Create comments for posts
        $posts->each(function ($post) use ($users) {
            Comment::factory(rand(0, 8))->create([
                'post_id' => $post->id,
                'user_id' => $users->random()->id,
            ]);
        });

        // Create random likes
        $posts->each(function ($post) use ($users) {
            $likers = $users->random(rand(0, 7));
            foreach ($likers as $user) {
                Like::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]);
            }
        });

        // Create random follows
        $users->each(function ($user) use ($users) {
            $following = $users->where('id', '!=', $user->id)->random(rand(0, 5));
            foreach ($following as $followUser) {
                Follow::create([
                    'follower_id' => $user->id,
                    'following_id' => $followUser->id,
                ]);
            }
        });

        // Create random bookmarks
        $users->each(function ($user) use ($posts) {
            $bookmarkedPosts = $posts->random(rand(0, 8));
            foreach ($bookmarkedPosts as $post) {
                Bookmark::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]);
            }
        });

        // Update like counts for posts
        $posts->each(function ($post) {
            $post->update(['like_count' => $post->likes()->count()]);
        });
    }
}
