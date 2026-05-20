<nav x-data="{ open: false }" class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex">
                <a href="{{ route('home') }}" class="flex items-center gap-3 text-lg font-bold text-emerald-700">
                    <x-application-logo class="h-10 w-auto" />
                    <span>Vai Ter Pelada</span>
                </a>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('peladas.index')" :active="request()->routeIs('peladas.*')">Peladas</x-nav-link>
                    <x-nav-link :href="route('ranking')" :active="request()->routeIs('ranking')">Ranking</x-nav-link>
                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                        <x-nav-link :href="route('jogador.peladas.minhas')" :active="request()->routeIs('jogador.*')">Jogador</x-nav-link>
                        @if(auth()->user()->isOrganizador())
                            <x-nav-link :href="route('organizador.peladas.index')" :active="request()->routeIs('organizador.*')">Organizador</x-nav-link>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.*')">Admin</x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:ms-6 sm:flex sm:items-center">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900">
                                {{ Auth::user()->name }} <span class="ms-2 rounded bg-emerald-100 px-2 py-0.5 text-xs text-emerald-700">{{ Auth::user()->role }}</span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
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

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
            <x-responsive-nav-link :href="route('peladas.index')" :active="request()->routeIs('peladas.*')">Peladas</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('ranking')" :active="request()->routeIs('ranking')">Ranking</x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('jogador.peladas.minhas')" :active="request()->routeIs('jogador.*')">Jogador</x-responsive-nav-link>
                @if(auth()->user()->isOrganizador())
                    <x-responsive-nav-link :href="route('organizador.peladas.index')" :active="request()->routeIs('organizador.*')">Organizador</x-responsive-nav-link>
                @endif
                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.*')">Admin</x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link :href="route('login')">Entrar</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">Cadastrar</x-responsive-nav-link>
            @endauth
        </div>
    </div>
</nav>
