<?php

namespace App\Filament\Exports;

use App\Models\Locacao;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;

class LocacaoExporter extends Exporter
{
    protected static ?string $model = Locacao::class;

    public function getXlsxHeaderCellStyle(): ?Style
    {
        return (new Style())
            ->setFontBold()
            // ->setFontItalic()
            ->setFontSize(14)
            ->setFontName('Arial')
            ->setFontColor(Color::rgb(9, 13, 10))
            ->setBackgroundColor(Color::rgb(230, 230, 235))
            ->setCellAlignment(CellAlignment::CENTER)
            ->setCellVerticalAlignment(CellVerticalAlignment::CENTER);
    }

    public function getXlsxCellStyle(): ?Style
    {
        return (new Style())
            ->setFontSize(12)
            ->setFontName('Arial');
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('cliente.nome'),
            ExportColumn::make('veiculo.modelo'),
            ExportColumn::make('veiculo.placa'),
            ExportColumn::make('data_saida')
                ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d/m/Y')),
            ExportColumn::make('hora_saida'),
            ExportColumn::make('data_retorno')
                ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d/m/Y')),
            ExportColumn::make('hora_retorno'),
            ExportColumn::make('km_saida'),
            ExportColumn::make('km_retorno'),
            ExportColumn::make('qtd_diarias'),
            ExportColumn::make('valor_desconto')
            ->formatStateUsing(function ($state) {
                return str_replace('.', ',', $state);
            }),
            ExportColumn::make('valor_total')
            ->formatStateUsing(function ($state) {
                return str_replace('.', ',', $state);
            }),
            ExportColumn::make('valor_total_desconto')
            ->formatStateUsing(function ($state) {
                return str_replace('.', ',', $state);
            }),
            ExportColumn::make('obs'),
            ExportColumn::make('status'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('status_financeiro'),
            ExportColumn::make('status_pago_financeiro'),
            ExportColumn::make('parcelas_financeiro'),
            ExportColumn::make('formaPgmto_financeiro'),
            ExportColumn::make('valor_parcela_financeiro')
            ->formatStateUsing(function ($state) {
                return str_replace('.', ',', $state);
            }),
            ExportColumn::make('valor_total_financeiro')
            ->formatStateUsing(function ($state) {
                return str_replace('.', ',', $state);
            }),
            ExportColumn::make('data_vencimento_financeiro')
                ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d/m/Y')),
            ExportColumn::make('ocorrencia')
                ->listAsJson(),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your locacao export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
