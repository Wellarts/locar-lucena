<?php

namespace App\Filament\Pages;

use App\Models\CustoVeiculo;
use App\Models\Locacao;
use App\Models\Veiculo;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Cache;
use Leandrocfe\FilamentPtbrFormFields\Money;

class LucroVeiculo extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.lucro-veiculo';
    protected static ?string $title = 'Lucratividade por Veículo';
    protected static ?string $navigationGroup = 'Consultas';
    
    public array $data = [];
    private $cacheKey = 'veiculos_placa_id';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                DatePicker::make('inicio')
                    ->label('Data de Início')
                    ->reactive()
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->calcularLucro($get, $set)),
                
                DatePicker::make('fim')
                    ->label('Data de Fim')
                    ->reactive()
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->calcularLucro($get, $set)),
                
                Select::make('veiculo_id')
                    ->searchable()
                    ->options($this->getVeiculosCache())
                    ->reactive()
                    ->label('Veículo')
                    ->afterStateUpdated(fn (Get $get, Set $set) => $this->calcularLucro($get, $set)),
                
                Money::make('total_locacao')
                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                    ->readOnly()
                    ->label('Total de Locação R$:'),
                
                Money::make('total_custo')
                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                    ->readOnly()
                    ->label('Total de Custos R$:'),
                
                Money::make('lucro')
                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                    ->readOnly()
                    ->label('Lucro Real R$:'),
            ])->columns(2)->inlineLabel();
    }

    /**
     * Obtém veículos do cache ou do banco de dados
     */
    private function getVeiculosCache(): array
    {
        return Cache::remember($this->cacheKey, 3600, function () {
            return Veiculo::select('id', 'placa')
                ->orderBy('placa')
                ->pluck('placa', 'id')
                ->toArray();
        });
    }

    /**
     * Calcula o lucro de forma otimizada
     */
    private function calcularLucro(Get $get, Set $set): void
    {
        $veiculoId = $get('veiculo_id');
        $inicio = $get('inicio');
        $fim = $get('fim');

        // Validação básica
        if (!$veiculoId || !$inicio || !$fim) {
            $this->resetValores($set);
            return;
        }

        // Usando chunk para processamento mais eficiente em grandes datasets
        $totalLocacao = $this->calcularTotalLocacao($veiculoId, $inicio, $fim);
        $totalCusto = $this->calcularTotalCusto($veiculoId, $inicio, $fim);

        $set('total_locacao', $totalLocacao);
        $set('total_custo', $totalCusto);
        $set('lucro', $totalLocacao - $totalCusto);
    }

    /**
     * Calcula total de locação com otimização de consulta
     */
    private function calcularTotalLocacao(int $veiculoId, string $inicio, string $fim): float
    {
        return Locacao::where('veiculo_id', $veiculoId)
            ->whereBetween('data_saida', [$inicio, $fim])
            ->selectRaw('COALESCE(SUM(valor_total_desconto), 0) as total')
            ->value('total') ?? 0;
    }

    /**
     * Calcula total de custo com otimização de consulta
     */
    private function calcularTotalCusto(int $veiculoId, string $inicio, string $fim): float
    {
        return CustoVeiculo::where('veiculo_id', $veiculoId)
            ->whereBetween('data', [$inicio, $fim])
            ->selectRaw('COALESCE(SUM(valor), 0) as total')
            ->value('total') ?? 0;
    }

    /**
     * Reseta os valores dos campos
     */
    private function resetValores(Set $set): void
    {
        $set('total_locacao', 0);
        $set('total_custo', 0);
        $set('lucro', 0);
    }
}