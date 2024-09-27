<?php

namespace App\Filament\Widgets;

use App\Models\ContasPagar;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ContasPagarHoje extends BaseWidget
{
  //  protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Para Pagar Hoje';

    protected static ?int $sort = 7;

    public function table(Table $table): Table
    {
        $ano = date('Y');
        $mes = date('m');
        $dia = date('d');

        return $table
            ->query(
                ContasPagar::query()->where('status', 0)->whereYear('data_vencimento', $ano)->whereMonth('data_vencimento', $mes)->whereDay('data_vencimento', $dia)
            )
            ->columns([
                Tables\Columns\TextColumn::make('fornecedor.nome')
                ->sortable(),

            Tables\Columns\TextColumn::make('ordem_parcela')
                ->alignCenter()
                ->label('Parcela Nº'),
            Tables\Columns\TextColumn::make('data_vencimento')
                ->label('Vencimento')
                ->sortable()
                ->alignCenter()
                ->badge()
                ->color('danger')
                ->date(),
         /*   Tables\Columns\TextColumn::make('valor_total')
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
                ]), */



            Tables\Columns\TextColumn::make('valor_parcela')
                ->label('Valor Parcela')
                ->summarize(Sum::make()->money('BRL')->label('Total'))
                ->alignCenter()
                ->badge()
                ->color('danger')
                ->money('BRL'),

            ]);
    }
}
