<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <x-page-header
            eyebrow="Admin"
            title="Editar usuario"
            description="Atualize permissoes, status, plano e dados principais do usuario."
        >
            <x-slot name="actions">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Voltar</a>
            </x-slot>
        </x-page-header>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 space-y-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-4 rounded-lg bg-slate-50 p-4">
                <x-user-avatar :user="$user" size="lg" />
                <div>
                    <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                </div>
            </div>

            <section>
                <h2 class="font-semibold text-slate-900">Dados pessoais</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <label class="text-sm font-medium text-slate-700">Nome
                        <input name="name" value="{{ old('name', $user->name) }}" class="mt-1 w-full rounded-md border-slate-300">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Apelido
                        <input name="apelido" value="{{ old('apelido', $user->apelido) }}" class="mt-1 w-full rounded-md border-slate-300">
                    </label>
                    <label class="text-sm font-medium text-slate-700">E-mail
                        <input name="email" value="{{ old('email', $user->email) }}" class="mt-1 w-full rounded-md border-slate-300">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Telefone
                        <input name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 w-full rounded-md border-slate-300">
                    </label>
                </div>
            </section>

            <section>
                <h2 class="font-semibold text-slate-900">Perfil esportivo</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <label class="text-sm font-medium text-slate-700">Cidade
                        <input name="cidade" value="{{ old('cidade', $user->cidade) }}" class="mt-1 w-full rounded-md border-slate-300">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Bairro
                        <input name="bairro" value="{{ old('bairro', $user->bairro) }}" class="mt-1 w-full rounded-md border-slate-300">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Posição
                        <input name="posicao" value="{{ old('posicao', $user->posicao) }}" class="mt-1 w-full rounded-md border-slate-300">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Nivel
                        <input type="number" min="1" max="5" name="nivel" value="{{ old('nivel', $user->nivel) }}" class="mt-1 w-full rounded-md border-slate-300">
                    </label>
                </div>
            </section>

            <section>
                <h2 class="font-semibold text-slate-900">Acesso e plano</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <label class="text-sm font-medium text-slate-700">Perfil
                        <select name="role" class="mt-1 w-full rounded-md border-slate-300">
                            @foreach(['admin', 'organizador', 'jogador'] as $role)
                                <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-sm font-medium text-slate-700">Status
                        <select name="status" class="mt-1 w-full rounded-md border-slate-300">
                            @foreach(['ativo', 'bloqueado', 'inativo'] as $status)
                                <option value="{{ $status }}" @selected(($user->status ?: 'ativo') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-sm font-medium text-slate-700">Plano
                        <select name="plano" class="mt-1 w-full rounded-md border-slate-300">
                            @foreach(['gratis' => 'Gratis', 'plus' => 'Plus', 'ilimitado' => 'Ilimitado'] as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(($user->plano ?: 'gratis') === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-sm font-medium text-slate-700">Limite de peladas
                        <input type="number" min="0" name="limite_peladas" value="{{ old('limite_peladas', $user->limite_peladas ?: 1) }}" class="mt-1 w-full rounded-md border-slate-300">
                    </label>
                </div>
                <label class="mt-4 flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="active" value="1" @checked($user->active)>
                    Conta ativa
                </label>
            </section>

            <div class="flex justify-end">
                <button class="rounded-md bg-emerald-600 px-5 py-2.5 font-semibold text-white hover:bg-emerald-700">Salvar alteracoes</button>
            </div>
        </form>
    </div>
</x-app-layout>


