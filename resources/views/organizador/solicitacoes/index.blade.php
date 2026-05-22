<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">Solicitações - {{ $pelada->nome }}</h1>
        <div class="mt-6 divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white">
            @forelse($solicitacoes as $solicitacao)
                @php($isConvite = str_starts_with($solicitacao->tipo_solicitacao ?? '', 'convite_'))
                <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold">{{ $solicitacao->user->name }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase text-emerald-700">{{ $isConvite ? 'Convite enviado - '.str_replace('convite_', '', $solicitacao->tipo_solicitacao) : str_replace('_', ' ', $solicitacao->tipo_solicitacao ?: $solicitacao->tipo) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Recebida em {{ $solicitacao->created_at->format('d/m/Y H:i') }}</p>
                        @if($solicitacao->user->phone)
                            <p class="mt-1 text-sm text-slate-700">WhatsApp: <a class="font-semibold text-emerald-700" href="https://wa.me/+55{{ preg_replace('/\D+/', '', $solicitacao->user->phone) }}" target="_blank">{{ $solicitacao->user->phone }}</a></p>
                        @endif
                        <p class="text-sm text-slate-600">{{ $solicitacao->mensagem ?: 'Sem mensagem' }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase text-slate-500">{{ $solicitacao->status }}</p>
                    </div>
                    @if($solicitacao->status === 'pendente' && ! $isConvite)
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('organizador.solicitacoes.aprovar', $solicitacao) }}">@csrf @method('PATCH')<button class="rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white">Aprovar</button></form>
                            <form method="POST" action="{{ route('organizador.solicitacoes.recusar', $solicitacao) }}">@csrf @method('PATCH')<button class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold">Recusar</button></form>
                        </div>
                    @elseif($solicitacao->status === 'pendente' && $isConvite)
                        <span class="rounded-md bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600">Aguardando resposta</span>
                    @endif
                </div>
            @empty
                <div class="p-6 text-center">
                    <p class="font-semibold text-slate-900">Nenhuma solicitação recebida ainda.</p>
                    <p class="mt-1 text-sm text-slate-600">Quando jogadores pedirem entrada ou solicitarem mensalidade nesta pelada, eles aparecerão aqui.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
