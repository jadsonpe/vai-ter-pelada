@php
    $storyGroups = collect($storyGroups ?? []);
@endphp

<section class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm" data-stories-root>
    <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">Stories</h2>
            <p class="mt-1 text-sm text-slate-500">Momentos rapidos dos peladeiros que voce acompanha.</p>
        </div>
        <a href="{{ route('player-stories.create') }}" class="inline-flex shrink-0 items-center justify-center rounded-md bg-emerald-600 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-700">
            Novo story
        </a>
    </div>

    <div class="flex gap-4 overflow-x-auto px-5 py-4">
        <a href="{{ route('player-stories.create') }}" class="flex w-20 shrink-0 flex-col items-center gap-2 text-center">
            <span class="flex h-16 w-16 items-center justify-center rounded-full border-2 border-dashed border-emerald-300 bg-emerald-50 text-2xl font-black text-emerald-700">+</span>
            <span class="w-full truncate text-xs font-bold text-slate-700">Seu story</span>
        </a>

        @foreach($storyGroups as $index => $group)
            <button type="button" class="flex w-20 shrink-0 flex-col items-center gap-2 text-center" data-story-open="{{ $index }}">
                <span class="rounded-full p-0.5 {{ ($group['seen'] ?? false) ? 'bg-slate-300' : 'bg-gradient-to-tr from-emerald-400 via-lime-300 to-sky-400' }}">
                    <span class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full border-2 border-white bg-slate-900 text-sm font-black text-white">
                        @if(! empty($group['avatar']))
                            <img src="{{ $group['avatar'] }}" alt="{{ $group['name'] }}" class="h-full w-full object-cover">
                        @else
                            {{ $group['initials'] ?? 'P' }}
                        @endif
                    </span>
                </span>
                <span class="w-full truncate text-xs font-bold text-slate-700">{{ $group['name'] }}</span>
            </button>
        @endforeach
    </div>

    @if($storyGroups->isEmpty())
        <div class="border-t border-slate-100 px-5 py-4 text-sm text-slate-500">
            Ainda nao ha stories ativos. Publique o primeiro momento da rodada.
        </div>
    @endif

    <script type="application/json" data-stories-json>@json($storyGroups->values())</script>

    <div class="fixed inset-0 z-[80] hidden bg-slate-950 text-white" data-story-viewer>
        <div class="mx-auto flex h-full max-w-md flex-col">
            <div class="space-y-3 p-4">
                <div class="flex gap-1" data-story-progress></div>
                <div class="flex items-center justify-between gap-3">
                    <a href="#" class="flex min-w-0 items-center gap-3" data-story-profile>
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-emerald-600 text-xs font-black" data-story-avatar></span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-bold" data-story-name></span>
                            <span class="block truncate text-xs text-slate-300" data-story-time></span>
                        </span>
                    </a>
                    <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-xl leading-none hover:bg-white/15" data-story-close aria-label="Fechar story">&times;</button>
                </div>
            </div>

            <div class="relative min-h-0 flex-1 bg-black">
                <button type="button" class="absolute inset-y-0 left-0 z-10 w-1/2" data-story-prev aria-label="Story anterior"></button>
                <button type="button" class="absolute inset-y-0 right-0 z-10 w-1/2" data-story-next aria-label="Proximo story"></button>
                <img src="" alt="Story" class="hidden h-full w-full object-contain" data-story-image>
                <video class="hidden h-full w-full object-contain" data-story-video playsinline controls></video>
            </div>

            <div class="space-y-3 p-4">
                <p class="min-h-6 text-sm leading-6 text-slate-100" data-story-caption></p>
                <div class="flex items-center justify-between gap-3">
                    <form method="post" action="#" class="hidden" data-story-report-form>
                        @csrf
                        <input type="hidden" name="reason" value="midia_inadequada">
                        <button type="submit" class="rounded-md border border-white/15 px-3 py-2 text-xs font-bold text-slate-200 hover:bg-white/10">
                            Denunciar
                        </button>
                    </form>
                    <form method="post" action="#" class="hidden" data-story-delete-form onsubmit="return confirm('Remover este story?')">
                        @csrf
                        @method('delete')
                        <button type="submit" class="rounded-md border border-red-300/40 px-3 py-2 text-xs font-bold text-red-100 hover:bg-red-500/10">
                            Remover
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
