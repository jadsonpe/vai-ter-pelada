<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')
        <h1 class="text-3xl font-bold text-slate-900">Membros - {{ $pelada->nome }}</h1>
        <form method="POST" action="{{ route('organizador.peladas.membros.store', $pelada) }}" class="mt-6 grid gap-3 rounded-lg border border-slate-200 bg-white p-5 md:grid-cols-4">
            @csrf
            <select name="user_id" class="rounded-md border-slate-300">@foreach($jogadores as $jogador)<option value="{{ $jogador->id }}">{{ $jogador->name }}</option>@endforeach</select>
            <select name="tipo" class="rounded-md border-slate-300"><option value="diarista">Diarista</option><option value="mensalista">Mensalista</option></select>
            <select name="status" class="rounded-md border-slate-300"><option value="ativo">Ativo</option><option value="inativo">Inativo</option><option value="bloqueado">Bloqueado</option></select>
            <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Adicionar</button>
        </form>
        <div class="mt-6 divide-y divide-slate-100 rounded-lg border border-slate-200 bg-white">
            @foreach($pelada->membros as $membro)
                <div class="flex items-center justify-between p-4">
                    <span>{{ $membro->user->name }} - {{ $membro->tipo }} - {{ $membro->status }}</span>
                    <form method="POST" action="{{ route('organizador.peladas.membros.destroy', [$pelada, $membro]) }}">@csrf @method('DELETE')<button class="text-sm text-red-600">Remover</button></form>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
