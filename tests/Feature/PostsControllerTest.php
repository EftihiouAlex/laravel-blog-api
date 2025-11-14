<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_post() {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title' => 'My Post',
            'post_content' => 'Content here',
            'tags' => [],
            'categories' => []
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', ['title' => 'My Post']);
    }

    /** @test */
    public function creating_post_always_adds_new_tag() {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title' => 'Test Post',
            'post_content' => 'Text',
        ]);

        $postId = $response->json('post.id');

        $this->assertDatabaseHas('tags', ['slug' => 'new']);
        $this->assertDatabaseHas('post_tag', [
            'post_id' => $postId,
            'tag_id' => Tag::where('slug', 'new')->first()->id,
        ]);
    }

    /** @test */
    public function can_show_post_by_slug() {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->getJson("/api/posts/{$post->id}/{$post->slug}");

        $response->assertStatus(200)
                ->assertJson([
                    'slug' => $post->slug
                ]);
    }


    /** @test */
    public function slug_mismatch_returns_404() {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $post = Post::factory()->create();

        $response = $this->getJson("/api/posts/{$post->id}/wrong-slug");

        $response->assertStatus(404);
    }

    /** @test */
    public function update_post_marks_edited_and_removes_new_tag() {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $tagNew = Tag::factory()->create(['slug' => 'new', 'name' => 'new']);

        $post = Post::factory()->create(['user_id' => $user->id]);
        $post->tags()->attach($tagNew);

        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Changed Title'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['wasEdited' => true]);

        $this->assertDatabaseMissing('post_tag', [
            'post_id' => $post->id,
            'tag_id' => $tagNew->id,
        ]);
    }

    /** @test */
    public function update_without_changes_returns_not_edited() {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $post = Post::factory()->create([
            'title' => 'Same Title',
            'post_content' => 'Same Content',
            'user_id' => $user->id
        ]);

        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Same Title',
            'post_content' => 'Same Content',
        ]);

        $response->assertJson(['wasEdited' => false]);
    }

    /** @test */
    public function user_can_delete_post() {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $post = Post::factory()->create();

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
