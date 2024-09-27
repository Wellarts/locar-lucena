<?php

namespace App\Filament\Widgets;

use App\Models\Agendamento;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AgendamentosLocacao extends BaseWidget
{
    protected static ?int $sort = 9;

    protected static ?string $heading = 'Próximos Agendamentos';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Agendamento::query()->where('status', 0)->orderby('data_saida', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('cliente.nome')
                ->sortable()
                ->searchable()
                ->label('Cliente'),
            Tables\Columns\TextColumn::make('veiculo.modelo')
                ->sortable()
                ->searchable()
                ->label('Veículo'),
            Tables\Columns\TextColumn::make('veiculo.placa')
                ->searchable()
                 ->label('Placa'),

            Tables\Columns\TextColumn::make('data_saida')
                ->badge()
                ->label('Data Saída')
                ->date()
                ->color(static function ($state): string {
                    $hoje = Carbon::today();
                    $dataSaida = Carbon::parse($state);
                    $qtd_dias = $hoje->diffInDays($dataSaida, false);
                 //  dd($qtd_dias.' - '.$dataSaida.' - '.$hoje);
                //   echo $qtd_dias;

                    if ($qtd_dias <= 3 && $qtd_dias >= 0) {
                        return 'danger';
                    }

                    if($qtd_dias < 0) {
                        return 'warning';
                    }

                    if($qtd_dias > 3) {
                        return 'success';
                    }



                }),

            Tables\Columns\TextColumn::make('hora_saida')
                ->sortable()
                ->label('Hora Saída'),
            Tables\Columns\TextColumn::make('data_retorno')
                ->label('Data Retorno')
                ->date(),
            Tables\Columns\TextColumn::make('hora_retorno')
                ->label('Hora Retorno'),
            ]);
    }
}
