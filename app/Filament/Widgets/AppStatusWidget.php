<?php

namespace App\Filament\Widgets;

use App\Models\AppSettings;
use Filament\Widgets\Widget;

class AppStatusWidget extends Widget
{
    protected static string $view        = 'filament.widgets.app-status-widget';
    protected static bool   $isLazy      = false;
    protected int|string|array $columnSpan = 'full';
    protected static ?int   $sort        = -2; // Dashboard'da en üstte

    public function getViewData(): array
    {
        $settings = AppSettings::get();

        return [
            'maintenanceMode'  => $settings->maintenance_mode,
            'forceUpdate'      => $settings->force_update_required,
            'showAnnouncement' => $settings->show_announcement,
            'latestVersion'    => $settings->latest_version ?? '1.0.0',
            'minimumVersion'   => $settings->minimum_version ?? '1.0.0',
        ];
    }

    public function toggleMaintenance(): void
    {
        $settings = AppSettings::get();
        $settings->update(['maintenance_mode' => ! $settings->maintenance_mode]);
        $this->dispatch('$refresh');
    }
}
