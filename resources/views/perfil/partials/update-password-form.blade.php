<section x-data="{ open: false }" class="rounded-lg border border-slate-200 bg-slate-50 p-4 transition-all">
    {{-- Cabeçalho transformado em um botão para expandir/recolher --}}
    <header @click="open = !open" class="flex cursor-pointer items-center justify-between select-none">
        <div>
            <h2 class="text-lg font-medium text-gray-900 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-slate-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                Alterar senha
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Atualize sua senha para manter sua conta segura.
            </p>
        </div>
        
        {{-- Indicador visual se está aberto ou fechado --}}
        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded border border-emerald-200 shadow-sm whitespace-nowrap ml-4" x-text="open ? 'Recolher ↑' : 'Alterar Senha ↓'"></span>
    </header>

    {{-- O formulário só aparece se 'open' for true --}}
    <div x-show="open" x-transition.duration.300ms class="mt-6 border-t border-slate-200 pt-6">
        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            @method('put')

            @if(auth()->user()->google_id)
                <div class="mb-2 text-sm text-gray-700 bg-amber-50 p-3 rounded border border-amber-200">
                    Você entrou usando sua conta Google e provavelmente não conhece a senha atual. Use o link abaixo para redefinir sua senha por email.
                    <div class="mt-2">
                        <a href="{{ route('password.request') }}" class="text-sm text-emerald-700 font-semibold underline">Redefinir senha por email</a>
                    </div>
                </div>
            @endif

            <div>
                <x-input-label for="update_password_current_password" value="Senha atual" />
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password" value="Nova senha" />
                <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" value="Confirmar nova senha" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>Salvar</x-primary-button>

                @if (session('status') === 'password-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600"
                    >Salvo.</p>
                @endif
            </div>
        </form>
    </div>
</section>