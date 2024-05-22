<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesOrderResource\Pages;
use App\Filament\Resources\SalesOrderResource\RelationManagers;
use App\Filament\Resources\SalesOrderResource\RelationManagers\SalesDetailRelationManager;
use App\Models\Customer;
use App\Models\SalesOrder;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomerResource;
use Filament\Support\Enums\MaxWidth;

class SalesOrderResource extends Resource
{
    protected static ?string $model = SalesOrder::class;
    protected static ?string $ownershipRelationship = "salesOrders";
    protected static ?string $tenantOwnershipRelationshipName = "team";
    protected static ?string $label = 'Sales Order';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Section::make()

                    ->schema([


                        TextInput::make('so_no')
                            ->label('No.SO')
                            ->default('SO_' .  date('ymd') . "_" . str_pad(SalesOrder::max('id') + 1, 4, '0', STR_PAD_LEFT)),
                        Select::make('customer_id')
                            ->label('Nama Pelanggan')
                            ->placeholder('Pilih')
                            ->searchable()
                            ->options(Customer::all()->pluck('nama_customer', 'id'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Mencari customer berdasarkan ID yang dipilih
                                $customer = Customer::with(['kelas', 'kategori_customer'])->find($state);

                                if ($customer) {
                                    // Mendapatkan class dan chanel dari customer
                                    $customerClass = $customer->kelas;
                                    $customerCategory = $customer->kategori_customer;

                                    // Mengupdate nilai customer_class_id dan customer_category_id
                                    $set('customer_class_id', $customerClass ? $customerClass->name : null);
                                    $set('customer_category_id', $customerCategory ? $customerCategory->name : null);
                                } else {
                                    // Jika customer tidak ditemukan, set nilai ke null
                                    $set('customer_class_id', null);
                                    $set('customer_category_id', null);
                                }
                            })
                            ->relationship('customer', 'nama_customer')
                            ->createOptionForm(fn (Form $form) => CustomerResource::form($form) ?? [])
                            ->editOptionForm(fn (Form $form) => CustomerResource::form($form) ?? [])
                            ->createOptionAction(fn ($action) => $action->modalWidth(MaxWidth::FiveExtraLarge)),

                        TextInput::make('customer_class_id')
                            ->label('Class')
                            ->default(fn ($get) => optional(Customer::with('kelas')->find($get('customer_id')))->kelas?->name)
                            ->disabled(),

                        TextInput::make('customer_category_id')
                            ->label('Chanel')
                            ->default(fn ($get) => optional(Customer::with('kategori_customer')->find($get('customer_id')))->kategori_customer?->name)
                            ->disabled(),

                        DatePicker::make('tanggal')
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y')
                    ])->columns(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable(),
                TextColumn::make('so_no')
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_barang')
                    ->numeric()
                    ->sortable(),
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
}
