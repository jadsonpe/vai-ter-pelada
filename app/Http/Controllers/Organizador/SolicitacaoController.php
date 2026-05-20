<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\Pelada;
use App\Models\PeladaMembro;
use App\Models\PeladaSolicitacao;
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

        PeladaMembro::updateOrCreate(
            ['pelada_id' => $solicitacao->pelada_id, 'user_id' => $solicitacao->user_id],
            ['tipo' => 'mensalista', 'status' => 'ativo', 'mensalista_desde' => now()->toDateString()]
        );

        $solicitacao->update([
            'status' => 'aprovada',
            'avaliado_por' => auth()->id(),
            'avaliado_em' => now(),
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
        ]);

        return back()->with('status', 'Solicitacao recusada.');
    }

    private function authorizeOwner(Pelada $pelada): void
    {
        abort_unless(auth()->user()->isAdmin() || $pelada->organizador_id === auth()->id(), 403);
    }
}
