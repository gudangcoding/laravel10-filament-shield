<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataAlamatResource\Pages;
use App\Filament\Resources\DataAlamatResource\RelationManagers;
use App\Models\DataAlamat;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class DataAlamatResource extends Resource
{
    protected static ?string $model = DataAlamat::class;
    protected static ?string $tenantRelationshipName = 'dataalamat';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel  = 'Data dan Alamat';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $userId = $user->id;
        return $form
            ->schema([
                Hidden::make('user_id')->default($userId),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('no_hp')
                    ->required()
                    ->maxLength(255),
                Textarea::make('alamat')
                    ->required(),
                Select::make('tipe')
                    ->options([
                        'customer' => 'Customer',
                        'supplier' => 'Supplier',
                        'karyawan' => 'Karyawan'
                    ]),
                TextInput::make('nama_bank')
                    ->maxLength(255),
                TextInput::make('no_rekening')
                    ->maxLength(255),
                TextInput::make('atas_nama')
                    ->maxLength(255),
                TextInput::make('nama_toko')
                    ->maxLength(255),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('tipe')
                    ->searchable(),
                TextColumn::make('no_hp')
                    ->searchable(),
                TextColumn::make('nama_bank')
                    ->searchable(),
                TextColumn::make('no_rekening')
                    ->searchable(),
                TextColumn::make('atas_nama')
                    ->searchable(),
                TextColumn::make('nama_toko')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
            'index' => Pages\ListDataAlamats::route('/'),
            'create' => Pages\CreateDataAlamat::route('/create'),
            'edit' => Pages\EditDataAlamat::route('/{record}/edit'),
        ];
    }
}
