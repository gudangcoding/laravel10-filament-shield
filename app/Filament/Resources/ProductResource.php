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
                    ->columns(2)
                    ->schema([
                        Card::make()
                            ->schema([

                                FileUpload::make('gambar')
                                    ->directory('product'),
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
                                TextInput::make('format_satuan')
                                    ->label('Format Satuan')
                                    ->default('Ctn/Box/Card/Pcs')
                                    ->helperText('COntoh : Ctn/Box/Card/Pcs'),

                                Hidden::make('team_id')->default($teamId),
                                Hidden::make('user_id')->default($user->id)
                            ])->columns(2),

                    ]),
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
