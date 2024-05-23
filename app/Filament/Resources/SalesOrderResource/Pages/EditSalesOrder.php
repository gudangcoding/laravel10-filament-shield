<?php

namespace App\Filament\Resources\SalesOrderResource\Pages;

use App\Filament\Resources\SalesOrderResource;
use Filament\Actions;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Tabel\Actions\Action;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EditSalesOrder extends EditRecord
{
    protected static string $resource = SalesOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getFooterActions(): array
    {
        return [
            Summarizer::make('koli')->label('Total Koli'),
        ];
    }

    public function getTitle(): string|Htmlable
    {

        return "Form Detail Order ";
    }
    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }


    protected function afterSave(): void
    {
        $salesOrderId = $this->record->id;
        $printUrl = URL::route('invoices.print', ['salesOrder' => $salesOrderId]);
        // $printUrl = URL::route('invoice', ['salesOrder' => $salesOrderId]);

        Redirect::away($printUrl, '_blank');
    }
}
