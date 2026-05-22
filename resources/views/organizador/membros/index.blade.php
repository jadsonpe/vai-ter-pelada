<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">Membros - {{ $pelada->nome }}</h1>
        <form method="POST" action="{{ route('organizador.peladas.membros.store', $pelada) }}" class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-5 md:grid-cols-[1fr_180px]">
            @csrf
            <div class="md:col-span-2">
                <h2 class="font-semibold text-slate-900">Convidar jogador</h2>
                <p class="mt-1 text-sm text-slate-600">Informe o e-mail de um usuário cadastrado. Ele receberá um convite e precisa aceitar para entrar na pelada.</p>
            </div>
            <div>
                <label for="email" class="text-sm font-medium text-slate-700">E-mail do jogador</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded-md border-slate-300" placeholder="jogador@email.com">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <label for="tipo" class="text-sm font-medium text-slate-700">Tipo</label>
                <select id="tipo" name="tipo" class="mt-1 w-full rounded-md border-slate-300">
                    <option value="diarista" @selected(old('tipo') === 'diarista')>Diarista</option>
                    <option value="mensalista" @selected(old('tipo') === 'mensalista')>Mensalista</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="mensagem" class="text-sm font-medium text-slate-700">Mensagem opcional</label>
                <textarea id="mensagem" name="mensagem" rows="2" class="mt-1 w-full rounded-md border-slate-300" placeholder="Ex: Estamos fechando o grupo de quarta-feira.">{{ old('mensagem') }}</textarea>
                <x-input-error :messages="$errors->get('mensagem')" class="mt-2" />
            </div>
            <button class="w-full rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white md:col-span-2">Enviar convite</button>
        </form>
        <form method="POST" action="{{ route('organizador.peladas.membros.update-many', $pelada) }}" class="mt-6 rounded-lg border border-slate-200 bg-white">
            @csrf
            @method('PATCH')
            <div class="divide-y divide-slate-100">
                @forelse($pelada->membros as $membro)
                    <div class="grid gap-3 p-4 lg:grid-cols-[1fr_180px_160px_130px_auto] lg:items-center">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $membro->nomeExibicao() }}</p>
                            <p class="text-sm text-slate-500">{{ $membro->user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $membro->user->email }}</p>
                        </div>
                        <input name="membros[{{ $membro->id }}][apelido]" value="{{ old("membros.$membro->id.apelido", $membro->apelido) }}" class="w-full rounded-md border-slate-300" placeholder="Apelido">
                        <select name="membros[{{ $membro->id }}][tipo]" class="w-full rounded-md border-slate-300">
                            <option value="diarista" @selected(old("membros.$membro->id.tipo", $membro->tipo) === 'diarista')>Diarista</option>
                            <option value="mensalista" @selected(old("membros.$membro->id.tipo", $membro->tipo) === 'mensalista')>Mensalista</option>
                        </select>
                        <select name="membros[{{ $membro->id }}][status]" class="w-full rounded-md border-slate-300">
                            <option value="ativo" @selected(old("membros.$membro->id.status", $membro->status) === 'ativo')>Ativo</option>
                            <option value="pendente" @selected(old("membros.$membro->id.status", $membro->status) === 'pendente')>Pendente</option>
                            <option value="bloqueado" @selected(old("membros.$membro->id.status", $membro->status) === 'bloqueado')>Bloqueado</option>
                            <option value="saiu" @selected(old("membros.$membro->id.status", $membro->status) === 'saiu')>Saiu</option>
                            <option value="inativo" @selected(old("membros.$membro->id.status", $membro->status) === 'inativo')>Inativo</option>
                        </select>
                        <input type="hidden" name="membros[{{ $membro->id }}][prioridade]" value="{{ $membro->prioridade }}">
                        <button type="submit" form="remover-membro-{{ $membro->id }}" onclick="return confirm('Remover este membro desta pelada?')" class="w-full rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 lg:w-auto">Remover</button>
                    </div>
                @empty
                    <p class="p-5 text-sm text-slate-600">Nenhum membro ativo cadastrado ainda.</p>
                @endforelse
            </div>
            <div class="flex flex-col gap-3 border-t border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">Altere todos os apelidos e salve de uma vez.</p>
                <button class="w-full rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white sm:w-auto">Salvar todos</button>
            </div>
        </form>

        @foreach($pelada->membros as $membro)
            <form id="remover-membro-{{ $membro->id }}" method="POST" action="{{ route('organizador.peladas.membros.destroy', [$pelada, $membro]) }}">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>
</x-app-layout>
