<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\Notificacao;
use App\Models\Pelada;
use App\Models\PeladaMembro;
use App\Models\PeladaSolicitacao;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembroController extends Controller
{
    public function index(Pelada $pelada): View
    {
        $this->authorizeOwner($pelada);

        return view('organizador.membros.index', [
            'pelada' => $pelada->load('membros.user'),
        ]);
    }

    public function store(Request $request, Pelada $pelada): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'tipo' => ['required', 'in:mensalista,diarista'],
            'mensagem' => ['nullable', 'string'],
        ]);

        $user = User::where('email', $data['email'])->firstOrFail();

        if ($pelada->membros()->where('user_id', $user->id)->where('status', 'ativo')->exists()) {
            return back()->with('status', 'Este jogador já é membro ativo desta pelada.');
        }

        $tipoSolicitacao = 'convite_'.$data['tipo'];
        $convitePendente = PeladaSolicitacao::where('pelada_id', $pelada->id)
            ->where('user_id', $user->id)
            ->where('status', 'pendente')
            ->where('tipo_solicitacao', $tipoSolicitacao)
            ->exists();

        if ($convitePendente) {
            return back()->with('status', 'Este jogador já possui um convite pendente para esta pelada.');
        }

        PeladaSolicitacao::create([
            'pelada_id' => $pelada->id,
            'user_id' => $user->id,
            'tipo' => 'mensalista',
            'tipo_solicitacao' => $tipoSolicitacao,
            'status' => 'pendente',
            'mensagem' => $data['mensagem'] ?? null,
        ]);

        Notificacao::create([
            'user_id' => $user->id,
            'titulo' => 'Convite para pelada',
            'mensagem' => auth()->user()->name.' convidou você para participar da pelada '.$pelada->nome.' como '.$data['tipo'].'.',
            'link' => route('jogador.peladas.minhas'),
        ]);

        return back()->with(
            'status',
            'Convite enviado para '.$user->name.'. Ele precisa aceitar para entrar na pelada.'
        );
    }

    public function destroy(Pelada $pelada, PeladaMembro $membro): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        abort_unless($membro->pelada_id === $pelada->id, 404);

        $membro->delete();

        return back()->with('status', 'Membro removido da pelada.');
    }

    public function updateMany(Request $request, Pelada $pelada): RedirectResponse
    {
        $this->authorizeOwner($pelada);

        $data = $request->validate([
            'membros' => ['array'],
            'membros.*.apelido' => ['nullable', 'max:255'],
            'membros.*.tipo' => ['required', 'in:mensalista,diarista'],
            'membros.*.status' => ['required', 'in:ativo,pendente,bloqueado,saiu,inativo'],
            'membros.*.prioridade' => ['nullable', 'integer', 'min:0'],
            'membros.*.observacao' => ['nullable'],
        ]);

        foreach ($data['membros'] ?? [] as $membroId => $membroData) {
            PeladaMembro::where('pelada_id', $pelada->id)
                ->where('id', $membroId)
                ->update($membroData);
        }

        return back()->with('status', 'Membros atualizados.');
    }

    private function authorizeOwner(Pelada $pelada): void
    {
        $this->redirectIfNotPeladaOwner($pelada);
    }
}
