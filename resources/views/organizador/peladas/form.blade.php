<x-app-layout>
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')

        <h1 class="text-3xl font-bold text-slate-900">{{ $pelada->exists ? 'Editar pelada' : 'Nova pelada' }}</h1>

        <form method="POST" enctype="multipart/form-data" action="{{ $pelada->exists ? route('organizador.peladas.update', $pelada) : route('organizador.peladas.store') }}" class="mt-6 grid gap-4 rounded-lg border border-slate-200 bg-white p-5">
            @csrf
            @if($pelada->exists)
                @method('PUT')
            @endif

            <div>
                <label class="text-sm font-medium">Imagem da pelada</label>
                @if($pelada->temImagemPropria())
                    <x-pelada-imagem variant="preview" :src="$pelada->imagemPropriaUrl()" :alt="$pelada->nome" class="mt-2" />
                    <label class="mt-2 flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remover_imagem" value="1" @checked(old('remover_imagem'))>
                        Remover imagem enviada
                    </label>
                @elseif($pelada->exists && ($padrao = $pelada->esporte?->imagemPadraoUrl()))
                    <x-pelada-imagem variant="preview" :src="$padrao" :alt="'Imagem padrão do '.$pelada->esporte->nome" class="mt-2 opacity-90" />
                    <p class="mt-1 text-xs text-slate-500">Imagem padrão do esporte. Envie um arquivo abaixo para substituir.</p>
                @endif
                <input type="file" name="imagem" accept="image/jpeg,image/png,image/webp" class="mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
                <p class="mt-1 text-xs text-slate-500">JPG, PNG ou WebP. Máximo 2 MB. Sem envio, usa a imagem padrão do esporte (public/images/esportes/).</p>
                <x-input-error :messages="$errors->get('imagem')" class="mt-2" />
            </div>

            <label class="text-sm font-medium">
                Esporte
                <select name="esporte_id" class="mt-1 w-full rounded-md border-slate-300">
                    @foreach($esportes as $esporte)
                        <option value="{{ $esporte->id }}" @selected(old('esporte_id', $pelada->esporte_id) == $esporte->id)>{{ $esporte->nome }}</option>
                    @endforeach
                </select>
            </label>

            <label class="text-sm font-medium">
                Nome da Pelada
                <input name="nome" value="{{ old('nome', $pelada->nome) }}" class="mt-1 w-full rounded-md border-slate-300">
                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium">
                    Data de fundacao
                    <input type="date" name="data_fundacao" value="{{ old('data_fundacao', optional($pelada->data_fundacao)->format('Y-m-d')) }}" max="{{ now()->toDateString() }}" class="mt-1 w-full rounded-md border-slate-300">
                    <x-input-error :messages="$errors->get('data_fundacao')" class="mt-2" />
                </label>

                <label class="text-sm font-medium">
                    Categoria
                    <select name="categoria" class="mt-1 w-full rounded-md border-slate-300">
                        @foreach(\App\Models\Pelada::CATEGORIAS as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected(old('categoria', $pelada->categoria ?: 'adulto') === $valor)>{{ $rotulo }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('categoria')" class="mt-2" />
                </label>
            </div>

            <div>
                <label for="descricao" class="text-sm font-medium">Descrição da Pelada</label>
                <textarea id="descricao" name="descricao" maxlength="200" rows="3" class="mt-1 w-full rounded-md border-slate-300" placeholder="Ex: Pelada semanal para quem curte jogo organizado, ambiente respeitoso e boa resenha depois da partida.">{{ old('descricao', $pelada->descricao) }}</textarea>
                <div class="mt-1 flex items-start justify-between gap-3 text-xs text-slate-500">
                    <x-input-error :messages="$errors->get('descricao')" />
                    <span class="shrink-0"><span id="descricao-count">0</span>/200 caracteres</span>
                </div>
            </div>

            <div class="font-semibold">ONDE FICA SUA PELADA?</div>

            <div>
                <label class="text-sm font-medium text-emerald-700">
                    Digite o CEP para preencher o endereço automaticamente
                    <input type="text" id="cep" name="cep" placeholder="00000-000" maxlength="9" class="mt-1 w-full rounded-md border-emerald-300 bg-emerald-50 focus:border-emerald-500 focus:ring-emerald-500">
                </label>
                <span id="cep-erro" class="text-xs text-red-500 hidden">CEP não encontrado.</span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium">
                    Cidade
                    <input id="cidade" name="cidade" value="{{ old('cidade', $pelada->cidade) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>
                <label class="text-sm font-medium">
                    Bairro
                    <input id="bairro" name="bairro" value="{{ old('bairro', $pelada->bairro) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>
            </div>

            <label class="text-sm font-medium">
                Nome do local/arena/quadra
                <input name="local_nome" value="{{ old('local_nome', $pelada->local_nome ?: $pelada->local) }}" class="mt-1 w-full rounded-md border-slate-300">
            </label>

            <label class="text-sm font-medium">
                Endereço (Rua e Número)
                <input id="endereco" name="endereco" value="{{ old('endereco', $pelada->endereco) }}" class="mt-1 w-full rounded-md border-slate-300">
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium">
                    Horário
                    <input type="time" name="horario" value="{{ old('horario', optional($pelada->horario)->format('H:i')) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>
                <label class="text-sm font-medium">
                    Vagas totais
                    <input type="number" name="vagas_totais" value="{{ old('vagas_totais', $pelada->vagas_totais ?: $pelada->capacidade ?: 20) }}" class="mt-1 w-full rounded-md border-slate-300">
                    <x-input-error :messages="$errors->get('vagas_totais')" class="mt-2" />
                </label>
            </div>

            <div class="grid gap-4 {{ $pelada->exists ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }}">
                <label class="text-sm font-medium">
                    Vagas diaristas
                    <input type="number" name="vagas_diaristas" value="{{ old('vagas_diaristas', $pelada->vagas_diaristas ?: 0) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>

                @if($pelada->exists)
                    <label class="text-sm font-medium">
                        Status
                        <select name="status" class="mt-1 w-full rounded-md border-slate-300">
                            @foreach(['ativa', 'pausada', 'encerrada'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $pelada->status ?: 'ativa') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                <label class="text-sm font-medium">
                    WhatsApp
                    <input name="whatsapp_contato" value="{{ old('whatsapp_contato', $pelada->whatsapp_contato) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium">
                    Valor mensalista
                    <input name="valor_mensalista" value="{{ old('valor_mensalista', $pelada->valor_mensalista) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>
                <label class="text-sm font-medium">
                    Valor diarista
                    <input name="valor_diarista" value="{{ old('valor_diarista', $pelada->valor_diarista) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>
            </div>

            <div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <label for="regras" class="text-sm font-medium">Regras</label>
                    <button type="button" id="usar-regras-padrao" class="inline-flex items-center justify-center rounded-md border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                        Usar regras padrão
                    </button>
                </div>
                <textarea id="regras" name="regras" rows="8" class="mt-1 w-full rounded-md border-slate-300" placeholder="Ex: Informe regras de pontualidade, respeito, pagamento, prioridade de mensalistas, sorteio de times e cuidado com materiais.">{{ old('regras', $pelada->regras) }}</textarea>
                <x-input-error :messages="$errors->get('regras')" class="mt-2" />
            </div>

            <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Salvar</button>
        </form>
    </div>

    <script>
        (() => {
            const descricao = document.getElementById('descricao');
            const contador = document.getElementById('descricao-count');
            const regras = document.getElementById('regras');
            const usarRegrasPadrao = document.getElementById('usar-regras-padrao');
          const regrasPadrao = `1. Respeito e Fair Play: o respeito entre todos os participantes é obrigatório. Não serão toleradas ofensas, discriminação, ameaças ou agressões físicas. Atitudes antidesportivas podem gerar advertência, suspensão ou exclusão da pelada.

2. Pontualidade: os jogos começam no horário agendado. Atrasos prejudicam o tempo de quadra/campo de todos e podem resultar em perda da vaga ou redução do tempo de jogo.

3. Confirmação de presença: os participantes devem confirmar presença dentro do prazo definido pela organização. Ausências sem aviso podem gerar perda de prioridade em futuras peladas.

4. Divisão de times: as equipes serão sorteadas ou definidas pela organização para manter o equilíbrio e a competitividade saudável entre os participantes.

5. Tempo de jogo: cada partida terá a duração padrão definida pela organização (tempo corrido, número de gols ou sistema de rodízio), garantindo a participação justa de todos.

6. Substituições: as trocas de jogadores devem ocorrer de forma organizada, sem prejudicar o andamento das partidas.

7. Faltas e conduta em campo: entradas violentas, agressões ou atitudes que coloquem outros participantes em risco não serão toleradas.

8. Cartões disciplinares: a organização poderá registrar cartões amarelos e vermelhos. Expulsões e reincidências podem resultar em suspensão temporária ou definitiva.

9. Mensalistas e diaristas: mensalistas possuem prioridade nas vagas. Diaristas devem confirmar presença e realizar o pagamento antes do início da pelada.

10. W.O. (ausência): participantes que confirmarem presença e não comparecerem sem justificativa poderão receber advertência e perder prioridade em futuras inscrições.

11. Cuidado com o espaço e materiais: zele pela quadra, campo, coletes, bolas e demais equipamentos utilizados pela turma.

12. Segurança: cada participante é responsável por suas condições físicas para a prática esportiva e pelo uso adequado dos equipamentos.

13. Estatísticas: a organização poderá registrar gols, assistências, cartões, presenças, vitórias e demais dados para rankings e histórico dos participantes.

14. Decisões da organização: situações não previstas nestas regras serão resolvidas pela organização, visando o bom andamento da pelada.

15. Objetivo principal: promover diversão, amizade, integração, saúde e espírito esportivo entre todos os participantes.`;

function atualizarContador() {
                if (!descricao || !contador) return;
                contador.textContent = descricao.value.length;
            }

            descricao?.addEventListener('input', atualizarContador);
            usarRegrasPadrao?.addEventListener('click', () => {
                if (!regras) return;
                regras.value = regrasPadrao;
                regras.focus();
            });

            atualizarContador();
        })();

        // Busca de CEP Gratuita (ViaCEP)
    const inputCep = document.getElementById('cep');
    const erroCep = document.getElementById('cep-erro');

    inputCep?.addEventListener('blur', function() {
        // Remove caracteres especiais deixando apenas números
        const cep = this.value.replace(/\D/g, "");

        // Valida se o CEP tem 8 dígitos
        if (cep.length === 8) {
            erroCep.classList.add('hidden');
            
            // Alerta visual de carregando nos campos
            document.getElementById('endereco').value = "...";
            document.getElementById('bairro').value = "...";
            document.getElementById('cidade').value = "...";

            // Consulta a API gratuita ViaCEP
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(dados => {
                    if (!dados.erro) {
                        // Preenche os campos com os dados retornados
                        document.getElementById('endereco').value = dados.logradouro;
                        document.getElementById('bairro').value = dados.bairro;
                        // Salva no formato "Cidade - UF" (Ex: São Paulo - SP)
                        document.getElementById('cidade').value = `${dados.localidade} - ${dados.uf}`;
                    } else {
                        // CEP pesquisado não existe
                        limparCamposEndereco();
                        erroCep.classList.remove('hidden');
                    }
                })
                .catch(() => {
                    limparCamposEndereco();
                    alert("Erro ao buscar o CEP. Tente digitar manualmente.");
                });
        }
    });

    // Máscara simples para o input de CEP (adiciona o hífen enquanto digita)
    inputCep?.addEventListener('input', function() {
        let v = this.value.replace(/\D/g, "");
        if (v.length > 5) {
            v = v.substring(0, 5) + "-" + v.substring(5, 8);
        }
        this.value = v;
    });

    function limparCamposEndereco() {
        document.getElementById('endereco').value = "";
        document.getElementById('bairro').value = "";
        document.getElementById('cidade').value = "";
    }
    </script>
</x-app-layout>
