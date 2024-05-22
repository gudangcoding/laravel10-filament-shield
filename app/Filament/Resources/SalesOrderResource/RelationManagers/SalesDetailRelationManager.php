<?php

namespace App\Filament\Resources\SalesOrderResource\RelationManagers;

use App\Models\Product;
use App\Models\SalesDetail;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;

class SalesDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'SalesDetail';
    public function table(Table $table): Table
    {
        return $table
            ->defaultGroup('koli')
            ->groupRecordsTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Group records'),
            )
            ->recordTitleAttribute('product_id')
            ->columns([
                ImageColumn::make('gambar_produk')
                    ->label('Gambar'),
                TextColumn::make('product.nama_produk')
                    ->label('Nama Produk'),
                TextColumn::make('harga')
                    ->label('Harga'),
                TextColumn::make('satuan')
                    ->label('Satuan'),
                TextColumn::make('qty')
                    ->label('Qty'),
                TextColumn::make('koli')
                    ->label('Koli'),
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
                    BulkAction::make('Satukan Koli')
                        ->icon('heroicon-m-check')
                        ->requiresConfirmation()
                        ->form([

                            Select::make('koli')
                                ->label('Koli')
                                ->options(function () {
                                    $lastKoli = SalesDetail::max('koli');
                                    // $koli = SalesDetail::select('koli');
                                    $nextKoli = $lastKoli ? $lastKoli + 1 : 1;
                                    return [
                                        null => 'Keluarkan', // Adding null as the first option
                                        $lastKoli => $lastKoli,
                                        $nextKoli => $nextKoli
                                    ];
                                })
                                ->default(null)
                                ->distinct(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                SalesDetail::where('id', $record->id)->update(['koli' => $data['koli']]);
                            });
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),

            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('satuan')
                    ->label('Satuan')
                    ->options([
                        'ctn' => 'Carton',
                        'box' => 'Box',
                        'bag' => 'Bag',
                        'card' => 'Card',
                        'lusin' => 'Lusin',
                        'pack' => 'Pack',
                        'pcs' => 'Pcs'
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Get $get) {
                        $ketemu = Product::find($get('product_id'));

                        if ($ketemu) {
                            $harga = match ($state) {
                                'ctn' => $ketemu->price_ctn,
                                'box' => $ketemu->price_box,
                                'bag' => $ketemu->price_bag,
                                'card' => $ketemu->price_card,
                                'lusin' => $ketemu->price_lsn,
                                'pack' => $ketemu->price_pack,
                                'pcs' => $ketemu->price_pcs,
                                default => 0,
                            };
                            // dd($harga);
                            $subtotal = $get('qty') * $harga;
                            $set('harga', number_format($subtotal, 2, '.', ''));
                            $set('subtotal', number_format($subtotal, 2, '.', ''));
                        }
                    })
                    ->columnSpan(1),
                Select::make('product_id')
                    ->label('Product')
                    ->options(Product::query()->pluck('nama_produk', 'id'))
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Forms\Set $set, Get $get) {
                        $ketemu = Product::find($state);
                        if ($ketemu) {
                            $satuan = $get('satuan');
                            $harga = match ($satuan) {
                                'ctn' => $ketemu->price_ctn,
                                'box' => $ketemu->price_box,
                                'bag' => $ketemu->price_bag,
                                'card' => $ketemu->price_card,
                                'lusin' => $ketemu->price_lsn,
                                'pack' => $ketemu->price_pack,
                                'pcs' => $ketemu->price_pcs,
                                default => 0,
                            };
                            $subtotal = $get('qty') * $harga;
                            $set('harga', $harga);
                            $set('subtotal', $subtotal);
                        } else {
                            $set('harga', 0);
                            $set('subtotal', 0);
                        }
                    })
                    ->distinct()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->columnSpan(['md' => 1])
                    ->searchable(),
                TextInput::make('qty')
                    ->label('Quantity')
                    ->default(1)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Forms\Set $set, Get $get) {
                        $harga = $get('harga');
                        $subtotal = $harga * $state;
                        $set('subtotal', number_format($subtotal, 2, '.', ''));
                    })
                    ->required()
                    ->columnSpan(1),
                TextInput::make('harga')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->columnSpan(1)
                    ->label('Unit Price')
                    ->required()
                    ->disabled(),
                TextInput::make('subtotal')
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->columnSpan(1)
                    ->label('Subtotal')
                    ->default(function ($state, Forms\Set $set, Get $get) {
                        return number_format($get('qty') * $get('harga'), 2, '.', '');
                    })
                    ->disabled(),

            ])->columns(5);
    }
}
