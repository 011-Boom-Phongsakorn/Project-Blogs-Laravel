<?php

namespace Tests\Feature;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_follow_another_user(): void
    {
        $follower = User::factory()->create();
        $following = User::factory()->create();

        $response = $this->actingAs($follower)->post(route('users.follow', $following));

        $response->assertStatus(200);
        $this->assertDatabaseHas('follows', [
            'follower_id' => $follower->id,
            'following_id' => $following->id
        ]);
    }

    public function test_authenticated_user_can_unfollow_user(): void
    {
        $follower = User::factory()->create();
        $following = User::factory()->create();

        // First follow
        Follow::create([
            'follower_id' => $follower->id,
            'following_id' => $following->id
        ]);

        // Then unfollow
        $response = $this->actingAs($follower)->post(route('users.follow', $following));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('follows', [
            'follower_id' => $follower->id,
            'following_id' => $following->id
        ]);
    }

    public function test_guest_cannot_follow_user(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('users.follow', $user));
        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_follow_themselves(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('users.follow', $user));

        // Should return error
        $response->assertStatus(400);
        $this->assertDatabaseMissing('follows', [
            'follower_id' => $user->id,
            'following_id' => $user->id
        ]);
    }

    public function test_user_cannot_follow_same_user_twice(): void
    {
        $follower = User::factory()->create();
        $following = User::factory()->create();

        // Follow once
        $this->actingAs($follower)->post(route('users.follow', $following));

        // Try to follow again - should toggle (unfollow)
        $this->actingAs($follower)->post(route('users.follow', $following));

        $this->assertDatabaseMissing('follows', [
            'follower_id' => $follower->id,
            'following_id' => $following->id
        ]);
    }

    public function test_can_view_user_followers(): void
    {
        $user = User::factory()->create();
        $follower1 = User::factory()->create();
        $follower2 = User::factory()->create();

        Follow::create(['follower_id' => $follower1->id, 'following_id' => $user->id]);
        Follow::create(['follower_id' => $follower2->id, 'following_id' => $user->id]);

        $response = $this->get(route('users.followers', $user));

        $response->assertStatus(200);
        $response->assertSee($follower1->name);
        $response->assertSee($follower2->name);
    }

    public function test_can_view_user_following(): void
    {
        $user = User::factory()->create();
        $following1 = User::factory()->create();
        $following2 = User::factory()->create();

        Follow::create(['follower_id' => $user->id, 'following_id' => $following1->id]);
        Follow::create(['follower_id' => $user->id, 'following_id' => $following2->id]);

        $response = $this->get(route('users.following', $user));

        $response->assertStatus(200);
        $response->assertSee($following1->name);
        $response->assertSee($following2->name);
    }
}