<x-app-layout>
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">{{ $pelada->exists ? 'Editar pelada' : 'Nova pelada' }}</h1>
        <form method="POST" enctype="multipart/form-data" action="{{ $pelada->exists ? route('organizador.peladas.update', $pelada) : route('organizador.peladas.store') }}" class="mt-6 grid gap-4 rounded-lg border border-slate-200 bg-white p-5">
            @csrf
            @if($pelada->exists) @method('PUT') @endif
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
            <label class="text-sm font-medium">Esporte
                <select name="esporte_id" class="mt-1 w-full rounded-md border-slate-300">
                    @foreach($esportes as $esporte)
                        <option value="{{ $esporte->id }}" @selected(old('esporte_id', $pelada->esporte_id) == $esporte->id)>{{ $esporte->nome }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm font-medium">Nome <input name="nome" value="{{ old('nome', $pelada->nome) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
            <label class="text-sm font-medium">Descrição <textarea name="descricao" class="mt-1 w-full rounded-md border-slate-300">{{ old('descricao', $pelada->descricao) }}</textarea></label>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium">Cidade <input name="cidade" value="{{ old('cidade', $pelada->cidade) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
                <label class="text-sm font-medium">Bairro <input name="bairro" value="{{ old('bairro', $pelada->bairro) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
            </div>
            <label class="text-sm font-medium">Nome do local/arena <input name="local_nome" value="{{ old('local_nome', $pelada->local_nome ?: $pelada->local) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
            <label class="text-sm font-medium">Endereço <input name="endereco" value="{{ old('endereco', $pelada->endereco) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
            <div class="grid gap-4 sm:grid-cols-3">
                <label class="text-sm font-medium">Dia da semana <input type="number" name="dia_semana" min="0" max="6" value="{{ old('dia_semana', $pelada->dia_semana) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
                <label class="text-sm font-medium">Horário <input type="time" name="horario" value="{{ old('horario', optional($pelada->horario)->format('H:i')) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
                <label class="text-sm font-medium">Vagas totais <input type="number" name="vagas_totais" value="{{ old('vagas_totais', $pelada->vagas_totais ?: $pelada->capacidade ?: 20) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <label class="text-sm font-medium">Vagas diaristas <input type="number" name="vagas_diaristas" value="{{ old('vagas_diaristas', $pelada->vagas_diaristas ?: 0) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
                <label class="text-sm font-medium">Status <select name="status" class="mt-1 w-full rounded-md border-slate-300">@foreach(['ativa','pausada','encerrada'] as $status)<option value="{{ $status }}" @selected(old('status', $pelada->status ?: 'ativa') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></label>
                <label class="text-sm font-medium">WhatsApp <input name="whatsapp_contato" value="{{ old('whatsapp_contato', $pelada->whatsapp_contato) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium">Valor mensalista <input name="valor_mensalista" value="{{ old('valor_mensalista', $pelada->valor_mensalista) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
                <label class="text-sm font-medium">Valor diarista <input name="valor_diarista" value="{{ old('valor_diarista', $pelada->valor_diarista) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
            </div>
            <label class="text-sm font-medium">Regras <textarea name="regras" class="mt-1 w-full rounded-md border-slate-300">{{ old('regras', $pelada->regras) }}</textarea></label>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="aceita_diarista" value="1" @checked(old('aceita_diarista', $pelada->aceita_diarista ?? true))> Aceita diarista</label>
            <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Salvar</button>
        </form>
    </div>
</x-app-layout>
