<?php

namespace App\Filament\Widgets;

use App\Models\ContasPagar;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ContasPagarHoje extends BaseWidget
{
    protected static ?string $heading = 'Para Pagar Hoje/Vencidas';

    protected static ?int $sort = 7;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ContasPagar::query()
                    ->where('status', 0)
                    ->whereDate('data_vencimento', '<=', now()->toDateString())
                    ->with([
                        'fornecedor' => function ($q) {
                            $q->select('id', 'nome');
                        },
                    ])
                    ->select([
                        'id',
                        'fornecedor_id',
                        'ordem_parcela',
                        'data_vencimento',
                        'valor_parcela',
                    ])
                    ->orderBy('data_vencimento', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('fornecedor.nome')
                    ->label('Fornecedor')
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
                    ->date('d/m/Y'),
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
