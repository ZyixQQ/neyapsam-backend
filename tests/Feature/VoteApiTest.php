<?php

namespace Tests\Feature;

use App\Enums\SuggestionStatus;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Suggestion;
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
}
