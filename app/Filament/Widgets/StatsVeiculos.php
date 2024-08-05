<?php

namespace App\Filament\Widgets;

use App\Models\Veiculo;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsVeiculos extends BaseWidget
{
    protected function getStats(): array
    {
        $ano = date('Y');
        $mes = date('m');
        $dia = date('d');
        // dd($ano);
        return [
            Stat::make('Veículos Ativos', Veiculo::where('status','=',1)->count())
                ->description('Ativos')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
            Stat::make('Veículos Locados', Veiculo::where('status','=',1)->where('status_locado','=',1)->count())
                ->description('Locados')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger'),
            Stat::make('Veículos Disponíveis', Veiculo::where('status','=',1)->where('status_locado','=',0)->count())
                ->description('Disponíveis')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
