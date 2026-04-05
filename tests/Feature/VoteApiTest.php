<?php

namespace Tests\Feature;

use App\Enums\SuggestionStatus;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_vote_endpoint_toggles_and_updates_score(): void
    {
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
            'title' => 'Superbad',
            'status' => SuggestionStatus::Approved,
        ]);

        $firstVote = $this->postJson("/api/v1/suggestions/{$suggestion->id}/vote", [
            'type' => 'up',
            'device_id' => 'device-xyz',
        ]);

        $firstVote
            ->assertOk()
            ->assertJsonPath('action', 'created')
            ->assertJsonPath('data.net_score', 1);

        $toggleVote = $this->postJson("/api/v1/suggestions/{$suggestion->id}/vote", [
            'type' => 'up',
            'device_id' => 'device-xyz',
        ]);

        $toggleVote
            ->assertOk()
            ->assertJsonPath('action', 'removed')
            ->assertJsonPath('data.net_score', 0);
    }

    public function test_vote_endpoint_preserves_existing_aggregate_counts_without_historical_vote_rows(): void
    {
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
            'title' => 'Groundhog Day',
            'status' => SuggestionStatus::Approved,
            'upvote_count' => 24,
            'downvote_count' => 26,
            'net_score' => -2,
        ]);

        $response = $this->postJson("/api/v1/suggestions/{$suggestion->id}/vote", [
            'type' => 'up',
            'device_id' => 'device-aggregate',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('action', 'created')
            ->assertJsonPath('data.upvote_count', 25)
            ->assertJsonPath('data.downvote_count', 26)
            ->assertJsonPath('data.net_score', -1);

        $this->assertDatabaseHas('suggestions', [
            'id' => $suggestion->id,
            'upvote_count' => 25,
            'downvote_count' => 26,
            'net_score' => -1,
        ]);
    }

    public function test_suggestions_include_viewer_vote_and_bookmark_state_for_authenticated_user(): void
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

        $suggestion = Suggestion::create([
            'subcategory_id' => $subcategory->id,
            'user_id' => $user->id,
            'title' => 'Superbad',
            'status' => SuggestionStatus::Approved,
        ]);

        Vote::create([
            'suggestion_id' => $suggestion->id,
            'user_id' => $user->id,
            'type' => 'up',
        ]);

        Bookmark::create([
            'user_id' => $user->id,
            'suggestion_id' => $suggestion->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/subcategories/{$subcategory->slug}/suggestions");

        $response
            ->assertOk()
            ->assertJsonPath('data.0.viewer_vote', 'up')
            ->assertJsonPath('data.0.is_bookmarked', true);
    }

    public function test_authenticated_user_can_bookmark_and_list_bookmarks(): void
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

        $suggestion = Suggestion::create([
            'subcategory_id' => $subcategory->id,
            'user_id' => $user->id,
            'title' => 'Rushmore',
            'status' => SuggestionStatus::Approved,
        ]);

        $bookmarkResponse = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/suggestions/{$suggestion->id}/bookmark");

        $bookmarkResponse
            ->assertCreated()
            ->assertJsonPath('data.is_bookmarked', true);

        $listResponse = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/me/bookmarks');

        $listResponse
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $suggestion->id);
    }

    public function test_authenticated_user_can_list_own_suggestions_by_status(): void
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

        Suggestion::create([
            'subcategory_id' => $subcategory->id,
            'user_id' => $user->id,
            'title' => 'Pending suggestion',
            'status' => SuggestionStatus::Pending,
        ]);

        Suggestion::create([
            'subcategory_id' => $subcategory->id,
            'user_id' => $user->id,
            'title' => 'Approved suggestion',
            'status' => SuggestionStatus::Approved,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/me/suggestions?status=pending');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Pending suggestion');
    }
}
