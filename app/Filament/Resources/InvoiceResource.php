<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        $products = Product::get();

        return $form
            ->schema([
                Section::make()
                    ->columns(1)
                    ->schema([
                        // Bidang ulang untuk barang faktur
                        Forms\Components\Repeater::make('invoiceProducts')
                            // Didefinisikan sebagai hubungan ke model InvoiceProduct
                            ->relationship()
                            ->schema([
                                // Dua bidang dalam setiap baris: produk dan kuantitas
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    // Pilihan adalah semua produk, tetapi kita telah memodifikasi tampilannya untuk menampilkan harga juga
                                    ->options(
                                        $products->mapWithKeys(function (Product $product) {
                                            return [$product->id => sprintf('%s ($%s)', $product->name, $product->price)];
                                        })
                                    )
                                    // Menonaktifkan opsi yang sudah dipilih di baris lain
                                    ->disableOptionWhen(function ($value, $state, Get $get) {
                                        return collect($get('../*.product_id'))
                                            ->reject(fn ($id) => $id == $state)
                                            ->filter()
                                            ->contains($value);
                                    })
                                    ->required(),
                                Forms\Components\TextInput::make('quantity')
                                    ->integer()
                                    ->default(1)
                                    ->required()
                            ])
                            // Bidang ulang ini langsung sehingga akan memicu pembaruan status pada setiap perubahan
                            ->live()
                            // Setelah menambahkan baris baru, kita perlu memperbarui total
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            // Setelah menghapus baris, kita perlu memperbarui total
                            ->deleteAction(
                                fn (Action $action) => $action->after(fn (Get $get, Set $set) => self::updateTotals($get, $set)),
                            )
                            // Menonaktifkan pengurutan ulang
                            ->reorderable(false)
                            ->columns(2)
                    ]),
                Section::make()
                    ->columns(1)
                    ->maxWidth('1/2')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            // Hanya dibaca, karena ini dihitung
                            ->readOnly()
                            ->prefix('$')
                            // Ini memungkinkan kita untuk menampilkan subtotal saat halaman edit dimuat
                            ->afterStateHydrated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            }),
                        Forms\Components\TextInput::make('taxes')
                            ->suffix('%')
                            ->required()
                            ->numeric()
                            ->default(20)
                            // Bidang langsung, karena kita perlu menghitung ulang total pada setiap perubahan
                            ->live(true)
                            // Ini memungkinkan kita untuk menampilkan subtotal saat halaman edit dimuat
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            }),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            // Hanya dibaca, karena ini dihitung
                            ->readOnly()
                            ->prefix('$')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        // Ambil semua produk yang dipilih dan hapus baris kosong
        $selectedProducts = collect($get('invoiceProducts'))
            ->filter(fn ($item) => !empty($item['product_id']) && !empty($item['quantity']));

        // Ambil harga untuk semua produk yang dipilih
        $prices = Product::find($selectedProducts
            ->pluck('product_id'))->pluck('price', 'id');

        // Hitung subtotal berdasarkan produk dan kuantitas yang dipilih
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
            return $subtotal + ($prices[$product['product_id']] * $product['quantity']);
        }, 0);

        // Perbarui status dengan nilai baru
        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('total', number_format($subtotal + ($subtotal * ($get('taxes') / 100)), 2, '.', ''));
    }
}
