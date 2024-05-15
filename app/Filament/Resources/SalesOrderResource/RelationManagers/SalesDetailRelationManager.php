<?php

namespace App\Filament\Resources\SalesOrderResource\RelationManagers;

use App\Models\DataAlamat;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'order_details';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('')
                    ->columns([
                        'sm' => 6,
                        'xl' => 6,
                        '2xl' => 8,
                    ])
                    ->schema([
                        Card::make('Detail Pelanggan')
                            ->columnSpan([
                                'md' => 4,
                            ])
                            ->schema([
                                Hidden::make('sales_order_id')
                                    ->required(),
                                TextInput::make('no_order')
                                    ->default('OR-' . random_int(100000, 999999))
                                    ->readOnly(),
                                Select::make('customer_id')
                                    ->label('Customer')
                                    ->options(DataAlamat::where('tipe', 'customer')->pluck('name', 'id'))
                                    ->required()

                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required(),
                                        TextInput::make('no_hp')
                                            ->label('Nomor HP')
                                            ->required(),
                                        TextInput::make('alamat')
                                            ->required(),
                                        Select::make('type')
                                            ->options([
                                                'customer' => 'Customer',
                                                'supplier' => 'Supplier',
                                                'other' => 'Other'
                                            ])
                                            ->default('customer')
                                            ->required(),
                                    ]),

                                Select::make('status')->label('Status')->options([
                                    'Baru' => 'Baru',
                                    'Pending' => 'Pending',
                                    'Diproses' => 'Diproses',
                                    'Completed' => 'Completed',
                                ])->required(),
                                Select::make('type_bayar')->label('Tipe Pembayaran')->options([
                                    'Cash Langsung' => 'Cash Langsung',
                                    'Cash Tempo' => 'Cash Tempo',
                                    'Tempo Langsung' => 'Tempo Langsung',
                                    'Cek Customer' => 'Cek Customer',
                                ])->required(),
                            ])
                            ->columns(2),

                        Card::make('Akumulasi Belanja')
                            ->columnSpan([
                                'md' => 2,
                            ])
                            ->schema([

                                Placeholder::make('amounts')
                                    ->label('Total Belanja')
                                    ->content(function ($get) {
                                        $sum = 0;
                                        foreach ($get('order_details') as $product) {
                                            $sum = $sum + ($product['harga'] * $product['qty']);
                                        }
                                        return $sum;
                                    }),
                                Hidden::make('amount'),
                                TextInput::make('dp')->label('DP')->required(),
                                TextInput::make('sisa')->label('Sisa')->required(),
                                TextInput::make('kembalian')->label('Kembalian')->required(),
                            ])
                            ->columns(1),
                    ]),


                Card::make('Order Details')
                    ->schema([
                        Repeater::make('order_details')
                            ->relationship('salesOrder')
                            ->schema([
                                Grid::make('')
                                    ->schema([
                                        Select::make('product_id')
                                            ->label('Product')
                                            ->options(Product::query()->pluck('name', 'id'))
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Forms\Set $set, Get $get) {
                                                $productVariant = ProductVariant::where('product_id', $state)->first();
                                                if ($productVariant) {
                                                    $harga = $productVariant->harga ?? 0;
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
                                            ->columnSpan([
                                                'md' => 1,
                                            ])

                                            ->searchable(),

                                        TextInput::make('qty')
                                            ->label('Quantity')
                                            ->default(1)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Forms\Set $set, Get $get) {
                                                $qty = $state;
                                                $harga = $get('harga');
                                                $subtotal = $harga * $qty;
                                                $set('subtotal', $subtotal);
                                            })
                                            ->required()
                                            ->columnSpan(1),
                                        TextInput::make('harga')
                                            ->columnSpan(1)
                                            ->label('Unit Price')
                                            ->required(),
                                        TextInput::make('subtotal')
                                            ->numeric()
                                            ->reactive()
                                            ->columnSpan(1)
                                            ->label('Subtotal')
                                            ->numeric()
                                            ->default(function ($state, Forms\Set $set, Get $get) {
                                                if ($get('qty') === '' && $get('harga')) {
                                                    $subttotal = $get('qty') * $get('harga');
                                                } else {
                                                    $subttotal = 0;
                                                }

                                                return  number_format($subttotal, 2, '.', '');
                                            })
                                            ->disabled()

                                    ])
                                    ->columns(4)

                            ])
                            ->addActionLabel('Tambah Produk'),

                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sales_order_id')
            ->columns([
                Tables\Columns\TextColumn::make('sales_order_id'),
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
}
