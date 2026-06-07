<?php

namespace App\Http\Controllers\Jogador;

use App\Http\Controllers\Controller;
use App\Models\Pelada;
use App\Models\PeladaJogo;
use App\Models\PeladaJogoParticipante;
use App\Models\PeladaMembro;
use App\Models\PeladaSolicitacao;
use App\Models\Notificacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeladaController extends Controller
{
    public function minhas(Request $request): View
    {
        $membrosQuery = $request->user()->memberships()
            ->with([
                'pelada.esporte',
                'pelada.jogos' => fn ($query) => $query
                    ->whereIn('status', ['aberto', 'fechado'])
                    ->where('data_hora', '>=', now()->startOfDay())
                    ->orderBy('data_hora'),
                'pelada.jogos.participantes' => fn ($query) => $query->where('user_id', $request->user()->id),
            ]);

        if ($request->filled('q')) {
            $search = $request->input('q');

            $membrosQuery->whereHas('pelada', function ($query) use ($search) {
                $query->where('nome', 'like', "%{$search}%")
                    ->orWhere('local_nome', 'like', "%{$search}%")
                    ->orWhere('local', 'like', "%{$search}%")
                    ->orWhere('cidade', 'like', "%{$search}%")
                    ->orWhere('bairro', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo') && in_array($request->input('tipo'), ['mensalista', 'diarista'], true)) {
            $membrosQuery->where('tipo', $request->input('tipo'));
        }

        if ($request->filled('status') && in_array($request->input('status'), ['ativo', 'pendente', 'bloqueado', 'saiu', 'inativo'], true)) {
            $membrosQuery->where('status', $request->input('status'));
        }

        $solicitacoesBase = PeladaSolicitacao::with('pelada')
            ->where('user_id', $request->user()->id)
            ->latest();

        $convitesQuery = (clone $solicitacoesBase)
            ->where('tipo_solicitacao', 'like', 'convite_%');

        if ($request->filled('convite_status') && in_array($request->input('convite_status'), ['pendente', 'aprovada', 'recusada'], true)) {
            $convitesQuery->where('status', $request->input('convite_status'));
        }

        $solicitacoesQuery = (clone $solicitacoesBase)
            ->where(function ($query) {
                $query->whereNull('tipo_solicitacao')
                    ->orWhere('tipo_solicitacao', 'not like', 'convite_%');
            });

        if ($request->filled('solicitacao_status') && in_array($request->input('solicitacao_status'), ['pendente', 'aprovada', 'recusada'], true)) {
            $solicitacoesQuery->where('status', $request->input('solicitacao_status'));
        }

        return view('jogador.minhas-peladas', [
            'membros' => $membrosQuery->get(),
            'convites' => $convitesQuery->get(),
            'solicitacoes' => $solicitacoesQuery->get(),
            'filtros' => $request->only(['q', 'tipo', 'status', 'convite_status', 'solicitacao_status']),
        ]);
    }

    public function confirmar(Request $request, PeladaJogo $jogo): RedirectResponse
    {
        $user = $request->user();
        $membro = PeladaMembro::where('pelada_id', $jogo->pelada_id)
            ->where('user_id', $user->id)
            ->where('status', 'ativo')
            ->first();

        if (! $membro) {
            return redirect()
                ->route('peladas.show', $jogo->pelada)
                ->with('status', 'Você precisa ser aceito como diarista ou mensalista antes de confirmar presença.');
        }

        $capacidade = $jogo->vagas_totais ?: $jogo->capacidade ?: $jogo->pelada->vagas_totais ?: $jogo->pelada->capacidade;
        $confirmados = $jogo->participantes()->where('status', 'confirmado')->count();
        $status = $confirmados < $capacidade ? 'confirmado' : 'fila';
        $posicao = null;

        if ($status === 'fila' && $membro->tipo === 'mensalista') {
            $diaristaConfirmado = $jogo->participantes()
                ->where('status', 'confirmado')
                ->where('tipo', 'diarista')
                ->latest('confirmado_em')
                ->first();

            if ($diaristaConfirmado) {
                $diaristaConfirmado->update([
                    'status' => 'fila',
                    'posicao_fila' => (int) $jogo->participantes()->where('status', 'fila')->max('posicao_fila') + 1,
                ]);
                $status = 'confirmado';
            }
        }

        if ($status === 'fila') {
            $posicao = (int) $jogo->participantes()->where('status', 'fila')->max('posicao_fila') + 1;
        }

        PeladaJogoParticipante::updateOrCreate(
            ['pelada_jogo_id' => $jogo->id, 'user_id' => $user->id],
            [
                'pelada_membro_id' => $membro->id,
                'tipo' => $membro->tipo,
                'tipo_no_jogo' => $membro->tipo,
                'status' => $status,
                'posicao_fila' => $posicao,
                'ordem_chegada' => (int) $jogo->participantes()->max('ordem_chegada') + 1,
                'confirmado_em' => now(),
                'cancelado_em' => null,
            ]
        );

        return back()->with('status', $status === 'confirmado' ? 'Presença confirmada.' : 'Pelada lotada: você entrou na fila.');
    }

    public function cancelar(Request $request, PeladaJogo $jogo): RedirectResponse
    {
        $participacao = $jogo->participantes()->where('user_id', $request->user()->id)->firstOrFail();
        $eraConfirmado = $participacao->status === 'confirmado';

        $participacao->update([
            'status' => 'cancelado',
            'cancelado_em' => now(),
            'posicao_fila' => null,
        ]);

        if ($eraConfirmado) {
            $this->promoverFila($jogo);
        }

        return back()->with('status', 'Presença cancelada.');
    }

    public function aceitarConvite(Request $request, PeladaSolicitacao $solicitacao): RedirectResponse
    {
        abort_unless($solicitacao->user_id === $request->user()->id, 403);
        abort_unless($solicitacao->status === 'pendente' && str_starts_with($solicitacao->tipo_solicitacao, 'convite_'), 404);

        $tipoMembro = str($solicitacao->tipo_solicitacao)->after('convite_')->toString();

        PeladaMembro::updateOrCreate(
            ['pelada_id' => $solicitacao->pelada_id, 'user_id' => $request->user()->id],
            [
                'tipo' => $tipoMembro,
                'status' => 'ativo',
                'data_entrada' => now()->toDateString(),
                'mensalista_desde' => $tipoMembro === 'mensalista' ? now()->toDateString() : null,
                'observacao' => 'Convite aceito pelo jogador',
            ]
        );

        $solicitacao->update([
            'status' => 'aprovada',
            'respondido_por' => $request->user()->id,
            'respondido_em' => now(),
        ]);

        Notificacao::create([
            'user_id' => $solicitacao->pelada->organizador_id,
            'titulo' => 'Convite aceito',
            'mensagem' => $request->user()->name.' aceitou o convite para '.$solicitacao->pelada->nome.'.',
            'link' => route('organizador.peladas.membros.index', $solicitacao->pelada),
        ]);

        return back()->with('status', 'Convite aceito. Voce agora participa desta pelada.');
    }

    public function recusarConvite(Request $request, PeladaSolicitacao $solicitacao): RedirectResponse
    {
        abort_unless($solicitacao->user_id === $request->user()->id, 403);
        abort_unless($solicitacao->status === 'pendente' && str_starts_with($solicitacao->tipo_solicitacao, 'convite_'), 404);

        $solicitacao->update([
            'status' => 'recusada',
            'respondido_por' => $request->user()->id,
            'respondido_em' => now(),
        ]);

        Notificacao::create([
            'user_id' => $solicitacao->pelada->organizador_id,
            'titulo' => 'Convite recusado',
            'mensagem' => $request->user()->name.' recusou o convite para '.$solicitacao->pelada->nome.'.',
            'link' => route('organizador.peladas.membros.index', $solicitacao->pelada),
        ]);

        return back()->with('status', 'Convite recusado.');
    }

    public function solicitarMensalista(Request $request, Pelada $pelada): RedirectResponse
    {
        $user = $request->user();

        if ($pelada->organizador_id === $user->id) {
            PeladaMembro::updateOrCreate(
                ['pelada_id' => $pelada->id, 'user_id' => $user->id],
                [
                    'tipo' => 'mensalista',
                    'status' => 'ativo',
                    'prioridade' => 100,
                    'data_entrada' => now()->toDateString(),
                    'mensalista_desde' => now()->toDateString(),
                    'observacao' => 'Organizador da pelada',
                ]
            );

            return back()->with('status', 'Voce e o organizador desta pelada e já esta cadastrado como mensalista.');
        }

        $membro = PeladaMembro::where('pelada_id', $pelada->id)->where('user_id', $user->id)->first();

        if ($membro && $membro->tipo === 'mensalista' && $membro->status === 'ativo') {
            return back()->with('status', 'Você já participa desta pelada como mensalista.');
        }

        $tipoSolicitacao = $membro ? 'virar_mensalista' : 'entrar_pelada';
        $tipoLegado = 'mensalista';

        if ($tipoSolicitacao === 'entrar_pelada' && blank($user->phone)) {
            $data = $request->validate([
                'phone' => ['required', 'string', 'max:30'],
                'mensagem' => ['nullable', 'string'],
            ]);

            $user->update(['phone' => $data['phone']]);
        } else {
            $request->validate([
                'mensagem' => ['nullable', 'string'],
            ]);
        }

        $pendente = PeladaSolicitacao::where('pelada_id', $pelada->id)
            ->where('user_id', $user->id)
            ->where('status', 'pendente')
            ->where('tipo_solicitacao', $tipoSolicitacao)
            ->exists();

        if ($pendente) {
            return back()->with('status', 'Você já possui uma solicitacao pendente para esta pelada.');
        }

        PeladaSolicitacao::create([
            'pelada_id' => $pelada->id,
            'user_id' => $user->id,
            'tipo' => $tipoLegado,
            'tipo_solicitacao' => $tipoSolicitacao,
            'status' => 'pendente',
            'mensagem' => $request->input('mensagem'),
        ]);

        Notificacao::create([
            'user_id' => $pelada->organizador_id,
            'titulo' => 'Nova solicitacao na pelada',
            'mensagem' => $user->name.' pediu para '.($tipoSolicitacao === 'entrar_pelada' ? 'participar da pelada' : 'virar mensalista').'. WhatsApp: '.($user->phone ?: 'nao informado'),
            'link' => route('organizador.peladas.solicitacoes.index', $pelada),
        ]);

        return back()->with('status', $tipoSolicitacao === 'entrar_pelada'
            ? 'Pedido para participar enviado ao organizador.'
            : 'Pedido para virar mensalista enviado ao organizador.');
    }

    private function promoverFila(PeladaJogo $jogo): void
    {
        $proximo = $jogo->participantes()
            ->where('status', 'fila')
            ->orderByRaw("tipo = 'mensalista' desc")
            ->orderBy('ordem_chegada')
            ->orderBy('posicao_fila')
            ->first();

        if ($proximo) {
            $proximo->update([
                'status' => 'confirmado',
                'posicao_fila' => null,
                'confirmado_em' => now(),
            ]);
        }
    }
}
