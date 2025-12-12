<?php

namespace App\Filament\Widgets;

use App\Models\CustoVeiculo;
use App\Models\Locacao;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class TotalLucratividade extends BaseWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        // Cache curto para reduzir carga do BD em acessos frequentes
        $totals = Cache::remember('filament.total_lucratividade', 60, function () {
            $locacoes = (float) Locacao::sum('valor_total_desconto');
            $despesas = (float) CustoVeiculo::sum('valor');

            return [
                'locacoes' => $locacoes,
                'despesas' => $despesas,
                'lucro' => $locacoes - $despesas,
            ];
        });

        return [
            Stat::make('Locações', number_format($totals['locacoes'], 2, ",", "."))
                ->description('Total de Locações')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
            Stat::make('Manutenções/Despesas', number_format($totals['despesas'], 2, ",", "."))
                ->description('Total de Manutenções/Despesas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger'),
            Stat::make('Locações - Manutenções', number_format($totals['lucro'], 2, ",", "."))
                ->description('Lucratividade')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
