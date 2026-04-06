<?php

namespace App\Services;

use App\Models\AppSettings;

class AppSettingsService
{
    public function getSettings(): AppSettings
    {
        return AppSettings::get();
    }

    public function updateSettings(array $data): AppSettings
    {
        $settings = AppSettings::get();
        $settings->update($data);

        return $settings->fresh();
    }

    /**
     * Uygulamanın güncel durumunu döner.
     *
     * Akış:
     *   1. maintenance_mode aktifse → 'maintenance'
     *   2. version < minimum_version → 'force_update'
     *   3. version < latest_version  → 'optional_update'
     *   4. Diğer → 'ok'
     *
     * Her duruma duyuru objesi eklenir.
     */
    public function checkAppStatus(string $version, string $platform): array
    {
        $settings = AppSettings::get();

        $announcement = $settings->show_announcement ? [
            'show'    => true,
            'id'      => (int) ($settings->updated_at?->timestamp ?? $settings->created_at?->timestamp ?? 0),
            'title'   => $settings->announcement_title,
            'message' => $settings->announcement_message,
            'type'    => $settings->announcement_type,
        ] : null;

        $storeUrl = [
            'ios'     => $settings->update_ios_url,
            'android' => $settings->update_android_url,
        ];

        // 1. Bakım modu
        if ($settings->maintenance_mode) {
            return [
                'status'       => 'maintenance',
                'maintenance'  => [
                    'enabled' => true,
                    'message' => $settings->maintenance_message
                        ?? 'Uygulama şu anda bakımda, kısa süre içinde geri döneceğiz.',
                ],
                'announcement' => $announcement,
            ];
        }

        $minVersion    = $settings->minimum_version ?? '1.0.0';
        $latestVersion = $settings->latest_version  ?? '1.0.0';

        // 2. Zorunlu güncelleme
        if (self::compareVersions($version, $minVersion) < 0) {
            return [
                'status' => 'force_update',
                'update' => [
                    'required'       => true,
                    'latestVersion'  => $latestVersion,
                    'currentVersion' => $version,
                    'message'        => $settings->update_message
                        ?? 'Uygulamayı kullanmaya devam etmek için lütfen güncelleyin.',
                    'storeUrl'       => $storeUrl,
                ],
                'announcement' => $announcement,
            ];
        }

        // 3. İsteğe bağlı güncelleme
        if (self::compareVersions($version, $latestVersion) < 0) {
            return [
                'status' => 'optional_update',
                'update' => [
                    'required'       => false,
                    'latestVersion'  => $latestVersion,
                    'currentVersion' => $version,
                    'message'        => $settings->update_message
                        ?? 'Yeni bir güncelleme mevcut!',
                    'storeUrl'       => $storeUrl,
                ],
                'announcement' => $announcement,
            ];
        }

        // 4. Normal
        return [
            'status'       => 'ok',
            'announcement' => $announcement,
        ];
    }

    /**
     * Semantic versioning karşılaştırması.
     * Döner: -1 (v1 < v2), 0 (eşit), 1 (v1 > v2)
     */
    public static function compareVersions(string $v1, string $v2): int
    {
        $a = array_map('intval', explode('.', $v1));
        $b = array_map('intval', explode('.', $v2));

        // Eksik parçaları 0 ile doldur
        while (count($a) < 3) {
            $a[] = 0;
        }
        while (count($b) < 3) {
            $b[] = 0;
        }

        for ($i = 0; $i < 3; $i++) {
            if ($a[$i] < $b[$i]) {
                return -1;
            }
            if ($a[$i] > $b[$i]) {
                return 1;
            }
        }

        return 0;
    }

    public static function isValidVersion(string $version): bool
    {
        return (bool) preg_match('/^\d+\.\d+\.\d+$/', $version);
    }
}
