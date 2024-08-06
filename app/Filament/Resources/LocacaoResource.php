<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocacaoResource\Pages;
use App\Filament\Resources\LocacaoResource\RelationManagers;
use App\Filament\Resources\LocacaoResource\RelationManagers\OcorrenciaRelationManager;
use App\Models\Cliente;
use App\Models\Locacao;
use App\Models\Veiculo;
use Carbon\Carbon;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Laravel\SerializableClosure\Serializers\Native;
use Leandrocfe\FilamentPtbrFormFields\Money;

class LocacaoResource extends Resource
{
    protected static ?string $model = Locacao::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Locações';

    protected static ?string $navigationGroup = 'Locar';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Dados da Locação')
                    ->schema([
                        Grid::make([
                            'xl' => 4,
                            '2xl' => 4,
                        ])
                            ->schema([
                                Forms\Components\Select::make('cliente_id')
                                    ->label('Cliente')
                                    ->searchable()
                                    ->native(false)
                                    ->columnSpan([
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ])
                                    ->live()
                                    ->required(false)
                                    ->options(Cliente::all()->pluck('nome', 'id')->toArray())
                                    ->afterStateUpdated(function ($state) {
                                        if ($state != null) {
                                            $cliente = Cliente::find($state);
                                            Notification::make()
                                                ->title('ATENÇÃO')
                                                ->body('A validade da CNH do cliente selecionado: ' . Carbon::parse($cliente->validade_cnh)->format('d/m/Y'))
                                                ->warning()
                                                ->persistent()
                                                ->send();
                                        }
                                    }),
                                Forms\Components\Select::make('veiculo_id')
                                    ->required(false)
                                    ->label('Veículo')
                                    ->live(onBlur: true)
                                    ->relationship(
                                        name: 'veiculo',
                                        modifyQueryUsing: fn (Builder $query) => $query->where('status', 1)->where('status_locado', 0)->orderBy('modelo')->orderBy('placa'),
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->modelo} {$record->placa}")
                                    ->searchable(['modelo', 'placa'])
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $veiculo = Veiculo::find($state);
                                        if ($state != null) {
                                            $set('km_saida', $veiculo->km_atual);
                                        }
                                    })
                                    ->columnSpan([
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ]),
                                Forms\Components\DatePicker::make('data_saida')
                                    ->default(Carbon::today())
                                    ->displayFormat('d/m/Y')
                                    ->label('Data Saída')
                                    ->required(false),
                                Forms\Components\TimePicker::make('hora_saida')
                                    ->seconds(false)
                                    ->default(Carbon::now())
                                    ->label('Hora Saída')
                                    ->required(false),
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
                                        $set('valor_desconto', '');
                                    })
                                    ->required(false),
                                Forms\Components\TimePicker::make('hora_retorno')
                                    ->seconds(false)
                                    ->default(Carbon::now())
                                    ->label('Hora Retorno')
                                    ->required(false),
                                Forms\Components\TextInput::make('km_saida')
                                    ->label('Km Saída')
                                    ->required(false),
                                Forms\Components\TextInput::make('km_retorno')
                                    ->label('Km Retorno'),

                            ]),
                        Fieldset::make('Valores')
                            ->schema([

                                Forms\Components\TextInput::make('qtd_diarias')
                                    ->extraInputAttributes(['tabindex' => 1, 'style' => 'font-weight: bolder; font-size: 1rem; color: #CF9A16;'])
                                    ->label('Qtd Diárias')
                                    ->readOnly()
                                    ->required(false),
                                Forms\Components\TextInput::make('valor_total')
                                    ->extraInputAttributes(['tabindex' => 1, 'style' => 'font-weight: bolder; font-size: 1rem; color: #D33644;'])
                                    ->label('Valor Total')
                                    ->numeric()
                                    // ->currencyMask(thousandSeparator: '.',decimalSeparator: ',',precision: 2)
                                    ->readOnly()
                                    ->required(false),
                                Forms\Components\TextInput::make('valor_desconto')
                                    ->extraInputAttributes(['tabindex' => 1, 'style' => 'font-weight: bolder; font-size: 1rem; color: #3668D3;'])
                                    ->label('Desconto')
                                    // ->numeric()
                                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                                    ->required(true)
                                    // ->live(debounce: 500)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, Get $get,) {
                                        $set('valor_total_desconto', ((float)$get('valor_total') - (float)$get('valor_desconto')));
                                    }),
                                Forms\Components\TextInput::make('valor_total_desconto')
                                    ->extraInputAttributes(['tabindex' => 1, 'style' => 'font-weight: bolder; font-size: 1rem; color: #17863E;'])
                                    ->label('Valor Total com Desconto')
                                    // ->currencyMask(thousandSeparator: '.',decimalSeparator: ',',precision: 2)
                                    ->numeric()
                                    ->readOnly()
                                    ->required(false),
                                Forms\Components\Textarea::make('obs')
                                    ->autosize()
                                    ->columnSpanFull()
                                    ->label('Observações'),

                                Fieldset::make('Financeiro')
                                    ->schema([
                                        Grid::make([
                                            'xl' => 4,
                                            '2xl' => 4,
                                        ])
                                            ->schema([
                                                Forms\Components\Toggle::make('status_financeiro')
                                                    ->live()
                                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                                    ->afterStateUpdated(
                                                        function (Get $get, Set $set, $state) {
                                                            if ($state == true) {
                                                                $set('valor_total_financeiro', ((float)$get('valor_total_desconto')));
                                                            } else {
                                                                $set('valor_parcela_financeiro', 0);
                                                                $set('parcelas_financeiro', ' ');
                                                                $set('formaPgmto_financeiro', '');
                                                                $set('valor_total_financeiro', 0);
                                                            }
                                                        }
                                                    )
                                                    ->columnSpan([
                                                        'xl' => 2,
                                                        '2xl' => 2,
                                                    ])
                                                    ->label('Desejar lançar no financeiro?'),
                                                Forms\Components\Toggle::make('status_pago_financeiro')
                                                    ->hidden(fn (Get $get): bool => !$get('status_financeiro'))
                                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                                    ->live()
                                                    ->afterStateUpdated(
                                                        function (Get $get, Set $set, $state) {
                                                            if ($state == true) {
                                                                $set('parcelas_financeiro', 1);
                                                                $set('valor_parcela_financeiro', ((float)$get('valor_total_desconto')));
                                                            } else {
                                                                $set('valor_parcela_financeiro', '');
                                                                $set('parcelas_financeiro', ' ');
                                                                $set('formaPgmto_financeiro', '');
                                                            }
                                                        }


                                                    )
                                                    ->label('Recebido'),
                                                Forms\Components\TextInput::make('parcelas_financeiro')
                                                    ->hidden(fn (Get $get): bool => !$get('status_financeiro'))
                                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(
                                                        function (Get $get, Set $set) {
                                                            $set('valor_parcela_financeiro', ((float)($get('valor_total_financeiro') / $get('parcelas_financeiro'))));
                                                        }
                                                    )
                                                    ->numeric()
                                                    ->label('Qtd Parcelas')
                                                    ->required(fn (Get $get): bool => $get('status_financeiro')),
                                                Forms\Components\Select::make('formaPgmto_financeiro')
                                                    ->hidden(fn (Get $get): bool => !$get('status_financeiro'))
                                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                                    ->default(4)
                                                    ->label('Forma de Pagamento')
                                                    ->required(fn (Get $get): bool => $get('status_financeiro'))
                                                    ->options([
                                                        1 => 'Dinheiro',
                                                        2 => 'Pix',
                                                        3 => 'Cartão',
                                                        4 => 'Boleto',
                                                    ]),
                                                Forms\Components\DatePicker::make('data_vencimento_financeiro')
                                                    ->hidden(fn (Get $get): bool => !$get('status_financeiro'))
                                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                                    ->required(fn (Get $get): bool => $get('status_financeiro'))
                                                    ->displayFormat('d/m/Y')
                                                    ->default(Carbon::now())
                                                    ->label("Vencimento da 1º"),

                                                Forms\Components\TextInput::make('valor_parcela_financeiro')
                                                    ->hidden(fn (Get $get): bool => !$get('status_financeiro'))
                                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                                    ->numeric()
                                                    ->label('Valor da Parcela')
                                                    ->readOnly()
                                                    ->required(false),
                                                Forms\Components\TextInput::make('valor_total_financeiro')
                                                    ->hidden(fn (Get $get): bool => !$get('status_financeiro'))
                                                    ->disabled(fn (string $context): bool => $context === 'edit')
                                                    ->default(function (Get $get) {
                                                        return 200;
                                                    })
                                                    ->numeric()
                                                    ->label('Valor Total')
                                                    ->readOnly()
                                                    ->required(false),
                                            ])


                                    ]),

                            ]),
                    ]),

                Fieldset::make('Ocorrências da Locação')
                    ->schema([
                        Repeater::make('ocorrencia')
                            ->label('Ocorrências')
                            ->schema([
                                Grid::make([
                                    'xl' => 3,
                                    '2xl' => 3,
                                ])
                                    ->schema([
                                        Select::make('tipo')
                                            ->options([
                                                'multa' => 'Multa',
                                                'colisao' => 'Colisão',
                                                'avaria' => 'Avaria',
                                                'danos_terceiros' => 'Danos a Terceiros',
                                                'outro' => 'Outros',
                                            ]),

                                        DateTimePicker::make('data_hora'),
                                        TextInput::make('valor'),
                                        Textarea::make('descricao')
                                            ->columnSpan(2)
                                            ->autosize()
                                            ->label('Descrição'),

                                        ToggleButtons::make('status')
                                            ->label('Concluído?')
                                            ->default(false)
                                            ->boolean()
                                            ->grouped()


                                    ])
                            ])
                            ->columnSpanFull()
                            ->addActionLabel('Novo')
                    ]),
                ToggleButtons::make('status')
                    ->options([
                        '0' => 'Locado',
                        '1' => 'Finalizar',

                    ])
                    ->colors([
                        '0' => 'danger',
                        '1' => 'success',
                    ])
                    ->inline()
                    ->default(0)
                    ->label('Status'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable()
                    ->label('ID'),
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
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('hora_saida')
                    ->date('H:m')
                    ->sortable()
                    ->label('Hora Saída'),
                Tables\Columns\TextColumn::make('data_retorno')
                    ->label('Data Retorno')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('hora_retorno')
                    ->date('H:m')
                    ->label('Hora Retorno'),
                Tables\Columns\TextColumn::make('Km_Percorrido')
                    ->label('Km Total')
                    ->getStateUsing(function (Locacao $record): int {

                        return ($record->km_retorno - $record->km_saida);
                    }),
                Tables\Columns\TextColumn::make('qtd_diarias')
                    ->label('Qtd Diárias'),
                // Tables\Columns\TextColumn::make('valor_total')
                //     ->money('BRL')
                //     ->label('Valor Total'),
                // Tables\Columns\TextColumn::make('valor_desconto')
                //     ->money('BRL')
                //     ->label('Desconto'),
                Tables\Columns\TextColumn::make('valor_total_desconto')
                    ->summarize(Sum::make()->money('BRL')->label('Total'))
                    ->money('BRL')
                    ->label('Valor Total'),
                Tables\Columns\TextColumn::make('status')
                    ->summarize(Count::make())
                    ->Label('Status')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                    })
                    ->formatStateUsing(function ($state) {
                        if ($state == 0) {
                            return 'Locado';
                        }
                        if ($state == 1) {
                            return 'Finalizada';
                        }
                    }),
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
                Filter::make('Locados')
                    ->query(fn (Builder $query): Builder => $query->where('status', false))
                    ->default(1),
                SelectFilter::make('cliente')->searchable()->relationship('cliente', 'nome'),
                SelectFilter::make('veiculo')->searchable()->relationship('veiculo', 'placa'),
                Tables\Filters\Filter::make('datas')
                    ->form([
                        DatePicker::make('data_saida_de')
                            ->label('Saída de:'),
                        DatePicker::make('data_saida_ate')
                            ->label('Saída ate:'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['data_saida_de'],
                                fn ($query) => $query->whereDate('data_saida', '>=', $data['data_saida_de'])
                            )
                            ->when(
                                $data['data_saida_ate'],
                                fn ($query) => $query->whereDate('data_saida', '<=', $data['data_saida_ate'])
                            );
                    })

            ])
            ->actions([
                Tables\Actions\Action::make('Imprimir')
                    ->url(fn (Locacao $record): string => route('imprimirLocacao', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()
                    ->modalHeading('Editar locação')
                    ->after(function ($data) {
                        if ($data['status'] == 1) {
                            $veiculo = Veiculo::find($data['veiculo_id']);
                            $veiculo->km_atual = $data['km_retorno'];
                            $veiculo->status_locado = 0;
                            $veiculo->save();
                        }
                    }),
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
            'index' => Pages\ManageLocacaos::route('/'),
        ];
    }
}
