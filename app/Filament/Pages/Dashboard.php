<?php

namespace App\Filament\Pages;

use App\Models\ContasPagar;
use App\Models\ContasReceber;
use App\Models\Veiculo;
use Carbon\Carbon;
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
        // -------------------------
        // ALERTAS DE VEÍCULOS
        // -------------------------
        // Buscar apenas veículos que estão com alertas ativos e que
        // atendem pelo menos uma condição de aviso (óleo, filtro, correia, pastilha)
        $veiculos = Veiculo::select([
                'id',
                'modelo',
                'placa',
                'prox_troca_oleo',
                'prox_troca_filtro',
                'prox_troca_correia',
                'prox_troca_pastilha',
                'km_atual',
                'aviso_troca_oleo',
                'aviso_troca_filtro',
                'aviso_troca_correia',
                'aviso_troca_pastilha',
            ])
            ->where('status_alerta', 1)
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereRaw('prox_troca_oleo - km_atual <= aviso_troca_oleo')
                  ->orWhereRaw('prox_troca_filtro - km_atual <= aviso_troca_filtro')
                  ->orWhereRaw('prox_troca_correia - km_atual <= aviso_troca_correia')
                  ->orWhereRaw('prox_troca_pastilha - km_atual <= aviso_troca_pastilha');
            })
            ->get();

        // Agrupar por tipo de alerta e montar mensagens compactas (uma notificação por tipo)
        $alerts = [
            'oleo' => [],
            'filtro' => [],
            'correia' => [],
            'pastilha' => [],
        ];

        foreach ($veiculos as $v) {
            $diffOleo = $v->prox_troca_oleo - $v->km_atual;
            if ($diffOleo <= $v->aviso_troca_oleo) {
                $alerts['oleo'][] = "{$v->modelo} ({$v->placa}) - Faltam {$diffOleo} Km";
            }

            $diffFiltro = $v->prox_troca_filtro - $v->km_atual;
            if ($diffFiltro <= $v->aviso_troca_filtro) {
                $alerts['filtro'][] = "{$v->modelo} ({$v->placa}) - Faltam {$diffFiltro} Km";
            }

            $diffCorreia = $v->prox_troca_correia - $v->km_atual;
            if ($diffCorreia <= $v->aviso_troca_correia) {
                $alerts['correia'][] = "{$v->modelo} ({$v->placa}) - Faltam {$diffCorreia} Km";
            }

            $diffPastilha = $v->prox_troca_pastilha - $v->km_atual;
            if ($diffPastilha <= $v->aviso_troca_pastilha) {
                $alerts['pastilha'][] = "{$v->modelo} ({$v->placa}) - Faltam {$diffPastilha} Km";
            }
        }

        // Envia uma notificação por tipo (se houver)
        if (!empty($alerts['oleo'])) {
            Notification::make()
                ->title('ATENÇÃO: Troca de óleo próxima')
                ->body(implode("\n", array_slice($alerts['oleo'], 0, 10)))
                ->danger()
                ->persistent()
                ->send();
        }

        if (!empty($alerts['filtro'])) {
            Notification::make()
                ->title('ATENÇÃO: Troca de filtro próxima')
                ->body(implode("\n", array_slice($alerts['filtro'], 0, 10)))
                ->danger()
                ->persistent()
                ->send();
        }

        if (!empty($alerts['correia'])) {
            Notification::make()
                ->title('ATENÇÃO: Troca da correia próxima')
                ->body(implode("\n", array_slice($alerts['correia'], 0, 10)))
                ->danger()
                ->persistent()
                ->send();
        }

        if (!empty($alerts['pastilha'])) {
            Notification::make()
                ->title('ATENÇÃO: Troca da pastilha próxima')
                ->body(implode("\n", array_slice($alerts['pastilha'], 0, 10)))
                ->danger()
                ->persistent()
                ->send();
        }

        // -------------------------
        // CONTAS A RECEBER
        // -------------------------
        $hoje = Carbon::today();
        $ate3dias = (clone $hoje)->addDays(3);

        // Buscar apenas os registros relevantes e já com relação carregada
        $contasReceberProximas = ContasReceber::select(['id', 'cliente_id', 'valor_parcela', 'data_vencimento'])
            ->where('status', '=', '0')
            ->whereBetween('data_vencimento', [$hoje->toDateString(), $ate3dias->toDateString()])
            ->with('cliente:id,nome')
            ->get();

        $contasReceberHoje = ContasReceber::select(['id', 'cliente_id', 'valor_parcela', 'data_vencimento'])
            ->where('status', '=', '0')
            ->whereDate('data_vencimento', $hoje->toDateString())
            ->with('cliente:id,nome')
            ->get();

        $contasReceberVencidas = ContasReceber::select(['id', 'cliente_id', 'valor_parcela', 'data_vencimento'])
            ->where('status', '=', '0')
            ->whereDate('data_vencimento', '<', $hoje->toDateString())
            ->with('cliente:id,nome')
            ->get();

        foreach ($contasReceberProximas as $cr) {
            Notification::make()
                ->title('ATENÇÃO: Conta a receber com vencimento próximo.')
                ->body('Do cliente <b>' . $cr->cliente->nome . '</b> no valor de R$ <b>' . $cr->valor_parcela . '</b> com vencimento em <b>' . Carbon::parse($cr->data_vencimento)->format('d/m/Y') . '</b>.')
                ->success()
                ->persistent()
                ->send();
        }

        foreach ($contasReceberHoje as $cr) {
            Notification::make()
                ->title('ATENÇÃO: Conta a receber com vencimento para hoje.')
                ->body('Do cliente <b>' . $cr->cliente->nome . '</b> no valor de R$ <b>' . $cr->valor_parcela . '</b> com vencimento em <b>' . Carbon::parse($cr->data_vencimento)->format('d/m/Y') . '</b>.')
                ->warning()
                ->persistent()
                ->send();
        }

        foreach ($contasReceberVencidas as $cr) {
            Notification::make()
                ->title('ATENÇÃO: Conta a receber vencida.')
                ->body('Do cliente <b>' . $cr->cliente->nome . '</b> no valor de R$ <b>' . $cr->valor_parcela . '</b> com vencimento em <b>' . Carbon::parse($cr->data_vencimento)->format('d/m/Y') . '</b>.')
                ->danger()
                ->persistent()
                ->send();
        }

        // -------------------------
        // CONTAS A PAGAR
        // -------------------------
        $contasPagarProximas = ContasPagar::select(['id', 'fornecedor_id', 'valor_parcela', 'data_vencimento'])
            ->where('status', '=', '0')
            ->whereBetween('data_vencimento', [$hoje->toDateString(), $ate3dias->toDateString()])
            ->with('fornecedor:id,nome')
            ->get();

        $contasPagarHoje = ContasPagar::select(['id', 'fornecedor_id', 'valor_parcela', 'data_vencimento'])
            ->where('status', '=', '0')
            ->whereDate('data_vencimento', $hoje->toDateString())
            ->with('fornecedor:id,nome')
            ->get();

        $contasPagarVencidas = ContasPagar::select(['id', 'fornecedor_id', 'valor_parcela', 'data_vencimento'])
            ->where('status', '=', '0')
            ->whereDate('data_vencimento', '<', $hoje->toDateString())
            ->with('fornecedor:id,nome')
            ->get();

        foreach ($contasPagarProximas as $cp) {
            Notification::make()
                ->title('ATENÇÃO: Conta a pagar com vencimento próximo.')
                ->body('Do fornecedor <b>' . $cp->fornecedor->nome . '</b> no valor de R$ <b>' . $cp->valor_parcela . '</b> com vencimento em <b>' . Carbon::parse($cp->data_vencimento)->format('d/m/Y') . '</b>.')
                ->success()
                ->persistent()
                ->send();
        }

        foreach ($contasPagarHoje as $cp) {
            Notification::make()
                ->title('ATENÇÃO: Conta a pagar com vencimento para hoje.')
                ->body('Do fornecedor <b>' . $cp->fornecedor->nome . '</b> no valor de R$ <b>' . $cp->valor_parcela . '</b> com vencimento em <b>' . Carbon::parse($cp->data_vencimento)->format('d/m/Y') . '</b>.')
                ->warning()
                ->persistent()
                ->send();
        }

        foreach ($contasPagarVencidas as $cp) {
            Notification::make()
                ->title('ATENÇÃO: Conta a pagar vencida.')
                ->body('Do fornecedor <b>' . $cp->fornecedor->nome . '</b> no valor de R$ <b>' . $cp->valor_parcela . '</b> com vencimento em <b>' . Carbon::parse($cp->data_vencimento)->format('d/m/Y') . '</b>.')
                ->danger()
                ->persistent()
                ->send();
        }
    }
}
