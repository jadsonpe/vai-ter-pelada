<x-app-layout>
    @section('title', 'Buscar jogadores | Vai Ter Pelada')

    <section class="bg-slate-950 text-white">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <p class="text-sm font-black uppercase tracking-[0.2em] text-emerald-300">Jogadores</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Busque peladeiros pelo nome, apelido ou @.</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">Encontre perfis públicos, veja a foto do jogador e acesse o card completo.</p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('jogadores.index') }}" class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <x-input-label for="busca" value="Pesquisar jogador" />
            <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                <x-text-input
                    id="busca"
                    name="busca"
                    type="search"
                    class="block w-full"
                    :value="$term"
                    placeholder="Ex: Gustavo, Canhota ou @gustavocanhota"
                    autocomplete="off"
                />
                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-black text-white transition hover:bg-emerald-700">
                    Buscar
                </button>
            </div>
            <p class="mt-2 text-xs text-slate-500">Digite pelo menos 2 caracteres para buscar.</p>
        </form>

        @if($newPlayers->isNotEmpty())
            <section class="mt-8">
                <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-black text-slate-950">Novos peladeiros cadastrados</h2>
                        <p class="mt-1 text-sm text-slate-500">Perfis completos com foto, cidade, esporte favorito e posição.</p>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    @foreach($newPlayers as $player)
                        @php
                            $profile = $player->playerProfile;
                            $position = $profile?->posicao_favorita ?: $player->posicao;
                            $profileUrl = $profile && $profile->publico
                                ? route('peladeiros.show', $profile)
                                : route('jogadores.show', $player);
                        @endphp

                        <a href="{{ $profileUrl }}" class="{{ $loop->iteration > 5 ? 'hidden sm:flex' : 'flex' }} group min-w-0 items-center gap-3 rounded-lg border border-slate-200 bg-white p-3 shadow-sm transition hover:border-emerald-300 hover:shadow-md">
                            <x-user-avatar :user="$player" size="md" />
                            <div class="min-w-0 flex-1">
                                <div class="flex min-w-0 items-center gap-2">
                                    <h3 class="truncate text-sm font-black text-slate-950 group-hover:text-emerald-700">{{ $player->name }}</h3>
                                    @if($player->username)
                                        <span class="hidden shrink-0 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600 xl:inline">{{ '@'.$player->username }}</span>
                                    @endif
                                </div>
                                <p class="mt-0.5 truncate text-xs font-semibold text-slate-500">{{ $player->cidade }}/{{ $player->estado }}</p>
                                <p class="mt-0.5 truncate text-xs text-slate-400">{{ $profile?->esportePrincipal?->nome }} · {{ $position }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="mt-8">
            @if($players === null)
                <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-black text-slate-950">Peladeiros recentes</h2>
                        <p class="mt-1 text-sm text-slate-500">Jogadores ativos que participaram de rodadas nos últimos 90 dias.</p>
                    </div>
                </div>

                @if($featuredPlayers->isEmpty())
                    <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                        <p class="font-semibold text-slate-800">Comece pesquisando por nome, apelido ou username.</p>
                        <p class="mt-2 text-sm text-slate-500">A busca só consulta o banco quando você envia o formulário.</p>
                    </div>
                @else
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach($featuredPlayers as $player)
                            @php
                                $profile = $player->playerProfile;
                                $profileUrl = $profile && $profile->publico
                                    ? route('peladeiros.show', $profile)
                                    : route('jogadores.show', $player);
                            @endphp

                            <a href="{{ $profileUrl }}" class="group flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-300 hover:shadow-md">
                                <x-user-avatar :user="$player" size="lg" />
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="truncate text-lg font-black text-slate-950 group-hover:text-emerald-700">{{ $player->name }}</h2>
                                        @if($player->username)
                                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">{{ '@'.$player->username }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 truncate text-sm text-slate-500">{{ $player->apelido ?: 'Sem apelido informado' }}</p>
                                    <p class="mt-1 text-xs font-semibold text-slate-400">{{ $player->participacoes_recentes_count }} participação(ões) recente(s)</p>
                                </div>
                                <span class="shrink-0 text-sm font-bold text-emerald-700">Ver perfil</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            @elseif($players->isEmpty())
                <div class="rounded-lg border border-slate-200 bg-white p-8 text-center shadow-sm">
                    <p class="font-semibold text-slate-800">Nenhum jogador encontrado.</p>
                    <p class="mt-2 text-sm text-slate-500">Tente outro nome, apelido ou @.</p>
                </div>
            @else
                <div class="mb-4 flex items-center justify-between gap-3">
                    <p class="text-sm font-semibold text-slate-600">
                        {{ $players->total() }} {{ Str::plural('jogador encontrado', $players->total()) }}
                    </p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($players as $player)
                        @php
                            $profile = $player->playerProfile;
                            $profileUrl = $profile && $profile->publico
                                ? route('peladeiros.show', $profile)
                                : route('jogadores.show', $player);
                        @endphp

                        <a href="{{ $profileUrl }}" class="group flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-300 hover:shadow-md">
                            <x-user-avatar :user="$player" size="lg" />
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="truncate text-lg font-black text-slate-950 group-hover:text-emerald-700">{{ $player->name }}</h2>
                                    @if($player->username)
                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">{{ '@'.$player->username }}</span>
                                    @endif
                                </div>
                                <p class="mt-1 truncate text-sm text-slate-500">{{ $player->apelido ?: 'Sem apelido informado' }}</p>
                            </div>
                            <span class="shrink-0 text-sm font-bold text-emerald-700">Ver perfil</span>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $players->links() }}
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
