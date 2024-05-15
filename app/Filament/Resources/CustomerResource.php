<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tab;


class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Data';
    public static function form(Form $form): Form
    {
        $role_id = auth()->user()->roles->pluck('id')->first();
        $user_id = auth()->user()->id;
        $user = Auth::user();
        $teamId = Filament::getTenant()->id; //$user->currentTeam->id
        return $form
            ->schema([
                Section::make('Customer Form')
                    ->columns(4)
                    ->schema([
                        // FileUpload::make('gambar')
                        //     ->directory('Customer'),
                        // TextInput::make('name')
                        //     ->live(onBlur: true)
                        //     ->afterStateUpdated(
                        //         fn (Set $set, ?string $state) =>
                        //         $set('slug', Str::slug($state))
                        //     )
                        //     ->label('Name')
                        //     ->required(),
                        // TextInput::make('slug')->label('Slug'),
                        // Select::make('category_id')
                        //     ->label('Kategori')
                        //     ->options(Category::pluck('name', 'id')->toArray())
                        //     ->default($form->getRecord()->category_id ?? null),
                        // TextInput::make('deskripsi')->label('Deskripsi'),
                        // TextInput::make('format_satuan')
                        //     ->label('Format Satuan')
                        //     ->default('Ctn/Box/Card/Pcs')
                        //     ->helperText('COntoh : Ctn/Box/Card/Pcs'),
                        TextInput::make('uuid')->label('UUID'),
                        TextInput::make('id_produk')->label('ID Produk'),
                        TextInput::make('nama_produk_cn')->label('Nama Produk (Cn)')->helperText('Nama Produk Versi Bahasa Cina'),
                        TextInput::make('nama_produk')->label('Nama Produk (ID)')->helperText('Nama Produk Versi Bahasa Indonesia'),
                        TextInput::make('tag')->label('Tag'),
                        TextInput::make('category_id')->label('Kategori'),
                        TextInput::make('deskripsi')->label('Deskripsi'),
                        Hidden::make('team_id')->default($teamId),
                        Hidden::make('user_id')->default($user->id)
                    ]),



                Tabs::make('Tab')

                    ->tabs([
                        Tabs\Tab::make('Info')
                            ->model(Customer::class) // target input data

                            ->schema([
                                // Field form terkait harga
                                TextInput::make('harga_jual')->label('Harga Jual'),
                                TextInput::make('harga_beli')->label('Harga Beli'),
                                // ... field form lainnya
                            ]),

                        Tabs\Tab::make('Keterangan')
                            ->model(Customer::class) // target input data

                            ->schema([
                                // Field form terkait inventori
                                TextInput::make('stok')->label('Stok'),
                                TextInput::make('minimum_stok')->label('Stok Minimum'),
                                // ... field form lainnya
                            ]),

                        Tabs\Tab::make('Pembayaran')
                            ->model(Customer::class) // target input data

                            ->schema([
                                // Field form terkait penjualan
                                TextInput::make('jumlah_terjual')->label('Jumlah Terjual'),
                                TextInput::make('pendapatan_penjualan')->label('Pendapatan Penjualan'),
                                // ... field form lainnya
                            ]),

                        Tabs\Tab::make('Pembelian')
                            ->model(Customer::class) // target input data

                            ->schema([
                                // Field form terkait pembelian
                                TextInput::make('jumlah_dibeli')->label('Jumlah Dibeli'),
                                TextInput::make('biaya_pembelian')->label('Biaya Pembelian'),
                                // ... field form lainnya
                            ]),


                    ])
                    ->columnSpanFull()




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_toko')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_bank')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_rek')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
