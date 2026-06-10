<x-app-layout>
    @php
        $description = $pelada->descricao ?: 'Veja detalhes, próximas rodadas e como participar desta pelada no Vai Ter Pelada.';
        $imageUrl = $pelada->imagemUrl() ?: asset('images/esportes/futebol.png');
        $price = $pelada->valor_diarista
            ? 'Diarista R$ '.number_format($pelada->valor_diarista, 2, ',', '.')
            : ($pelada->valor_mensalista ? 'Mensalista R$ '.number_format($pelada->valor_mensalista, 2, ',', '.') : 'Valores a combinar');
    @endphp

    @section('title', $pelada->nome.' | Vai Ter Pelada')

    @push('meta')
        <meta name="description" content="{{ $description }}">
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $pelada->nome }} | Vai Ter Pelada">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:url" content="{{ $shareUrl }}">
        <meta property="og:image" content="{{ $imageUrl }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $pelada->nome }} | Vai Ter Pelada">
        <meta name="twitter:description" content="{{ $description }}">
        <meta name="twitter:image" content="{{ $imageUrl }}">
    @endpush

    <div class="bg-slate-100">
        <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <x-pelada-imagem variant="hero" :src="$pelada->imagemUrl()" :alt="$pelada->nome" />

                <div class="p-5 sm:p-8">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 ring-1 ring-emerald-200">{{ $pelada->esporte->nome }}</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ $pelada->categoriaLabel() }}</span>
                        <span class="rounded-full bg-slate-950 px-3 py-1 text-xs font-bold text-white">{{ $price }}</span>
                    </div>

                    <div class="mt-5 grid gap-6 lg:grid-cols-[minmax(0,1fr)_280px] lg:items-start">
                        <div>
                            <h1 class="text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">{{ $pelada->nome }}</h1>
                            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">{{ $description }}</p>

                            <div class="mt-6 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Local</p>
                                    <p class="mt-1 truncate font-semibold text-slate-950">{{ $pelada->local_nome ?: $pelada->local }}</p>
                                    <p class="mt-0.5 truncate text-sm text-slate-500">{{ $pelada->bairro }}{{ $pelada->cidade ? ' - '.$pelada->cidade : '' }}</p>
                                </div>
                                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Jogadores</p>
                                    <p class="mt-1 text-xl font-black text-slate-950">{{ $membrosAtivosCount }}</p>
                                    <p class="mt-0.5 text-sm text-slate-500">ativo(s)</p>
                                </div>
                                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Vagas</p>
                                    <p class="mt-1 text-xl font-black text-slate-950">{{ $pelada->vagas_totais ?: $pelada->capacidade }}</p>
                                    <p class="mt-0.5 text-sm text-slate-500">totais</p>
                                </div>
                            </div>
                        </div>

                        <aside class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                            <p class="text-sm font-bold text-emerald-950">Quer participar?</p>
                            <p class="mt-1 text-sm leading-6 text-emerald-900">Entre no Vai Ter Pelada para pedir participação e acompanhar as próximas rodadas.</p>
                            <div class="mt-4 grid gap-2">
                                <a href="{{ route('register', ['redirect' => route('peladas.show', $pelada)]) }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">
                                    Cadastrar
                                </a>
                                <a href="{{ route('login', ['redirect' => route('peladas.show', $pelada)]) }}" class="inline-flex items-center justify-center rounded-md border border-emerald-300 bg-white px-4 py-2 text-sm font-bold text-emerald-800 hover:bg-emerald-100">
                                    Entrar
                                </a>
                                <button type="button" data-share-page data-share-url="{{ $shareUrl }}" data-share-title="{{ $pelada->nome }}" data-share-text="Olha esta pelada no Vai Ter Pelada:" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">
                                    Compartilhar
                                </button>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>

            @if($rodadas->isNotEmpty())
                <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-black text-slate-950">Próximas rodadas</h2>
                    <div class="mt-4 divide-y divide-slate-100">
                        @foreach($rodadas as $rodada)
                            <div class="flex flex-col gap-1 py-3 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-bold text-slate-950">{{ $rodada->titulo }}</p>
                                    <p class="mt-0.5 text-sm text-slate-500">{{ $rodada->data_hora->format('d/m/Y H:i') }}</p>
                                </div>
                                <span class="w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">{{ ucfirst($rodada->status) }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </section>
    </div>
</x-app-layout>
