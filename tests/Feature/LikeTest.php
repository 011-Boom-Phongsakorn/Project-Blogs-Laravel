<?php

namespace Tests\Feature;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_like_post(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $response = $this->actingAs($user)->post(route('posts.like', $post));

        $response->assertStatus(200);
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);
    }

    public function test_authenticated_user_can_unlike_post(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        // First like
        Like::create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        // Then unlike
        $response = $this->actingAs($user)->post(route('posts.like', $post));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);
    }

    public function test_guest_cannot_like_post(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $response = $this->post(route('posts.like', $post));
        $response->assertRedirect(route('login'));
    }

    public function test_like_count_increments_when_liked(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'like_count' => 0
        ]);

        $this->actingAs($user)->post(route('posts.like', $post));

        $post->refresh();
        $this->assertEquals(1, $post->like_count);
    }

    public function test_like_count_decrements_when_unliked(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $author->id,
            'like_count' => 1
        ]);

        Like::create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        $this->actingAs($user)->post(route('posts.like', $post));

        $post->refresh();
        $this->assertEquals(0, $post->like_count);
    }

    public function test_user_cannot_like_same_post_twice(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        // Like once
        $this->actingAs($user)->post(route('posts.like', $post));

        // Try to like again - should toggle (unlike)
        $this->actingAs($user)->post(route('posts.like', $post));

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);
    }
}