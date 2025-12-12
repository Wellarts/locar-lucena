<?php

namespace App\Filament\Widgets;

use App\Models\Locacao;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class VeiculoChegando extends BaseWidget
{
    protected static ?string $heading = 'Próximos Retornos';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $hoje = Carbon::today();

        return $table
            ->query(
                Locacao::query()
                    ->with('veiculo')
                    ->where('status', 0)
                    ->orderBy('data_retorno', 'asc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('veiculo.modelo')
                    ->badge()
                    ->color('warning')
                    ->label('Modelo'),
                Tables\Columns\TextColumn::make('veiculo.placa')
                    ->label('Placa'),
                Tables\Columns\TextColumn::make('veiculo.ano')
                    ->label('Ano'),
                Tables\Columns\TextColumn::make('data_retorno')
                    ->badge()
                    ->label('Data Retorno')
                    ->date('d/m/Y')
                    ->color(function ($state) use ($hoje): string {
                        if (empty($state)) {
                            return 'secondary';
                        }

                        try {
                            $dataRetorno = Carbon::parse($state);
                        } catch (\Throwable $e) {
                            return 'secondary';
                        }

                        $qtd_dias = $hoje->diffInDays($dataRetorno, false);

                        if ($qtd_dias < 0) {
                            return 'danger';
                        }

                        if ($qtd_dias <= 3) {
                            return 'warning';
                        }

                        return 'success';
                    }),
            ])
            ->defaultPaginationPageOption(5);
    }
}
