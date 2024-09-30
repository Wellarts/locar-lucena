<?php

namespace App\Filament\Resources\CustoVeiculoResource\Pages;

use App\Filament\Resources\CustoVeiculoResource;
use App\Models\Veiculo;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCustoVeiculos extends ManageRecords
{
    protected static string $resource = CustoVeiculoResource::class;

    protected static ?string $title = 'Despesas/Manutenções';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo')
                ->modalHeading('Criar Despesa/Manutenção')
                ->after(function ($data) {
                    
                        $veiculo = Veiculo::find($data['veiculo_id']);
                        $veiculo->km_atual = $data['km_atual'];
                        $veiculo->save();
                        
                    })
        ];
    }
}
