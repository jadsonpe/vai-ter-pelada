<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\Pelada;
use App\Models\PeladaMembro;
use App\Models\PeladaSolicitacao;
use App\Models\Notificacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SolicitacaoController extends Controller
{
    public function index(Pelada $pelada): View
    {
        $this->authorizeOwner($pelada);

        return view('organizador.solicitacoes.index', [
            'pelada' => $pelada,
            'solicitacoes' => $pelada->solicitacoes()->with('user')->latest()->get(),
        ]);
    }

    public function aprovar(PeladaSolicitacao $solicitacao): RedirectResponse
    {
        $this->authorizeOwner($solicitacao->pelada);

        $tipoSolicitacao = $solicitacao->tipo_solicitacao ?: ($solicitacao->tipo === 'entrada' ? 'entrar_pelada' : 'virar_mensalista');
        $tipoMembro = $tipoSolicitacao === 'entrar_pelada' ? 'diarista' : 'mensalista';

        PeladaMembro::updateOrCreate(
            ['pelada_id' => $solicitacao->pelada_id, 'user_id' => $solicitacao->user_id],
            [
                'tipo' => $tipoMembro,
                'status' => 'ativo',
                'data_entrada' => now()->toDateString(),
                'mensalista_desde' => $tipoMembro === 'mensalista' ? now()->toDateString() : null,
            ]
        );

        $solicitacao->update([
            'status' => 'aprovada',
            'avaliado_por' => auth()->id(),
            'avaliado_em' => now(),
            'respondido_por' => auth()->id(),
            'respondido_em' => now(),
        ]);

        Notificacao::create([
            'user_id' => $solicitacao->user_id,
            'titulo' => 'Solicitacao aprovada',
            'mensagem' => 'Seu pedido em '.$solicitacao->pelada->nome.' foi aprovado.',
            'link' => route('peladas.show', $solicitacao->pelada),
        ]);

        return back()->with('status', 'Solicitacao aprovada.');
    }

    public function recusar(PeladaSolicitacao $solicitacao): RedirectResponse
    {
        $this->authorizeOwner($solicitacao->pelada);
        $solicitacao->update([
            'status' => 'recusada',
            'avaliado_por' => auth()->id(),
            'avaliado_em' => now(),
            'respondido_por' => auth()->id(),
            'respondido_em' => now(),
        ]);

        Notificacao::create([
            'user_id' => $solicitacao->user_id,
            'titulo' => 'Solicitacao recusada',
            'mensagem' => 'Seu pedido em '.$solicitacao->pelada->nome.' foi recusado.',
            'link' => route('peladas.show', $solicitacao->pelada),
        ]);

        return back()->with('status', 'Solicitacao recusada.');
    }

    private function authorizeOwner(Pelada $pelada): void
    {
        $this->redirectIfNotPeladaOwner($pelada);
    }
}
