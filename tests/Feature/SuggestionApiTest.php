<?php

namespace Tests\Feature;

use App\Enums\SuggestionStatus;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuggestionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_account_cannot_submit_suggestion(): void
    {
        $guest = User::factory()->create([
            'email' => 'device-user@guest.neyapsam.local',
            'device_id' => 'device-guest',
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

        $response = $this->actingAs($guest, 'sanctum')
            ->postJson('/api/v1/suggestions', [
                'subcategory_id' => $subcategory->id,
                'title' => 'Guest should fail',
            ]);

        $response
            ->assertForbidden()
            ->assertJsonPath('message', 'Guest accounts cannot submit suggestions.');
    }

    public function test_authenticated_user_can_submit_anonymous_suggestion_and_view_it_in_mine_filter(): void
    {
        $user = User::factory()->create([
            'name' => 'Ali Veli',
            'username' => 'ali_veli',
            'email' => 'ali@example.com',
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

        $createResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/suggestions', [
                'subcategory_id' => $subcategory->id,
                'title' => 'Anonim onerim',
                'description' => 'Kisa not',
                'show_identity' => false,
            ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('data.show_identity', false)
            ->assertJsonPath('data.user.id', null)
            ->assertJsonPath('data.user.is_anonymous', true)
            ->assertJsonPath('data.user.display_name', 'A. V.');

        $mineResponse = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/subcategories/{$subcategory->slug}/suggestions?sort=mine");

        $mineResponse
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Anonim onerim')
            ->assertJsonPath('data.0.show_identity', false)
            ->assertJsonPath('data.0.user.display_name', 'A. V.');
    }

    public function test_report_endpoint_accepts_reason_and_details(): void
    {
        $user = User::factory()->create();

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

        $suggestion = $subcategory->suggestions()->create([
            'user_id' => $user->id,
            'title' => 'Problemli icerik',
            'status' => SuggestionStatus::Approved,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/suggestions/{$suggestion->id}/report", [
                'reason' => 'Spam',
                'details' => 'Ayni oneriyi tekrar tekrar paylasiyor.',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Report submitted successfully.');

        $this->assertDatabaseHas('reports', [
            'suggestion_id' => $suggestion->id,
            'user_id' => $user->id,
            'reason' => 'Spam',
            'details' => 'Ayni oneriyi tekrar tekrar paylasiyor.',
        ]);
    }
}
