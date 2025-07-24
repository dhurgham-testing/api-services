<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class TokensRelationManager extends RelationManager
{
    protected static string $relationship = 'tokens';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Token Name'),
                Tables\Columns\TextColumn::make('last_used_at')->dateTime()->label('Last Used'),
                Tables\Columns\TextColumn::make('created_at')->since()->label('Created'),
            ])

            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
