<x-app-layout>
    @section('title', ($jogador->apelido ?: $jogador->name).' | Peladeiro no Vai Ter Pelada')

    @push('meta')
        <meta name="description" content="Perfil esportivo de {{ $jogador->apelido ?: $jogador->name }} no Vai Ter Pelada. Estatisticas, peladas, ranking e reputacao de jogador.">
        <meta property="og:title" content="{{ $jogador->apelido ?: $jogador->name }} | Vai Ter Pelada">
        <meta property="og:description" content="{{ $profile->headline ?: 'Perfil esportivo de peladeiro com estatisticas, peladas e reputacao.' }}">
        <meta property="og:url" content="{{ $profile->shareUrl() }}">
        <meta property="og:image" content="{{ $profile->shareImageUrl() }}">
        <meta property="og:image:type" content="image/svg+xml">
    @endpush

    @php
        $displayName = $jogador->apelido ?: $jogador->name;
        $mainSport = $profile->esportePrincipal?->nome ?: 'Multiesporte';
        $mainPosition = $profile->posicao_favorita
            ?: $jogador->esportePerfis->firstWhere('esporte_id', $profile->esporte_principal_id)?->posicao
            ?: 'Posicao livre';
        $coverStyle = $profile->bannerUrl() ? "background-image: linear-gradient(90deg, rgba(2,6,23,.92), rgba(2,6,23,.45)), url('{$profile->bannerUrl()}')" : null;
        $statCards = [
            ['label' => 'Jogos', 'value' => $stats['jogos']],
            ['label' => 'Vitorias', 'value' => $stats['vitorias']],
            ['label' => 'Gols', 'value' => $stats['gols']],
            ['label' => 'Assist.', 'value' => $stats['assistencias']],
            ['label' => 'MVPs', 'value' => $stats['mvps']],
            ['label' => 'Media', 'value' => number_format($stats['media'], 2)],
            ['label' => 'Aproveit.', 'value' => number_format((float) $stats['aproveitamento'], 0).'%'],
            ['label' => 'Destaques', 'value' => $stats['destaques']],
        ];
    @endphp

    <div class="bg-slate-950 text-white">
        <section
            class="relative overflow-hidden bg-gradient-to-br {{ $profile->coverClass() }}"
            @if($coverStyle) style="{{ $coverStyle }}; background-size: cover; background-position: center;" @endif
        >
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(16,185,129,.22),transparent_32%),linear-gradient(180deg,transparent,rgba(2,6,23,.96))]"></div>
            <div class="relative mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-end">
                        <div class="relative">
                            <x-user-avatar :user="$jogador" size="xl" class="h-28 w-28 border-4 border-emerald-300 shadow-2xl shadow-emerald-950/40" />
                            <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 rounded-full bg-emerald-400 px-3 py-1 text-xs font-black uppercase tracking-wide text-slate-950">{{ $rankingSocial }}</span>
                        </div>

                        <div>
                            <p class="text-sm font-bold uppercase tracking-[0.25em] text-emerald-300">Identificação do Peladeiro</p>
                            <h1 class="mt-2 text-4xl font-black tracking-tight sm:text-5xl">{{ $displayName }}</h1>
                            @if($profile->headline)
                                <p class="mt-3 max-w-2xl text-sm font-medium text-slate-200">{{ $profile->headline }}</p>
                            @endif
                            <div class="mt-4 flex flex-wrap gap-2 text-xs font-bold uppercase tracking-wide">
                                <span class="rounded-full bg-white/10 px-3 py-1 text-white ring-1 ring-white/15">{{ $mainSport }}</span>
                                <span class="rounded-full bg-white/10 px-3 py-1 text-white ring-1 ring-white/15">{{ $mainPosition }}</span>
                                @if($jogador->idade())
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-white ring-1 ring-white/15">{{ $jogador->idade() }} anos</span>
                                @endif
                                @if($jogador->cidade || $jogador->bairro)
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-white ring-1 ring-white/15">{{ collect([$jogador->bairro, $jogador->cidade])->filter()->implode(' - ') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-2 sm:grid-cols-2 lg:min-w-[520px] lg:grid-cols-4">
                        <a href="{{ route('peladas.index') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-400 px-4 py-3 text-sm font-black text-slate-950 hover:bg-emerald-300">Convidar para pelada</a>
                        <button type="button" onclick="navigator.share ? navigator.share({title: document.title, url: '{{ $profile->shareUrl() }}'}) : navigator.clipboard.writeText('{{ $profile->shareUrl() }}')" class="inline-flex items-center justify-center rounded-md border border-white/15 bg-white/10 px-4 py-3 text-sm font-bold text-white hover:bg-white/15">Compartilhar perfil</button>
                        <a href="{{ $whatsappShareUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-md border border-emerald-300/40 bg-emerald-300/10 px-4 py-3 text-sm font-bold text-emerald-100 hover:bg-emerald-300/20">WhatsApp</a>
                        @auth
                            @if(auth()->id() === $jogador->id)
                                <a href="{{ route('perfil.edit') }}" class="inline-flex items-center justify-center rounded-md border border-white/15 bg-white/5 px-4 py-3 text-sm font-bold text-slate-200">Editar perfil</a>
                            @elseif($isFollowing)
                                <form method="POST" action="{{ route('peladeiros.unfollow', $profile) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full rounded-md border border-emerald-300/40 bg-emerald-300/10 px-4 py-3 text-sm font-bold text-emerald-100 hover:bg-emerald-300/20">Seguindo</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('peladeiros.follow', $profile) }}">
                                    @csrf
                                    <button class="w-full rounded-md border border-white/15 bg-white/5 px-4 py-3 text-sm font-bold text-slate-100 hover:bg-white/10">Seguir</button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-md border border-white/15 bg-white/5 px-4 py-3 text-sm font-bold text-slate-100 hover:bg-white/10">Seguir</a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>

        <main class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">
            <section class="grid gap-3 sm:grid-cols-2">
                <a href="{{ route('peladeiros.followers', $profile) }}" class="rounded-lg border border-white/10 bg-white/[0.06] p-4 shadow-xl shadow-slate-950/20 hover:border-emerald-300/60">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Seguidores</p>
                    <p class="mt-2 text-3xl font-black text-white">{{ $followersCount }}</p>
                </a>
                <a href="{{ route('peladeiros.following', $profile) }}" class="rounded-lg border border-white/10 bg-white/[0.06] p-4 shadow-xl shadow-slate-950/20 hover:border-emerald-300/60">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Seguindo</p>
                    <p class="mt-2 text-3xl font-black text-white">{{ $followingCount }}</p>
                </a>
            </section>

            <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($statCards as $card)
                    <div class="rounded-lg border border-white/10 bg-white/[0.06] p-4 shadow-xl shadow-slate-950/20">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-black text-white">{{ $card['value'] }}</p>
                    </div>
                @endforeach
            </section>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
                <section class="space-y-6">
                    <div class="rounded-lg border border-white/10 bg-white/[0.06] p-5 shadow-xl shadow-slate-950/20">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-xl font-black">Nivel e reputacao</h2>
                                <p class="mt-1 text-sm text-slate-400">Ranking social preparado para votos de craque, perna de pau, destaques e titulos.</p>
                            </div>
                            <span class="rounded-full bg-emerald-400 px-4 py-2 text-sm font-black text-slate-950">{{ $rankingSocial }}</span>
                        </div>
                        <div class="mt-5 grid gap-3 sm:grid-cols-5">
                            @foreach(['Perna de Pau', 'Reserva de Luxo', 'Craque do Baba', 'Rei da Quadra', 'Dono da Bola'] as $level)
                                <div class="rounded-md border {{ $level === $rankingSocial ? 'border-emerald-300 bg-emerald-300/15 text-emerald-100' : 'border-white/10 bg-slate-900/50 text-slate-400' }} p-3 text-center text-xs font-black uppercase tracking-wide">
                                    {{ $level }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-lg border border-white/10 bg-white/[0.06] p-5 shadow-xl shadow-slate-950/20">
                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-black">Peladas que participa</h2>
                                <p class="mt-1 text-sm text-slate-400">Clubes, turmas e campos onde o jogador marca presenca.</p>
                            </div>
                            <span class="text-sm font-bold text-slate-400">{{ $peladas->count() }} ativa(s)</span>
                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            @forelse($peladas as $membro)
                                @php
                                    $pelada = $membro->pelada;
                                    $position = $jogador->esportePerfis->firstWhere('esporte_id', $pelada->esporte_id)?->posicao ?: $profile->posicao_favorita ?: 'Flex';
                                @endphp
                                <a href="{{ route('peladas.show', $pelada) }}" class="group overflow-hidden rounded-lg border border-white/10 bg-slate-900/70 hover:border-emerald-300/60">
                                    <x-pelada-imagem :src="$pelada->imagemUrl()" :alt="$pelada->nome" class="h-32 w-full object-cover opacity-90 transition group-hover:scale-[1.02]" />
                                    <div class="p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="font-black text-white">{{ $pelada->nome }}</p>
                                                <p class="mt-1 text-sm text-slate-400">{{ collect([$pelada->bairro, $pelada->cidade])->filter()->implode(' - ') }}</p>
                                            </div>
                                            <span class="rounded-full bg-emerald-400/15 px-2.5 py-1 text-xs font-bold text-emerald-200">{{ $pelada->categoriaLabel() }}</span>
                                        </div>
                                        <div class="mt-4 flex flex-wrap gap-2 text-xs font-bold uppercase tracking-wide text-slate-300">
                                            <span class="rounded-full bg-white/10 px-2.5 py-1">{{ $pelada->esporte->nome }}</span>
                                            <span class="rounded-full bg-white/10 px-2.5 py-1">{{ $position }}</span>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <p class="rounded-lg border border-white/10 bg-slate-900/70 p-5 text-sm text-slate-400 md:col-span-2">Este peladeiro ainda nao aparece em peladas ativas.</p>
                            @endforelse
                        </div>
                    </div>
                </section>

                <aside class="space-y-6">
                    <section class="rounded-lg border border-white/10 bg-white/[0.06] p-5 shadow-xl shadow-slate-950/20">
                        <h2 class="text-xl font-black">Bio esportiva</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-300">{{ $profile->bio ?: 'Peladeiro em evolucao. Em breve mais dados de desempenho, conquistas e estilo de jogo.' }}</p>
                    </section>

                    <section class="rounded-lg border border-white/10 bg-white/[0.06] p-5 shadow-xl shadow-slate-950/20">
                        <h2 class="text-xl font-black">Votos recebidos</h2>
                        <div class="mt-4 space-y-2">
                            @foreach($voteLabels as $type => $label)
                                <div class="flex items-center justify-between gap-3 rounded-md bg-slate-900/70 px-3 py-2 text-sm">
                                    <span class="font-bold text-white">{{ $label }}</span>
                                    <span class="text-slate-300">{{ $profile->votes->where('type', $type)->count() }}</span>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="rounded-lg border border-white/10 bg-white/[0.06] p-5 shadow-xl shadow-slate-950/20">
                        <h2 class="text-xl font-black">Posicoes</h2>
                        <div class="mt-4 space-y-2">
                            @forelse($jogador->esportePerfis->filter(fn ($perfil) => filled($perfil->posicao)) as $perfil)
                                <div class="flex items-center justify-between gap-3 rounded-md bg-slate-900/70 px-3 py-2 text-sm">
                                    <span class="font-bold text-white">{{ $perfil->esporte->nome }}</span>
                                    <span class="text-slate-300">{{ $perfil->posicao }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400">Nenhuma posicao especifica informada.</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-lg border border-white/10 bg-white/[0.06] p-5 shadow-xl shadow-slate-950/20">
                        <h2 class="text-xl font-black">Social</h2>
                        <div class="mt-4 grid gap-2">
                            @forelse($socialLinks as $platform => $link)
                                <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="rounded-md border border-white/10 bg-slate-900/70 px-3 py-2 text-sm font-bold capitalize text-slate-200 hover:border-emerald-300/60">{{ $platform }}</a>
                            @empty
                                <p class="text-sm text-slate-400">Sem redes sociais publicas.</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-lg border border-white/10 bg-white/[0.06] p-5 shadow-xl shadow-slate-950/20">
                        <h2 class="text-xl font-black">Conquistas</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @php($achievements = $profile->achievements->map(fn ($achievement) => $achievement->title)->merge($jogador->badges->map(fn ($badge) => $badge->nome)))
                            @forelse($achievements as $achievement)
                                <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-bold text-emerald-100">{{ $achievement }}</span>
                            @empty
                                <p class="text-sm text-slate-400">Nenhuma conquista desbloqueada ainda.</p>
                            @endforelse
                        </div>
                    </section>
                </aside>
            </div>
        </main>
    </div>
</x-app-layout>
