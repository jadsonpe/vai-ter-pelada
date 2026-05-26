<x-app-layout>
    @php
        $displayName = $jogador->apelido ?: $jogador->name;
        $title = $mode === 'seguidores' ? 'Seguidores' : 'Seguindo';
    @endphp

    @section('title', $title.' de '.$displayName.' | Vai Ter Pelada')

    <div class="min-h-screen bg-slate-950 text-white">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <a href="{{ route('peladeiros.show', $profile) }}" class="text-sm font-bold text-emerald-300 hover:text-emerald-200">Voltar ao perfil</a>
                    <h1 class="mt-2 text-3xl font-black">{{ $title }}</h1>
                    <p class="mt-1 text-sm text-slate-400">{{ $displayName }} no Vai Ter Pelada</p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($users as $user)
                    @php($publicProfile = $user->publicProfile())
                    <a href="{{ route('peladeiros.show', $publicProfile) }}" class="rounded-lg border border-white/10 bg-white/[0.06] p-4 shadow-xl shadow-slate-950/20 hover:border-emerald-300/60">
                        <div class="flex items-center gap-3">
                            <x-user-avatar :user="$user" size="md" />
                            <div class="min-w-0">
                                <p class="truncate font-black text-white">{{ $user->apelido ?: $user->name }}</p>
                                <p class="truncate text-sm text-slate-400">{{ $publicProfile->esportePrincipal?->nome ?: 'Peladeiro' }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-lg border border-white/10 bg-white/[0.06] p-6 text-sm text-slate-300 sm:col-span-2 lg:col-span-3">
                        Nenhum jogador por aqui ainda.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
