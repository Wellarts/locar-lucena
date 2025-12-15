<?php

namespace App\Filament\Pages;

use App\Models\CustoVeiculo;
use App\Models\Locacao;
use App\Models\Veiculo;
use Filament\Forms\Components\Grid;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Leandrocfe\FilamentPtbrFormFields\Money;

class LucroVeiculo extends Page implements HasForms
{

    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.lucro-veiculo';

    protected static ?string $title = 'Lucratividade por Veículo';

    protected static ?string $navigationGroup = 'Consultas';

    protected static bool $shouldRegisterNavigation = false;

    public array $data = [];


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
                    ->label('Data de Início'),
                DatePicker::make('fim')
                    ->label('Data de Fim'),
                Forms\Components\Select::make('veiculo_id')
                    ->required(false)
                    ->label('Veículo')
                    ->live(onBlur: true)
                    ->relationship(
                        name: 'veiculo',
                        modifyQueryUsing: function (Builder $query, $context) {
                            if ($context === 'create') {
                                $query->where('status', 1)->where('status_locado', 0)->orderBy('modelo')->orderBy('placa');
                            } else {
                                $query->where('status', 1)->orderBy('modelo')->orderBy('placa');
                            }
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->modelo} {$record->placa}")
                    ->searchable(['modelo', 'placa'])
                    ->afterStateUpdated(function (Set $set, $state) {
                        $veiculo = Veiculo::find($state);
                        if ($state != null) {
                            $set('km_saida', $veiculo->km_atual);
                        }
                    })
                    ->columnSpan([
                        'xl' => 2,
                        '2xl' => 2,
                    ]),
                //  Forms\Components\TextInput::make('total_locacao')
                Money::make('total_locacao')
                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                    ->readOnly()
                    //  ->money('BRL')
                    ->label('Total de Locação R$:'),
                Money::make('total_custo')
                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                    ->readOnly()
                    //  ->money('BRL')
                    ->label('Total de Custos R$:'),
                Money::make('lucro')
                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 2)
                    ->readOnly()
                    // ->money('BRL')
                    ->label('Lucro Real R$:'),



            ])->columns(2)->inlineLabel();
    }
}
