<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\Esporte;
use App\Models\Pelada;
use App\Models\PeladaMembro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        if (! auth()->user()->podeCriarPelada()) {
            return view('organizador.peladas.upgrade', [
                'peladasCriadas' => auth()->user()->peladasOrganizadas()->count(),
                'limite' => auth()->user()->limite_peladas ?: 1,
            ]);
        }

        return view('organizador.peladas.form', [
            'pelada' => new Pelada(),
            'esportes' => Esporte::where('ativo', true)->orderBy('nome')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $request->user()->podeCriarPelada()) {
            return redirect()
                ->route('organizador.peladas.index')
                ->with('status', 'Seu plano atual permite criar apenas uma pelada. Atualize seu plano para criar mais.');
        }

        $data = $this->validateData($request);
        $data['organizador_id'] = $request->user()->id;
        $data['slug'] = Str::slug($data['nome']).'-'.Str::lower(Str::random(5));
        $data['local'] = $data['local_nome'];
        $data['capacidade'] = $data['vagas_totais'];
        $data['ativa'] = $data['status'] === 'ativa';

        $pelada = Pelada::create($data);
        $this->syncImagem($request, $pelada);

        PeladaMembro::updateOrCreate(
            ['pelada_id' => $pelada->id, 'user_id' => $request->user()->id],
            [
                'tipo' => 'mensalista',
                'status' => 'ativo',
                'prioridade' => 100,
                'data_entrada' => now()->toDateString(),
                'mensalista_desde' => now()->toDateString(),
                'observacao' => 'Organizador da pelada',
            ]
        );

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
        $data = $this->validateData($request);
        $data['local'] = $data['local_nome'];
        $data['capacidade'] = $data['vagas_totais'];
        $data['ativa'] = $data['status'] === 'ativa';
        $pelada->update($data);
        $this->syncImagem($request, $pelada);

        return redirect()->route('organizador.peladas.index')->with('status', 'Pelada atualizada.');
    }

    public function destroy(Pelada $pelada): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        $this->deleteImagem($pelada);
        $pelada->delete();

        return back()->with('status', 'Pelada removida.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'esporte_id' => ['required', 'exists:esportes,id'],
            'nome' => ['required', 'max:255'],
            'descricao' => ['nullable'],
            'cidade' => ['nullable', 'max:255'],
            'bairro' => ['nullable', 'max:255'],
            'local_nome' => ['required', 'max:255'],
            'endereco' => ['nullable', 'max:255'],
            'dia_semana' => ['nullable', 'integer', 'between:0,6'],
            'horario' => ['nullable', 'date_format:H:i'],
            'vagas_totais' => ['required', 'integer', 'min:2'],
            'vagas_diaristas' => ['nullable', 'integer', 'min:0'],
            'aceita_diarista' => ['nullable', 'boolean'],
            'valor_mensalista' => ['nullable', 'numeric', 'min:0'],
            'valor_diarista' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:ativa,pausada,encerrada'],
            'regras' => ['nullable'],
            'whatsapp_contato' => ['nullable', 'max:30'],
            'imagem' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'remover_imagem' => ['nullable', 'boolean'],
        ]) + [
            'aceita_diarista' => $request->boolean('aceita_diarista'),
            'requer_aprovacao' => true,
            'vagas_diaristas' => (int) $request->input('vagas_diaristas', 0),
        ];
    }

    private function authorizeOwner(Pelada $pelada): void
    {
        $this->redirectIfNotPeladaOwner($pelada);
    }

    private function syncImagem(Request $request, Pelada $pelada): void
    {
        if ($request->boolean('remover_imagem')) {
            $this->deleteImagem($pelada);
            $pelada->update(['imagem' => null]);

            return;
        }

        if (! $request->hasFile('imagem')) {
            return;
        }

        $this->deleteImagem($pelada);
        $pelada->update(['imagem' => $this->storeImagem($request->file('imagem'))]);
    }

    private function storeImagem(UploadedFile $file): string
    {
        return $file->store('peladas', 'public');
    }

    private function deleteImagem(Pelada $pelada): void
    {
        if ($pelada->imagem) {
            Storage::disk('public')->delete($pelada->imagem);
        }
    }
}
