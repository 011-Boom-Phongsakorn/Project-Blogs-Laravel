<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_post_list(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
    }

    public function test_guest_can_view_published_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'status' => 'published'
        ]);

        $response = $this->get(route('posts.show', $post));
        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    public function test_guest_cannot_access_create_post_form(): void
    {
        $response = $this->get(route('posts.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => 'Test Post Title',
            'content' => 'This is test content for the post.',
            'status' => 'published'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post Title',
            'user_id' => $user->id,
            'status' => 'published'
        ]);
    }

    public function test_user_can_create_draft_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => 'Draft Post',
            'content' => 'This is a draft post.',
            'status' => 'draft'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'title' => 'Draft Post',
            'status' => 'draft'
        ]);
    }

    public function test_user_can_edit_their_own_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('posts.edit', $post));
        $response->assertStatus(200);
    }

    public function test_user_cannot_edit_other_users_post(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->get(route('posts.edit', $post));
        $response->assertStatus(403);
    }

    public function test_user_can_update_their_own_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status' => 'published'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title'
        ]);
    }

    public function test_user_can_delete_their_own_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('posts.destroy', $post));
        $response->assertRedirect();
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_user_cannot_delete_other_users_post(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->delete(route('posts.destroy', $post));
        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_only_published_posts_shown_on_home(): void
    {
        $user = User::factory()->create();
        $publishedPost = Post::factory()->create([
            'user_id' => $user->id,
            'status' => 'published'
        ]);
        $draftPost = Post::factory()->create([
            'user_id' => $user->id,
            'status' => 'draft'
        ]);

        $response = $this->get(route('home'));
        $response->assertSee($publishedPost->title);
        $response->assertDontSee($draftPost->title);
    }
}