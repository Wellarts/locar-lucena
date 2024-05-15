<?php

namespace App\Filament\Pages;

use App\Models\Veiculo;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Facades\FilamentIcon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Support\Facades\Route;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $routePath = '/';

    protected static ?int $navigationSort = -2;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::pages.dashboard';

    /*    public function mount() {
        Notification::make()
            ->title('ATENÇÃO')
            ->persistent()
            ->danger()
            ->body('Sua mensalidade está atrasada, regularize sua assinatura para evitar o bloqueio do sistema.
            PIX: 28708223831')
            ->actions([
                Action::make('Entendi')
                    ->button()
                    ->close(),
                ])
            ->send();
    } */

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ??
            static::$title ??
            __('filament-panels::pages/dashboard.title');
    }

    public static function getNavigationIcon(): ?string
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve('panels::pages.dashboard.navigation-item')
            ?? (Filament::hasTopNavigation() ? 'heroicon-m-home' : 'heroicon-o-home');
    }

    public static function routes(Panel $panel): void
    {
        Route::get(static::getRoutePath(), static::class)
            ->middleware(static::getRouteMiddleware($panel))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->name(static::getSlug());
    }

    public static function getRoutePath(): string
    {
        return static::$routePath;
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return Filament::getWidgets();
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getVisibleWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getWidgets());
    }

    /**
     * @return int | string | array<string, int | string | null>
     */
    public function getColumns(): int | string | array
    {
        return 2;
    }

    public function getTitle(): string | Htmlable
    {
        return static::$title ?? __('filament-panels::pages/dashboard.title');
    }

    public function mount(): void
    {

        $dados = new LocacaoPorMes();
        $dados->mount();

        $veiculos = Veiculo::all();

        foreach ($veiculos as $veiculo) {
            if ($veiculo->status_alerta == 1 and $veiculo->status == 1) {
                if ($veiculo->km_atual >= $veiculo->aviso_troca_oleo) {
                    Notification::make()
                        ->title('ATENÇÃO: Veículos com troca de óleo próxima')
                        ->body('Veiculo: ' . $veiculo->modelo . ' Placa: ' . $veiculo->placa)
                        ->danger()
                        ->persistent()
                        ->send();
                }

                if ($veiculo->km_atual >= $veiculo->aviso_troca_filtro) {
                    Notification::make()
                        ->title('ATENÇÃO: Veículos com troca do filtro próxima')
                        ->body('Veiculo: ' . $veiculo->modelo . ' Placa: ' . $veiculo->placa)
                        ->danger()
                        ->persistent()
                        ->send();
                }

                if ($veiculo->km_atual >= $veiculo->aviso_troca_correia) {
                    Notification::make()
                        ->title('ATENÇÃO: Veículos com troca da correia próxima')
                        ->body('Veiculo: ' . $veiculo->modelo . ' Placa: ' . $veiculo->placa)
                        ->danger()
                        ->persistent()
                        ->send();
                }

                if ($veiculo->km_atual >= $veiculo->aviso_troca_pastilha) {
                    Notification::make()
                        ->title('ATENÇÃO: Veículos com troca da pastilha próxima')
                        ->body('Veiculo: ' . $veiculo->modelo . ' Placa: ' . $veiculo->placa)
                        ->danger()
                        ->persistent()
                        ->send();
                }
            }
        }
    }
}
