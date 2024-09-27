<?php

namespace App\Filament\Widgets;

use App\Models\CustoVeiculo;
use App\Models\Locacao;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalLucratividade extends BaseWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        return [
            Stat::make('Locações', number_format(Locacao::all()->sum('valor_total_desconto'), 2, ",", "."))
                ->description('Total de Locações')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
            Stat::make('Manutenções', number_format(CustoVeiculo::all()->sum('valor'), 2, ",", "."))
                ->description('Total de Manutenções')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger'),
            Stat::make('Locações - Manutenções', number_format(Locacao::all()->sum('valor_total_desconto') - CustoVeiculo::all()->sum('valor'), 2, ",", "."))
                ->description('Lucratividade')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
