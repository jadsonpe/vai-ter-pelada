<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\Pelada;
use App\Models\PeladaCaixaMovimentacao;
use App\Models\PeladaJogo;
use App\Models\PeladaJogoParticipante;
use App\Models\PeladaMembro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CaixaController extends Controller
{
    public function index(Request $request, Pelada $pelada): View
    {
        $this->authorizeOwner($pelada);

        $competencia = Carbon::createFromFormat('Y-m', $request->input('mes', now()->format('Y-m')))
            ->startOfMonth();

        $jogoSelecionado = $pelada->jogos()
            ->with(['participantes.user', 'participantes.membro.user'])
            ->when($request->filled('jogo_id'), fn ($query) => $query->where('id', $request->integer('jogo_id')))
            ->latest('data_hora')
            ->first();

        $movimentacoes = $pelada->caixaMovimentacoes()
            ->with(['user', 'membro.user', 'jogo'])
            ->whereBetween('data_pagamento', [$competencia->copy()->startOfMonth(), $competencia->copy()->endOfMonth()])
            ->latest('data_pagamento')
            ->latest()
            ->get();

        $entradas = $movimentacoes->where('tipo', 'entrada')->sum('valor');
        $saidas = $movimentacoes->where('tipo', 'saida')->sum('valor');
        $entradasGerais = $pelada->caixaMovimentacoes()->where('tipo', 'entrada')->sum('valor');
        $saidasGerais = $pelada->caixaMovimentacoes()->where('tipo', 'saida')->sum('valor');

        $mensalistas = $pelada->membros()
            ->with('user')
            ->where('tipo', 'mensalista')
            ->where('status', 'ativo')
            ->orderBy('apelido')
            ->get();

        $mensalidadesPagas = $pelada->caixaMovimentacoes()
            ->where('tipo', 'entrada')
            ->where('categoria', 'mensalidade')
            ->whereDate('competencia', $competencia->toDateString())
            ->get()
            ->keyBy('pelada_membro_id');

        $diariasPagas = collect();

        if ($jogoSelecionado) {
            $diariasPagas = $pelada->caixaMovimentacoes()
                ->where('tipo', 'entrada')
                ->where('categoria', 'diaria')
                ->where('pelada_jogo_id', $jogoSelecionado->id)
                ->get()
                ->keyBy('pelada_jogo_participante_id');
        }

        return view('organizador.caixa.index', [
            'pelada' => $pelada->load('jogos'),
            'competencia' => $competencia,
            'jogos' => $pelada->jogos()->latest('data_hora')->take(12)->get(),
            'jogoSelecionado' => $jogoSelecionado,
            'mensalistas' => $mensalistas,
            'mensalidadesPagas' => $mensalidadesPagas,
            'diariasPagas' => $diariasPagas,
            'movimentacoes' => $movimentacoes,
            'entradas' => $entradas,
            'saidas' => $saidas,
            'saldo' => $entradas - $saidas,
            'saldoGeral' => $entradasGerais - $saidasGerais,
        ]);
    }

    public function registrarMensalidade(Request $request, Pelada $pelada, PeladaMembro $membro): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        abort_unless($membro->pelada_id === $pelada->id, 404);

        $data = $request->validate([
            'mes' => ['required', 'date_format:Y-m'],
            'valor' => ['required'],
            'forma_pagamento' => ['nullable', 'string', 'max:50'],
            'observacao' => ['nullable', 'string'],
        ]);

        $data['valor'] = $this->parseMoney($data['valor']);

        $competencia = Carbon::createFromFormat('Y-m', $data['mes'])->startOfMonth();

        PeladaCaixaMovimentacao::updateOrCreate(
            [
                'pelada_id' => $pelada->id,
                'pelada_membro_id' => $membro->id,
                'categoria' => 'mensalidade',
                'competencia' => $competencia->toDateString(),
            ],
            [
                'user_id' => $membro->user_id,
                'registrado_por' => $request->user()->id,
                'tipo' => 'entrada',
                'descricao' => 'Mensalidade - '.$membro->nomeExibicao(),
                'valor' => $data['valor'],
                'data_pagamento' => now()->toDateString(),
                'forma_pagamento' => $data['forma_pagamento'] ?? null,
                'observacao' => $data['observacao'] ?? null,
            ]
        );

        return back()->with('status', 'Mensalidade registrada.');
    }

    public function registrarDiaria(Request $request, Pelada $pelada, PeladaJogo $jogo, PeladaJogoParticipante $participante): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        abort_unless($jogo->pelada_id === $pelada->id && $participante->pelada_jogo_id === $jogo->id, 404);

        $data = $request->validate([
            'valor' => ['required'],
            'forma_pagamento' => ['nullable', 'string', 'max:50'],
            'observacao' => ['nullable', 'string'],
        ]);

        $data['valor'] = $this->parseMoney($data['valor']);

        PeladaCaixaMovimentacao::updateOrCreate(
            [
                'pelada_id' => $pelada->id,
                'pelada_jogo_id' => $jogo->id,
                'pelada_jogo_participante_id' => $participante->id,
                'categoria' => 'diaria',
            ],
            [
                'pelada_membro_id' => $participante->pelada_membro_id,
                'user_id' => $participante->user_id,
                'registrado_por' => $request->user()->id,
                'tipo' => 'entrada',
                'descricao' => 'Diária - '.($participante->membro?->nomeExibicao() ?: $participante->user->name),
                'valor' => $data['valor'],
                'data_pagamento' => now()->toDateString(),
                'competencia' => optional($jogo->data_hora)->startOfMonth()?->toDateString(),
                'forma_pagamento' => $data['forma_pagamento'] ?? null,
                'observacao' => $data['observacao'] ?? null,
            ]
        );

        return back()->with('status', 'Diária registrada.');
    }

    public function store(Request $request, Pelada $pelada): RedirectResponse
    {
        $this->authorizeOwner($pelada);

        $data = $request->validate([
            'tipo' => ['required', 'in:entrada,saida'],
            'categoria' => ['required', 'string', 'max:50'],
            'descricao' => ['required', 'string', 'max:255'],
            'valor' => ['required'],
            'data_pagamento' => ['required', 'date'],
            'forma_pagamento' => ['nullable', 'string', 'max:50'],
            'observacao' => ['nullable', 'string'],
        ]);

        $data['valor'] = $this->parseMoney($data['valor']);

        $pelada->caixaMovimentacoes()->create($data + [
            'registrado_por' => $request->user()->id,
            'competencia' => Carbon::parse($data['data_pagamento'])->startOfMonth()->toDateString(),
        ]);

        return back()->with('status', 'Lançamento registrado no caixa.');
    }

    public function destroy(Pelada $pelada, PeladaCaixaMovimentacao $movimentacao): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        abort_unless($movimentacao->pelada_id === $pelada->id, 404);

        $movimentacao->delete();

        return back()->with('status', 'Lançamento removido.');
    }

    private function authorizeOwner(Pelada $pelada): void
    {
        $this->redirectIfNotPeladaManager($pelada);
    }

    private function parseMoney(mixed $value): float
    {
        $raw = trim((string) $value);
        $normalized = str_contains($raw, ',')
            ? str_replace(['.', ','], ['', '.'], $raw)
            : $raw;
        $amount = (float) $normalized;

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'valor' => 'Informe um valor maior que zero.',
            ]);
        }

        return $amount;
    }
}
