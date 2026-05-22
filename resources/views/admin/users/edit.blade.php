<x-app-layout>
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-900">Editar usuário</h1>
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 grid gap-4 rounded-lg border border-slate-200 bg-white p-5">
            @csrf @method('PUT')
            <input name="name" value="{{ old('name', $user->name) }}" class="rounded-md border-slate-300">
            <input name="apelido" value="{{ old('apelido', $user->apelido) }}" class="rounded-md border-slate-300" placeholder="Apelido">
            <input name="email" value="{{ old('email', $user->email) }}" class="rounded-md border-slate-300">
            <input name="phone" value="{{ old('phone', $user->phone) }}" class="rounded-md border-slate-300" placeholder="Telefone">
            <div class="grid gap-4 sm:grid-cols-2">
                <input name="cidade" value="{{ old('cidade', $user->cidade) }}" class="rounded-md border-slate-300" placeholder="Cidade">
                <input name="bairro" value="{{ old('bairro', $user->bairro) }}" class="rounded-md border-slate-300" placeholder="Bairro">
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <input name="posicao" value="{{ old('posicao', $user->posicao) }}" class="rounded-md border-slate-300" placeholder="Posição">
                <input type="number" min="1" max="5" name="nivel" value="{{ old('nivel', $user->nivel) }}" class="rounded-md border-slate-300" placeholder="Nível 1-5">
            </div>
            <select name="role" class="rounded-md border-slate-300">
                @foreach(['admin','organizador','jogador'] as $role)<option value="{{ $role }}" @selected($user->role === $role)>{{ $role }}</option>@endforeach
            </select>
            <select name="status" class="rounded-md border-slate-300">
                @foreach(['ativo','bloqueado','inativo'] as $status)<option value="{{ $status }}" @selected(($user->status ?: 'ativo') === $status)>{{ $status }}</option>@endforeach
            </select>
            <div class="grid gap-4 sm:grid-cols-2">
                <select name="plano" class="rounded-md border-slate-300">
                    @foreach(['gratis' => 'Grátis', 'plus' => 'Plus', 'ilimitado' => 'Ilimitado'] as $valor => $rotulo)<option value="{{ $valor }}" @selected(($user->plano ?: 'gratis') === $valor)>{{ $rotulo }}</option>@endforeach
                </select>
                <input type="number" min="0" name="limite_peladas" value="{{ old('limite_peladas', $user->limite_peladas ?: 1) }}" class="rounded-md border-slate-300" placeholder="Limite de peladas">
            </div>
            <label class="flex gap-2 text-sm"><input type="checkbox" name="active" value="1" @checked($user->active)> Ativo</label>
            <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Salvar</button>
        </form>
    </div>
</x-app-layout>
