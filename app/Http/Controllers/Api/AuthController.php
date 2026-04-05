<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GuestAuthRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->string('username')->toString(),
                'username' => $request->string('username')->toString(),
                'email' => $request->string('email')->toString(),
                'password' => $request->string('password')->toString(),
                'email_verified_at' => now(),
            ]);

            $this->upgradeGuestData(
                $request->string('device_id')->toString(),
                $user,
            );

            return $user->fresh();
        });

        return response()->json($this->payloadFor($user), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password) || $user->is_banned) {
            return response()->json([
                'message' => 'The provided credentials are invalid.',
            ], 422);
        }

        $user = DB::transaction(function () use ($request, $user) {
            $this->upgradeGuestData(
                $request->string('device_id')->toString(),
                $user,
            );

            return $user->fresh();
        });

        return response()->json($this->payloadFor($user));
    }

    public function guest(GuestAuthRequest $request): JsonResponse
    {
        $deviceId = $request->string('device_id')->toString();

        $user = User::where('device_id', $deviceId)->first();

        if (! $user) {
            $username = $this->generateGuestUsername();

            $user = User::create([
                'name' => $username,
                'username' => $username,
                'email' => "{$username}@guest.neyapsam.local",
                'password' => Str::password(24),
                'device_id' => $deviceId,
                'email_verified_at' => now(),
            ]);
        }

        return response()->json($this->payloadFor($user));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    protected function payloadFor(User $user): array
    {
        $token = $user->createToken('mobile')->plainTextToken;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => self::userData($user),
        ];
    }

    public static function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'avatar_url' => $user->avatar_url,
            'post_count' => $user->post_count,
            'device_id' => $user->device_id,
        ];
    }

    protected function upgradeGuestData(string $deviceId, User $user): void
    {
        if (blank($deviceId)) {
            return;
        }

        $guestUser = User::query()
            ->where('device_id', $deviceId)
            ->whereKeyNot($user->id)
            ->first();

        if (! $guestUser) {
            return;
        }

        $guestUser->suggestions()->update(['user_id' => $user->id]);
        $guestUser->reports()->update(['user_id' => $user->id]);

        $guestVotes = $guestUser->votes()->get();

        foreach ($guestVotes as $guestVote) {
            $existingVote = Vote::query()
                ->where('suggestion_id', $guestVote->suggestion_id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingVote) {
                $guestVoteType = $guestVote->type;
                $guestVote->delete();
                $existingVote->suggestion?->applyVoteTypeTransition($guestVoteType, null);
                continue;
            }

            $guestVote->update([
                'user_id' => $user->id,
                'device_id' => null,
            ]);
        }

        $guestBookmarks = $guestUser->bookmarks()->get();

        foreach ($guestBookmarks as $guestBookmark) {
            $duplicateBookmark = $user->bookmarks()
                ->where('suggestion_id', $guestBookmark->suggestion_id)
                ->exists();

            if ($duplicateBookmark) {
                $guestBookmark->delete();
                continue;
            }

            $guestBookmark->update(['user_id' => $user->id]);
        }

        $user->forceFill([
            'post_count' => $user->suggestions()->count(),
        ])->save();

        $guestUser->tokens()->delete();
        $guestUser->delete();
    }

    protected function generateGuestUsername(): string
    {
        do {
            $username = 'guest_' . Str::lower(Str::random(8));
        } while (User::where('username', $username)->exists());

        return $username;
    }
}
