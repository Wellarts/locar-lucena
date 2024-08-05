<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustoVeiculoResource\Pages;
use App\Filament\Resources\CustoVeiculoResource\RelationManagers;
use App\Models\CustoVeiculo;
use App\Models\Fornecedor;
use App\Models\Veiculo;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustoVeiculoResource extends Resource
{
    protected static ?string $model = CustoVeiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Manutenção de Veículos';

    protected static ?string $navigationGroup = 'Manutenções';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Select::make('fornecedor_id')
                        ->searchable()
                        ->label('Fornecedor')
                        ->required()
                        ->options(Fornecedor::all()->pluck('nome', 'id')->toArray()),
                    Forms\Components\Select::make('veiculo_id')
                        ->relationship(
                            name: 'veiculo',
                            modifyQueryUsing: fn (Builder $query) => $query->orderBy('modelo')->orderBy('placa'),
                        )
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->modelo} {$record->placa}")
                        ->searchable(['modelo', 'placa']),

                Forms\Components\TextInput::make('km_atual')
                    ->label('Km Atual')
                    ->required(),
                Forms\Components\DatePicker::make('data')
                    ->default(now())
                    ->required(),
                Forms\Components\Textarea::make('descricao')
                    ->label('Descrição do Serviço/Peça')
                    ->autosize()
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\TextInput::make('valor')
                    ->label('Valor Total')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fornecedor.nome')
                ->sortable(),
            Tables\Columns\TextColumn::make('veiculo.modelo')
                ->sortable()
                ->label('Veículo'),
            Tables\Columns\TextColumn::make('veiculo.placa')
                ->label('Placa'),
            Tables\Columns\TextColumn::make('km_atual')
                ->label('Km Atual'),
            Tables\Columns\TextColumn::make('data')
                ->sortable()
                ->date('d/m/Y'),
            Tables\Columns\TextColumn::make('valor')
                ->summarize(Sum::make()->money('BRL')->label('Total'))
                ->money('BRL')
                ->label('Valor Total'),
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
            SelectFilter::make('fornecedor')->searchable()->relationship('fornecedor', 'nome'),
            SelectFilter::make('veiculo')->searchable()->relationship('veiculo', 'placa')->label('Veículo - (Placa)'),
            Tables\Filters\Filter::make('datas')
            ->form([
                DatePicker::make('data_de')
                    ->label('Saída de:'),
                DatePicker::make('data_ate')
                    ->label('Saída ate:'),
            ])
            ->query(function ($query, array $data) {
                return $query
                    ->when($data['data_de'],
                        fn($query) => $query->whereDate('data', '>=', $data['data_de']))
                    ->when($data['data_ate'],
                        fn($query) => $query->whereDate('data', '<=', $data['data_ate']));
           })
        ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->modalHeading('Editar custo veículo'),
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
            'index' => Pages\ManageCustoVeiculos::route('/'),
        ];
    }
}
