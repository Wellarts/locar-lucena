<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TotalLucratividade extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

     protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $totais = Cache::remember('dashboard_lucratividade', 300, function () {
            return (object) [
                'total_locacoes' => DB::table('locacaos')->sum('valor_total_desconto'),
                'total_custos' => DB::table('custo_veiculos')->sum('valor'),
                'total_veiculos' => DB::table('veiculos')->count(),
            ];
        });

        // Calcular lucro
        $lucroTotal = $totais->total_locacoes - $totais->total_custos;

        return [
            Stat::make('Faturamento Total', number_format($totais->total_locacoes, 2, ',', '.'))
                ->description('Todas as locações')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            
            Stat::make('Custos Totais', number_format($totais->total_custos, 2, ',', '.'))
                ->description('Manutenções e despesas')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('danger'),
            
            Stat::make('Lucro Líquido', number_format($lucroTotal, 2, ',', '.'))
                ->description('Faturamento - Custos')
                ->icon('heroicon-o-chart-bar')
                ->color($lucroTotal >= 0 ? 'success' : 'danger'),
        ];
    }
}