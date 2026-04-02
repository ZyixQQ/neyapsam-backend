<?php

namespace App\Filament\Resources;

use App\Enums\SuggestionStatus;
use App\Filament\Resources\SuggestionResource\Pages;
use App\Models\Suggestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SuggestionResource extends Resource
{
    protected static ?string $model = Suggestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('subcategory_id')
                ->relationship('subcategory', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'username')
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('title')->required()->maxLength(120),
            Forms\Components\Textarea::make('description')->rows(4),
            Forms\Components\Select::make('status')
                ->options(collect(SuggestionStatus::cases())->mapWithKeys(fn ($case) => [$case->value => ucfirst($case->value)]))
                ->required(),
            Forms\Components\TextInput::make('upvote_count')->numeric()->required(),
            Forms\Components\TextInput::make('downvote_count')->numeric()->required(),
            Forms\Components\TextInput::make('net_score')->numeric()->required(),
            Forms\Components\Toggle::make('is_featured')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('subcategory.name')->label('Subcategory')->searchable(),
                Tables\Columns\TextColumn::make('user.username')->label('User')->toggleable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('net_score')->sortable(),
                Tables\Columns\TextColumn::make('upvote_count')->sortable(),
                Tables\Columns\TextColumn::make('downvote_count')->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
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
            'index' => Pages\ListSuggestions::route('/'),
            'create' => Pages\CreateSuggestion::route('/create'),
            'edit' => Pages\EditSuggestion::route('/{record}/edit'),
        ];
    }
}
