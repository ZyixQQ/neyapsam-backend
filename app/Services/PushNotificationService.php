<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';
    private const CHUNK_SIZE    = 100; // Expo max per request

    /**
     * Belirli bir kullanıcıya bildirim gönderir.
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = []): bool
    {
        $user = User::find($userId);

        if (! $user || ! $user->push_notifications_enabled || ! $user->push_token) {
            return false;
        }

        $results = $this->sendBulkNotifications([[
            'to'    => $user->push_token,
            'title' => $title,
            'body'  => $body,
            'data'  => $data,
            'sound' => 'default',
        ]]);

        return ! empty($results) && ($results[0]['status'] ?? '') === 'ok';
    }

    /**
     * Birden fazla kullanıcıya bildirim gönderir.
     * Döner: ['success' => [...], 'failed' => [...]]
     */
    public function sendToMultipleUsers(array $userIds, string $title, string $body, array $data = []): array
    {
        $users = User::whereIn('id', $userIds)
            ->where('push_notifications_enabled', true)
            ->whereNotNull('push_token')
            ->get();

        if ($users->isEmpty()) {
            return ['success' => [], 'failed' => []];
        }

        $messages = $users->map(fn (User $user) => [
            'to'    => $user->push_token,
            'title' => $title,
            'body'  => $body,
            'data'  => $data,
            'sound' => 'default',
        ])->values()->all();

        $ticketResults = $this->sendBulkNotifications($messages);

        $success = [];
        $failed  = [];

        foreach ($users as $index => $user) {
            $ticket = $ticketResults[$index] ?? [];
            if (($ticket['status'] ?? '') === 'ok') {
                $success[] = $user->id;
            } else {
                $failed[] = $user->id;
            }
        }

        return compact('success', 'failed');
    }

    /**
     * Expo Push API'ye toplu mesaj gönderir.
     * Döner: ticket array
     */
    public function sendBulkNotifications(array $messages): array
    {
        if (empty($messages)) {
            return [];
        }

        $chunks  = array_chunk($messages, self::CHUNK_SIZE);
        $tickets = [];

        foreach ($chunks as $chunk) {
            try {
                $response = Http::withHeaders([
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                    'Accept-Encoding' => 'gzip, deflate',
                ])->post(self::EXPO_PUSH_URL, $chunk);

                if ($response->successful()) {
                    $data    = $response->json('data', []);
                    $tickets = array_merge($tickets, $data);
                } else {
                    Log::warning('Expo push notification failed', [
                        'status'   => $response->status(),
                        'response' => $response->body(),
                    ]);
                    // Başarısız ticket'ları ekle
                    foreach ($chunk as $msg) {
                        $tickets[] = ['status' => 'error', 'message' => 'HTTP error'];
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Expo push notification exception', ['error' => $e->getMessage()]);
                foreach ($chunk as $msg) {
                    $tickets[] = ['status' => 'error', 'message' => $e->getMessage()];
                }
            }
        }

        return $tickets;
    }

    /**
     * Expo push token formatını doğrular.
     */
    public static function isValidExpoPushToken(string $token): bool
    {
        return (bool) preg_match('/^Expo(?:nent)?PushToken\[.+\]$/', $token)
            || strlen($token) > 10; // APNS / FCM raw token'ları da kabul et
    }
}
