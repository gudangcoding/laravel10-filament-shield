<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesOrderResource\Pages;
use App\Filament\Resources\SalesOrderResource\RelationManagers;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SalesOrder;

use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Forms\Components;

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

                Card::make('Order')
                    ->schema([
                        TextInput::make('customer_name')->label('Customer Name')->required(),
                        TextInput::make('order_number')->label('Order Number')->required(),
                        DateTimePicker::make('order_date')->label('Order Date')->required(),
                        TextInput::make('total_amount')->label('Total Amount')->required(),
                        Select::make('status')->label('Status')->options([
                            'Pending' => 'Pending',
                            'Processing' => 'Processing',
                            'Completed' => 'Completed',
                        ])->required(),
                    ])
                    ->columns(3),
                Card::make('Order Details')
                    ->schema([
                        Repeater::make('order_details')
                            ->schema([
                                Grid::make('')
                                    ->schema([
                                        Select::make('product_id')
                                            ->label('Product Name')
                                            ->live()
                                            ->options(Product::pluck('name', 'id')->toArray())
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                // Pastikan $state adalah array dan memiliki kunci 'product_id'
                                                if (is_array($state) && isset($state['product_id'])) {
                                                    $productId = $state['product_id'];
                                                    $product = ProductVariant::findOrFail($productId);
                                                    if ($product) {
                                                        $set('harga', $product->price);
                                                    }
                                                }
                                            })
                                            ->required(),

                                        TextInput::make('qty')
                                            ->label('Quantity')
                                            ->default(1)
                                            ->required()
                                            ->columnSpan(1),
                                        TextInput::make('harga')
                                            ->columnSpan(1)
                                            ->label('Unit Price')
                                            ->required(),
                                        TextInput::make('subtotal')
                                            ->columnSpan(1)
                                            ->label('Subtotal')
                                        // ->default(function ($data) {
                                        //     return (string)($data->qty * $data->harga);
                                        // })
                                        // ->default(fn ($state) => $state->qty * $state->harga)
                                        // ->disabled(),
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
