<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\RelationManagers\ProductVariantRelationManager;
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

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;


    // protected static ?string $tenantOwnershipRelationshipName = 'products';
    protected static ?string $tenantRelationshipName = 'produk';
    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Master Data';
    public static function form(Form $form): Form
    {
        $user = Auth::user();
        $teamId = Filament::getTenant()->id; //$user->currentTeam->id



        return $form
            ->schema([
                Section::make('Product Form')
                    ->columns(2)
                    ->schema([
                        Card::make()
                            ->schema([

                                TextInput::make('name')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn (Set $set, ?string $state) =>
                                        $set('slug', str::slug($state))
                                    )
                                    ->label('Name')
                                    ->required(),
                                TextInput::make('slug')->label('Slug'),
                                Select::make('category_id')
                                    ->label('Kategori')
                                    ->options(Category::pluck('name', 'id')->toArray())
                                    ->default($form->getRecord()->category_id ?? null),
                                TextInput::make('deskripsi')->label('Deskripsi'),
                                Hidden::make('team_id')->default($teamId),
                                Hidden::make('user_id')->default($user->id)
                            ])->columns(2),


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
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
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
}
