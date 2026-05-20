<?php

namespace App\Http\Controllers\Jogador;

use App\Http\Controllers\Controller;
use App\Models\Pelada;
use App\Models\PeladaJogo;
use App\Models\PeladaJogoParticipante;
use App\Models\PeladaMembro;
use App\Models\PeladaSolicitacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeladaController extends Controller
{
    public function minhas(Request $request): View
    {
        return view('jogador.minhas-peladas', [
            'membros' => $request->user()->memberships()->with('pelada.esporte', 'pelada.jogos')->get(),
            'solicitacoes' => PeladaSolicitacao::with('pelada')->where('user_id', $request->user()->id)->latest()->get(),
        ]);
    }

    public function confirmar(Request $request, PeladaJogo $jogo): RedirectResponse
    {
        $user = $request->user();
        $membro = PeladaMembro::firstOrCreate(
            ['pelada_id' => $jogo->pelada_id, 'user_id' => $user->id],
            ['tipo' => 'diarista', 'status' => 'ativo']
        );

        $capacidade = $jogo->capacidade ?: $jogo->pelada->capacidade;
        $confirmados = $jogo->participantes()->where('status', 'confirmado')->count();
        $status = $confirmados < $capacidade ? 'confirmado' : 'fila';
        $posicao = null;

        if ($status === 'fila') {
            $posicao = (int) $jogo->participantes()->where('status', 'fila')->max('posicao_fila') + 1;
        }

        PeladaJogoParticipante::updateOrCreate(
            ['pelada_jogo_id' => $jogo->id, 'user_id' => $user->id],
            [
                'pelada_membro_id' => $membro->id,
                'tipo' => $membro->tipo,
                'status' => $status,
                'posicao_fila' => $posicao,
                'confirmado_em' => now(),
                'cancelado_em' => null,
            ]
        );

        return back()->with('status', $status === 'confirmado' ? 'Presenca confirmada.' : 'Pelada lotada: voce entrou na fila.');
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

        return back()->with('status', 'Presenca cancelada.');
    }

    public function solicitarMensalista(Request $request, Pelada $pelada): RedirectResponse
    {
        PeladaSolicitacao::create([
            'pelada_id' => $pelada->id,
            'user_id' => $request->user()->id,
            'tipo' => 'mensalista',
            'status' => 'pendente',
            'mensagem' => $request->input('mensagem'),
        ]);

        return back()->with('status', 'Solicitacao enviada ao organizador.');
    }

    private function promoverFila(PeladaJogo $jogo): void
    {
        $proximo = $jogo->participantes()
            ->where('status', 'fila')
            ->orderByRaw("tipo = 'mensalista' desc")
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
