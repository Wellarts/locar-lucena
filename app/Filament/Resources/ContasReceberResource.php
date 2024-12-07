<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContasReceberResource\Pages;
use App\Filament\Resources\ContasReceberResource\RelationManagers;
use App\Models\Cliente;
use App\Models\ContasReceber;
use App\Models\FluxoCaixa;
use Carbon\Carbon;
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
use App\Filament\Exports\ContasReceberExporter;
use Filament\Actions\Exports\Enums\ExportFormat;

class ContasReceberResource extends Resource
{
    protected static ?string $model = ContasReceber::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $title = 'Contas a Receber';

    protected static ?string $navigationLabel = 'Contas a Receber';

    protected static ?string $navigationGroup = 'Financeiro';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('cliente_id')
                    ->disabled(function ($context) {
                        if ($context == 'edit') {
                            return true;
                        } else {
                            return false;
                        }
                    })
                    ->label('Cliente')
                    ->searchable()
                    ->options(Cliente::all()->pluck('nome', 'id')->toArray())
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
                    ->default(7)
                    ->label('Próximas Parcelas'),
                Forms\Components\TextInput::make('parcelas')
                    ->hiddenOn('edit')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, callable $set) {
                        if ($get('parcelas') != 1) {
                            $set('valor_parcela', ((float)($get('valor_total') / $get('parcelas'))));
                            $set('status', 0);
                            $set('valor_recebido', 0);
                            $set('data_recebimento', null);
                            $set('data_vencimento',  Carbon::now()->addDays($get('proxima_parcela'))->format('Y-m-d'));
                        } else {

                            $set('valor_parcela', $get('valor_total'));
                            $set('status', 1);
                            $set('valor_recebido', $get('valor_total'));
                            $set('data_recebimento', Carbon::now()->format('Y-m-d'));
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
                    ->displayFormat('d/m/Y')
                    ->default(Carbon::now())
                    ->label("Data do Vencimento"),
                Forms\Components\DatePicker::make('data_recebimento')
                    ->displayFormat('d/m/Y')
                    ->default(Carbon::now())
                    ->label("Data do Recebimento"),
                Forms\Components\Toggle::make('status')
                    ->default('true')
                    ->label('Recebido')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(
                        function (Get $get, callable $set) {
                            if ($get('status') == 1) {
                                $set('valor_recebido', $get('valor_parcela'));
                                $set('data_recebimento',  Carbon::now()->format('Y-m-d'));
                            } else {

                                $set('valor_recebido', 0);
                                $set('data_recebimento', null);
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
                Forms\Components\TextInput::make('valor_recebido')
                    ->label('Valor Recebido')
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
                    ->exporter(ContasReceberExporter::class)
                    ->formats([
                        ExportFormat::Xlsx,
                    ])
                    ->columnMapping(false)
                    ->label('Exportar Contas')
                    ->modalHeading('Confirmar exportação?')
            ])
            ->columns([
                Tables\Columns\TextColumn::make('cliente.nome')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ordem_parcela')
                    ->alignCenter()
                    ->label('Parcela Nº'),
                Tables\Columns\TextColumn::make('data_vencimento')
                    ->label('Data Vencimento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('valor_total')
                    ->label('Valor Total')
                    ->alignCenter()
                    ->badge()
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
                    ->alignCenter()
                    ->badge()
                    ->color('danger')
                    ->money('BRL'),
                Tables\Columns\IconColumn::make('status')
                    ->label('Recebido')
                    ->boolean(),
                Tables\Columns\TextColumn::make('valor_recebido')
                    ->label('Valor Recebido')
                    ->summarize(Sum::make()->money('BRL')->label('Total'))
                    ->label('Valor Recebido')
                    ->alignCenter()
                    ->badge()
                    ->color('warning')
                    ->money('BRL'),
                Tables\Columns\TextColumn::make('data_recebimento')
                    ->label('Data Recebimento')
                    ->date('d/m/Y')
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),
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
                Filter::make('A receber')
                    ->query(fn(Builder $query): Builder => $query->where('status', false))->default(true),
                Filter::make('Recebidas')
                    ->query(fn(Builder $query): Builder => $query->where('status', true)),
                SelectFilter::make('cliente')->relationship('cliente', 'nome')->searchable(),
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
                    ->modalHeading('Editar conta a receber')
                    ->after(function ($data, $record) {

                        if ($record->status == true) {

                            $addFluxoCaixa = [
                                'valor' => ($record->valor_parcela),
                                'tipo'  => 'CREDITO',
                                'obs'   => 'Recebimento da conta do cliente ' . $record->cliente->nome . ' da parcela nº: ' . $record->ordem_parcela . '',
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContasRecebers::route('/'),
        ];
    }
}
