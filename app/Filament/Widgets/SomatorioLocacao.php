<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SomatorioLocacao extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getCards(): array
    {
        $now = Carbon::now();
        $ano = $now->year;
        $mes = $now->month;
        $hoje = $now->toDateString();

        $cacheKey = "locacoes_somatorio_{$ano}_{$mes}_{$hoje}";

        $totals = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($ano, $mes, $hoje) {
            return DB::table('locacaos')
                ->select(
                    DB::raw('COALESCE(SUM(valor_total_desconto), 0) as total_geral'),
                    DB::raw("COALESCE(SUM(CASE WHEN YEAR(data_saida) = {$ano} AND MONTH(data_saida) = {$mes} THEN valor_total_desconto ELSE 0 END), 0) as total_mes"),
                    DB::raw("COALESCE(SUM(CASE WHEN DATE(data_saida) = '{$hoje}' THEN valor_total_desconto ELSE 0 END), 0) as total_dia")
                )
                ->first();
        });

        return [
            Stat::make('Total de Locações', number_format($totals->total_geral, 2, ",", "."))
                ->description('Todo Período')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total de Locações', number_format($totals->total_mes, 2, ",", "."))
                ->description('Este mês')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total de Locações', number_format($totals->total_dia, 2, ",", "."))
                ->description('Hoje')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}