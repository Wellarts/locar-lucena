<?php

namespace App\Filament\Exports;

use App\Models\CustoVeiculo;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;

class CustoVeiculoExporter extends Exporter
{
    protected static ?string $model = CustoVeiculo::class;

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
            ExportColumn::make('fornecedor.nome'),
            ExportColumn::make('veiculo.modelo'),
            ExportColumn::make('veiculo.placa'),
            ExportColumn::make('km_atual'),
            ExportColumn::make('data')
                ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d/m/Y')),
            ExportColumn::make('descricao'),
            ExportColumn::make('valor')
            ->formatStateUsing(function ($state) {
                return str_replace('.', ',', $state);
            }),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your custo veiculo export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
