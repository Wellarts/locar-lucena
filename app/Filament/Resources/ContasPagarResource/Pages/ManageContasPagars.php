<?php

namespace App\Filament\Resources\ContasPagarResource\Pages;

use App\Filament\Resources\ContasPagarResource;
use App\Models\ContasPagar;
use App\Models\FluxoCaixa;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageContasPagars extends ManageRecords
{
    protected static string $resource = ContasPagarResource::class;

    protected static ?string $title = 'Contas a Pagar';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Lançar Conta')
                ->after(
                    function ($data, $record) {
                        if ($record->parcelas > 1) {
                            $valor_parcela = ($record->valor_total / $record->parcelas);
                            $vencimentos = Carbon::create($record->data_vencimento);
                            for ($cont = 1; $cont < $data['parcelas']; $cont++) {
                                $dataVencimentos = $vencimentos->addDays($data['proxima_parcela']);
                                $parcelas = [
                                    'fornecedor_id' => $data['fornecedor_id'],
                                    'valor_total' => $data['valor_total'],
                                    'parcelas' => $data['parcelas'],
                                    'formaPgmto' => $data['formaPgmto'],
                                    'ordem_parcela' => $cont + 1,
                                    'data_vencimento' => $dataVencimentos,
                                    'valor_pago' => 0.00,
                                    'status' => 0,
                                    'obs' => $data['obs'],
                                    'valor_parcela' => $valor_parcela,
                                ];
                                ContasPagar::create($parcelas);
                            }
                        } else {
                            if (($data['status'] == 1)) {
                                $addFluxoCaixa = [
                                    'valor' => ($record->valor_total * -1),
                                    'tipo'  => 'DEBITO',
                                    'obs'   => 'Pagamento da conta do fornecedor '.$record->fornecedor->nome.'',
                                ];

                                FluxoCaixa::create($addFluxoCaixa);
                            }
                        }
                    }
                ),

        ];
    }
}
