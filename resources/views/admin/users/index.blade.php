<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">Usuarios</h1>
        <div class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50"><tr><th class="p-3">Nome</th><th class="p-3">Email</th><th class="p-3">Role</th><th class="p-3"></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                        <tr><td class="p-3">{{ $user->name }}</td><td class="p-3">{{ $user->email }}</td><td class="p-3">{{ $user->role }}</td><td class="p-3 text-right"><a class="text-emerald-700" href="{{ route('admin.users.edit', $user) }}">Editar</a></td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-app-layout>
