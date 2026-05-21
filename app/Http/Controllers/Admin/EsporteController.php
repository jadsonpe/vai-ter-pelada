<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Esporte;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EsporteController extends Controller
{
    public function index(): View
    {
        return view('admin.esportes.index', ['esportes' => Esporte::orderBy('nome')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['nome' => ['required', 'max:255'], 'icone' => ['nullable', 'max:255'], 'ativo' => ['nullable', 'boolean']]);
        $data['slug'] = Str::slug($data['nome']);
        $data['ativo'] = $request->boolean('ativo', true);
        Esporte::create($data);

        return back()->with('status', 'Esporte criado.');
    }

    public function update(Request $request, Esporte $esporte): RedirectResponse
    {
        $data = $request->validate(['nome' => ['required', 'max:255'], 'icone' => ['nullable', 'max:255'], 'ativo' => ['nullable', 'boolean']]);
        $data['slug'] = Str::slug($data['nome']);
        $data['ativo'] = $request->boolean('ativo');
        $esporte->update($data);

        return back()->with('status', 'Esporte atualizado.');
    }

    public function destroy(Esporte $esporte): RedirectResponse
    {
        $esporte->delete();

        return back()->with('status', 'Esporte removido.');
    }
}
