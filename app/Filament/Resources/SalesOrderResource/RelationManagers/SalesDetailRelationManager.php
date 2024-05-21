<?php

namespace App\Filament\Resources\SalesOrderResource\RelationManagers;

use App\Models\Product;
use App\Models\SalesDetail;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'SalesDetail';
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_id')
            ->columns([
                TextColumn::make('product_id'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('product_id')
                    ->label('Cari Produk')
                    ->options(Product::whereNotNull('id')->pluck('nama_produk', 'id'))
                    ->searchable()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state) {
                        // Mencari customer berdasarkan ID yang dipilih
                        $produk = Product::find($state);

                        if ($produk) {
                            $salesDetail = new SalesDetail;
                            $salesDetail->sales_order_id = $this->record->getKey();
                            $salesDetail->product_id = $state;
                            $salesDetail->satuan_id;
                            $salesDetail->harga;
                            $salesDetail->qty = 1;
                            $salesDetail->subtotal = $salesDetail->harga * $salesDetail->qty;
                            $salesDetail->save();
                        }
                    }),

            ]);
    }
}
