<?php

namespace App\Filament\Widgets;

use App\Models\Veiculo;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsVeiculo extends BaseWidget
{
    protected static ?int $sort = 1;

    // TTL em segundos para cache (ajuste conforme necessário)
    protected function getCacheTtl(): int
    {
        return 30;
    }

    protected function getStats(): array
    {
        $counts = Cache::remember('stats:veiculo', $this->getCacheTtl(), function () {
            return Veiculo::selectRaw('COUNT(*) AS total_active, SUM(CASE WHEN status_locado = 1 THEN 1 ELSE 0 END) AS locados')
                ->where('status', 1)
                ->first()
                ->toArray();
        });

        $totalActive = (int) ($counts['total_active'] ?? 0);
        $locados = (int) ($counts['locados'] ?? 0);
        $disponiveis = max(0, $totalActive - $locados);

        return [
            Stat::make('Veículos Ativos', $totalActive)
                ->description('Ativos')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),

            Stat::make('Veículos Locados', $locados)
                ->description('Locados')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger'),

            Stat::make('Veículos Disponíveis', $disponiveis)
                ->description('Disponíveis')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}
