<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'username' => 'tester',
            'email' => 'tester@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('user.username', 'tester')
            ->assertJsonStructure(['token', 'token_type', 'user']);
    }

    public function test_guest_auth_creates_a_device_bound_user(): void
    {
        $response = $this->postJson('/api/v1/auth/guest', [
            'device_id' => 'device-123',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['device_id']]);

        $this->assertDatabaseHas('users', [
            'device_id' => 'device-123',
        ]);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('user.email', 'login@example.com');
    }
}
