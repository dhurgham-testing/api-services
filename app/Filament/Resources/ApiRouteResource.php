<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiRouteResource\Pages;
use App\Models\ApiRoute;
use Exception;
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
                Forms\Components\Select::make('middleware')
                    ->required()
                    ->label('Middleware')
                    ->searchable()
                    ->options([
                        "api" => "API",
                        "web" => "WEB",
                    ])
                    ->live(onBlur: true)
                    ->placeholder('Select a middleware first...')
                    ->afterStateUpdated(function ($state, $set) {
                        \Log::info("Middleware updated to: {$state}");
                        $set('service_group', '');
                        $set('controller_name', '');
                        $set('method_name', '');
                    }),

                Forms\Components\TextInput::make('service_group')
                    ->required()
                    ->disabled(fn($get) => !$get('middleware'))
                    ->maxLength(255)
                    ->label('Service Group (e.g., youtube, spotify)')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        \Log::info("Service group updated to: {$state}");
                        $set('controller_name', '');
                        $set('method_name', '');
                    }),

                Forms\Components\Select::make('controller_name')
                    ->required()
                    ->disabled(fn($get) => !$get('service_group'))
                    ->label('Controller Name')
                    ->options(function ($get) {
                        $service_group = $get('service_group');

                        if (!$service_group) {
                            \Log::info("No service group selected for controller options");
                            return [];
                        }

                        $controllers = [];
                        $middleware = $get('middleware') ?? 'api';
                        $base_path = $service_group == "/"
                            ? app_path("Http/Controllers/" . ucfirst($middleware))
                            : app_path("Http/Controllers/" . ucfirst($middleware) . "/" . ucfirst($service_group));

                        \Log::info("Looking for controllers in path: {$base_path}");
                        \Log::info("Middleware: {$middleware}, Service Group: {$service_group}");

                        if (is_dir($base_path)) {
                            $files = glob($base_path . "/*.php");
                            \Log::info("Found files: " . implode(', ', $files));
                            foreach ($files as $file) {
                                $filename = basename($file, '.php');
                                $controllers[ucfirst($filename)] = ucfirst($filename);
                            }
                        } else {
                            \Log::warning("Directory does not exist: {$base_path}");
                        }

                        \Log::info("Available controllers: " . implode(', ', array_keys($controllers)));
                        return $controllers;
                    })
                    ->searchable()
                    ->placeholder('Select a controller...')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set) {
                        \Log::info("Controller selected: {$state}");
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
                        $middleware = $get('middleware') ?? 'api';
                        $controller_name = $get('controller_name');
                        $service_group = $get('service_group');

                        \Log::info("Method options requested - Middleware: {$middleware}, Controller: {$controller_name}, Service Group: {$service_group}");

                        if (!$controller_name || !$service_group) {
                            \Log::warning("Missing controller_name or service_group for method options");
                            return [];
                        }

                        $controller_class = $service_group == "/" ? "App\\Http\\Controllers\\" . ucfirst($middleware) . "\\{$controller_name}" : "App\\Http\\Controllers\\" . ucfirst($middleware) ."\\" . ucfirst($service_group) . "\\{$controller_name}";
                        $controller_name_parts = explode('\\', $controller_class);
                        $last_part = end($controller_name_parts);
                        $controller_class = str_replace($last_part, ucfirst($last_part), $controller_class);
                        
                        \Log::info("Initial controller class: {$controller_class}");
                        
                        // Try multiple case variations if class doesn't exist
                        $possible_classes = [
                            $controller_class,
                            str_replace(ucfirst($last_part), $last_part, $controller_class), // original case
                            str_replace(ucfirst($last_part), strtolower($last_part), $controller_class), // lowercase
                            str_replace(ucfirst($last_part), strtoupper($last_part), $controller_class), // uppercase
                        ];
                        
                        \Log::info("Trying possible classes: " . implode(', ', $possible_classes));
                        
                        $found_class = null;
                        foreach ($possible_classes as $class) {
                            if (class_exists($class)) {
                                $found_class = $class;
                                \Log::info("Found existing class: {$class}");
                                break;
                            }
                        }
                        
                        if (!$found_class) {
                            \Log::warning("No class found. Tried: " . implode(', ', $possible_classes));
                            return [];
                        }
                        
                        $controller_class = $found_class;
                        \Log::info("Using controller class: {$controller_class}");

                        try {
                            $reflection = new \ReflectionClass($controller_class);
                            \Log::info("Reflection successful for: {$controller_class}");

                            $public_methods = collect($reflection->getMethods(\ReflectionMethod::IS_PUBLIC))
                                ->filter(function ($method) use ($controller_class) {
                                    $isValid = strtolower($method->class) === strtolower($controller_class) && !$method->isConstructor();
                                    \Log::info("Method {$method->getName()} from {$method->class} - Valid: " . ($isValid ? 'true' : 'false'));
                                    return $isValid;
                                })
                                ->mapWithKeys(fn($method) => [$method->getName() => $method->getName()]);

                            $method_names = $public_methods->keys()->toArray();
                            \Log::info("Found methods: " . implode(', ', $method_names));
                            return $public_methods->toArray();
                        } catch (\ReflectionException $e) {
                            \Log::error("Reflection failed for {$controller_class}: " . $e->getMessage());
                            return [];
                        }
                    })
                    ->searchable()
                    ->placeholder('Select a controller first...')
                    ->afterStateUpdated(function ($state, $set) {
                        \Log::info("Method selected: {$state}");
                    }),

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

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('middleware')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'api' => 'warning',
                        'web' => 'success',
                        default => 'secondary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_group')
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                Tables\Columns\TextColumn::make('route_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('controller_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('method_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('http_method')
                    ->badge()
                    ->color('success')
                    ->searchable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->searchable(),

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
