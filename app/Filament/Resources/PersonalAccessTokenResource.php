<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonalAccessTokenResource\Pages;
use App\Models\User;
use App\Models\Ability;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PersonalAccessToken;

class PersonalAccessTokenResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'API';

    protected static ?string $navigationLabel = 'API Tokens';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Token Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('tokenable_id')
                    ->label('User')
                    ->searchable()
                    ->options(User::query()->pluck('name', 'id'))
                    ->required(),

                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Expires At')
                    ->default(now()->addWeeks(3))
                    ->nullable(),

                Forms\Components\Select::make('abilities')
                    ->label('Abilities')
                    ->multiple()
                    ->searchable()
                    ->relationship('abilities', 'name')
                    ->preload()
                    ->hint('Select abilities for this token'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('abilities'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Token Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('token')
                    ->label('Token')
                    ->copyable()
                    ->copyMessage('Token copied to clipboard')
                    ->copyMessageDuration(1500)
                    ->limit(20)
                    ->searchable(),

                Tables\Columns\TextColumn::make('tokenable.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('abilities_count')
                    ->label('Abilities Count')
                    ->counts('abilities')
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_used_at')
                    ->label('Last Used')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonalAccessTokens::route('/'),
            'create' => Pages\CreatePersonalAccessToken::route('/create'),
            'edit' => Pages\EditPersonalAccessToken::route('/{record}/edit'),
            'show-token' => Pages\ShowToken::route('/show-token'),
        ];
    }
}
