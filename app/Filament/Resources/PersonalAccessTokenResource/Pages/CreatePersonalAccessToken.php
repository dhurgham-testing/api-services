<?php

namespace App\Filament\Resources\PersonalAccessTokenResource\Pages;

use App\Filament\Resources\PersonalAccessTokenResource;
use App\Models\PersonalAccessToken;
use App\Services\TokenService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms;
use Filament\Forms\Form;
use Webbingbrasil\FilamentCopyActions\Forms\Actions\CopyAction;

class CreatePersonalAccessToken extends CreateRecord
{
    protected static string $resource = PersonalAccessTokenResource::class;

    public string $plain_token = '';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Order')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Token Name')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\Select::make('tokenable_id')
                                ->label('User')
                                ->searchable()
                                ->options(\App\Models\User::query()->pluck('name', 'id'))
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
                                ->options(\App\Models\Ability::query()->pluck('name', 'id')),
                        ]),

                    Step::make('Review')
                        ->schema([
                            Forms\Components\Placeholder::make('Token Name')
                                ->content(fn ($get) => $get('name')),

                            Forms\Components\Placeholder::make('User')
                                ->content(function ($get) {
                                    $userId = $get('tokenable_id');
                                    $user = \App\Models\User::find($userId);
                                    return $user ? $user->name : '-';
                                }),

                            Forms\Components\Placeholder::make('Expires At')
                                ->content(fn ($get) => $get('expires_at')),

                            Forms\Components\Placeholder::make('Abilities')
                                ->content(function ($get) {
                                    $abilities = $get('abilities');
                                    if (is_array($abilities) && !empty($abilities)) {
                                        $abilityNames = \App\Models\Ability::whereIn('id', $abilities)->pluck('name')->toArray();
                                        return implode(', ', $abilityNames);
                                    }
                                    return 'No abilities selected';
                                }),

                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('create_token')
                                    ->label('Create Token')
                                    ->icon('heroicon-o-key')
                                    ->color('success')
                                    ->action(function ($get, $set) {
                                        $tokenService = new \App\Services\TokenService();

                                        $data = [
                                            'name' => $get('name'),
                                            'tokenable_id' => $get('tokenable_id'),
                                            'expires_at' => $get('expires_at'),
                                            'abilities' => $get('abilities') ?? [],
                                        ];

                                        $result = $tokenService->createToken($data);
                                        $this->plain_token = $result['plain_text_token'];

                                        // Jump to the final wizard step
                                        $set('__activeWizardStep', 2);
                                    })
                                    ->requiresConfirmation()
                                    ->modalHeading('Create Token')
                                    ->modalDescription('Are you sure you want to create this token?')
                                    ->modalSubmitActionLabel('Yes, Create Token'),
                            ]),
                        ]),

                    Wizard\Step::make('Token Created')
                        ->schema([
                            Forms\Components\Placeholder::make('Your Token')
                                ->content(fn () => $this->plain_token)
                                ->hintAction(
                                    CopyAction::make()
                                        ->copyable(fn () => $this->plain_token)
                                )
                                ->label('Copy this token now, it will not be shown again.'),

                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('done')
                                    ->label('Done')
                                    ->icon('heroicon-m-check-circle')
                                    ->color('primary')
                                    ->action(fn () => redirect(PersonalAccessTokenResource::getUrl('index'))),
                            ]),
                        ])
                        ->visible(fn () => filled($this->plain_token)),
                ])
                    ->statePath('data')
                    ->skippable(false)
                    ->persistStepInQueryString(),
            ]);
    }


    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Token')
                ->modalHeading('Create API Token')
                ->registerModalActions([
                    Actions\Action::make('showToken')
                        ->label('Your Token')
                        ->modalHeading('Token Created Successfully')
                        ->modalContent(fn (): string => view('filament.token-modal', [
                            'token' => $this->plain_token,
                        ])->render())
                        ->cancelParentActions(),
                ])
                ->after(fn () => $this->mountAction('showToken')),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
