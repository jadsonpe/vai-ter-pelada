<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\Pelada;
use App\Models\PeladaJogo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JogoController extends Controller
{
    public function index(Pelada $pelada): View
    {
        $this->authorizeOwner($pelada);

        return view('organizador.jogos.index', [
            'pelada' => $pelada->load('jogos.participantes.user'),
        ]);
    }

    public function store(Request $request, Pelada $pelada): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        $data = $request->validate([
            'titulo' => ['required', 'max:255'],
            'data_hora' => ['required', 'date'],
            'capacidade' => ['nullable', 'integer', 'min:2'],
        ]);

        $pelada->jogos()->create($data + ['status' => 'aberto']);

        return back()->with('status', 'Rodada criada.');
    }

    public function participantes(PeladaJogo $jogo): View
    {
        $this->authorizeOwner($jogo->pelada);

        return view('organizador.jogos.participantes', [
            'jogo' => $jogo->load('pelada', 'participantes.user'),
        ]);
    }

    private function authorizeOwner(Pelada $pelada): void
    {
        abort_unless(auth()->user()->isAdmin() || $pelada->organizador_id === auth()->id(), 403);
    }
}
