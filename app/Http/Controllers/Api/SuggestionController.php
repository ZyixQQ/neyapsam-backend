<?php

namespace App\Http\Controllers\Api;

use App\Enums\SuggestionStatus;
use App\Enums\VoteType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportStoreRequest;
use App\Http\Requests\SuggestionStoreRequest;
use App\Http\Requests\VoteRequest;
use App\Models\Report;
use App\Models\Subcategory;
use App\Models\Suggestion;
use App\Models\Vote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SuggestionController extends Controller
{
    public function index(Subcategory $subcategory, Request $request): JsonResponse
    {
        abort_unless($subcategory->is_active && $subcategory->category?->is_active, 404);

        $sort = $request->string('sort', 'top')->toString();
        $perPage = max(1, min(50, $request->integer('per_page', 20)));

        $query = $subcategory->suggestions()
            ->approved()
            ->with(['user:id,username,avatar_url', 'subcategory.category']);

        match ($sort) {
            'new' => $query->latest(),
            'controversial' => $query
                ->orderByRaw('(upvote_count + downvote_count) DESC')
                ->orderByDesc('net_score')
                ->latest(),
            default => $query
                ->orderByDesc('net_score')
                ->orderByDesc('upvote_count')
                ->latest(),
        };

        $suggestions = $query->paginate($perPage)->withQueryString();

        return response()->json($this->paginatedResponse($suggestions, $sort));
    }

    public function show(Suggestion $suggestion): JsonResponse
    {
        abort_unless($suggestion->status === SuggestionStatus::Approved, 404);

        $suggestion->load(['user:id,username,avatar_url', 'subcategory.category']);

        return response()->json([
            'data' => $this->suggestionData($suggestion),
        ]);
    }

    public function store(SuggestionStoreRequest $request): JsonResponse
    {
        $user = $request->user();
        $deviceId = $request->string('device_id')->toString();

        if (! $user && blank($deviceId)) {
            return response()->json([
                'message' => 'device_id is required for guest submissions.',
            ], 422);
        }

        $subcategory = Subcategory::query()
            ->whereKey($request->integer('subcategory_id'))
            ->where('is_active', true)
            ->firstOrFail();

        $suggestion = Suggestion::create([
            'subcategory_id' => $subcategory->id,
            'user_id' => $user?->id,
            'title' => $request->string('title')->trim()->toString(),
            'description' => $request->filled('description') ? $request->string('description')->trim()->toString() : null,
            'status' => SuggestionStatus::Approved,
        ]);

        if ($user) {
            $user->increment('post_count');
        }

        $suggestion->load(['user:id,username,avatar_url', 'subcategory.category']);

        return response()->json([
            'message' => 'Suggestion created successfully.',
            'data' => $this->suggestionData($suggestion),
        ], 201);
    }

    public function vote(Suggestion $suggestion, VoteRequest $request): JsonResponse
    {
        abort_unless($suggestion->status === SuggestionStatus::Approved, 404);

        [$column, $value] = $this->resolveIdentity($request);

        if ($value === null) {
            return response()->json([
                'message' => 'device_id is required for guest voting.',
            ], 422);
        }

        $voteType = VoteType::from($request->string('type')->toString());

        /** @var Vote|null $existingVote */
        $existingVote = $suggestion->votes()->where($column, $value)->first();
        $action = 'created';

        if ($existingVote) {
            if ($existingVote->type === $voteType) {
                $existingVote->delete();
                $action = 'removed';
            } else {
                $existingVote->update(['type' => $voteType]);
                $action = 'updated';
            }
        } else {
            $suggestion->votes()->create([
                'user_id' => $column === 'user_id' ? $value : null,
                'device_id' => $column === 'device_id' ? $value : null,
                'type' => $voteType,
            ]);
        }

        $suggestion->refreshVoteStats();
        $suggestion->load(['user:id,username,avatar_url', 'subcategory.category']);

        return response()->json([
            'message' => 'Vote processed successfully.',
            'action' => $action,
            'data' => $this->suggestionData($suggestion->fresh(['user:id,username,avatar_url', 'subcategory.category'])),
        ]);
    }

    public function report(Suggestion $suggestion, ReportStoreRequest $request): JsonResponse
    {
        abort_unless($suggestion->status === SuggestionStatus::Approved, 404);

        Report::create([
            'suggestion_id' => $suggestion->id,
            'user_id' => $request->user()?->id,
            'reason' => $request->string('reason')->trim()->toString(),
        ]);

        return response()->json([
            'message' => 'Report submitted successfully.',
        ], 201);
    }

    protected function resolveIdentity(Request $request): array
    {
        if ($request->user()) {
            return ['user_id', $request->user()->id];
        }

        $deviceId = $request->string('device_id')->toString();

        return ['device_id', blank($deviceId) ? null : $deviceId];
    }

    protected function paginatedResponse(LengthAwarePaginator $paginator, string $sort): array
    {
        return [
            'data' => $paginator->getCollection()->map(fn (Suggestion $suggestion) => $this->suggestionData($suggestion))->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'sort' => $sort,
            ],
        ];
    }

    protected function suggestionData(Suggestion $suggestion): array
    {
        return [
            'id' => $suggestion->id,
            'title' => $suggestion->title,
            'description' => $suggestion->description,
            'upvote_count' => $suggestion->upvote_count,
            'downvote_count' => $suggestion->downvote_count,
            'net_score' => $suggestion->net_score,
            'status' => $suggestion->status->value,
            'is_featured' => $suggestion->is_featured,
            'created_at' => $suggestion->created_at?->toISOString(),
            'user' => $suggestion->user ? [
                'id' => $suggestion->user->id,
                'username' => $suggestion->user->username,
                'avatar_url' => $suggestion->user->avatar_url,
            ] : null,
            'subcategory' => [
                'id' => $suggestion->subcategory->id,
                'name' => $suggestion->subcategory->name,
                'slug' => $suggestion->subcategory->slug,
                'category' => [
                    'id' => $suggestion->subcategory->category->id,
                    'name' => $suggestion->subcategory->category->name,
                    'slug' => $suggestion->subcategory->category->slug,
                ],
            ],
        ];
    }
}
