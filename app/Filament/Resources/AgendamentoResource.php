<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendamentoResource\Pages;
use App\Filament\Resources\AgendamentoResource\RelationManagers;
use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Veiculo;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgendamentoResource extends Resource
{
    protected static ?string $model = Agendamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Locar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Dados do Agendamento')
                    ->schema([
                        Grid::make([
                            'xl' => 4,
                            '2xl' => 4,
                        ])
                            ->schema([

                                Forms\Components\Select::make('cliente_id')
                                    ->label('Cliente')
                                    ->native(false)
                                    ->searchable()
                                    ->options(Cliente::all()->pluck('nome', 'id')->toArray())
                                    ->columnSpan([
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('veiculo_id')
                                    ->relationship(
                                        name: 'veiculo',
                                        modifyQueryUsing: fn (Builder $query) => $query->where('status',1)->orderBy('modelo')->orderBy('placa'),
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->modelo} {$record->placa}")
                                    ->searchable(['modelo', 'placa'])
                                    ->columnSpan([
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ]),

                                Forms\Components\DatePicker::make('data_saida')
                                    ->displayFormat('d/m/Y')
                                    ->label('Data Saída')
                                    ->reactive()
                                    ->required(),
                                Forms\Components\TimePicker::make('hora_saida')
                                    ->label('Hora Saída')
                                    ->required(),
                                Forms\Components\DatePicker::make('data_retorno')
                                    ->displayFormat('d/m/Y')
                                    ->label('Data Retorno')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                        $dt_saida = Carbon::parse($get('data_saida'));
                                        $dt_retorno = Carbon::parse($get('data_retorno'));
                                        $qtd_dias = $dt_retorno->diffInDays($dt_saida);
                                        $set('qtd_diarias', $qtd_dias);

                                        $carro = Veiculo::find($get('veiculo_id'));
                                        $set('valor_total', ($carro->valor_diaria * $qtd_dias));
                                    })
                                    ->required(),
                                Forms\Components\TimePicker::make('hora_retorno')
                                    ->label('Hora Retorno')
                                    ->required(),
                            ]),
                        Fieldset::make('Valores')
                            ->schema([
                                Forms\Components\TextInput::make('qtd_diarias')
                                    ->readOnly()
                                    ->label('Qtd Diárias')
                                    ->required(),
                                Forms\Components\TextInput::make('valor_total')
                                    ->readOnly()
                                    ->label('Valor Total')
                                    ->required(),
                                Forms\Components\TextInput::make('valor_desconto')
                                    ->numeric()
                                    ->label('Valor Desconto'),
                                Forms\Components\TextInput::make('valor_pago')
                                    ->numeric()
                                    ->hint('Pagamento Antecipado')
                                    ->required()
                                    ->label('Valor Pago')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, Get $get,) {
                                        $set('valor_restante', (((float)$get('valor_total') - (float)$get('valor_desconto')) - (float)$get('valor_pago')));
                                    }),

                                Forms\Components\TextInput::make('valor_restante')
                                    ->readOnly()
                                    ->label('Valor Restante')
                                    ->required(),
                                Forms\Components\Textarea::make('obs')
                                    ->label('Observações'),
                                Forms\Components\Toggle::make('status')
                                    ->label('Finalizar Agendamento'),

                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cliente.nome')
                    ->sortable()
                    ->searchable()
                    ->label('Cliente'),
                Tables\Columns\TextColumn::make('veiculo.modelo')
                    ->sortable()
                    ->searchable()
                    ->label('Veículo'),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->searchable()
                    ->label('Placa'),
                Tables\Columns\TextColumn::make('data_saida')
                    ->label('Data Saída')
                    ->date(),
                Tables\Columns\TextColumn::make('hora_saida')
                    ->sortable()
                    ->label('Hora Saída'),
                Tables\Columns\TextColumn::make('data_retorno')
                    ->label('Data Retorno')
                    ->date(),
                Tables\Columns\TextColumn::make('hora_retorno')
                    ->label('Hora Retorno'),
                Tables\Columns\TextColumn::make('qtd_diarias')
                    ->label('Qtd Diárias'),
                Tables\Columns\TextColumn::make('valor_total')
                    ->label('Valor Total')
                    ->money('BRL'),
                Tables\Columns\TextColumn::make('valor_desconto')
                    ->label('Valor Desconto')
                    ->money('BRL'),
                Tables\Columns\TextColumn::make('valor_pago')
                    ->label('Valor Pago')
                    ->money('BRL'),
                Tables\Columns\TextColumn::make('valor_restante')
                    ->label('Valor Restante')
                    ->money('BRL'),
                Tables\Columns\TextColumn::make('obs')
                    ->label('Observações'),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('Finalizado'),
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
                Tables\Actions\Action::make('Imprimir')
                ->url(fn (Agendamento $record): string => route('imprimirAgendamento', $record))
                ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ManageAgendamentos::route('/'),
        ];
    }
}
