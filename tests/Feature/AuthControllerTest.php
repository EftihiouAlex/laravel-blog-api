<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register() {
        $response = $this->postJson('/api/register', [
            'name' => 'Alex',
            'email' => 'alex@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['user', 'token']);

        $this->assertDatabaseHas('users', ['email' => 'alex@example.com']);
    }

    /** @test */
    public function register_requires_valid_data() {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422);
    }

    /** @test */
    public function user_can_login() {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret123'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['user', 'token']);
    }

    /** @test */
    public function user_cannot_login_with_wrong_credentials() {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpass'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_logout() {
        $user = User::factory()->create();

        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logged out']);
    }
}
