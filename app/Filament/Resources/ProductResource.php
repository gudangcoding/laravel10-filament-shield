<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // protected static ?string $navigationGroup = 'Master Data';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Information')
                    ->columns(2)
                    ->schema([
                        Card::make()
                            ->schema([
                                TextInput::make('name')->label('Name')->required(),
                                TextInput::make('slug')->label('Slug')->required(),
                                TextInput::make('satuan')->label('Satuan')->required(),
                                Select::make('kategori')
                                    ->label('Kategori')
                                    ->options(Category::pluck('name', 'id')->toArray()),
                                TextInput::make('deskripsi')->label('Deskripsi')->required(),
                                FileUpload::make('gambar')
                                    ->label('Gambar')
                                    ->image(),
                                TextInput::make('harga')->label('Harga')->required(),
                                TextInput::make('stok')->label('Stok')->required(),
                            ])->columns(4),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('satuan')
                    ->label('Satuan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok')
                    ->label('Stok')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('gambar')
                    ->label('Gambar')
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
}