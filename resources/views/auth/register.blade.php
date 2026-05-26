<x-guest-layout>
    <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
        <h1 class="text-lg font-bold text-slate-950">Crie sua conta</h1>
        <p class="mt-1 text-sm text-slate-700">Depois do cadastro, voce podera participar de peladas, seguir jogadores e confirmar rodadas.</p>
    </div>

    <a href="{{ route('auth.google.redirect') }}" class="flex w-full items-center justify-center gap-3 rounded-md border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white font-bold text-red-500">G</span>
        Cadastrar com Google
    </a>

    <div class="my-6 flex items-center gap-3">
        <div class="h-px flex-1 bg-slate-200"></div>
        <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">ou</span>
        <div class="h-px flex-1 bg-slate-200"></div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nome" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Senha" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar senha" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <p class="mt-4 text-xs leading-5 text-slate-500">
            Ao cadastrar, voce concorda com os
            <a href="{{ route('termos') }}" class="font-semibold text-emerald-700 hover:text-emerald-800" target="_blank">Termos de Uso</a>
            e declara ciencia da
            <a href="{{ route('privacidade') }}" class="font-semibold text-emerald-700 hover:text-emerald-800" target="_blank">Politica de Privacidade</a>.
        </p>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                Já tem conta?
            </a>

            <x-primary-button class="ms-4">
                Cadastrar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
