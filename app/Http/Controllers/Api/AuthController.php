<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GuestAuthRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->string('username')->toString(),
            'username' => $request->string('username')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => $request->string('password')->toString(),
            'email_verified_at' => now(),
        ]);

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
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
                'post_count' => $user->post_count,
                'device_id' => $user->device_id,
            ],
        ];
    }

    protected function generateGuestUsername(): string
    {
        do {
            $username = 'guest_' . Str::lower(Str::random(8));
        } while (User::where('username', $username)->exists());

        return $username;
    }
}
