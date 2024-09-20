<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FluxoCaixaResource\Pages;
use App\Filament\Resources\FluxoCaixaResource\RelationManagers;
use App\Models\FluxoCaixa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\Filter;

class FluxoCaixaResource extends Resource
{
    protected static ?string $model = FluxoCaixa::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = 'Fluxo de Caixa';

    protected static ?string $navigationGroup = 'Financeiro';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tipo')
                    ->options([
                        'CREDITO' => 'CREDITO',
                        'DEBITO' => 'DEBITO',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('valor')
                    ->numeric()
                    ->required(),

                Forms\Components\Textarea::make('obs')
                    ->label('Descrição')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tipo')
                    ->searchable()
                    ->badge()
                    ->color(static function ($state): string {
                        if ($state === 'CREDITO') {
                            return 'success';
                        }

                        return 'danger';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor')
                    ->summarize(Sum::make()->money('BRL')->label('Total'))
                    ->money('BRL'),
                Tables\Columns\TextColumn::make('obs')
                    ->label('Descrição')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data Hora')
                    ->dateTime()
                    ->sortable(),
                // ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('CREDITO')
                    ->label('Crédito')
                    ->query(fn(Builder $query): Builder => $query->where('tipo', 'CREDITO')),
                Filter::make('DEBITO')
                    ->label('Débito')
                    ->query(fn(Builder $query): Builder => $query->where('tipo', 'DEBITO')),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('data_de')
                            ->label('Data de:'),
                        Forms\Components\DatePicker::make('data_ate')
                            ->label('Data até:'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['data_de'],
                                fn($query) => $query->whereDate('created_at', '>=', $data['data_de'])
                            )
                            ->when(
                                $data['data_ate'],
                                fn($query) => $query->whereDate('created_at', '<=', $data['data_ate'])
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Editar lançamento de caixa'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFluxoCaixas::route('/'),
        ];
    }
}
