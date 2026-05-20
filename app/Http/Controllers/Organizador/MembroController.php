<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\Pelada;
use App\Models\PeladaMembro;
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
            'jogadores' => User::where('role', 'jogador')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, Pelada $pelada): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'tipo' => ['required', 'in:mensalista,diarista'],
            'status' => ['required', 'in:ativo,inativo,bloqueado'],
        ]);

        PeladaMembro::updateOrCreate(
            ['pelada_id' => $pelada->id, 'user_id' => $data['user_id']],
            $data + ['mensalista_desde' => $data['tipo'] === 'mensalista' ? now()->toDateString() : null]
        );

        return back()->with('status', 'Membro salvo.');
    }

    public function destroy(Pelada $pelada, PeladaMembro $membro): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        $membro->delete();

        return back()->with('status', 'Membro removido.');
    }

    private function authorizeOwner(Pelada $pelada): void
    {
        abort_unless(auth()->user()->isAdmin() || $pelada->organizador_id === auth()->id(), 403);
    }
}
