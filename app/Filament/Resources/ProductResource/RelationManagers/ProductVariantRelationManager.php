<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class ProductVariantRelationManager extends RelationManager
{
    protected static string $relationship = 'ProductVariant';


    public function form(Form $form): Form
    {

        return $form
            ->schema([

                // FileUpload::make('gambar'),
                FileUpload::make('gambar')
                    ->label('Upload Gambar')
                    ->placeholder('Pilih file')
                    // ->helpMessage('Pilih gambar yang ingin diupload')
                    ->rules(['image', 'max:2048']) // Aturan validasi: file gambar, maksimal 2MB
                    ->store(function (FileUpload $file) {
                        return $file->store('product', 'product_images'); // Simpan file di direktori storage/app/public/product
                    }),
                FileUpload::make('gambar')
                    ->label('Upload Gambar')
                    ->placeholder('Pilih file')
                    ->helpMessage('Pilih gambar yang ingin diupload')
                    ->rules(['image', 'max:2048']) // Aturan validasi: file gambar, maksimal 2MB
                    ->store(function (FileUpload $file) {
                        return Storage::disk('product_images')->put('product', $file->store()); // Simpan file di direktori storage/app/public/product
                    }),
                Forms\Components\TextInput::make('harga')
                    ->label('Harga')
                    ->required(),
                Forms\Components\TextInput::make('satuan')
                    ->label('Satuan')
                    ->required(),
                Forms\Components\TextInput::make('isi')
                    ->label('Isi')
                    ->required(),
                Forms\Components\TextInput::make('stok')
                    ->label('Stok')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_id')
            ->columns([
                Tables\Columns\TextColumn::make('gambar'),
                Tables\Columns\TextColumn::make('harga'),
                Tables\Columns\TextColumn::make('satuan'),
                Tables\Columns\TextColumn::make('isi'),
                Tables\Columns\TextColumn::make('stok'),
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
