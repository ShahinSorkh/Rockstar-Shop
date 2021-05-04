<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_whoami_api()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')
            ->get('/api/auth/whoami');

        $response->assertSuccessful()
            ->json($user);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create();
        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertSuccessful()
            ->assertJson([
                'api_token' => $user->api_token,
            ]);
    }

    public function test_user_cannot_login_with_wrong_password()
    {
        $user = User::factory()->create();
        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ])
            ->assertUnauthorized();
    }

    public function test_user_can_register()
    {
        $user_email = $this->faker->email;
        $user_name = $this->faker->name;
        $this->postJson('/api/auth/register', [
            'name' => $user_name,
            'email' => $user_email,
            'password' => 'longS3cure$ecret',
        ])
            ->assertSuccessful();
        $this->assertDatabaseHas('users', [
            'name' => $user_name,
            'email' => $user_email,
        ]);
    }

    public function test_user_cannot_register_twice()
    {
        $user = User::factory()->create();
        $this->postJson('/api/auth/register', [
            'name' => $this->faker->name,
            'email' => $user->email,
            'password' => 'longS3cure$ecret',
        ])
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_cannot_login_with_invalid_input()
    {
        $this->postJson('/api/auth/login', [
            'email' => 'notemail',
        ])
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_user_cannot_register_with_invalid_input()
    {
        $this->postJson('/api/auth/register', [
            'email' => 'notemail',
            'password' => 'pass',
        ])
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_unauthenticated_user_fails_on_revoke()
    {
        $this->postJson('/api/auth/revoke')
            ->assertUnauthorized();
    }

    public function test_user_can_revoke_her_token()
    {
        $user = User::factory()->create();
        $old_token = $user->api_token;
        $response = $this->actingAs($user, 'api')
            ->postJson('/api/auth/revoke')
            ->assertSuccessful()
            ->decodeResponseJson();
        $this->assertNotEquals($old_token, $response['api_token']);
    }
}
