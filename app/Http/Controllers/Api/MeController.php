<?php

namespace App\Http\Controllers\Api;

use App\Enums\SuggestionStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MeController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'data' => AuthController::userData($request->user()),
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(array_map(
                static fn (SuggestionStatus $status) => $status->value,
                SuggestionStatus::cases(),
            ))],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $user = $request->user();
        $perPage = (int) ($validated['per_page'] ?? 15);

        $query = $user->suggestions()
            ->with([
                'user:id,username,avatar_url',
                'subcategory.category',
                'votes' => fn ($voteQuery) => $voteQuery->where('user_id', $user->id),
                'bookmarks' => fn ($bookmarkQuery) => $bookmarkQuery->where('user_id', $user->id),
            ])
            ->latest();

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $suggestions = $query->paginate($perPage)->withQueryString();

        return response()->json(SuggestionController::paginatedSuggestionResponse(
            $suggestions,
            'mine',
            $user,
        ));
    }

    public function bookmarks(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = max(1, min(50, $request->integer('per_page', 15)));

        $suggestions = $user->bookmarkedSuggestions()
            ->approved()
            ->with([
                'user:id,username,avatar_url',
                'subcategory.category',
                'votes' => fn ($voteQuery) => $voteQuery->where('user_id', $user->id),
                'bookmarks' => fn ($bookmarkQuery) => $bookmarkQuery->where('user_id', $user->id),
            ])
            ->latest('bookmarks.created_at')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json(SuggestionController::paginatedSuggestionResponse(
            $suggestions,
            'bookmarked',
            $user,
        ));
    }
}
