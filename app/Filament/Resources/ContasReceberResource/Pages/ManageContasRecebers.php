<?php

namespace App\Filament\Resources\ContasReceberResource\Pages;

use App\Filament\Resources\ContasReceberResource;
use App\Models\ContasReceber;
use App\Models\FluxoCaixa;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageContasRecebers extends ManageRecords
{
    protected static string $resource = ContasReceberResource::class;

    protected static ?string $title = 'Contas a Receber';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Lançar Conta')
            ->after(function ($data, $record) {
              if($record->parcelas > 1)
                {
                    $valor_parcela = ($record->valor_total / $record->parcelas);
                    $vencimentos = Carbon::create($record->data_vencimento);
                    for($cont = 1; $cont < $data['parcelas']; $cont++)
                    {
                                        $dataVencimentos = $vencimentos->addDays($data['proxima_parcela']);
                                        $parcelas = [
                                        'cliente_id' => $data['cliente_id'],
                                        'valor_total' => $data['valor_total'],
                                        'parcelas' => $data['parcelas'],
                                        'formaPgmto' => $data['formaPgmto'],
                                        'ordem_parcela' => $cont+1,
                                        'data_vencimento' => $dataVencimentos,
                                        'valor_recebido' => 0.00,
                                        'status' => 0,
                                        'obs' => $data['obs'],
                                        'valor_parcela' => $valor_parcela,
                                        ];
                            ContasReceber::create($parcelas);
                    }

                }
                else
                {
                   if(($data['status'] == 1))
                   {
                    $addFluxoCaixa = [
                        'valor' => ($record->valor_total),
                        'tipo'  => 'CREDITO',
                        'obs'   => 'Recebimento da conta do cliente '.$record->cliente->nome.'',
                    ];

                    FluxoCaixa::create($addFluxoCaixa);

                   }

                }


            }
        ),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
         //   ContasReceberStats::class,
        ];
    }
}

