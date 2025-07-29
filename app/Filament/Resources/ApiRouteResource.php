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
                    ->label('Service Group (e.g., youtube, spotify)')
                    ->live(onBlur: true),

                Forms\Components\Select::make('controller_name')
                    ->required()
                    ->disabled(fn($get) => !$get('service_group'))
                    ->label('Controller Name')
                    ->options(function ($get) {
                        $service_group = $get('service_group');

                        if (!$service_group) {
                            return [];
                        }

                        $controllers = [];
                        $base_path = app_path("Http/Api/" . ucfirst($service_group) . "/Controllers");

                        if (is_dir($base_path)) {
                            $files = glob($base_path . "/*Controller.php");
                            foreach ($files as $file) {
                                $filename = basename($file, '.php');
                                $controllers[$filename] = $filename;
                            }
                        }

                        return $controllers;
                    })
                    ->searchable()
                    ->placeholder('Select a controller...')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        $set('method_name', '');
                    }),

                Forms\Components\TextInput::make('route_name')
                    ->required()
                    ->disabled(fn($get) => !$get('controller_name'))
                    ->maxLength(255)
                    ->label('Route Name (e.g., search, convert-to-mp3)'),

                Forms\Components\Select::make('method_name')
                    ->required()
                    ->disabled(fn($get) => !$get('controller_name'))
                    ->label('Method Name')
                    ->options(function ($get) {
                        $controller_name = $get('controller_name');
                        $service_group = $get('service_group');

                        if (!$controller_name || !$service_group) {
                            return [];
                        }


                        $controller_class = "App\\Http\\Api\\" . ucfirst($service_group) . "\\Controllers\\{$controller_name}";

                        if (!class_exists($controller_class)) {
                            return [];
                        }

                        try {
                            $reflection = new \ReflectionClass($controller_class);

                            $public_methods = collect($reflection->getMethods(\ReflectionMethod::IS_PUBLIC))
                                ->filter(function ($method) use ($controller_class) {
                                    return $method->class === $controller_class && !$method->isConstructor();
                                })
                                ->mapWithKeys(fn($method) => [$method->getName() => $method->getName()]);

                            return $public_methods->toArray();
                        } catch (\ReflectionException $e) {
                            return [];
                        }
                    })
                    ->searchable()
                    ->placeholder('Select a controller first...'),

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
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('route_name'),

                Tables\Columns\TextColumn::make('controller_name'),

                Tables\Columns\TextColumn::make('method_name'),

                Tables\Columns\TextColumn::make('http_method')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiRoutes::route('/'),
        ];
    }
}
