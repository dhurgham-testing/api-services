<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiRouteResource\Pages;
use App\Models\ApiRoute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApiRouteResource extends Resource
{
    protected static ?string $model = ApiRoute::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'API';

    protected static ?string $navigationLabel = 'API Routes';

    protected static ?string $title = 'API Routes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('service_group')
                    ->required()
                    ->maxLength(255)
                    ->label('Service Group (e.g., youtube, spotify)'),

                Forms\Components\TextInput::make('route_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Route Name (e.g., search, convert-to-mp3)'),

                Forms\Components\TextInput::make('controller_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Controller Name (e.g., YouTubeDownloaderController)'),

                Forms\Components\TextInput::make('method_name')
                    ->required()
                    ->maxLength(255)
                    ->label('Method Name (e.g., search, convertToMp3)'),

                Forms\Components\Select::make('http_method')
                    ->required()
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'PATCH' => 'PATCH',
                        'DELETE' => 'DELETE',
                    ])
                    ->default('POST'),

                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service_group')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('route_name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('controller_name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('method_name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('http_method')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_group')
                    ->options([
                        'youtube' => 'YouTube',
                        'spotify' => 'Spotify',
                        'other' => 'Other',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Default Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiRoutes::route('/'),
            'create' => Pages\CreateApiRoute::route('/create'),
            'edit' => Pages\EditApiRoute::route('/{record}/edit'),
        ];
    }
}
