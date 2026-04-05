<?php

namespace Tests\Feature;

use App\Enums\SuggestionStatus;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\Vote;
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

    public function test_authenticated_user_can_fetch_me_endpoint(): void
    {
        $user = User::factory()->create([
            'username' => 'me-user',
            'email' => 'me@example.com',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/auth/me');

        $response
            ->assertOk()
            ->assertJsonPath('data.username', 'me-user')
            ->assertJsonPath('data.email', 'me@example.com');
    }

    public function test_register_can_upgrade_guest_contributions_with_device_id(): void
    {
        $guest = User::factory()->create([
            'username' => 'guest_upgrade',
            'email' => 'guest_upgrade@guest.neyapsam.local',
            'device_id' => 'device-upgrade',
        ]);

        $category = Category::create([
            'name' => 'Ne Izlesem',
            'slug' => 'ne-izlesem',
            'icon' => '🎬',
            'color' => '#F97316',
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => 'Komedi',
            'slug' => 'komedi',
            'icon' => '😂',
        ]);

        $suggestion = Suggestion::create([
            'subcategory_id' => $subcategory->id,
            'user_id' => $guest->id,
            'title' => 'Guest contribution',
            'status' => SuggestionStatus::Approved,
        ]);

        Vote::create([
            'suggestion_id' => $suggestion->id,
            'user_id' => $guest->id,
            'type' => 'up',
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'username' => 'tester',
            'email' => 'tester@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'device_id' => 'device-upgrade',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('user.username', 'tester')
            ->assertJsonPath('user.post_count', 1);

        $newUserId = $response->json('user.id');

        $this->assertDatabaseHas('suggestions', [
            'id' => $suggestion->id,
            'user_id' => $newUserId,
        ]);

        $this->assertDatabaseHas('votes', [
            'suggestion_id' => $suggestion->id,
            'user_id' => $newUserId,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $guest->id,
        ]);
    }
}
