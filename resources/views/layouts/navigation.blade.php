<nav class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex min-w-0">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 text-lg font-bold text-emerald-700">
                    <x-application-logo class="h-10 w-auto" />
                    <span class="truncate">Vai Ter Pelada</span>
                </a>
                <div class="hidden space-x-8 lg:-my-px lg:ms-10 lg:flex">
                    <x-nav-link :href="route('peladas.index')" :active="request()->routeIs('peladas.*')">Peladas</x-nav-link>
                    {{-- <x-nav-link :href="route('ranking')" :active="request()->routeIs('ranking')">Ranking</x-nav-link> --}}
                    {{-- <x-nav-link :href="route('arenas.index')" :active="request()->routeIs('arenas.*')">Arenas</x-nav-link> --}}
                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('jogador.peladas.*') || request()->routeIs('jogador.avaliacoes.*')">Jogador</x-nav-link>
                        <x-nav-link :href="route('jogadores.index')" :active="request()->routeIs('jogadores.index')">Peladeiros</x-nav-link>
                        <x-nav-link :href="route('player-stories.create')" :active="request()->routeIs('player-stories.*')">Story</x-nav-link>
                        <x-nav-link :href="route('player-posts.index')" :active="request()->routeIs('player-posts.*')">Postar foto</x-nav-link>
                        @if(auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">Admin</x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden lg:ms-6 lg:flex lg:items-center">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900">
                                <x-user-avatar :user="Auth::user()" size="xs" />
                                <span class="max-w-36 truncate">{{ Auth::user()->name }}</span>
                                @if($notificacoesNaoLidas)
                                    <span class="ms-2 rounded bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700">{{ $notificacoesNaoLidas }}</span>
                                @endif
                                <span class="ms-2 rounded bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700">{{ Auth::user()->role }}</span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('perfil.edit')">Perfil</x-dropdown-link>
                            <x-dropdown-link :href="route('player-stories.create')">Novo story</x-dropdown-link>
                            <x-dropdown-link :href="route('player-posts.index')">Nova publicação</x-dropdown-link>
                            <x-dropdown-link :href="route('jogadores.index')">Buscar jogadores</x-dropdown-link>
                            <x-dropdown-link :href="route('dashboard', ['aba' => 'avaliacoes'])">Avaliacoes</x-dropdown-link>
                            <x-dropdown-link :href="route('dashboard', ['aba' => 'mensagens'])">Mensagens {{ $notificacoesNaoLidas ? '('.$notificacoesNaoLidas.')' : '' }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Sair</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-700 hover:text-emerald-700">Entrar</a>
                    <a href="{{ route('register') }}" class="ms-4 rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Cadastrar</a>
                @endauth
            </div>

            <div class="flex items-center gap-2 lg:hidden">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="relative inline-flex items-center rounded-full border border-slate-200 bg-slate-50 p-1.5">
                                <x-user-avatar :user="Auth::user()" size="xs" />
                                @if($notificacoesNaoLidas)
                                    <span class="absolute -right-1 -top-1 rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] font-bold leading-none text-white">{{ $notificacoesNaoLidas }}</span>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('perfil.edit')">Perfil</x-dropdown-link>
                            <x-dropdown-link :href="route('dashboard')">Painel do jogador</x-dropdown-link>
                            <x-dropdown-link :href="route('player-stories.create')">Novo story</x-dropdown-link>
                            <x-dropdown-link :href="route('player-posts.index')">Publicar foto</x-dropdown-link>
                            <x-dropdown-link :href="route('jogadores.index')">Buscar jogadores</x-dropdown-link>
                            <x-dropdown-link :href="route('dashboard', ['aba' => 'avaliacoes'])">Avaliacoes</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Sair</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="rounded-md border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700">Entrar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@php
    $mobileItemClass = function (bool $active): string {
        return $active
            ? 'text-emerald-700'
            : 'text-slate-500 hover:text-emerald-700';
    };

    $publishActive = request()->routeIs('player-posts.*');
@endphp

<nav class="fixed inset-x-0 bottom-0 z-50 border-t border-slate-200 bg-white/95 shadow-[0_-10px_30px_rgba(15,23,42,0.08)] backdrop-blur lg:hidden">
    <div class="mx-auto flex max-w-lg items-center justify-around gap-1 px-2 pb-[max(0.65rem,env(safe-area-inset-bottom))] pt-2">
        <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex h-14 min-w-0 flex-1 flex-col items-center justify-center gap-1 rounded-lg text-[11px] font-semibold {{ $mobileItemClass(auth()->check() ? request()->routeIs('dashboard') : request()->routeIs('home')) }}">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10v10h14V10" />
            </svg>
            <span class="block max-w-full truncate leading-none">{{ auth()->check() ? 'Jogador' : 'Inicio' }}</span>
        </a>

        <a href="{{ route('peladas.index') }}" class="flex h-14 min-w-0 flex-1 flex-col items-center justify-center gap-1 rounded-lg text-[11px] font-semibold {{ $mobileItemClass(request()->routeIs('peladas.*')) }}">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="8" />
                <path stroke-linecap="round" stroke-linejoin="round" d="m8 9 4-2 4 2v5l-4 3-4-3z" />
            </svg>
            <span class="block max-w-full truncate leading-none">Peladas</span>
        </a>

        @auth
            <a href="{{ route('player-posts.index') }}" class="group flex h-14 min-w-0 flex-1 flex-col items-center justify-center gap-1 rounded-lg text-[11px] font-black {{ $publishActive ? 'text-emerald-700' : 'text-slate-600 hover:text-emerald-700' }}" aria-label="Publicar foto">
                <span class="{{ $publishActive ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-slate-950 text-white shadow-lg shadow-slate-950/20 group-hover:bg-emerald-600 group-hover:shadow-emerald-600/20' }} -mt-5 inline-flex h-11 w-11 items-center justify-center rounded-full transition">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8.5A2.5 2.5 0 0 1 6.5 6H8l1.25-1.5h5.5L16 6h1.5A2.5 2.5 0 0 1 20 8.5v7A2.5 2.5 0 0 1 17.5 18h-11A2.5 2.5 0 0 1 4 15.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9.5v5M9.5 12h5" />
                    </svg>
                </span>
                <span class="block max-w-full truncate leading-none">Publicar</span>
            </a>

            <a href="{{ route('jogadores.index') }}" class="flex h-14 min-w-0 flex-1 flex-col items-center justify-center gap-1 rounded-lg text-[11px] font-semibold {{ $mobileItemClass(request()->routeIs('jogadores.*')) }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="6" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16 16 4 4" />
                </svg>
                <span class="block max-w-full truncate leading-none">Peladeiros</span>
            </a>

            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('perfil.edit') }}" class="relative flex h-14 min-w-0 flex-1 flex-col items-center justify-center gap-1 rounded-lg text-[11px] font-semibold {{ $mobileItemClass(request()->routeIs('perfil.*') || request()->routeIs('admin.*')) }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="8" r="4" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6" />
                </svg>
                <span class="block max-w-full truncate leading-none">{{ auth()->user()->isAdmin() ? 'Admin' : 'Perfil' }}</span>
                @if(($notificacoesNaoLidas ?? 0) && !auth()->user()->isAdmin())
                    <span class="absolute right-4 top-1 rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] font-bold leading-none text-white">{{ $notificacoesNaoLidas }}</span>
                @endif
            </a>
        @else
            <a href="{{ route('login') }}" class="flex h-14 min-w-0 flex-1 flex-col items-center justify-center gap-1 rounded-lg text-[11px] font-semibold {{ $mobileItemClass(request()->routeIs('login')) }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4v18h-4" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5M15 12H3" />
                </svg>
                <span class="block max-w-full truncate leading-none">Entrar</span>
            </a>

            <a href="{{ route('register') }}" class="flex h-14 min-w-0 flex-1 flex-col items-center justify-center gap-1 rounded-lg text-[11px] font-semibold {{ $mobileItemClass(request()->routeIs('register')) }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                </svg>
                <span class="block max-w-full truncate leading-none">Cadastrar</span>
            </a>
        @endauth
    </div>
</nav>
