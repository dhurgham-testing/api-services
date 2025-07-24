<?php

namespace App\Filament\Resources\PersonalAccessTokenResource\Pages;

use App\Filament\Resources\PersonalAccessTokenResource;
use App\Models\PersonalAccessToken;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonalAccessToken extends EditRecord
{
    protected static string $resource = PersonalAccessTokenResource::class;

    public function getRecord(): PersonalAccessToken
    {
        $record = parent::getRecord();
        $record->load('abilities');
        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
