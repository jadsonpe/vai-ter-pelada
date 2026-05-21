<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        return view('admin.banners.index', ['banners' => Banner::latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Banner::create($this->data($request));

        return back()->with('status', 'Banner criado.');
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $banner->update($this->data($request));

        return back()->with('status', 'Banner atualizado.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $banner->delete();

        return back()->with('status', 'Banner removido.');
    }

    private function data(Request $request): array
    {
        $data = $request->validate([
            'titulo' => ['required', 'max:255'],
            'imagem' => ['nullable', 'max:255'],
            'link' => ['nullable', 'max:255'],
            'posicao' => ['required', 'max:50'],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date'],
            'ativo' => ['nullable', 'boolean'],
        ]) + ['ativo' => $request->boolean('ativo')];

        $data['imagem_url'] = $data['imagem'] ?? null;
        $data['link_url'] = $data['link'] ?? null;
        $data['inicio_em'] = $data['data_inicio'] ?? null;
        $data['fim_em'] = $data['data_fim'] ?? null;

        return $data;
    }
}
