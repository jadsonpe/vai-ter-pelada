<x-app-layout>
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">{{ $pelada->exists ? 'Editar pelada' : 'Nova pelada' }}</h1>
        <form method="POST" action="{{ $pelada->exists ? route('organizador.peladas.update', $pelada) : route('organizador.peladas.store') }}" class="mt-6 grid gap-4 rounded-lg border border-slate-200 bg-white p-5">
            @csrf
            @if($pelada->exists) @method('PUT') @endif
            <label class="text-sm font-medium">Esporte
                <select name="esporte_id" class="mt-1 w-full rounded-md border-slate-300">
                    @foreach($esportes as $esporte)
                        <option value="{{ $esporte->id }}" @selected(old('esporte_id', $pelada->esporte_id) == $esporte->id)>{{ $esporte->nome }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm font-medium">Nome <input name="nome" value="{{ old('nome', $pelada->nome) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
            <label class="text-sm font-medium">Descricao <textarea name="descricao" class="mt-1 w-full rounded-md border-slate-300">{{ old('descricao', $pelada->descricao) }}</textarea></label>
            <label class="text-sm font-medium">Local <input name="local" value="{{ old('local', $pelada->local) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
            <div class="grid gap-4 sm:grid-cols-3">
                <label class="text-sm font-medium">Dia semana <input type="number" name="dia_semana" min="0" max="6" value="{{ old('dia_semana', $pelada->dia_semana) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
                <label class="text-sm font-medium">Horario <input type="time" name="horario" value="{{ old('horario', optional($pelada->horario)->format('H:i')) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
                <label class="text-sm font-medium">Capacidade <input type="number" name="capacidade" value="{{ old('capacidade', $pelada->capacidade ?: 20) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium">Valor mensalista <input name="valor_mensalista" value="{{ old('valor_mensalista', $pelada->valor_mensalista) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
                <label class="text-sm font-medium">Valor diarista <input name="valor_diarista" value="{{ old('valor_diarista', $pelada->valor_diarista) }}" class="mt-1 w-full rounded-md border-slate-300"></label>
            </div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="ativa" value="1" @checked(old('ativa', $pelada->ativa ?? true))> Ativa</label>
            <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Salvar</button>
        </form>
    </div>
</x-app-layout>
