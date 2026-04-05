<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Enums\SuggestionStatus;
use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('ban_post')
                ->label('Gönderiyi Pasifleştir')
                ->icon('heroicon-o-no-symbol')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Gönderiyi pasifleştir')
                ->modalDescription('Bu öneri artık herkese görünmez hale gelecek. Devam etmek istiyor musunuz?')
                ->hidden(fn () => $this->record->suggestion?->status === SuggestionStatus::Rejected)
                ->action(function () {
                    $this->record->suggestion?->update(['status' => SuggestionStatus::Rejected]);
                    Notification::make()->title('Öneri pasifleştirildi.')->success()->send();
                    $this->refreshFormData([]);
                }),

            Actions\Action::make('ban_user')
                ->label('Kullanıcıyı Banla')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Kullanıcıyı banla')
                ->modalDescription('Bu kullanıcı bir daha gönderi gönderemeye ve panele giremez hale gelecek. Devam etmek istiyor musunuz?')
                ->hidden(fn () => ! $this->record->suggestion?->user || $this->record->suggestion->user->is_banned)
                ->action(function () {
                    $this->record->suggestion?->user?->update(['is_banned' => true]);
                    Notification::make()->title('Kullanıcı banlandı.')->success()->send();
                    $this->refreshFormData([]);
                }),

            Actions\EditAction::make(),
        ];
    }
}
