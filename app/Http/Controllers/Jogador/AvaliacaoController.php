<?php

namespace App\Http\Controllers\Jogador;

use App\Http\Controllers\Controller;
use App\Models\AvaliacaoPartida;
use App\Models\Notificacao;
use App\Models\PeladaJogo;
use App\Models\PeladaJogoParticipante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvaliacaoController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $jogos = PeladaJogo::with(['pelada.esporte', 'participantes.user'])
            ->whereBetween('data_hora', [now()->subDays(3), now()])
            ->where('data_hora', '<=', now())
            ->whereHas('participantes', fn ($query) => $query->where('user_id', $user->id)->where('presente_local', true))
            ->get();

        $pendingGames = $jogos->map(function (PeladaJogo $jogo) use ($user) {
            $avaliados = $jogo->participantes
                ->filter(fn ($participante) => $participante->user_id && $participante->user_id !== $user->id)
                ->filter(fn ($participante) => $participante->presente_local)
                ->reject(fn ($participante) => AvaliacaoPartida::where('pelada_jogo_id', $jogo->id)
                    ->where('avaliador_id', $user->id)
                    ->where('avaliado_id', $participante->user_id)
                    ->exists())
                ->values();

            return (object) [
                'jogo' => $jogo,
                'avaliados' => $avaliados,
            ];
        })->filter(fn ($item) => $item->avaliados->isNotEmpty())->values();

        return view('jogador.avaliacoes.index', [
            'pendingGames' => $pendingGames,
            'recebidas' => $user->avaliacoesRecebidas()
                ->with(['avaliador', 'jogo.pelada'])
                ->latest()
                ->take(8)
                ->get(),
            'feitas' => $user->avaliacoesFeitas()
                ->with(['avaliado', 'jogo.pelada'])
                ->latest()
                ->take(8)
                ->get(),
            'mediaRecebida' => $user->rating_average,
            'totalRecebidas' => $user->rating_count,
            'totalFeitas' => $user->avaliacoesFeitas()->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pelada_jogo_id' => ['required', 'exists:pelada_jogos,id'],
            'avaliado_id' => ['required', 'exists:users,id'],
            'estrelas' => ['required', 'integer', 'between:1,5'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $jogo = PeladaJogo::findOrFail($data['pelada_jogo_id']);

        if (! $jogo->data_hora->between(now()->subDays(3), now())) {
            return back()->with('status', 'Avaliações só podem ser feitas até 3 dias após a partida.');
        }

        $participacao = PeladaJogoParticipante::where('pelada_jogo_id', $jogo->id)
            ->where('user_id', $user->id)
            ->where('presente_local', true)
            ->first();

        if (! $participacao) {
            return back()->with('status', 'Somente jogadores presentes na partida podem avaliar.');
        }

        if ($data['avaliado_id'] === $user->id) {
            return back()->with('status', 'Você não pode se avaliar.');
        }

        $avaliadoParticipacao = PeladaJogoParticipante::where('pelada_jogo_id', $jogo->id)
            ->where('user_id', $data['avaliado_id'])
            ->where('presente_local', true)
            ->first();

        if (! $avaliadoParticipacao) {
            return back()->with('status', 'O jogador avaliado precisa estar presente e cadastrado no sistema.');
        }

        if (AvaliacaoPartida::where('pelada_jogo_id', $jogo->id)
            ->where('avaliador_id', $user->id)
            ->where('avaliado_id', $data['avaliado_id'])
            ->exists()) {
            return back()->with('status', 'Você já avaliou este jogador para esta partida.');
        }

        $avaliacao = AvaliacaoPartida::create([
            'pelada_jogo_id' => $jogo->id,
            'avaliador_id' => $user->id,
            'avaliado_id' => $data['avaliado_id'],
            'estrelas' => $data['estrelas'],
            'comentario' => $data['comentario'],
        ]);

        $user->addPoints(5, 'avaliou', 'Avaliação emitida para partida ' . $jogo->titulo, 'jogo:' . $jogo->id);

        $avaliado = $avaliadoParticipacao->user;
        if ($avaliado) {
            if ($data['estrelas'] === 5) {
                $avaliado->addPoints(10, 'recebeu_5', 'Recebeu avaliação 5 estrelas', 'jogo:' . $jogo->id);
            }

            $avaliado->refreshBadges();
                Notificacao::create([
                'user_id' => $avaliado->id,
                'titulo' => 'Você recebeu uma nova avaliação',
                'mensagem' => sprintf('%s avaliou você com %s estrelas.', $user->name, $data['estrelas']),
                'link' => route('perfil.edit'),
            ]);
        }

        $user->refreshBadges();

        return back()->with('status', 'Avaliação enviada com sucesso.');
    }
}
