<?php

namespace App\Filament\Resources;

use App\Enums\SuggestionStatus;
use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Moderation';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('suggestion_id')
                ->relationship('suggestion', 'title')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'username')
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('reason')->required()->maxLength(255),
            Forms\Components\Textarea::make('details')->rows(4)->columnSpanFull(),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Şikayet Detayı')
                ->schema([
                    Infolists\Components\TextEntry::make('reason')->label('Şikayet Sebebi'),
                    Infolists\Components\TextEntry::make('user.username')->label('Şikayet Eden'),
                    Infolists\Components\TextEntry::make('details')->label('Ek Açıklama')->columnSpanFull()->placeholder('—'),
                    Infolists\Components\TextEntry::make('created_at')->label('Şikayet Tarihi')->dateTime('d.m.Y H:i'),
                ])->columns(2),

            Infolists\Components\Section::make('Öneri İçeriği')
                ->schema([
                    Infolists\Components\TextEntry::make('suggestion.title')
                        ->label('Başlık')
                        ->columnSpanFull()
                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                    Infolists\Components\TextEntry::make('suggestion.description')
                        ->label('Açıklama')
                        ->columnSpanFull()
                        ->placeholder('Açıklama girilmemiş.')
                        ->prose(),
                    Infolists\Components\TextEntry::make('suggestion.status')
                        ->label('Durum')
                        ->badge()
                        ->formatStateUsing(fn ($state) => match ($state) {
                            SuggestionStatus::Approved => 'Yayında',
                            SuggestionStatus::Pending  => 'Beklemede',
                            SuggestionStatus::Rejected => 'Reddedildi',
                            default => $state,
                        })
                        ->color(fn ($state) => match ($state) {
                            SuggestionStatus::Approved => 'success',
                            SuggestionStatus::Pending  => 'warning',
                            SuggestionStatus::Rejected => 'danger',
                            default => 'gray',
                        }),
                    Infolists\Components\TextEntry::make('suggestion.upvote_count')->label('Beğeni'),
                    Infolists\Components\TextEntry::make('suggestion.downvote_count')->label('Beğenmeme'),
                    Infolists\Components\TextEntry::make('suggestion.subcategory.category.name')->label('Kategori'),
                    Infolists\Components\TextEntry::make('suggestion.subcategory.name')->label('Alt Kategori'),
                    Infolists\Components\TextEntry::make('suggestion.created_at')->label('Öneri Tarihi')->dateTime('d.m.Y H:i'),
                ])->columns(2),

            Infolists\Components\Section::make('Gönderiyi Gönderen Kişi')
                ->schema([
                    Infolists\Components\TextEntry::make('suggestion.user.username')->label('Kullanıcı Adı')->placeholder('Anonim'),
                    Infolists\Components\TextEntry::make('suggestion.user.name')->label('Ad Soyad')->placeholder('—'),
                    Infolists\Components\TextEntry::make('suggestion.user.email')->label('E-posta')->placeholder('—'),
                    Infolists\Components\IconEntry::make('suggestion.user.is_banned')
                        ->label('Banlı mı?')
                        ->boolean()
                        ->trueIcon('heroicon-o-x-circle')
                        ->falseIcon('heroicon-o-check-circle')
                        ->trueColor('danger')
                        ->falseColor('success'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('suggestion.title')
                    ->label('Öneri')
                    ->searchable()
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\TextColumn::make('suggestion.user.username')
                    ->label('Gönderen'),
                Tables\Columns\TextColumn::make('suggestion.status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        SuggestionStatus::Approved => 'Yayında',
                        SuggestionStatus::Pending  => 'Beklemede',
                        SuggestionStatus::Rejected => 'Reddedildi',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        SuggestionStatus::Approved => 'success',
                        SuggestionStatus::Pending  => 'warning',
                        SuggestionStatus::Rejected => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('user.username')->label('Şikayet Eden')->toggleable(),
                Tables\Columns\TextColumn::make('reason')->label('Sebep')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('created_at')->label('Tarih')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()->label('İncele'),
                Tables\Actions\Action::make('ban_post')
                    ->label('Gönderiyi Pasifleştir')
                    ->icon('heroicon-o-no-symbol')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Gönderiyi pasifleştir')
                    ->modalDescription('Bu öneri artık herkese görünmez hale gelecek. Devam etmek istiyor musunuz?')
                    ->hidden(fn (Report $record) => $record->suggestion?->status === SuggestionStatus::Rejected)
                    ->action(function (Report $record) {
                        $record->suggestion?->update(['status' => SuggestionStatus::Rejected]);
                        Notification::make()->title('Öneri pasifleştirildi.')->success()->send();
                    }),
                Tables\Actions\Action::make('ban_user')
                    ->label('Kullanıcıyı Banla')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Kullanıcıyı banla')
                    ->modalDescription('Bu kullanıcı bir daha gönderi gönderemez hale gelecek. Devam etmek istiyor musunuz?')
                    ->hidden(fn (Report $record) => ! $record->suggestion?->user || $record->suggestion->user->is_banned)
                    ->action(function (Report $record) {
                        $record->suggestion?->user?->update(['is_banned' => true]);
                        Notification::make()->title('Kullanıcı banlandı.')->success()->send();
                    }),
                Tables\Actions\EditAction::make()->label('Düzenle'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit'   => Pages\EditReport::route('/{record}/edit'),
            'view'   => Pages\ViewReport::route('/{record}'),
        ];
    }
}
