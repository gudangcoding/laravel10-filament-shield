<?php

namespace App\Filament\Resources\DataAlamatResource\Pages;

use App\Filament\Resources\DataAlamatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListDataAlamats extends ListRecords
{
    protected static string $resource = DataAlamatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return "Data dan Alamat " . auth()->user()->roles->pluck('name')->first();
    }
}
