<?php

namespace Tests\Feature;

use App\Models\Bookmark;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_bookmark_post(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $response = $this->actingAs($user)->post(route('posts.bookmark', $post));

        $response->assertStatus(200);
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);
    }

    public function test_authenticated_user_can_unbookmark_post(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        // First bookmark
        Bookmark::create([
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);

        // Then unbookmark
        $response = $this->actingAs($user)->post(route('posts.bookmark', $post));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);
    }

    public function test_guest_cannot_bookmark_post(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $response = $this->post(route('posts.bookmark', $post));
        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_bookmark_same_post_twice(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        // Bookmark once
        $this->actingAs($user)->post(route('posts.bookmark', $post));

        // Try to bookmark again - should toggle (unbookmark)
        $this->actingAs($user)->post(route('posts.bookmark', $post));

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $user->id,
            'post_id' => $post->id
        ]);
    }

    public function test_user_can_view_their_bookmarks(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $post1 = Post::factory()->create(['user_id' => $author->id, 'status' => 'published']);
        $post2 = Post::factory()->create(['user_id' => $author->id, 'status' => 'published']);

        Bookmark::create(['user_id' => $user->id, 'post_id' => $post1->id]);
        Bookmark::create(['user_id' => $user->id, 'post_id' => $post2->id]);

        $response = $this->actingAs($user)->get(route('bookmarks.index'));

        $response->assertStatus(200);
        $response->assertSee($post1->title);
        $response->assertSee($post2->title);
    }

    public function test_user_can_only_see_their_own_bookmarks(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $author = User::factory()->create();

        $post1 = Post::factory()->create(['user_id' => $author->id, 'status' => 'published']);
        $post2 = Post::factory()->create(['user_id' => $author->id, 'status' => 'published']);

        Bookmark::create(['user_id' => $user1->id, 'post_id' => $post1->id]);
        Bookmark::create(['user_id' => $user2->id, 'post_id' => $post2->id]);

        $response = $this->actingAs($user1)->get(route('bookmarks.index'));

        $response->assertStatus(200);
        $response->assertSee($post1->title);
        $response->assertDontSee($post2->title);
    }

    public function test_guest_cannot_view_bookmarks(): void
    {
        $response = $this->get(route('bookmarks.index'));
        $response->assertRedirect(route('login'));
    }
}