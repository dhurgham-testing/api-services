<?php

namespace App\Filament\Resources\ApiRouteResource\Pages;

use App\Filament\Resources\ApiRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiRoute extends EditRecord
{
    protected static string $resource = ApiRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
} 