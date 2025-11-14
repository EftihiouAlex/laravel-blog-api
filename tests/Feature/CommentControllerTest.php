<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_comment() {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $post = Post::factory()->create();

        $response = $this->postJson("/api/posts/{$post->id}/comments", [
            'comment_content' => 'Nice!'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', [
            'comment_content' => 'Nice!'
        ]);
    }

    /** @test */
    public function guest_cannot_comment() {
        $post = Post::factory()->create();

        $response = $this->postJson("/api/posts/{$post->id}/comments", [
            'comment_content' => 'Nice!'
        ]);

        $response->assertStatus(401);
    }

   /** @test */
    public function can_show_comments_by_user() {
        $user = User::factory()->create();

        Comment::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/users/{$user->id}/comments");

        $response->assertStatus(200)
                ->assertJsonCount(2);
    }
}
