<?php

namespace App\Http\Controllers\Api;

use App\Enums\SuggestionStatus;
use App\Enums\VoteType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportStoreRequest;
use App\Http\Requests\SuggestionStoreRequest;
use App\Http\Requests\VoteRequest;
use App\Models\Bookmark;
use App\Models\Report;
use App\Models\Subcategory;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuggestionController extends Controller
{
    public function index(Subcategory $subcategory, Request $request): JsonResponse
    {
        abort_unless($subcategory->is_active && $subcategory->category?->is_active, 404);

        $viewer = $this->currentViewer($request);
        $sort = $request->string('sort', 'top')->toString();
        $perPage = max(1, min(50, $request->integer('per_page', 20)));

        if ($sort === 'mine' && ! $viewer) {
            return response()->json([
                'message' => 'Login required to view your suggestions.',
            ], 401);
        }

        $query = $subcategory->suggestions()->with($this->suggestionRelationsFor($viewer));

        if ($sort === 'mine') {
            $query->where('user_id', $viewer->id);
        } else {
            $query->approved();
        }

        match ($sort) {
            'mine' => $query->latest(),
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

        return response()->json(self::paginatedSuggestionResponse($suggestions, $sort, $viewer));
    }

    public function show(Suggestion $suggestion, Request $request): JsonResponse
    {
        $viewer = $this->currentViewer($request);
        $canView = $suggestion->status === SuggestionStatus::Approved
            || ($viewer && $viewer->id === $suggestion->user_id);

        abort_unless($canView, 404);

        $suggestion->load($this->suggestionRelationsFor($viewer));

        return response()->json([
            'data' => $this->suggestionData($suggestion, $viewer),
        ]);
    }

    public function store(SuggestionStoreRequest $request): JsonResponse
    {
        $user = $this->currentViewer($request);

        if (! $user) {
            return response()->json([
                'message' => 'Login required to submit suggestions.',
            ], 401);
        }

        if ($user->is_banned) {
            return response()->json([
                'message' => 'Your account has been banned. You cannot submit suggestions.',
            ], 403);
        }

        if ($user->isGuestAccount()) {
            return response()->json([
                'message' => 'Guest accounts cannot submit suggestions.',
            ], 403);
        }

        $subcategory = Subcategory::query()
            ->whereKey($request->integer('subcategory_id'))
            ->where('is_active', true)
            ->firstOrFail();

        $suggestion = Suggestion::create([
            'subcategory_id' => $subcategory->id,
            'user_id' => $user->id,
            'title' => $request->string('title')->trim()->toString(),
            'description' => $request->filled('description') ? $request->string('description')->trim()->toString() : null,
            'show_identity' => $request->boolean('show_identity', true),
            'status' => SuggestionStatus::Approved,
        ]);

        $user->increment('post_count');

        $suggestion->load(['user:id,name,username,avatar_url', 'subcategory.category']);

        return response()->json([
            'message' => 'Suggestion created successfully.',
            'data' => $this->suggestionData($suggestion, $user),
        ], 201);
    }

    public function vote(Suggestion $suggestion, VoteRequest $request): JsonResponse
    {
        abort_unless($suggestion->status === SuggestionStatus::Approved, 404);

        $viewer = $this->currentViewer($request);

        [$column, $value] = $this->resolveIdentity($request, $viewer);

        if ($value === null) {
            return response()->json([
                'message' => 'device_id is required for guest voting.',
            ], 422);
        }

        $voteType = VoteType::from($request->string('type')->toString());

        [$action, $suggestion] = DB::transaction(function () use ($column, $suggestion, $value, $viewer, $voteType): array {
            $suggestion = Suggestion::query()
                ->lockForUpdate()
                ->findOrFail($suggestion->getKey());

            /** @var Vote|null $existingVote */
            $existingVote = $suggestion->votes()->where($column, $value)->first();
            $previousType = $existingVote?->type;
            $nextType = $previousType;
            $action = 'created';

            if ($existingVote) {
                if ($previousType === $voteType) {
                    $existingVote->delete();
                    $nextType = null;
                    $action = 'removed';
                } else {
                    $existingVote->update(['type' => $voteType]);
                    $nextType = $voteType;
                    $action = 'updated';
                }
            } else {
                $suggestion->votes()->create([
                    'user_id' => $column === 'user_id' ? $value : null,
                    'device_id' => $column === 'device_id' ? $value : null,
                    'type' => $voteType,
                ]);

                $nextType = $voteType;
            }

            $suggestion->applyVoteTypeTransition($previousType, $nextType);

            return [$action, $suggestion->fresh($this->suggestionRelationsFor($viewer))];
        });

        return response()->json([
            'message' => 'Vote processed successfully.',
            'action' => $action,
            'data' => $this->suggestionData($suggestion, $viewer),
        ]);
    }

    public function bookmark(Suggestion $suggestion, Request $request): JsonResponse
    {
        abort_unless($suggestion->status === SuggestionStatus::Approved, 404);

        Bookmark::firstOrCreate([
            'user_id' => $request->user()->id,
            'suggestion_id' => $suggestion->id,
        ]);

        return response()->json([
            'message' => 'Suggestion bookmarked successfully.',
            'data' => $this->suggestionData(
                $suggestion->fresh($this->suggestionRelationsFor($request->user())),
                $request->user(),
            ),
        ], 201);
    }

    public function removeBookmark(Suggestion $suggestion, Request $request): JsonResponse
    {
        $suggestion->bookmarks()
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json([
            'message' => 'Suggestion bookmark removed successfully.',
            'data' => $this->suggestionData(
                $suggestion->fresh($this->suggestionRelationsFor($request->user())),
                $request->user(),
            ),
        ]);
    }

    public function report(Suggestion $suggestion, ReportStoreRequest $request): JsonResponse
    {
        abort_unless($suggestion->status === SuggestionStatus::Approved, 404);

        $viewer = $this->currentViewer($request);

        Report::create([
            'suggestion_id' => $suggestion->id,
            'user_id' => $viewer?->id,
            'reason' => $request->string('reason')->trim()->toString(),
            'details' => $request->filled('details') ? $request->string('details')->trim()->toString() : null,
        ]);

        return response()->json([
            'message' => 'Report submitted successfully.',
        ], 201);
    }

    protected function resolveIdentity(Request $request, ?User $viewer = null): array
    {
        if ($viewer) {
            return ['user_id', $viewer->id];
        }

        $deviceId = $request->string('device_id')->toString();

        return ['device_id', blank($deviceId) ? null : $deviceId];
    }

    public static function paginatedSuggestionResponse(
        LengthAwarePaginator $paginator,
        string $sort,
        ?User $viewer = null,
    ): array
    {
        $controller = app(self::class);

        return [
            'data' => $paginator->getCollection()
                ->map(fn (Suggestion $suggestion) => $controller->suggestionData($suggestion, $viewer))
                ->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'sort' => $sort,
            ],
        ];
    }

    protected function suggestionData(Suggestion $suggestion, ?User $viewer = null): array
    {
        $viewerVote = null;
        $isBookmarked = false;

        if ($viewer) {
            $vote = $suggestion->relationLoaded('votes')
                ? $suggestion->votes->first()
                : $suggestion->votes()->where('user_id', $viewer->id)->first();

            $bookmark = $suggestion->relationLoaded('bookmarks')
                ? $suggestion->bookmarks->first()
                : $suggestion->bookmarks()->where('user_id', $viewer->id)->first();

            $viewerVote = $vote?->type?->value;
            $isBookmarked = $bookmark !== null;
        }

        return [
            'id' => $suggestion->id,
            'title' => $suggestion->title,
            'description' => $suggestion->description,
            'upvote_count' => $suggestion->upvote_count,
            'downvote_count' => $suggestion->downvote_count,
            'net_score' => $suggestion->net_score,
            'status' => $suggestion->status->value,
            'is_featured' => $suggestion->is_featured,
            'show_identity' => $suggestion->show_identity,
            'viewer_vote' => $viewerVote,
            'is_bookmarked' => $isBookmarked,
            'created_at' => $suggestion->created_at?->toISOString(),
            'user' => $this->suggestionUserData($suggestion),
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

    protected function suggestionUserData(Suggestion $suggestion): ?array
    {
        if (! $suggestion->user) {
            return null;
        }

        $displayName = $suggestion->show_identity
            ? $suggestion->user->username
            : $this->maskedAuthorLabel($suggestion->user);

        return [
            'id' => $suggestion->show_identity ? $suggestion->user->id : null,
            'username' => $displayName,
            'display_name' => $displayName,
            'avatar_url' => $suggestion->show_identity ? $suggestion->user->avatar_url : null,
            'is_anonymous' => ! $suggestion->show_identity,
        ];
    }

    protected function maskedAuthorLabel(User $user): string
    {
        $parts = collect(preg_split('/[\s._-]+/', trim((string) ($user->name ?: $user->username))))
            ->filter()
            ->values();

        $first = $parts->get(0);
        $second = $parts->get(1);

        if ($first && $second) {
            return Str::upper(mb_substr($first, 0, 1)) . '. ' . Str::upper(mb_substr($second, 0, 1)) . '.';
        }

        if ($first) {
            return Str::upper(mb_substr($first, 0, 1)) . '.';
        }

        return 'A.';
    }

    protected function suggestionRelationsFor(?User $viewer = null): array
    {
        $relations = ['user:id,name,username,avatar_url', 'subcategory.category'];

        if (! $viewer) {
            return $relations;
        }

        return [
            ...$relations,
            'votes' => fn ($query) => $query->where('user_id', $viewer->id),
            'bookmarks' => fn ($query) => $query->where('user_id', $viewer->id),
        ];
    }

    protected function currentViewer(Request $request): ?User
    {
        /** @var User|null $viewer */
        $viewer = $request->user('sanctum') ?? $request->user();

        return $viewer;
    }
}
