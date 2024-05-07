<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataAlamatResource\Pages;
use App\Filament\Resources\DataAlamatResource\RelationManagers;
use App\Models\DataAlamat;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DataAlamatResource extends Resource
{
    protected static ?string $model = DataAlamat::class;
    //grpupnama
    // protected static ?string $navigationGroup = 'Master Data';


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel  = 'Data dan Alamat';
    protected static ?string $tenantOwnershipRelationshipName  = 'team';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data dan ALamat')
                    ->schema([
                        Card::make('')
                            ->schema([
                                TextInput::make('nama')->label('Nama')->required(),
                                TextInput::make('no_hp')->label('No HP')->required(),
                                TextInput::make('alamat')->label('Alamat')->required(),
                                TextInput::make('kelurahan')->label('Kelurahan')->required(),
                                TextInput::make('kec')->label('Kec')->required(),
                                TextInput::make('kel')->label('Kel')->required(),
                                TextInput::make('kab')->label('Kab')->required(),
                                TextInput::make('prov')->label('Prov')->required(),
                                TextInput::make('kodepos')->label('Kodepos')->required(),
                                TextInput::make('tipe')->label('Tipe')->required(),
                                TextInput::make('nama_bank')->label('Nama Bank')->required(),
                                TextInput::make('no_rekening')->label('No Rekening')->required(),
                                TextInput::make('atas_nama')->label('Atas Nama')->required(),
                                TextInput::make('nama_toko')->label('Nama Toko')->required(),
                            ])
                            ->columns(3),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
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
            'index' => Pages\ListDataAlamats::route('/'),
            'create' => Pages\CreateDataAlamat::route('/create'),
            'edit' => Pages\EditDataAlamat::route('/{record}/edit'),
        ];
    }
}
