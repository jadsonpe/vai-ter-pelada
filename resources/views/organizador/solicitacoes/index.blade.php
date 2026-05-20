<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">Solicitacoes - {{ $pelada->nome }}</h1>
        <div class="mt-6 divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white">
            @foreach($solicitacoes as $solicitacao)
                <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold">{{ $solicitacao->user->name }}</p>
                        <p class="text-sm text-slate-600">{{ $solicitacao->mensagem ?: 'Sem mensagem' }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase text-slate-500">{{ $solicitacao->status }}</p>
                    </div>
                    @if($solicitacao->status === 'pendente')
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('organizador.solicitacoes.aprovar', $solicitacao) }}">@csrf @method('PATCH')<button class="rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white">Aprovar</button></form>
                            <form method="POST" action="{{ route('organizador.solicitacoes.recusar', $solicitacao) }}">@csrf @method('PATCH')<button class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold">Recusar</button></form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
