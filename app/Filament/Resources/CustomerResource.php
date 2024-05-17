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
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tab;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Fieldset;


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


                        TextInput::make('id_customer')
                            ->label('ID Customer')
                            ->unique()
                            ->required(),
                        TextInput::make('nama_customer')
                            ->label('Nama Customer')
                            ->required(),
                        TextInput::make('daerah_customer')
                            ->label('Daerah Customer')
                            ->required(),
                        TextInput::make('class')
                            ->label('Class')
                            ->default('DP PAKING'),
                        TextInput::make('category')
                            ->label('Category')
                            ->default('TOKO'),
                        TextInput::make('sisa_limit_hutang')
                            ->label('Sisa Limit Hutang'),
                        TextInput::make('total_hutang')
                            ->label('Total Hutang'),
                        TextInput::make('hutang_dlm_tempo')
                            ->label('Hutang Dalam Tempo'),
                        TextInput::make('hutang_lewat_tempo')
                            ->label('Hutang Lewat Tempo'),
                        TextInput::make('limit_nota')
                            ->label('Limit Nota'),
                        TextInput::make('limit_hutang')
                            ->label('Limit Hutang'),
                        Hidden::make('uuid_customer'),
                        Hidden::make('team_id')->default($teamId),
                        Hidden::make('user_id')->default($user->id)
                    ]),



                Tabs::make('Tab')

                    ->tabs([
                        Tabs\Tab::make('Info')
                            ->model(Customer::class) // target input data

                            ->schema([
                                Repeater::make('contacts')
                                    ->schema([
                                        TextInput::make('no_hp')
                                            ->label('No. HP'),
                                        TextInput::make('nama')
                                            ->label('Nama'),
                                        TextInput::make('hubungan')
                                            ->label('Hubungan'),
                                        TextInput::make('app_tag')
                                            ->label('App [TAG]')
                                            ->helperText('WA/IG/LINE etc'),
                                    ])
                                    ->columns(4)
                                    ->label('Contacts'),
                                // ... field form lainnya
                            ]),

                        Tabs\Tab::make('Keterangan')
                            ->model(Customer::class) // target input data

                            ->schema([
                                Fieldset::make('Alamat Kirim')
                                    ->schema([
                                        TextInput::make('negara')
                                            ->label('Negara')
                                            ->default('INDONESIA'),
                                        TextInput::make('provinsi')
                                            ->label('Provinsi')
                                            ->default('JAKARTA'),
                                        TextInput::make('kabupaten_kota')
                                            ->label('Kabupaten/Kota')
                                            ->default('JAKARTA BARAT'),
                                        TextInput::make('kecamatan')
                                            ->label('Kecamatan')
                                            ->default('DAAN MOGOT'),
                                        TextInput::make('kelurahan_desa')
                                            ->label('Kelurahan/Desa')
                                            ->default('KAPUK'),
                                        TextInput::make('rt_rw')
                                            ->label('RT/RW')
                                            ->default('001/01'),
                                        TextInput::make('alamat')
                                            ->label('Alamat')
                                            ->default('Jl. PETERNAKAN II, Gg IKAN ASIN'),
                                        TextInput::make('no_unit')
                                            ->label('No/Unit')
                                            ->default('No.16'),
                                        TextInput::make('tambahan')
                                            ->label('Tambahan')
                                            ->default('KOMPLEK PERGUDANGAN NAGATA, GUDANG S3'),
                                        TextInput::make('kode_pos')
                                            ->label('Kode Pos')
                                            ->default('11420'),
                                    ])
                            ]),

                        Tabs\Tab::make('Catatan')
                            ->icon('heroicon-o-bell')
                            ->model(Customer::class) // target input data via model

                            ->schema([
                                Textarea::make('perhatian')
                                    ->label('Perhatian')
                                    ->placeholder("CONTOH: SUKA PESAN DIKIT2 MULTI SO\nCONTOH: SUKA BILANG KIRIMNYA KURANG BARANG"),

                            ]),
                        Tabs\Tab::make('Pembayaran')
                            ->model(Customer::class) // target input data

                            ->schema([
                                Repeater::make('pembayaran')
                                    ->schema([
                                        TextInput::make('atas_nama')->label('Atas Nama'),
                                        TextInput::make('no_rek')->label('No Rekening'),
                                        TextInput::make('nominal')->label('nominal'),
                                        TextInput::make('sisa')->label('sisa'),
                                    ])
                                    ->columns(2)
                                    ->label('Info Rekening Bank')
                                    ->addActionLabel('Tambah Bank'),
                            ]),

                        Tabs\Tab::make('Pembelian')
                            ->model(Customer::class) // target input data

                            ->schema([
                                DatePicker::make('tgl_beli')
                                    ->label('Tgl Beli')
                                    ->displayFormat('d/m/Y')
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
