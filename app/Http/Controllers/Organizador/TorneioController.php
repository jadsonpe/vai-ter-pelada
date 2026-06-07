<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\Pelada;
use App\Models\PeladaMembro;
use App\Models\Torneio;
use App\Models\TorneioCartao;
use App\Models\TorneioGol;
use App\Models\TorneioGrupo;
use App\Models\TorneioJogo;
use App\Models\TorneioParticipante;
use App\Models\TorneioTime;
use App\Models\TorneioTimeJogador;
use App\Services\TorneioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TorneioController extends Controller
{
    public function __construct(private readonly TorneioService $service)
    {
    }

    public function index(Pelada $pelada): View
    {
        $this->authorizeOwner($pelada);
        $this->authorizeSupportedSport($pelada);

        return view('organizador.torneios.index', [
            'pelada' => $pelada->load(['esporte', 'torneios' => fn ($query) => $query->latest('data_torneio')]),
        ]);
    }

    public function create(Pelada $pelada): View
    {
        $this->authorizeOwner($pelada);
        $this->authorizeSupportedSport($pelada);

        return view('organizador.torneios.form', [
            'pelada' => $pelada,
            'torneio' => new Torneio([
                'data_torneio' => now(),
                'jogadores_por_time' => 5,
                'quantidade_times' => 4,
                'formato' => 'pontos_corridos',
                'tipo_confronto' => 'ida',
                'quantidade_grupos' => 2,
                'classificados_total' => 2,
                'classificados_por_grupo' => 1,
                'tipo_confronto_mata_mata' => 'unico',
                'tipo_confronto_final' => 'unico',
                'wo_gols_vencedor' => 3,
                'wo_gols_perdedor' => 0,
                'wo_conta_saldo' => true,
            ]),
        ]);
    }

    public function store(Request $request, Pelada $pelada): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        $this->authorizeSupportedSport($pelada);

        $data = $this->validatedTorneio($request);
        $this->validatedTorneioImages($request);
        $baseSlug = Str::slug($data['nome'].' '.$pelada->slug) ?: 'torneio';
        $slug = $baseSlug;
        $count = 2;

        while (Torneio::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$count++;
        }

        $torneio = $pelada->torneios()->create($data + ['slug' => $slug, 'status' => 'rascunho']);
        $this->syncTorneioImages($request, $torneio);

        return redirect()
            ->route('organizador.torneios.show', $torneio)
            ->with('status', 'Torneio criado. Agora adicione os participantes.');
    }

    public function edit(Torneio $torneio): View
    {
        $this->authorizeOwner($torneio->pelada);

        return view('organizador.torneios.form', [
            'pelada' => $torneio->pelada,
            'torneio' => $torneio,
        ]);
    }

    public function update(Request $request, Torneio $torneio): RedirectResponse
    {
        $this->authorizeOwner($torneio->pelada);

        $this->validatedTorneioImages($request);
        $torneio->update($this->validatedTorneio($request));
        $this->syncTorneioImages($request, $torneio);

        return redirect()->route('organizador.torneios.show', $torneio)->with('status', 'Torneio atualizado.');
    }

    public function show(Torneio $torneio): View
    {
        $this->authorizeOwner($torneio->pelada);

        $torneio->load($this->torneioRelations());

        return view('organizador.torneios.show', [
            'torneio' => $torneio,
            'pelada' => $torneio->pelada,
            'membrosDisponiveis' => $this->membrosDisponiveis($torneio),
            'restantes' => $torneio->participantes->where('status', 'ativo')->filter(fn ($participante) => ! $participante->timeJogador),
            'classificacao' => $this->service->classificacao($torneio),
            'artilharia' => $this->service->artilharia($torneio),
            'disciplina' => $this->service->disciplina($torneio),
            'torneioEncerrado' => $torneio->finalRealizada(),
        ]);
    }

    public function addParticipantes(Request $request, Torneio $torneio): RedirectResponse
    {
        $this->authorizeOwner($torneio->pelada);
        if ($response = $this->redirectIfTorneioEncerrado($torneio)) {
            return $response;
        }

        $data = $request->validate([
            'membros' => ['nullable', 'array'],
            'membros.*' => ['integer'],
            'nomes_manuais' => ['nullable', 'string'],
        ]);

        foreach ($data['membros'] ?? [] as $membroId) {
            $membro = PeladaMembro::where('pelada_id', $torneio->pelada_id)->find($membroId);

            if (! $membro) {
                continue;
            }

            TorneioParticipante::updateOrCreate(
                ['torneio_id' => $torneio->id, 'user_id' => $membro->user_id],
                [
                    'pelada_membro_id' => $membro->id,
                    'tipo' => $membro->tipo,
                    'nome_manual' => null,
                    'status' => 'ativo',
                ]
            );
        }

        collect(preg_split('/\r\n|\r|\n/', (string) ($data['nomes_manuais'] ?? '')))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->each(function (string $name) use ($torneio) {
                TorneioParticipante::firstOrCreate(
                    ['torneio_id' => $torneio->id, 'nome_manual' => $name],
                    ['tipo' => 'manual', 'status' => 'ativo']
                );
            });

        return back()->with('status', 'Participantes atualizados.');
    }

    public function updateParticipante(Request $request, TorneioParticipante $participante): RedirectResponse
    {
        $this->authorizeOwner($participante->torneio->pelada);
        if ($response = $this->redirectIfTorneioEncerrado($participante->torneio)) {
            return $response;
        }

        $participante->update($request->validate([
            'goleiro' => ['nullable', 'boolean'],
            'cabeca_chave' => ['nullable', 'boolean'],
            'status' => ['required', 'in:ativo,removido'],
        ]));

        return back()->with('status', 'Participante atualizado.');
    }

    public function updateParticipantesMany(Request $request, Torneio $torneio): RedirectResponse
    {
        $this->authorizeOwner($torneio->pelada);
        if ($response = $this->redirectIfTorneioEncerrado($torneio)) {
            return $response;
        }

        $data = $request->validate([
            'participantes' => ['nullable', 'array'],
            'participantes.*.perfil' => ['required', 'in:normal,goleiro,cabeca,goleiro_cabeca'],
            'participantes.*.status' => ['required', 'in:ativo,removido'],
        ]);

        foreach ($data['participantes'] ?? [] as $id => $payload) {
            $participante = $torneio->participantes()->find($id);

            if (! $participante) {
                continue;
            }

            $participante->update([
                'goleiro' => in_array($payload['perfil'], ['goleiro', 'goleiro_cabeca'], true),
                'cabeca_chave' => in_array($payload['perfil'], ['cabeca', 'goleiro_cabeca'], true),
                'status' => $payload['status'],
            ]);
        }

        return back()->with('status', 'Participantes atualizados.');
    }

    public function removeParticipante(TorneioParticipante $participante): RedirectResponse
    {
        $this->authorizeOwner($participante->torneio->pelada);
        if ($response = $this->redirectIfTorneioEncerrado($participante->torneio)) {
            return $response;
        }
        $participante->delete();

        return back()->with('status', 'Participante removido.');
    }

    public function sortearTimes(Torneio $torneio): RedirectResponse
    {
        $this->authorizeOwner($torneio->pelada);
        if ($response = $this->redirectIfTorneioEncerrado($torneio)) {
            return $response;
        }

        if ($torneio->times()->exists()) {
            return back()->with('status', 'Os times já foram sorteados. Agora você pode apenas adicionar jogadores restantes aos times.');
        }

        $participantes = $torneio->participantes()
            ->where('status', 'ativo')
            ->get()
            ->shuffle();

        $timesCompletos = min(
            $torneio->quantidade_times,
            intdiv($participantes->count(), max(1, $torneio->jogadores_por_time))
        );

        if ($timesCompletos < 2) {
            return back()->with('status', 'Adicione participantes suficientes para formar pelo menos 2 times completos.');
        }

        $goleiros = $participantes->where('goleiro', true)->values();
        $cabecas = $participantes->where('cabeca_chave', true)->where('goleiro', false)->values();
        $outros = $participantes->where('goleiro', false)->where('cabeca_chave', false)->values();
        $fila = $goleiros->concat($cabecas)->concat($outros)->values();

        $times = collect();
        for ($i = 1; $i <= $timesCompletos; $i++) {
            $times->push(TorneioTime::create([
                'torneio_id' => $torneio->id,
                'nome' => 'Time '.$i,
                'ordem' => $i,
            ]));
        }

        $maxJogadores = $timesCompletos * $torneio->jogadores_por_time;
        foreach ($fila->take($maxJogadores)->values() as $index => $participante) {
            $time = $times[$index % $timesCompletos];
            TorneioTimeJogador::create([
                'torneio_time_id' => $time->id,
                'torneio_participante_id' => $participante->id,
                'ordem' => intdiv($index, $timesCompletos) + 1,
            ]);
        }

        $torneio->update(['status' => 'times_sorteados']);

        return back()->with('status', 'Times sorteados. Jogadores restantes ficaram listados fora dos times.');
    }

    public function addJogadorTime(Request $request, TorneioTime $time): RedirectResponse
    {
        $this->authorizeOwner($time->torneio->pelada);
        if ($response = $this->redirectIfTorneioEncerrado($time->torneio)) {
            return $response;
        }

        $data = $request->validate([
            'torneio_participante_id' => ['nullable', 'integer', 'exists:torneio_participantes,id'],
            'nome_manual' => ['nullable', 'string', 'max:120'],
        ]);

        if (empty($data['torneio_participante_id']) && empty($data['nome_manual'])) {
            return back()->with('status', 'Escolha um jogador restante ou informe um nome manual.');
        }

        if (! empty($data['nome_manual'])) {
            $participante = $time->torneio->participantes()->create([
                'nome_manual' => trim($data['nome_manual']),
                'tipo' => 'manual',
                'status' => 'ativo',
            ]);
        } else {
            $participante = $time->torneio
                ->participantes()
                ->where('status', 'ativo')
                ->findOrFail($data['torneio_participante_id']);
        }

        if ($participante->timeJogador()->exists()) {
            return back()->with('status', 'Esse jogador já está em um time.');
        }

        TorneioTimeJogador::create([
            'torneio_time_id' => $time->id,
            'torneio_participante_id' => $participante->id,
            'ordem' => ((int) $time->jogadores()->max('ordem')) + 1,
        ]);

        return back()->with('status', 'Jogador adicionado ao time.');
    }

    public function updateTime(Request $request, TorneioTime $time): RedirectResponse
    {
        $this->authorizeOwner($time->torneio->pelada);
        if ($response = $this->redirectIfTorneioEncerrado($time->torneio)) {
            return $response;
        }

        $time->update($request->validate(['nome' => ['required', 'string', 'max:80']]));

        return back()->with('status', 'Time atualizado.');
    }

    public function gerarJogos(Torneio $torneio): RedirectResponse
    {
        $this->authorizeOwner($torneio->pelada);
        if ($response = $this->redirectIfTorneioEncerrado($torneio)) {
            return $response;
        }

        $torneio->load('times');
        if ($torneio->times->count() < 2) {
            return back()->with('status', 'Sorteie os times antes de gerar jogos.');
        }

        $torneio->jogos()->delete();
        $torneio->grupos()->delete();

        match ($torneio->formato) {
            'mata_mata' => $this->gerarMataMata($torneio, $torneio->times),
            'grupos_mata_mata' => $this->gerarGruposMataMata($torneio, $torneio->times),
            default => $this->gerarGrupoUnico($torneio, $torneio->times),
        };

        $torneio->update(['status' => 'jogos_gerados']);

        return back()->with('status', 'Jogos gerados.');
    }

    public function resultado(Request $request, TorneioJogo $jogo): RedirectResponse
    {
        $this->authorizeOwner($jogo->torneio->pelada);
        if ($response = $this->redirectIfTorneioEncerrado($jogo->torneio)) {
            return $response;
        }
        $this->normalizarNumerosDaSumula($request);

        $data = $request->validate([
            'gols_a' => ['nullable', 'integer', 'min:0'],
            'gols_b' => ['nullable', 'integer', 'min:0'],
            'vencedor_id' => ['nullable', 'integer', 'exists:torneio_times,id'],
            'decidido_penaltis' => ['nullable', 'boolean'],
            'wo' => ['nullable', 'boolean'],
            'wo_vencedor_id' => ['nullable', 'integer', 'exists:torneio_times,id'],
            'observacao' => ['nullable', 'string'],
            'gols' => ['nullable', 'array'],
            'gols.*.participante_id' => ['nullable', 'integer', 'exists:torneio_participantes,id'],
            'gols.*.time_id' => ['nullable', 'integer', 'exists:torneio_times,id'],
            'gols.*.quantidade' => ['nullable', 'integer', 'min:1'],
            'cartoes' => ['nullable', 'array'],
            'cartoes.*.participante_id' => ['nullable', 'integer', 'exists:torneio_participantes,id'],
            'cartoes.*.time_id' => ['nullable', 'integer', 'exists:torneio_times,id'],
            'cartoes.*.tipo' => ['nullable', 'in:amarelo,vermelho,azul'],
            'cartoes.*.tipos' => ['nullable', 'array'],
            'cartoes.*.tipos.*' => ['nullable', 'in:amarelo,vermelho,azul'],
            'cartoes.*.quantidade' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($request->boolean('wo')) {
            $winnerId = (int) $data['wo_vencedor_id'];
            $loserId = $winnerId === (int) $jogo->time_a_id ? $jogo->time_b_id : $jogo->time_a_id;
            $data['gols_a'] = $winnerId === (int) $jogo->time_a_id ? $jogo->torneio->wo_gols_vencedor : $jogo->torneio->wo_gols_perdedor;
            $data['gols_b'] = $winnerId === (int) $jogo->time_b_id ? $jogo->torneio->wo_gols_vencedor : $jogo->torneio->wo_gols_perdedor;
            $data['vencedor_id'] = $winnerId;
            $data['wo_perdedor_id'] = $loserId;
        } else {
            $data['vencedor_id'] = $this->resolveWinner($jogo, $data);
        }

        $jogo->update([
            'gols_a' => $data['gols_a'] ?? 0,
            'gols_b' => $data['gols_b'] ?? 0,
            'vencedor_id' => $data['vencedor_id'],
            'decidido_penaltis' => $request->boolean('decidido_penaltis'),
            'wo' => $request->boolean('wo'),
            'wo_vencedor_id' => $data['wo_vencedor_id'] ?? null,
            'wo_perdedor_id' => $data['wo_perdedor_id'] ?? null,
            'status' => 'finalizado',
            'observacao' => $data['observacao'] ?? null,
        ]);

        $jogo->gols()->delete();
        $jogo->cartoes()->delete();

        if (! $jogo->wo) {
            foreach ($data['gols'] ?? [] as $gol) {
                if (! empty($gol['participante_id']) && ! empty($gol['time_id']) && ! empty($gol['quantidade'])) {
                    TorneioGol::create([
                        'torneio_jogo_id' => $jogo->id,
                        'torneio_time_id' => $gol['time_id'],
                        'torneio_participante_id' => $gol['participante_id'],
                        'quantidade' => $gol['quantidade'],
                    ]);
                }
            }
        }

        foreach ($data['cartoes'] ?? [] as $cartao) {
            $tipos = collect($cartao['tipos'] ?? [])
                ->when(! empty($cartao['tipo']), fn ($collection) => $collection->push($cartao['tipo']))
                ->filter()
                ->unique()
                ->values();

            if (! empty($cartao['participante_id']) && ! empty($cartao['time_id']) && $tipos->isNotEmpty()) {
                foreach ($tipos as $tipo) {
                    TorneioCartao::create([
                        'torneio_jogo_id' => $jogo->id,
                        'torneio_time_id' => $cartao['time_id'],
                        'torneio_participante_id' => $cartao['participante_id'],
                        'tipo' => $tipo,
                        'quantidade' => $cartao['quantidade'] ?? 1,
                    ]);
                }
            } elseif (! empty($cartao['participante_id']) && ! empty($cartao['time_id']) && ! empty($cartao['tipo']) && ! empty($cartao['quantidade'])) {
                TorneioCartao::create([
                    'torneio_jogo_id' => $jogo->id,
                    'torneio_time_id' => $cartao['time_id'],
                    'torneio_participante_id' => $cartao['participante_id'],
                    'tipo' => $cartao['tipo'],
                    'quantidade' => $cartao['quantidade'],
                ]);
            }
        }

        $jogo = $jogo->fresh('torneio.grupos.times');
        $this->preencherClassificadosDosGrupos($jogo->torneio);
        $this->avancarVencedor($jogo);

        if ($jogo->fase === 'final' && $jogo->status === 'finalizado') {
            $jogo->torneio->update(['status' => 'finalizado']);
        }

        return back()->with('status', 'Resultado salvo.');
    }

    private function redirectIfTorneioEncerrado(Torneio $torneio): ?RedirectResponse
    {
        if (! $torneio->finalRealizada()) {
            return null;
        }

        return back()->with('status', 'A final já foi realizada. Times, jogadores e súmulas deste torneio estão bloqueados.');
    }

    private function validatedTorneioImages(Request $request): void
    {
        $request->validate([
            'imagem' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'mural_fotos' => ['nullable', 'array', 'max:4'],
            'mural_fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remover_imagem' => ['nullable', 'boolean'],
            'remover_mural_fotos' => ['nullable', 'array'],
            'remover_mural_fotos.*' => ['string'],
        ]);
    }

    private function syncTorneioImages(Request $request, Torneio $torneio): void
    {
        if ($request->boolean('remover_imagem') && $torneio->imagem) {
            Storage::disk('public')->delete($torneio->imagem);
            $torneio->forceFill(['imagem' => null])->save();
        }

        if ($request->hasFile('imagem')) {
            if ($torneio->imagem) {
                Storage::disk('public')->delete($torneio->imagem);
            }

            $torneio->forceFill([
                'imagem' => $this->storeTorneioImage($request->file('imagem')),
            ])->save();
        }

        $mural = collect($torneio->mural_fotos ?: [])->filter()->values();
        $removidas = collect($request->input('remover_mural_fotos', []))->filter()->values();

        if ($removidas->isNotEmpty()) {
            $removidas
                ->filter(fn ($path) => $mural->contains($path))
                ->each(fn ($path) => Storage::disk('public')->delete($path));

            $mural = $mural->reject(fn ($path) => $removidas->contains($path))->values();
        }

        $uploads = collect($request->file('mural_fotos', []))->filter();
        $availableSlots = max(0, 4 - $mural->count());

        $uploads->take($availableSlots)->each(function (UploadedFile $file) use (&$mural) {
            $mural->push($this->storeTorneioImage($file));
        });

        if ($request->hasFile('mural_fotos') || $removidas->isNotEmpty()) {
            $torneio->forceFill(['mural_fotos' => $mural->take(4)->values()->all()])->save();
        }
    }

    private function storeTorneioImage(UploadedFile $file): string
    {
        return $file->store('torneios', 'public');
    }

    private function normalizarNumerosDaSumula(Request $request): void
    {
        $payload = $request->all();

        foreach (['gols_a', 'gols_b', 'vencedor_id', 'wo_vencedor_id'] as $field) {
            if (isset($payload[$field]) && is_string($payload[$field]) && ctype_digit($payload[$field])) {
                $payload[$field] = (string) ((int) $payload[$field]);
            }
        }

        foreach (['gols', 'cartoes'] as $group) {
            foreach ($payload[$group] ?? [] as $key => $item) {
                foreach (['participante_id', 'time_id', 'quantidade'] as $field) {
                    if (isset($item[$field]) && is_string($item[$field]) && ctype_digit($item[$field])) {
                        $payload[$group][$key][$field] = (string) ((int) $item[$field]);
                    }
                }
            }
        }

        $request->replace($payload);
    }

    private function validatedTorneio(Request $request): array
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'data_torneio' => ['required', 'date'],
            'jogadores_por_time' => ['required', 'integer', 'min:2', 'max:20'],
            'quantidade_times' => ['required', 'integer', 'min:2', 'max:64'],
            'formato' => ['required', 'in:pontos_corridos,grupos_mata_mata,mata_mata'],
            'tipo_confronto' => ['nullable', 'in:ida,ida_volta'],
            'quantidade_grupos' => ['nullable', 'integer', 'in:2,4,8,16'],
            'classificados_total' => ['nullable', 'integer', 'in:2,4,8,16,32'],
            'classificados_por_grupo' => ['nullable', 'integer', 'min:1', 'max:8'],
            'tipo_confronto_mata_mata' => ['required', 'in:unico,ida_volta'],
            'tipo_confronto_final' => ['required', 'in:unico,ida_volta'],
            'terceiro_lugar' => ['nullable', 'boolean'],
            'wo_gols_vencedor' => ['required', 'integer', 'min:1', 'max:20'],
            'wo_gols_perdedor' => ['required', 'integer', 'min:0', 'max:20'],
            'wo_conta_saldo' => ['nullable', 'boolean'],
            'regras' => ['nullable', 'string'],
        ]) + [
            'terceiro_lugar' => $request->boolean('terceiro_lugar'),
            'wo_conta_saldo' => $request->boolean('wo_conta_saldo'),
            'tipo_confronto' => $request->input('tipo_confronto', 'ida'),
        ];

        $this->validateTournamentFormat($data);

        return $data;
    }

    private function validateTournamentFormat(array $data): void
    {
        $times = (int) $data['quantidade_times'];
        $format = $data['formato'];

        if ($format === 'mata_mata' && ! $this->isPowerOfTwo($times)) {
            throw ValidationException::withMessages([
                'formato' => 'Mata-mata direto exige quantidade de times em potencia de 2: 4, 8, 16, 32 ou 64.',
            ]);
        }

        if ($format === 'pontos_corridos') {
            $classified = (int) ($data['classificados_total'] ?? 0);

            if (! $this->isPowerOfTwo($classified) || $classified >= $times) {
                throw ValidationException::withMessages([
                    'classificados_total' => 'No grupo unico, os classificados precisam ser uma potencia de 2 e menor que a quantidade de times.',
                ]);
            }
        }

        if ($format === 'grupos_mata_mata') {
            $groups = (int) ($data['quantidade_grupos'] ?? 0);
            $perGroup = (int) ($data['classificados_por_grupo'] ?? 0);
            $totalClassified = $groups * $perGroup;

            if ($groups >= $times || $groups <= 0 || $times % $groups !== 0) {
                throw ValidationException::withMessages([
                    'quantidade_grupos' => 'A quantidade de grupos deve ser menor que os times e dividir os times sem sobra.',
                ]);
            }

            if ($perGroup >= ($times / $groups) || ! $this->isPowerOfTwo($totalClassified)) {
                throw ValidationException::withMessages([
                    'classificados_por_grupo' => 'O total de classificados dos grupos deve fechar uma potencia de 2 para o mata-mata.',
                ]);
            }
        }
    }

    private function isPowerOfTwo(int $number): bool
    {
        return $number > 0 && ($number & ($number - 1)) === 0;
    }

    private function membrosDisponiveis(Torneio $torneio): Collection
    {
        $ids = $torneio->participantes()->whereNotNull('pelada_membro_id')->pluck('pelada_membro_id');

        return $torneio->pelada->membros()
            ->with('user')
            ->where('status', 'ativo')
            ->whereNotIn('id', $ids)
            ->orderBy('tipo')
            ->get();
    }

    private function gerarPontosCorridos(Torneio $torneio, Collection $times, string $fase, ?TorneioGrupo $grupo = null): void
    {
        $ordem = 1;
        $rodadas = collect();
        $fila = $times->values();

        if ($fila->count() % 2 !== 0) {
            $fila->push(null);
        }

        $quantidadeTimes = $fila->count();
        $totalRodadas = max(1, $quantidadeTimes - 1);
        $jogosPorRodada = (int) ($quantidadeTimes / 2);

        for ($rodada = 1; $rodada <= $totalRodadas; $rodada++) {
            $jogosDaRodada = collect();

            for ($i = 0; $i < $jogosPorRodada; $i++) {
                $timeA = $fila[$i];
                $timeB = $fila[$quantidadeTimes - 1 - $i];

                if (! $timeA || ! $timeB) {
                    continue;
                }

                if ($rodada % 2 === 0) {
                    [$timeA, $timeB] = [$timeB, $timeA];
                }

                $jogosDaRodada->push([$timeA, $timeB]);
                $this->criarJogo($torneio, $fase, $timeA, $timeB, $ordem++, $grupo, $rodada);
            }

            $rodadas->push($jogosDaRodada);

            $fixo = $fila->shift();
            $ultimo = $fila->pop();
            $fila->prepend($ultimo);
            $fila->prepend($fixo);
        }

        if ($torneio->tipo_confronto === 'ida_volta') {
            foreach ($rodadas as $index => $jogosDaRodada) {
                foreach ($jogosDaRodada as [$timeA, $timeB]) {
                    $this->criarJogo($torneio, $fase, $timeB, $timeA, $ordem++, $grupo, $totalRodadas + $index + 1);
                }
            }
        }
    }

    private function gerarGrupoUnico(Torneio $torneio, Collection $times): void
    {
        $this->gerarPontosCorridos($torneio, $times, 'pontos_corridos');
        $classificados = (int) ($torneio->classificados_total ?: min(4, max(2, $times->count() - 1)));
        $this->criarChaveEliminatoria($torneio, $classificados, 'Classificado');
    }

    private function gerarGruposMataMata(Torneio $torneio, Collection $times): void
    {
        $groupCount = (int) ($torneio->quantidade_grupos ?: ($times->count() >= 8 ? 4 : 2));
        $groups = collect(range(1, $groupCount))->map(fn ($number) => TorneioGrupo::create([
            'torneio_id' => $torneio->id,
            'nome' => 'Grupo '.chr(64 + $number),
            'ordem' => $number,
        ]));

        foreach ($times->values() as $index => $time) {
            $group = $groups[$index % $groupCount];
            $group->times()->attach($time->id);
        }

        foreach ($groups as $group) {
            $this->gerarPontosCorridos($torneio, $group->times()->get(), 'grupo', $group);
        }

        $classificadosPorGrupo = (int) ($torneio->classificados_por_grupo ?: 1);
        $this->criarChaveEliminatoria($torneio, $groupCount * $classificadosPorGrupo, 'Classificado');
    }

    private function gerarMataMata(Torneio $torneio, Collection $times): void
    {
        $this->criarChaveEliminatoria($torneio, $times->count(), null, $times->shuffle()->values());
    }

    private function criarChaveEliminatoria(Torneio $torneio, int $teamCount, ?string $placeholder = null, ?Collection $times = null): void
    {
        $size = 1;
        while ($size < $teamCount) {
            $size *= 2;
        }

        $fase = match ($size) {
            2 => 'final',
            4 => 'semifinal',
            8 => 'quartas',
            16 => 'oitavas',
            default => 'mata_mata',
        };

        $previous = collect();
        for ($i = 0; $i < $size / 2; $i++) {
            $timeA = $times?->get($i * 2);
            $timeB = $times?->get(($i * 2) + 1);
            $previous->push($this->criarJogo($torneio, $fase, $timeA, $timeB, $i + 1));
        }

        $currentSize = $size / 2;
        while ($currentSize > 1) {
            $nextFase = match ((int) ($currentSize / 2)) {
                1 => 'final',
                2 => 'semifinal',
                4 => 'quartas',
                default => 'mata_mata',
            };
            $next = collect();

            for ($i = 0; $i < $currentSize / 2; $i++) {
                $nextGame = $this->criarJogo($torneio, $nextFase, null, null, $i + 1);
                $previous->slice($i * 2, 2)->each(fn (TorneioJogo $game) => $game->update(['proximo_jogo_id' => $nextGame->id]));
                $next->push($nextGame);
            }

            $previous = $next;
            $currentSize /= 2;
        }

        if ($torneio->terceiro_lugar) {
            $this->criarJogo($torneio, 'terceiro_lugar', null, null, 1);
        }
    }

    private function criarJogo(Torneio $torneio, string $fase, ?TorneioTime $timeA, ?TorneioTime $timeB, int $ordem, ?TorneioGrupo $grupo = null, int $rodada = 1): TorneioJogo
    {
        return TorneioJogo::create([
            'torneio_id' => $torneio->id,
            'torneio_grupo_id' => $grupo?->id,
            'time_a_id' => $timeA?->id,
            'time_b_id' => $timeB?->id,
            'fase' => $fase,
            'rodada' => $rodada,
            'ordem' => $ordem,
            'status' => $timeA && ! $timeB ? 'finalizado' : 'pendente',
            'vencedor_id' => $timeA && ! $timeB ? $timeA->id : null,
        ]);
    }

    private function resolveWinner(TorneioJogo $jogo, array $data): ?int
    {
        $golsA = (int) ($data['gols_a'] ?? 0);
        $golsB = (int) ($data['gols_b'] ?? 0);

        if ($golsA > $golsB) {
            return $jogo->time_a_id;
        }

        if ($golsB > $golsA) {
            return $jogo->time_b_id;
        }

        return $data['vencedor_id'] ?? null;
    }

    private function avancarVencedor(TorneioJogo $jogo): void
    {
        if (! $jogo->isEliminatorio() || ! $jogo->proximo_jogo_id || ! $jogo->vencedor_id) {
            return;
        }

        $next = TorneioJogo::find($jogo->proximo_jogo_id);
        if (! $next) {
            return;
        }

        if (! $next->time_a_id) {
            $next->update(['time_a_id' => $jogo->vencedor_id]);
        } elseif (! $next->time_b_id && $next->time_a_id !== $jogo->vencedor_id) {
            $next->update(['time_b_id' => $jogo->vencedor_id]);
        }
    }

    private function preencherClassificadosDosGrupos(Torneio $torneio): void
    {
        if (! in_array($torneio->formato, ['grupos_mata_mata', 'pontos_corridos'], true)) {
            return;
        }

        $groupGames = $torneio->jogos()
            ->whereIn('fase', ['grupo', 'pontos_corridos'])
            ->get();

        if ($groupGames->isEmpty() || $groupGames->contains(fn (TorneioJogo $jogo) => $jogo->status !== 'finalizado')) {
            return;
        }

        $firstKnockout = $torneio->jogos()
            ->whereNotIn('fase', ['grupo', 'pontos_corridos'])
            ->whereNull('time_a_id')
            ->whereNull('time_b_id')
            ->orderByRaw("FIELD(fase, 'oitavas', 'quartas', 'semifinal', 'final', 'terceiro_lugar')")
            ->orderBy('ordem')
            ->get();

        if ($firstKnockout->isEmpty()) {
            return;
        }

        $slots = $firstKnockout->count() * 2;

        if ($torneio->formato === 'pontos_corridos') {
            $classificados = (int) ($torneio->classificados_total ?: $slots);
            $qualified = $this->service->classificacao($torneio)
                ->take($classificados)
                ->pluck('time')
                ->values();
        } else {
            $perGroup = (int) ($torneio->classificados_por_grupo ?: max(1, (int) ceil($slots / max(1, $torneio->grupos->count()))));
            $qualified = $torneio->grupos
                ->flatMap(fn ($grupo) => $this->service->classificacao($torneio, $grupo)->take($perGroup)->pluck('time'))
                ->unique('id')
                ->take($slots)
                ->values();
        }

        foreach ($firstKnockout as $index => $game) {
            $game->update([
                'time_a_id' => $qualified->get($index * 2)?->id,
                'time_b_id' => $qualified->get(($index * 2) + 1)?->id,
            ]);
        }
    }

    private function torneioRelations(): array
    {
        return [
            'pelada.membros.user',
            'participantes.user',
            'participantes.membro.user',
            'participantes.timeJogador.time',
            'times.jogadores.participante.user',
            'times.jogadores.participante.membro.user',
            'grupos.times',
            'jogos.timeA.jogadores.participante',
            'jogos.timeB.jogadores.participante',
            'jogos.gols.participante',
            'jogos.cartoes.participante',
        ];
    }

    private function authorizeOwner(Pelada $pelada): void
    {
        $this->redirectIfNotPeladaManager($pelada);
    }

    private function authorizeSupportedSport(Pelada $pelada): void
    {
        abort_unless(in_array($pelada->esporte?->slug, ['futebol', 'society', 'futsal'], true), 404);
    }
}
