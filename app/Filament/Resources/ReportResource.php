<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('suggestion.title')->label('Suggestion')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('user.username')->label('Reporter')->toggleable(),
                Tables\Columns\TextColumn::make('reason')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
