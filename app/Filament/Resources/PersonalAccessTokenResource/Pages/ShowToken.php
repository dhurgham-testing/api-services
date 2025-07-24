<?php

namespace App\Filament\Resources\PersonalAccessTokenResource\Pages;

use App\Filament\Resources\PersonalAccessTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\Page;

class ShowToken extends Page
{
    protected static string $resource = PersonalAccessTokenResource::class;

    protected static string $view = 'filament.resources.personal-access-token-resource.pages.show-token';

    public ?string $token = null;

    public function mount(): void
    {
        $this->token = request()->query('token');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('copy_token')
                ->label('Copy Token')
                ->icon('heroicon-o-clipboard')
                ->extraAttributes([
                    'x-data' => '',
                    'x-on:click' => "navigator.clipboard.writeText('{$this->token}').then(() => \$el.innerText = 'Copied!')",
                ]),
            Actions\Action::make('back_to_list')
                ->label('Back to List')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
