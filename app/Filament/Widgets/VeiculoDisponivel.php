<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Veiculo;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class VeiculoDisponivel extends BaseWidget
{
    // protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Veículos Disponíveis';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Seleciona somente as colunas necessárias para a tabela (menos I/O)
                Veiculo::query()
                    ->select(['id', 'modelo', 'cor', 'ano', 'placa'])
                    ->where('status', 1)
                    ->where('status_locado', 0)
                    ->orderBy('modelo', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('modelo')
                    ->badge()
                    ->color('success')
                    ->label('Modelo'),
                Tables\Columns\TextColumn::make('cor'),
                Tables\Columns\TextColumn::make('ano'),
                Tables\Columns\TextColumn::make('placa'),
            ])
            ->defaultPaginationPageOption(5);
    }
}
