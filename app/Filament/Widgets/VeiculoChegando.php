<?php

namespace App\Filament\Widgets;

use App\Models\Locacao;
use App\Models\Veiculo;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class VeiculoChegando extends BaseWidget
{

    protected static ?string $heading = 'Próximos Retornos';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                //  Veiculo::query()->where('status',1)->where('status_locado', 1)->orderby('data_retorno', 'asc')
                Locacao::query()->where('status', 0)->orderby('data_retorno', 'asc')
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
                    ->color(static function ($state): string {
                        $hoje = Carbon::today();
                        $dataRetorno = Carbon::parse($state);
                        $qtd_dias = $hoje->diffInDays($dataRetorno, false);
                     //  dd($qtd_dias.' - '.$dataSaida.' - '.$hoje);
                      // echo $qtd_dias;

                        if ($qtd_dias <= 3 && $qtd_dias >= 0) {
                            return 'warning';
                        }

                        if($qtd_dias < 0) {
                            return 'danger';
                        }

                        if($qtd_dias > 3) {
                            return 'success';
                        }



                    }),

            ])
            ->defaultPaginationPageOption(5);
    }
}
