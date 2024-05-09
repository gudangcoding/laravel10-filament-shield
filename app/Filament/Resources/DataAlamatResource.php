<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataAlamatResource\Pages;
use App\Filament\Resources\DataAlamatResource\RelationManagers;
use App\Models\DataAlamat;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
    protected static ?string $tenantRelationshipName = 'dataalamat';
    protected static ?string $navigationLabel  = 'Data Alamat';
    protected static ?string $recordTitleAttribute = 'nama'; //untuk global search
    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make('Form Data dan Alamat')
                    ->schema([
                        Select::make('tipe')
                            ->options([
                                'Customer' => 'Customer',
                                'Supplier' => 'Supplier',
                                'Karyawan' => 'Karyawan',
                            ]),
                        TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('no_hp')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('alamat')
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('kelurahan')
                            ->maxLength(255),
                        TextInput::make('kec')
                            ->maxLength(255),
                        TextInput::make('kel')
                            ->maxLength(255),
                        TextInput::make('kab')
                            ->maxLength(255),
                        TextInput::make('prov')
                            ->maxLength(255),
                        TextInput::make('kodepos')
                            ->maxLength(255),
                        TextInput::make('nama_bank')
                            ->maxLength(255),
                        TextInput::make('no_rekening')
                            ->maxLength(255),
                        TextInput::make('atas_nama')
                            ->maxLength(255),
                        TextInput::make('nama_toko')
                            ->maxLength(255),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelurahan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kec')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kab')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prov')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kodepos')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipe')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_bank')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_rekening')
                    ->searchable(),
                Tables\Columns\TextColumn::make('atas_nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_toko')
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
            'index' => Pages\ListDataAlamats::route('/'),
            'create' => Pages\CreateDataAlamat::route('/create'),
            'edit' => Pages\EditDataAlamat::route('/{record}/edit'),
        ];
    }
}
