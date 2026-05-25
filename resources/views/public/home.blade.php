<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        @include('shared.status')
    </div>

    {{-- Hero Section otimizada para visitantes --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(16,185,129,0.15),transparent_30%),radial-gradient(circle_at_bottom_left,_rgba(15,23,42,0.95),transparent_40%)]"></div>
        
        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-20">
            <div class="text-center lg:text-left">
                {{-- Badge --}}
                <div class="mb-6 flex justify-center lg:justify-start">
                    <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-4 py-1.5 text-xs font-semibold tracking-wide text-emerald-300 backdrop-blur-sm">
                        🎯 Encontre. Organize. Jogue.
                    </span>
                </div>

                {{-- Título --}}
                <h1 class="mx-auto max-w-3xl text-3xl font-bold tracking-tight sm:text-4xl md:text-5xl lg:mx-0">
                    Sua plataforma de peladas do 
                    <span class="bg-gradient-to-r from-emerald-400 to-emerald-300 bg-clip-text text-transparent">agendamento à finalização</span>
                </h1>

                {{-- Subtítulo --}}
                <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-slate-300 lg:mx-0">
                    Veja peladas disponíveis, gerencie confirmações, consulte endereços e mantenha seu time alinhado. 
                    <span class="font-semibold text-emerald-300">Tudo em um só lugar!</span>
                </p>

                {{-- Call to Action principal para não logados --}}
                <div class="mt-8 flex flex-col items-center gap-4 sm:flex-row lg:justify-start">
                    <a href="{{ route('peladas.index') }}" 
                       class="inline-flex w-full items-center justify-center rounded-full bg-emerald-500 px-8 py-3.5 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-500/30 transition-all hover:scale-105 hover:bg-emerald-400 sm:w-auto">
                        🔍 Explorar peladas
                    </a>
                    <a href="{{ route('register') }}" 
                       class="inline-flex w-full items-center justify-center gap-2 rounded-full border border-white/20 bg-white/5 px-8 py-3.5 text-sm font-semibold text-white backdrop-blur-sm transition-all hover:border-emerald-400/50 hover:bg-white/10 hover:text-emerald-100 sm:w-auto">
                        📝 Criar conta grátis
                        <span class="text-xs text-emerald-300">→</span>
                    </a>
                </div>

                {{-- Benefícios principais (mais compactos) --}}
                <div class="mt-12 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300">👥</div>
                        <p class="text-xs text-slate-200">Gestão de participantes</p>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300">📅</div>
                        <p class="text-xs text-slate-200">Calendário intuitivo</p>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300">📍</div>
                        <p class="text-xs text-slate-200">Localização fácil</p>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300">💬</div>
                        <p class="text-xs text-slate-200">Comunicação rápida</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Section - Dados motivadores --}}
    <section class="border-b border-slate-200 bg-gradient-to-r from-emerald-50 to-white">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid gap-6 text-center sm:grid-cols-3">
                <div class="rounded-2xl p-4">
                    <p class="text-3xl font-bold text-emerald-600">+50</p>
                    <p class="mt-1 text-sm text-slate-600">Peladas realizadas</p>
                </div>
                <div class="rounded-2xl p-4">
                    <p class="text-3xl font-bold text-emerald-600">+500</p>
                    <p class="mt-1 text-sm text-slate-600">Jogadores ativos</p>
                </div>
                <div class="rounded-2xl p-4">
                    <p class="text-3xl font-bold text-emerald-600">15</p>
                    <p class="mt-1 text-sm text-slate-600">Bairros atendidos</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Seção "Como funciona" para novos usuários --}}
    <section class="bg-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-slate-900 sm:text-3xl">✨ Como funciona?</h2>
                <p class="mt-3 text-slate-600">3 passos simples para começar a jogar</p>
            </div>
            
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                <div class="text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-2xl font-bold text-emerald-600">1</div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">Encontre uma pelada</h3>
                    <p class="mt-2 text-sm text-slate-600">Explore as peladas disponíveis perto de você</p>
                </div>
                <div class="text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-2xl font-bold text-emerald-600">2</div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">Confirme presença</h3>
                    <p class="mt-2 text-sm text-slate-600">Garanta sua vaga com um clique</p>
                </div>
                <div class="text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-2xl font-bold text-emerald-600">3</div>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">Jogue e repita</h3>
                    <p class="mt-2 text-sm text-slate-600">Participe das partidas e crie novas amizades</p>
                </div>
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    Começar agora
                    <span>→</span>
                </a>
            </div>
        </div>
    </section>

    {{-- Peladas em destaque - simplificado para visitantes --}}
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-2 border-b border-slate-200 pb-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">⚽ Peladas em destaque</h2>
                <p class="mt-1 text-sm text-slate-500">Confira as próximas partidas perto de você</p>
            </div>
            <a class="text-sm font-semibold text-emerald-600 hover:text-emerald-700" href="{{ route('peladas.index') }}">
                Ver todas →
            </a>
        </div>

        @if($peladas->isNotEmpty())
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach($peladas as $pelada)
                    <div class="group overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition-all hover:shadow-lg hover:border-emerald-200">
                        <a href="{{ route('peladas.show', $pelada) }}">
                            <x-pelada-imagem :src="$pelada->imagemUrl()" :alt="$pelada->nome" class="h-48 w-full object-cover transition-transform group-hover:scale-105" />
                            <div class="p-5">
                                <div class="flex items-center justify-between">
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">
                                        {{ $pelada->esporte->nome }}
                                    </span>
                                    @if($pelada->data)
                                        <span class="text-xs text-slate-500">{{ $pelada->data->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                                <h3 class="mt-3 text-lg font-semibold text-slate-900 group-hover:text-emerald-600">{{ $pelada->nome }}</h3>
                                <p class="mt-1 text-sm text-slate-600">
                                    📍 {{ $pelada->local_nome ?: $pelada->local }}
                                </p>
                                <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                                    <p class="text-xs text-slate-500">Organizador: {{ $pelada->organizador->name }}</p>
                                    <span class="text-xs font-medium text-emerald-600 group-hover:underline">Ver detalhes →</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-lg bg-slate-50 p-8 text-center">
                <p class="text-slate-500">Nenhuma pelada disponível no momento.</p>
                <p class="mt-2 text-sm text-slate-400">Volte em breve para conferir novidades!</p>
            </div>
        @endif

        {{-- Patrocinadores mais organizados --}}
        @if($patrocinadores->isNotEmpty())
            <div class="mt-12 rounded-xl border border-slate-200 bg-slate-50/50 p-6">
                <h2 class="text-center text-sm font-semibold uppercase tracking-wide text-slate-500">Nossos patrocinadores</h2>
                <div class="mt-4 flex flex-wrap items-center justify-center gap-4">
                    @foreach($patrocinadores as $patrocinador)
                        <a class="rounded-lg bg-white px-5 py-2 text-sm text-slate-700 shadow-sm transition hover:shadow-md hover:text-emerald-600" 
                           href="{{ $patrocinador->link ?: $patrocinador->site_url ?: '#' }}" 
                           target="_blank" 
                           rel="noopener noreferrer">
                            {{ $patrocinador->nome }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Call to action final para conversão --}}
        <div class="mt-12 rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-500 p-6 text-center text-white shadow-lg sm:p-8">
            <h3 class="text-xl font-bold sm:text-2xl">Pronto para começar a jogar?</h3>
            <p class="mt-2 text-emerald-50">Cadastre-se gratuitamente e participe das próximas peladas!</p>
            <a href="{{ route('register') }}" class="mt-4 inline-block rounded-full bg-white px-6 py-2.5 text-sm font-semibold text-emerald-600 transition hover:bg-slate-100">
                Criar conta agora
            </a>
        </div>
    </section>
</x-app-layout>