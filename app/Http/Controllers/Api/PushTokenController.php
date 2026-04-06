<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PushNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushTokenController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'push_token' => ['required', 'string', 'max:500'],
            'platform'   => ['required', 'in:ios,android'],
            'enabled'    => ['boolean'],
        ]);

        $user = $request->user();

        if (! PushNotificationService::isValidExpoPushToken($validated['push_token'])) {
            return response()->json(['message' => 'Geçersiz push token formatı.'], 422);
        }

        $user->update([
            'push_token'                  => $validated['push_token'],
            'platform'                    => $validated['platform'],
            'push_notifications_enabled'  => $validated['enabled'] ?? true,
            'push_token_updated_at'       => now(),
        ]);

        return response()->json(['message' => 'Push token kaydedildi.']);
    }

    public function disable(Request $request): JsonResponse
    {
        $request->user()->update(['push_notifications_enabled' => false]);

        return response()->json(['message' => 'Bildirimler devre dışı bırakıldı.']);
    }
}
