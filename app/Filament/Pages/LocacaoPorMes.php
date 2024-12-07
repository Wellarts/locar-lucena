<?php

namespace App\Filament\Pages;

use App\Models\Locacao;
use App\Models\Temp_lucratividade;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class LocacaoPorMes extends Page implements HasTable
{
    #teste
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.locacao-por-mes';

    protected static ?string $navigationGroup = 'Consultas';

    protected static ?string $title = 'Faturamento Mensal';

    public static function shouldRegisterNavigation(): bool
        {
            return true;
        }

        public function mount()
        {
            Temp_lucratividade::truncate();

            $Locacoes = Locacao::all();

            foreach($Locacoes as $Locacao){

                $valorLocacaoDia = ($Locacao->valor_total_desconto / $Locacao->qtd_diarias);

               // dd($valorLocacaoDia);
                $dataDiarias = Carbon::create($Locacao->data_saida)->addDay(1);
                    for($x=1;$x<=$Locacao->qtd_diarias;$x++){

                        $addLocacaoDia = [
                            'cliente_id'  => $Locacao->cliente_id,
                            'veiculo_id'  => $Locacao->veiculo_id,
                            'data_saida'  => $dataDiarias,
                            'qtd_diaria'  => 1,
                            'valor_diaria'  => $valorLocacaoDia,
                        ];

                        Temp_lucratividade::create($addLocacaoDia);
                        $dataDiarias = Carbon::create($dataDiarias)->addDay(1);
                    }
            }


        }

    public function table(Table $table): Table
    {
        return $table
            ->query(Temp_lucratividade::query())
            ->description('Esta consulta divide as locações pela quantidade de diárias e coloca cada diária da locação
        na data dos dias em sequência a partir da data da saída, assim teremos o valor da diária por data da utilização do véiculo.')
          //  ->defaultGroup('data_venda','year')
            ->columns([
                            TextColumn::make('cliente.nome')
                                ->sortable()
                                ->searchable(),
                            TextColumn::make('veiculo.modelo')
                                ->sortable()
                                ->searchable()
                                ->label('Veículo'),
                            TextColumn::make('veiculo.placa')
                                ->searchable()
                                 ->label('Placa'),
                            TextColumn::make('data_saida')
                                ->label('Data Saída')
                                ->date('d/m/Y')
                                ->sortable()
                                ->alignCenter(),
                            TextColumn::make('qtd_diaria')
                                ->Summarize(Count::make('qtd_diaria')->label('Total de Diárias'))
                                ->alignCenter()
                                ->label('Qtd Diárias'),
                            TextColumn::make('valor_diaria')
                                ->summarize(Sum::make()->money('BRL')->label('Total'))
                                ->money('BRL')
                                ->label('Valor Total'),


            ])
            ->filters([
                SelectFilter::make('cliente')->searchable()->relationship('cliente', 'nome'),
                SelectFilter::make('veiculo')->searchable()->relationship('veiculo', 'placa'),
                Tables\Filters\Filter::make('datas')
                   ->form([
                       DatePicker::make('data_saida_de')
                           ->label('Saída de:'),
                       DatePicker::make('data_saida_ate')
                           ->label('Saída ate:'),
                   ])
                   ->query(function ($query, array $data) {
                       return $query
                           ->when($data['data_saida_de'],
                               fn($query) => $query->whereDate('data_saida', '>=', $data['data_saida_de']))
                           ->when($data['data_saida_ate'],
                               fn($query) => $query->whereDate('data_saida', '<=', $data['data_saida_ate']));
                  })

                ])
            ->bulkActions([

                ExportBulkAction::make(),
            ]);
    }
}
