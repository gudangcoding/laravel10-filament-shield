<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesOrderResource\Pages;
use App\Filament\Resources\SalesOrderResource\RelationManagers;
use App\Filament\Resources\SalesOrderResource\RelationManagers\SalesDetailRelationManager;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SalesOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Closure;
// use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Forms\Components;
use Filament\Support\RawJs;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Livewire\Attributes\Reactive;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use Filament\Tables\Actions\Action;

class SalesOrderResource extends Resource
{
    protected static ?string $model = SalesOrder::class;
    protected static ?string $tenantRelationshipName = 'invoice';
    protected static ?string $navigationLabel = 'Sales Order';
    protected static ?int $navigationSort = -1;
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
                                TextInput::make('no_order')
                                    ->default('OR-' . random_int(100000, 999999))
                                    ->readOnly(),
                                Select::make('customer_id')
                                    ->label('Customer')
                                    ->options(Customer::get()->pluck('name', 'id'))
                                    // ->options(Customer::where('tipe', 'customer')->pluck('name', 'id'))
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
                                        $orderDetails = $get('order_details');
                                        if (is_array($orderDetails)) {
                                            foreach ($orderDetails as $product) {
                                                $harga = isset($product['harga']) ? (float) $product['harga'] : 0;
                                                $qty = isset($product['qty']) ? (float) $product['qty'] : 0;
                                                $sum += $harga * $qty;
                                            }
                                        }
                                        return number_format($sum, 2, '.', ',');
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
                            ->relationship()
                            ->schema([
                                Grid::make('')
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
                                                $product = Product::find($get('product_id'));
                                                if ($product) {
                                                    $priceField = 'price_' . $state;
                                                    $price = (float) $product->$priceField;
                                                    $set('harga', $price);
                                                    $subtotal = (float) $get('qty') * $price;
                                                    $set('subtotal', number_format($subtotal, 2, '.', ''));
                                                }
                                            })
                                            ->columnSpan(1),
                                        Select::make('product_id')
                                            ->label('Product')
                                            ->options(Product::query()->pluck('nama_produk', 'id'))
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Forms\Set $set, Get $get) {
                                                $product = Product::find($state);
                                                if ($product) {
                                                    $satuan = $get('satuan');
                                                    $priceField = 'price_' . $satuan;
                                                    $price = (float) $product->$priceField;
                                                    $set('harga', $price);
                                                    $subtotal = (float) $get('qty') * $price;
                                                    $set('subtotal', number_format($subtotal, 2, '.', ''));
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
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, Forms\Set $set, Get $get) {
                                                $price = (float) $get('harga');
                                                $subtotal = $price * (float) $state;
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
                                                return number_format((float) $get('qty') * (float) $get('harga'), 2, '.', '');
                                            })
                                            ->disabled(),
                                    ])
                                    ->columns(5),
                            ])
                            ->addActionLabel('Tambah Produk'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_order')->label('Nama Pelanggan')->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->label('Jumlah Barang')->sortable(),
                Tables\Columns\TextColumn::make('total_barang')->label('Total')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-circle')
                    ->action(function (Model $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                Blade::render('pdf', ['record' => $record])
                            )->stream();
                        }, $record->number . '.pdf');
                    }),
                Action::make('Download Pdf')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (SalesOrder $record): string => route('/invoice', ['record' => $record]))
                    ->openUrlInNewTab(),
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
            SalesDetailRelationManager::class,
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
