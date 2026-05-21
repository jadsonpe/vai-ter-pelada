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
        $data = $request->validate([
            'nome' => ['required', 'max:255'],
            'logo' => ['nullable', 'max:255'],
            'link' => ['nullable', 'max:255'],
            'telefone' => ['nullable', 'max:30'],
            'ativo' => ['nullable', 'boolean'],
        ]) + ['ativo' => $request->boolean('ativo')];

        $data['logo_url'] = $data['logo'] ?? null;
        $data['site_url'] = $data['link'] ?? null;

        return $data;
    }
}
