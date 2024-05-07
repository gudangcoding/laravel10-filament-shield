<?php

namespace App\Filament\Resources\DataAlamatResource\Pages;

use App\Filament\Resources\DataAlamatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataAlamat extends EditRecord
{
    protected static string $resource = DataAlamatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
