<?php

namespace App\Filament\Widgets;

use App\Models\Veiculo;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TotalLucratividade extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $totais = Cache::remember('dashboard_lucratividade', 300, function () {
            return DB::table('veiculos as v')
                ->select([
                    DB::raw('COUNT(DISTINCT v.id) as total_veiculos'),
                    DB::raw('COALESCE(SUM(l.valor_total_desconto), 0) as total_locacoes'),
                    DB::raw('COALESCE(SUM(cv.valor), 0) as total_custos'),
                    DB::raw('COALESCE(SUM(l.valor_total_desconto), 0) - COALESCE(SUM(cv.valor), 0) as lucro_total')
                ])
                ->leftJoin('locacaos as l', 'v.id', '=', 'l.veiculo_id')
                ->leftJoin('custo_veiculos as cv', 'v.id', '=', 'cv.veiculo_id')
                ->first();
        });

        return [
            // Stat::make('Veículos Ativos', $totais->total_veiculos)
            //     ->description('Total cadastrados')
            //     ->icon('heroicon-o-truck')
            //     ->color('primary'),
            
            Stat::make('Faturamento Total', number_format($totais->total_locacoes, 2, ',', '.'))
                ->description('Com locações')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            
            Stat::make('Custos Totais', number_format($totais->total_custos, 2, ',', '.'))
                ->description('Manutenções e despesas')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('danger'),
            
            Stat::make('Lucro Líquido', number_format($totais->lucro_total, 2, ',', '.'))
                ->description('Faturamento - Custos')
                ->icon('heroicon-o-chart-bar')
                ->color($totais->lucro_total >= 0 ? 'success' : 'danger'),
        ];
    }
}