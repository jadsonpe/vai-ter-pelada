<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patrocinador;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatrocinadorController extends Controller
{
    public function index(): View
    {
        return view('admin.patrocinadores.index', ['patrocinadores' => Patrocinador::latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Patrocinador::create($this->data($request));

        return back()->with('status', 'Patrocinador criado.');
    }

    public function update(Request $request, Patrocinador $patrocinador): RedirectResponse
    {
        $patrocinador->update($this->data($request));

        return back()->with('status', 'Patrocinador atualizado.');
    }

    public function destroy(Patrocinador $patrocinador): RedirectResponse
    {
        $patrocinador->delete();

        return back()->with('status', 'Patrocinador removido.');
    }

    private function data(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'max:255'],
            'logo_url' => ['nullable', 'url'],
            'site_url' => ['nullable', 'url'],
            'ativo' => ['nullable', 'boolean'],
        ]) + ['ativo' => $request->boolean('ativo')];
    }
}
