<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('shared.status')

        <x-page-header
            eyebrow="Admin"
            title="Usuarios"
            description="Gerencie perfis, permissoes, bloqueios e limites de criacao de peladas."
        />

        <div class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[860px] w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Usuario</th>
                            <th class="px-4 py-3">Contato</th>
                            <th class="px-4 py-3">Perfil</th>
                            <th class="px-4 py-3">Plano</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Acoes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <x-user-avatar :user="$user" size="sm" />
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $user->apelido ?: 'Sem apelido' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-slate-800">{{ $user->email }}</p>
                                    <p class="text-xs text-slate-500">{{ $user->phone ?: 'Telefone nao informado' }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <x-status-badge>{{ ucfirst($user->role) }}</x-status-badge>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-slate-800">{{ ucfirst($user->plano ?: 'gratis') }}</p>
                                    <p class="text-xs text-slate-500">{{ $user->limite_peladas ?: 1 }} pelada(s)</p>
                                </td>
                                <td class="px-4 py-4">
                                    <x-status-badge :variant="$user->active && $user->status === 'ativo' ? 'ativo' : ($user->status ?: 'inativo')">
                                        {{ $user->active ? ucfirst($user->status ?: 'ativo') : 'Inativo' }}
                                    </x-status-badge>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <a class="inline-flex rounded-md border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50" href="{{ route('admin.users.edit', $user) }}">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">Nenhum usuario encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">{{ $users->links() }}</div>
    </div>
</x-app-layout>

