<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Esporte;
use App\Models\Pelada;
use App\Models\PeladaJogo;
use App\Models\Patrocinador;
use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicController extends Controller
{
    private const SEARCH_COLUMNS = ['nome', 'descricao', 'local_nome', 'endereco', 'bairro', 'cidade'];

    public function home(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'dashboard');
        }

        return view('public.home', [
            'banners' => Cache::remember('public.home.banners', now()->addMinutes(10), fn () => Banner::where('ativo', true)->latest()->get()),
            'peladas' => Cache::remember('public.home.peladas.football', now()->addMinutes(5), fn () => Pelada::with(['esporte', 'organizador'])->whereHas('esporte', fn ($query) => $query->permitidos())->where('ativa', true)->latest()->take(6)->get()),
            'patrocinadores' => Cache::remember('public.home.patrocinadores', now()->addMinutes(30), fn () => Patrocinador::where('ativo', true)->get()),
        ]);
    }

    public function peladas(Request $request): View
    {
        $peladasQuery = Pelada::with(['esporte', 'organizador'])
            ->whereHas('esporte', fn ($query) => $query->permitidos())
            ->where('ativa', true)
            ->where('status', 'ativa');

        // Search text (nome, descricao, local, bairro, cidade)
        if ($request->filled('q')) {
            $q = $request->q;
            $peladasQuery->where(function ($query) use ($q) {
                if ($this->supportsFullText()) {
                    $query->whereFullText(self::SEARCH_COLUMNS, $q);
                } else {
                    $query->where('nome', 'like', "%{$q}%")
                        ->orWhere('descricao', 'like', "%{$q}%")
                        ->orWhere('local_nome', 'like', "%{$q}%")
                        ->orWhere('endereco', 'like', "%{$q}%")
                        ->orWhere('bairro', 'like', "%{$q}%")
                        ->orWhere('cidade', 'like', "%{$q}%");
                }
            });
        }

        // Price filtering (mensalista / diarista / ambos)
        $priceType = $request->get('price_type', 'both');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');

        if ($priceMin !== null || $priceMax !== null) {
            $min = $priceMin !== null ? (float) $priceMin : 0;
            $max = $priceMax !== null ? (float) $priceMax : PHP_FLOAT_MAX;

            if ($priceType === 'mensalista') {
                $peladasQuery->whereBetween('valor_mensalista', [$min, $max]);
            } elseif ($priceType === 'diarista') {
                $peladasQuery->whereBetween('valor_diarista', [$min, $max]);
            } else {
                $peladasQuery->where(function ($query) use ($min, $max) {
                    $query->whereBetween('valor_mensalista', [$min, $max])
                        ->orWhereBetween('valor_diarista', [$min, $max]);
                });
            }
        }

        if ($request->filled('cidade')) {
            $peladasQuery->where('cidade', $request->cidade);
        }

        if ($request->filled('bairro')) {
            $peladasQuery->where('bairro', $request->bairro);
        }

        if ($request->filled('esporte_id')) {
            $peladasQuery->where('esporte_id', $request->esporte_id);
        }

        if ($request->filled('categoria')) {
            $peladasQuery->where('categoria', $request->categoria);
        }

        // Sorting
        $sort = $request->get('sort');
        if ($sort === 'price_asc') {
            if ($priceType === 'diarista') {
                $peladasQuery->orderBy('valor_diarista');
            } elseif ($priceType === 'mensalista') {
                $peladasQuery->orderBy('valor_mensalista');
            } else {
                $peladasQuery->orderByRaw(
                    'CASE
                        WHEN valor_mensalista IS NULL THEN valor_diarista
                        WHEN valor_diarista IS NULL THEN valor_mensalista
                        WHEN valor_mensalista <= valor_diarista THEN valor_mensalista
                        ELSE valor_diarista
                     END'
                );
            }
        } elseif ($sort === 'price_desc') {
            if ($priceType === 'diarista') {
                $peladasQuery->orderByDesc('valor_diarista');
            } elseif ($priceType === 'mensalista') {
                $peladasQuery->orderByDesc('valor_mensalista');
            } else {
                $peladasQuery->orderByRaw(
                    'CASE
                        WHEN valor_mensalista IS NULL THEN valor_diarista
                        WHEN valor_diarista IS NULL THEN valor_mensalista
                        WHEN valor_mensalista >= valor_diarista THEN valor_mensalista
                        ELSE valor_diarista
                     END DESC'
                );
            }
        }

        $rodadasQuery = PeladaJogo::with(['pelada.esporte', 'pelada.organizador'])
            ->withCount([
                'participantes as confirmados_count' => fn ($query) => $query->where('status', 'confirmado'),
            ])
            ->whereIn('status', ['aberto', 'fechado'])
            ->whereBetween('data_hora', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->whereHas('pelada', function ($query) use ($request) {
                $query->where('ativa', true)->where('status', 'ativa');
                $query->whereHas('esporte', fn ($esporteQuery) => $esporteQuery->permitidos());

                if ($request->filled('cidade')) {
                    $query->where('cidade', $request->cidade);
                }

                if ($request->filled('bairro')) {
                    $query->where('bairro', $request->bairro);
                }

                if ($request->filled('esporte_id')) {
                    $query->where('esporte_id', $request->esporte_id);
                }

                if ($request->filled('categoria')) {
                    $query->where('categoria', $request->categoria);
                }

                if ($request->filled('q')) {
                    $q = $request->q;
                    $query->where(function ($q2) use ($q) {
                        if ($this->supportsFullText()) {
                            $q2->whereFullText(self::SEARCH_COLUMNS, $q);
                        } else {
                            $q2->where('nome', 'like', "%{$q}%")
                                ->orWhere('descricao', 'like', "%{$q}%")
                                ->orWhere('local_nome', 'like', "%{$q}%")
                                ->orWhere('endereco', 'like', "%{$q}%")
                                ->orWhere('bairro', 'like', "%{$q}%")
                                ->orWhere('cidade', 'like', "%{$q}%");
                        }
                    });
                }

                if (($request->input('price_min') !== null) || ($request->input('price_max') !== null)) {
                    $priceType = $request->get('price_type', 'both');
                    $min = $request->input('price_min') !== null ? (float) $request->input('price_min') : 0;
                    $max = $request->input('price_max') !== null ? (float) $request->input('price_max') : PHP_FLOAT_MAX;

                    if ($priceType === 'mensalista') {
                        $query->whereBetween('valor_mensalista', [$min, $max]);
                    } elseif ($priceType === 'diarista') {
                        $query->whereBetween('valor_diarista', [$min, $max]);
                    } else {
                        $query->where(function ($q3) use ($min, $max) {
                            $q3->whereBetween('valor_mensalista', [$min, $max])
                                ->orWhereBetween('valor_diarista', [$min, $max]);
                        });
                    }
                }
            });

        return view('public.peladas', [
            'peladas' => $peladasQuery->latest()->paginate(12)->withQueryString(),
            'rodadas' => $rodadasQuery->orderBy('data_hora')->paginate(6)->withQueryString(),
            'esportes' => Cache::remember('public.filters.esportes.football', now()->addHour(), fn () => Esporte::permitidos()->where('ativo', true)->orderBy('nome')->get()),
            'cidades' => Cache::remember('public.filters.cidades.football', now()->addHour(), fn () => Pelada::whereHas('esporte', fn ($query) => $query->permitidos())->where('ativa', true)->whereNotNull('cidade')->distinct()->orderBy('cidade')->pluck('cidade')),
            'bairros' => Cache::remember('public.filters.bairros.football', now()->addHour(), fn () => Pelada::whereHas('esporte', fn ($query) => $query->permitidos())->where('ativa', true)->whereNotNull('bairro')->distinct()->orderBy('bairro')->pluck('bairro')),
            'categorias' => Pelada::CATEGORIAS,
            'filtros' => $request->only(['cidade', 'bairro', 'esporte_id', 'categoria', 'q', 'price_type', 'price_min', 'price_max', 'sort']),
        ]);
    }

    private function supportsFullText(): bool
    {
        return Cache::remember('database.peladas.supports_fulltext', now()->addDay(), function () {
            $driver = DB::getDriverName();

            if (!in_array($driver, ['mysql', 'mariadb'], true)) {
                return false;
            }

            try {
                $dbName = DB::connection()->getDatabaseName();
                $columnPlaceholders = implode(', ', array_fill(0, count(self::SEARCH_COLUMNS), '?'));

                $result = DB::selectOne(
                    "SELECT 1 AS supported
                     FROM information_schema.STATISTICS
                     WHERE table_schema = ?
                        AND table_name = ?
                        AND index_type = 'FULLTEXT'
                     GROUP BY index_name
                     HAVING COUNT(*) = ?
                        AND SUM(column_name IN ({$columnPlaceholders})) = ?
                     LIMIT 1",
                    [
                        $dbName,
                        'peladas',
                        count(self::SEARCH_COLUMNS),
                        ...self::SEARCH_COLUMNS,
                        count(self::SEARCH_COLUMNS),
                    ]
                );

                return (bool) $result;
            } catch (\Throwable $e) {
                return false;
            }
        });
    }

    public function pelada(Pelada $pelada): View
    {
        $user = Auth::user();
        $rodadas = $pelada->jogos()
            ->with(['participantes.user'])
            ->whereIn('status', ['aberto', 'fechado'])
            ->where('data_hora', '>=', now()->startOfDay())
            ->orderBy('data_hora')
            ->take(5)
            ->get();

        $pelada->load(['esporte', 'organizador']);
        $membrosAtivosCount = $pelada->membros()->where('status', 'ativo')->count();
        $membrosPreview = $pelada->membros()
            ->with('user.playerProfile')
            ->where('status', 'ativo')
            ->orderBy('prioridade')
            ->take(12)
            ->get();

        return view('public.pelada', [
            'pelada' => $pelada,
            'rodadas' => $rodadas,
            'membrosAtivosCount' => $membrosAtivosCount,
            'membrosPreview' => $membrosPreview,
            'membro' => $user ? $pelada->membros()->where('user_id', $user->id)->first() : null,
            'isOwner' => $user && $pelada->organizador_id === $user->id,
            'solicitacaoPendente' => $user
                ? $pelada->solicitacoes()
                    ->where('user_id', $user->id)
                    ->where('status', 'pendente')
                    ->latest()
                    ->first()
                : null,
            'reportReasons' => Report::reasonsFor('pelada'),
        ]);
    }

    public function peladaPublic(Pelada $pelada): View|RedirectResponse
    {
        abort_unless($pelada->ativa && $pelada->status === 'ativa', 404);

        if (Auth::check()) {
            return redirect()->route('peladas.show', $pelada);
        }

        $pelada->load(['esporte', 'organizador']);
        $rodadas = $pelada->jogos()
            ->whereIn('status', ['aberto', 'fechado'])
            ->where('data_hora', '>=', now()->startOfDay())
            ->orderBy('data_hora')
            ->take(3)
            ->get();

        return view('public.pelada-share', [
            'pelada' => $pelada,
            'rodadas' => $rodadas,
            'membrosAtivosCount' => $pelada->membros()->where('status', 'ativo')->count(),
            'shareUrl' => route('peladas.public.show', $pelada),
        ]);
    }

    public function ranking(): View
    {
        return view('public.ranking', [
            'jogadores' => User::withCount(['participacoes' => fn ($query) => $query->where('status', '=', 'confirmado')])
                ->withAvg('avaliacoesRecebidas', 'estrelas')
                ->withCount('avaliacoesRecebidas')
                ->where('role', '=', 'jogador')
                ->orderByDesc('avaliacoes_recebidas_avg_estrelas')
                ->orderByDesc('participacoes_count')
                ->paginate(15),
            'weeklyLeaderboard' => User::withSum(['userPoints as weekly_points' => fn ($query) => $query->where('created_at', '>=', now()->subWeek())], 'valor')
                ->where('role', 'jogador')
                ->orderByDesc('weekly_points')
                ->take(10)
                ->get(),
            'monthlyLeaderboard' => User::withSum(['userPoints as monthly_points' => fn ($query) => $query->where('created_at', '>=', now()->subMonth())], 'valor')
                ->where('role', 'jogador')
                ->orderByDesc('monthly_points')
                ->take(10)
                ->get(),
        ]);
    }

    public function jogador(User $user): View
    {
        $user->load(['esportePerfis.esporte', 'badges']);

        return view('public.jogador', [
            'jogador' => $user,
            'presencasConfirmadas' => $user->participacoes()->where('status', 'confirmado')->count(),
            'peladasAtivas' => $user->memberships()->where('status', 'ativo')->count(),
        ]);
    }

    public function termos(): View
    {
        return view('public.termos');
    }

    public function privacidade(): View
    {
        return view('public.privacidade');
    }
}
