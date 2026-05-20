<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\Esporte;
use App\Models\Pelada;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PeladaController extends Controller
{
    public function index(Request $request): View
    {
        return view('organizador.peladas.index', [
            'peladas' => Pelada::with('esporte')
                ->where('organizador_id', $request->user()->id)
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('organizador.peladas.form', [
            'pelada' => new Pelada(),
            'esportes' => Esporte::where('ativo', true)->orderBy('nome')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['organizador_id'] = $request->user()->id;
        $data['slug'] = Str::slug($data['nome']).'-'.Str::lower(Str::random(5));

        Pelada::create($data);

        return redirect()->route('organizador.peladas.index')->with('status', 'Pelada criada.');
    }

    public function edit(Pelada $pelada): View
    {
        $this->authorizeOwner($pelada);

        return view('organizador.peladas.form', [
            'pelada' => $pelada,
            'esportes' => Esporte::where('ativo', true)->orderBy('nome')->get(),
        ]);
    }

    public function update(Request $request, Pelada $pelada): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        $pelada->update($this->validateData($request));

        return redirect()->route('organizador.peladas.index')->with('status', 'Pelada atualizada.');
    }

    public function destroy(Pelada $pelada): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        $pelada->delete();

        return back()->with('status', 'Pelada removida.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'esporte_id' => ['required', 'exists:esportes,id'],
            'nome' => ['required', 'max:255'],
            'descricao' => ['nullable'],
            'local' => ['required', 'max:255'],
            'dia_semana' => ['nullable', 'integer', 'between:0,6'],
            'horario' => ['nullable', 'date_format:H:i'],
            'capacidade' => ['required', 'integer', 'min:2'],
            'valor_mensalista' => ['nullable', 'numeric', 'min:0'],
            'valor_diarista' => ['nullable', 'numeric', 'min:0'],
            'ativa' => ['nullable', 'boolean'],
        ]) + ['ativa' => $request->boolean('ativa')];
    }

    private function authorizeOwner(Pelada $pelada): void
    {
        abort_unless(auth()->user()->isAdmin() || $pelada->organizador_id === auth()->id(), 403);
    }
}
