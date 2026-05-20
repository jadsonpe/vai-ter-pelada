<x-app-layout>
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-slate-900">Editar usuario</h1>
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 grid gap-4 rounded-lg border border-slate-200 bg-white p-5">
            @csrf @method('PUT')
            <input name="name" value="{{ old('name', $user->name) }}" class="rounded-md border-slate-300">
            <input name="email" value="{{ old('email', $user->email) }}" class="rounded-md border-slate-300">
            <input name="phone" value="{{ old('phone', $user->phone) }}" class="rounded-md border-slate-300" placeholder="Telefone">
            <select name="role" class="rounded-md border-slate-300">
                @foreach(['admin','organizador','jogador'] as $role)<option value="{{ $role }}" @selected($user->role === $role)>{{ $role }}</option>@endforeach
            </select>
            <label class="flex gap-2 text-sm"><input type="checkbox" name="active" value="1" @checked($user->active)> Ativo</label>
            <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Salvar</button>
        </form>
    </div>
</x-app-layout>
