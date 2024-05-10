<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesOrderResource\Pages;
use App\Filament\Resources\SalesOrderResource\RelationManagers;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SalesOrder;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Forms\Components;
use Filament\Tables\Columns\Summarizers\Sum;

class SalesOrderResource extends Resource
{
    protected static ?string $model = SalesOrder::class;
    protected static ?string $navigationLabel = 'Sales Order';
    protected static ?string $navigationGroup = 'Marketing';
    // protected static ?string $recordTitleAttribute = 'customer_name'; //untuk global search
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // protected static ?string $navigationGroup = 'Sales';
    public static function getNavigationBadge(): ?string
    {
        return SalesOrder::count();
    }

    public static function form(Form $form): Form
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
                                TextInput::make('customer_name')
                                    ->label('Customer Name')
                                    ->required(),
                                TextInput::make('order_number')->label('Order Number')->required(),
                                TextInput::make('total_amount')->label('Total Amount')->required(),
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
                                TextInput::make('total_amount')
                                    ->label('Total')
                                    //https://stackoverflow.com/questions/70765417/laravel-filament-sum-and-count-repeater
                                    // ->default(function ($state, Forms\Set $set, Get $get) {
                                    //     $orderDetails = $get('order_details.subtotal');
                                    //     if ($orderDetails !== null) {
                                    //         $total = $orderDetails->sum(function ($detail) {
                                    //             return $detail['subtotal'] ?? 0;
                                    //         });
                                    //         return $total;
                                    //     } else {
                                    //         return 0;
                                    //     }
                                    // })
                                    ->prefix('Rp')
                                    // ->postfix('m²')
                                    // ->default(function (Closure $get) {
                                    //     $fields = $get('order_details');
                                    //     $sum = 0;
                                    //     foreach ($fields as $field) {
                                    //         foreach ($field as $value) {
                                    //             if ($value == "") {
                                    //                 $value = 0;
                                    //             }
                                    //             $sum += $value;
                                    //         }
                                    //     }
                                    //     return $sum;
                                    // })
                                    // ->placeholder(fn (Closure $get) => count($get('order_details.subtotal')))
                                    // ->default(0)

                                    ->default(function (Get $get) {
                                        $fields = $get('order_details');
                                        $sum = 0;
                                        if ($fields) {
                                            foreach ($fields as $field) {
                                                if (is_array($field) || is_object($field)) {
                                                    foreach ($field as $value) {
                                                        if ($value == "") {
                                                            $value = 0;
                                                        }
                                                        $sum += $value;
                                                    }
                                                }
                                            }
                                        }
                                        return $sum;
                                    })

                                    ->disabled(),
                                TextInput::make('dp')->label('DP')->required(),
                                TextInput::make('sisa')->label('Sisa')->required(),
                            ])
                            ->columns(1),
                    ]),


                Card::make('Order Details')
                    ->schema([
                        Repeater::make('order_details')
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
                                            ->postfix('Rp')
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

                                                return  $subttotal;
                                            })
                                            ->afterStateUpdated(function ($state, Forms\Set $set, Get $get) {
                                                $subtotal = $state;
                                                $totalAmount = $get('total_amount');
                                                if ($subtotal != $totalAmount) {
                                                    $set('total_amount', $subtotal);
                                                }
                                            })

                                            ->disabled(),

                                    ])->columns(4),
                            ])
                            ->addActionLabel('Tambah'),

                    ])





            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')->label('Nama Pelanggan')->sortable(),
                Tables\Columns\TextColumn::make('order_number')->label('Nomor Order')->sortable(),
                Tables\Columns\TextColumn::make('order_date')->label('Tanggal Order')->date()->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->label('Jumlah Total')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalesOrders::route('/'),
            'create' => Pages\CreateSalesOrder::route('/create'),
            'edit' => Pages\EditSalesOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        //jika bukan admin atau super admin
        // if (!auth()->user()->hasRole('admin') || !auth()->user()->hasRole('super_admin')) {
        //     $query->where('user_id', auth()->id());
        // }

        return $query;
    }
}
