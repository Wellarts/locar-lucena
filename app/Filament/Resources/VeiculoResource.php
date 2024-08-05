<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VeiculoResource\Pages;
use App\Filament\Resources\VeiculoResource\RelationManagers;
use App\Models\Marca;
use App\Models\Veiculo;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VeiculoResource extends Resource
{
    protected static ?string $model = Veiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Veículos';

    protected static ?string $navigationGroup = 'Cadastros';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                        Fieldset::make('Veículo')
                            ->schema([
                                Grid::make([
                                    'xl' => 3,
                                    '2xl' => 3,
                                ])->schema([
                                Forms\Components\TextInput::make('modelo'),
                                Forms\Components\Select::make('marca_id')
                                    ->label('Marca')
                                    ->required()
                                    ->options(Marca::all()->pluck('nome', 'id')->toArray()),
                                Forms\Components\TextInput::make('ano')
                                    ->numeric(),
                                Forms\Components\TextInput::make('placa')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('cor')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('km_atual')
                                    ->label('Km Atual')
                                    ->numeric(),
                                Forms\Components\DatePicker::make('data_compra'),
                                Forms\Components\TextInput::make('chassi')
                                ->label('Nº do Chassi'),
                                Forms\Components\TextInput::make('valor_diaria')
                                    ->live(onBlur: true)
                                    ->label('Valor Diária')
                                    ->numeric(),
                            ]),
                        Fieldset::make('Manutenção')
                            ->schema([
                                Grid::make([
                                    'xl' => 2,
                                    '2xl' => 2,
                                ])->schema([
                                    Forms\Components\Toggle::make('status_alerta')
                                        ->columnSpanFull()
                                        ->default(false)
                                        ->label('Ativar/Desativar - Alertas'),
                                    Forms\Components\TextInput::make('prox_troca_oleo')
                                        ->label('Próxima Troca de Óleo - Km'),
                                    Forms\Components\TextInput::make('aviso_troca_oleo')
                                        ->label('Aviso Troca do Óleo - Km'),
                                    Forms\Components\TextInput::make('prox_troca_filtro')
                                        ->label('Próxima Troca do Filtro - Km'),
                                    Forms\Components\TextInput::make('aviso_troca_filtro')
                                        ->label('Aviso Troca do Filtro - Km'),
                                    Forms\Components\TextInput::make('prox_troca_correia')
                                        ->label('Próxima Troca da Correia - Km'),
                                    Forms\Components\TextInput::make('aviso_troca_correia')
                                        ->label('Aviso Troca da Correia - Km'),
                                    Forms\Components\TextInput::make('prox_troca_pastilha')
                                        ->label('Próxima Troca da Pastilha - Km'),
                                    Forms\Components\TextInput::make('aviso_troca_pastilha')
                                        ->label('Aviso Troca da Pastilha - Km'),



                                ]),

                                    ]),
                                    Forms\Components\Toggle::make('status')
                                    ->default(true)
                                    ->label('Ativar/Desativar - Veículo')
                                    ->columnSpan([
                                        'xl' => 2,
                                        '2xl' => 2,
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('modelo')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('marca.nome'),
                Tables\Columns\TextColumn::make('ano'),
                Tables\Columns\TextColumn::make('placa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cor'),
                Tables\Columns\TextColumn::make('km_atual')
                    ->label('Km Atual'),
                Tables\Columns\TextColumn::make('valor_diaria')
                    ->label('Valor Diária')
                    ->money('BRL'),
                Tables\Columns\IconColumn::make('status')
                    ->sortable()
                    ->boolean(),
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
                Filter::make('Ativos')
                ->query(fn (Builder $query): Builder => $query->where('status',true)),
                Filter::make('Inativos')
                ->query(fn (Builder $query): Builder => $query->where('status',false)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Editar veículo'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVeiculos::route('/'),
        ];
    }
}
