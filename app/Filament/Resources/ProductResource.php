<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\RelationManagers\ProductVariantRelationManager;
use App\Filament\Resources\ProductResource\Widgets\ProductOverview;
use App\Models\Category;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Team;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tab;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\RawJs;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;


    // protected static ?string $tenantOwnershipRelationshipName = 'products';
    protected static ?string $tenantRelationshipName = 'produk';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $recordTitleAttribute = 'name'; //untuk global search
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Data';
    public static function getNavigationBadge(): ?string
    {
        return Product::count();
    }




    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $teamId = Filament::getTenant()->id; //$user->currentTeam->id
        return $form
            ->schema([
                Section::make('Product Form')
                    ->columns(4)
                    ->schema([
                        // FileUpload::make('gambar')
                        //     ->directory('product'),
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
                        TextInput::make('nama_produk_cn')->label('Nama Produk (Cn)'),
                        // ->helperText('Nama Produk Versi Bahasa Cina'),
                        TextInput::make('nama_produk')->label('Nama Produk (ID)'),
                        // ->helperText('Nama Produk Versi Bahasa Indonesia'),
                        // TextInput::make('tag')->label('Tag')
                        //     ->mask('/'),
                        // ->mask('Ctn/Dz/card/Pcs')
                        // ->placeholder('/'),
                        //
                        // TextInput::make('data')
                        //     ->label('Data')
                        //     ->mask('----/----/----/----') // Initial mask for the format
                        //     ->rules(['regex:/^\d{4}\/\d{4}\/\d{4}\/\d{4}$/']) // Regex validation for the format
                        // ,
                        TextInput::make('tes')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        TextInput::make('category_id')->label('Kategori'),
                        TextInput::make('deskripsi')->label('Deskripsi'),
                        Hidden::make('team_id')->default($teamId),
                        Hidden::make('user_id')->default($user->id)
                    ]),



                Tabs::make('Tab')

                    ->tabs([
                        Tabs\Tab::make('Harga')
                            ->model(Product::class) // target input data

                            ->schema([
                                // Field form terkait harga
                                TextInput::make('harga_jual')->label('Harga Jual'),
                                TextInput::make('harga_beli')->label('Harga Beli'),
                                // ... field form lainnya
                            ]),

                        Tabs\Tab::make('Inventori')
                            ->model(Product::class) // target input data

                            ->schema([
                                // Field form terkait inventori
                                TextInput::make('stok')->label('Stok'),
                                TextInput::make('minimum_stok')->label('Stok Minimum'),
                                // ... field form lainnya
                            ]),

                        Tabs\Tab::make('Penjualan')
                            ->model(Product::class) // target input data

                            ->schema([
                                // Field form terkait penjualan
                                TextInput::make('jumlah_terjual')->label('Jumlah Terjual'),
                                TextInput::make('pendapatan_penjualan')->label('Pendapatan Penjualan'),
                                // ... field form lainnya
                            ]),

                        Tabs\Tab::make('Pembelian')
                            ->model(Product::class) // target input data

                            ->schema([
                                // Field form terkait pembelian
                                TextInput::make('jumlah_dibeli')->label('Jumlah Dibeli'),
                                TextInput::make('biaya_pembelian')->label('Biaya Pembelian'),
                                // ... field form lainnya
                            ]),

                        Tabs\Tab::make('Bea Cukai')
                            ->model(Product::class) // target input data

                            ->schema([
                                // Field form terkait bea cukai
                                TextInput::make('bea_masuk')->label('Bea Masuk'),
                                TextInput::make('bea_keluar')->label('Bea Keluar'),
                                // ... field form lainnya
                            ]),
                    ])
                    ->columnSpanFull()




            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();
        $userId = $user->id;
        return $table
            ->modifyQueryUsing(function (Builder $query) use ($userId) {
                // filter jika bukan super_admin
                if (!auth()->user()->hasAnyRole(['admin', 'super_admin'])) {
                    $query->where('user_id', $userId);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
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
            ProductVariantRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ProductOverview::class,
        ];
    }
}
