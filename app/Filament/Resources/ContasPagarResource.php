<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ContasPagarExporter;
use App\Filament\Resources\ContasPagarResource\Pages;
use App\Filament\Resources\ContasPagarResource\RelationManagers;
use App\Models\ContasPagar;
use App\Models\FluxoCaixa;
use App\Models\Fornecedor;
use Carbon\Carbon;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ExportAction;


class ContasPagarResource extends Resource
{
    protected static ?string $model = ContasPagar::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $title = 'Contas a Pagar';

    protected static ?string $navigationLabel = 'Contas a Pagar';

    protected static ?string $navigationGroup = 'Financeiro';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('fornecedor_id')
                    ->label('Fornecedor')
                    ->disabled(function ($context) {
                        if ($context == 'edit') {
                            return true;
                        } else {
                            return false;
                        }
                    })

                    ->options(Fornecedor::all()->pluck('nome', 'id')->toArray())
                    ->required(),
                Forms\Components\TextInput::make('valor_total')
                    ->disabled(function ($context) {
                        if ($context == 'edit') {
                            return true;
                        } else {
                            return false;
                        }
                    })
                    ->label('Valor Total')
                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                    ->numeric()
                    ->prefix('R$')
                    ->inputMode('decimal')
                    ->required(),
                Forms\Components\Select::make('proxima_parcela')
                    ->hiddenOn('edit')
                    ->options([
                        '7' => 'Semanal',
                        '15' => 'Quinzenal',
                        '30' => 'Mensal',
                    ])
                    ->default(30)
                    ->label('Próximas Parcelas'),
                Forms\Components\TextInput::make('parcelas')
                    ->hiddenOn('edit')
                    ->live(onBlur: true)
                    ->numeric()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        if ($get('parcelas') != 1) {
                            $set('valor_parcela', (($get('valor_total') / $get('parcelas'))));
                            $set('status', 0);
                            $set('valor_pago', 0);
                            $set('data_pagamento', null);
                            $set('data_vencimento',  Carbon::now()->addDays($get('proxima_parcela'))->format('Y-m-d'));
                        } else {
                            $set('valor_parcela', $get('valor_total'));
                            $set('status', 1);
                            $set('valor_pago', $get('valor_total'));
                            $set('data_pagamento', Carbon::now()->format('Y-m-d'));
                            $set('data_vencimento',  Carbon::now()->format('Y-m-d'));
                        }
                    })
                    ->required(),
                Forms\Components\Select::make('formaPgmto')
                    ->default('2')
                    ->label('Forma de Pagamento')
                    ->required()
                    ->options([
                        1 => 'Dinheiro',
                        2 => 'Pix',
                        3 => 'Cartão',
                        4 => 'Boleto',
                    ]),
                Forms\Components\Hidden::make('ordem_parcela')
                    ->label('Parcela Nº')
                    ->default('1'),

                Forms\Components\DatePicker::make('data_vencimento')
                    ->label('Data Vencimento')
                    ->displayFormat('d/m/Y')
                    ->default(now())
                    ->required(),
                Forms\Components\DatePicker::make('data_pagamento')
                    ->displayFormat('d/m/Y')
                    ->default(now())
                    ->label("Data Pagamento"),
                Forms\Components\Toggle::make('status')
                    ->default('true')
                    ->label('Pago')
                    ->required()
                    ->live()
                    ->afterStateUpdated(
                        function (Get $get, Set $set, $state) {
                            if ($state == true) {
                                $set('valor_pago', $get('valor_parcela'));
                                $set('data_pagamento', Carbon::now()->format('Y-m-d'));
                            } else {

                                $set('valor_pago', 0);
                                $set('data_pagamento', null);
                            }
                        }
                    ),

                Forms\Components\TextInput::make('valor_parcela')
                    ->label('Valor Parcela')
                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                    ->numeric()
                    ->prefix('R$')
                    ->inputMode('decimal')
                    ->required(),
                Forms\Components\TextInput::make('valor_pago')
                    ->label('Valor Pago')
                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                    ->numeric()
                    ->prefix('R$')
                    ->inputMode('decimal'),
                Forms\Components\Textarea::make('obs')
                    ->label('Observações'),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('data_vencimento', 'asc')
            ->headerActions([
                ExportAction::make()
                    ->exporter(ContasPagarExporter::class)
                    ->formats([
                        ExportFormat::Xlsx,
                    ])
                    ->columnMapping(false)
                    ->label('Exportar Contas')
                    ->modalHeading('Confirmar exportação?')
            ])
            ->columns([
                Tables\Columns\TextColumn::make('fornecedor.nome')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ordem_parcela')
                    ->alignCenter()
                    ->label('Parcela Nº'),
                Tables\Columns\TextColumn::make('data_vencimento')
                    ->label('Data Vencimento')
                    ->badge()
                    ->sortable()
                    ->alignCenter()
                    ->color('danger')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('valor_total')
                    ->label('Valor Total')
                    ->badge()
                    ->alignCenter()
                    ->color('success')
                    ->money('BRL'),
                Tables\Columns\SelectColumn::make('formaPgmto')
                    ->Label('Forma de Pagamento')
                    ->disabled()
                    ->options([
                        1 => 'Dinheiro',
                        2 => 'Pix',
                        3 => 'Cartão',
                        4 => 'Boleto',
                    ]),
                Tables\Columns\TextColumn::make('valor_parcela')
                    ->label('Valor Parcela')
                    ->summarize(Sum::make()->money('BRL')->label('Total'))
                    ->badge()
                    ->alignCenter()
                    ->color('danger')
                    ->money('BRL'),
                Tables\Columns\IconColumn::make('status')
                    ->label('Pago')
                    ->boolean(),
                Tables\Columns\TextColumn::make('valor_pago')
                    ->summarize(Sum::make()->money('BRL')->label('Total'))
                    ->badge()
                    ->alignCenter()
                    ->color('warning')
                    ->money('BRL'),
                Tables\Columns\TextColumn::make('data_pagamento')
                    ->label('Data Pagamento')
                    ->alignCenter()
                    ->badge()
                    ->color('warning')
                    ->date('d/m/Y'),
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
                Filter::make('A pagar')
                    ->query(fn(Builder $query): Builder => $query->where('status', false))->default(true),
                Filter::make('Pagas')
                    ->query(fn(Builder $query): Builder => $query->where('status', true)),
                SelectFilter::make('fornecedor')->relationship('fornecedor', 'nome'),
                Tables\Filters\Filter::make('data_vencimento')
                    ->form([
                        Forms\Components\DatePicker::make('vencimento_de')
                            ->label('Vencimento de:'),
                        Forms\Components\DatePicker::make('vencimento_ate')
                            ->label('Vencimento até:'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['vencimento_de'],
                                fn($query) => $query->whereDate('data_vencimento', '>=', $data['vencimento_de'])
                            )
                            ->when(
                                $data['vencimento_ate'],
                                fn($query) => $query->whereDate('data_vencimento', '<=', $data['vencimento_ate'])
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Editar conta a pagar')
                    ->after(function ($data, $record) {

                        if ($record->status = 1) {
                            $addFluxoCaixa = [
                                'valor' => ($record->valor_parcela * -1),
                                'tipo'  => 'DEBITO',
                                'obs'   => 'Pagamento da conta do fornecedor ' . $record->fornecedor->nome . ' da parcela nº: ' . $record->ordem_parcela . '',
                            ];

                            FluxoCaixa::create($addFluxoCaixa);
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
            'index' => Pages\ManageContasPagars::route('/'),
        ];
    }
}
