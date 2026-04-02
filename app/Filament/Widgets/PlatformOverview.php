<?php

namespace App\Filament\Widgets;

use App\Models\Report;
use App\Models\Suggestion;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $dailySuggestions = Suggestion::whereDate('created_at', today())->count();
        $weeklySuggestions = Suggestion::where('created_at', '>=', now()->subWeek())->count();
        $pendingReports = Report::count();
        $topSuggestion = Suggestion::query()
            ->orderByRaw('(upvote_count + downvote_count) DESC')
            ->first();

        return [
            Stat::make('Gunluk oneriler', (string) $dailySuggestions)
                ->description('Bugun eklenen suggestion sayisi'),
            Stat::make('Haftalik oneriler', (string) $weeklySuggestions)
                ->description('Son 7 gunde eklenen suggestion sayisi'),
            Stat::make('Toplam kullanici', (string) User::count())
                ->description('Kayitli ve guest kullanicilar dahil'),
            Stat::make('Bekleyen raporlar', (string) $pendingReports)
                ->description($topSuggestion ? "En cok oylanan: {$topSuggestion->title}" : 'Henuz suggestion yok'),
        ];
    }
}
