<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Informações do jogador
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Atualize seus dados de contato e endereço. Esses dados são importantes para organizar as peladas corretamente.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('perfil.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="Nome completo" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="apelido" value="Apelido" />
                    <x-text-input id="apelido" name="apelido" type="text" class="mt-1 block w-full" :value="old('apelido', $user->apelido)" autocomplete="nickname" />
                    <x-input-error class="mt-2" :messages="$errors->get('apelido')" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <div>
                    <x-input-label for="phone" value="Telefone" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" required autocomplete="tel" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <x-input-label for="estado" value="Estado" />
                    <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado', $user->estado)" required autocomplete="address-level1" />
                    <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                </div>

                <div>
                    <x-input-label for="cidade" value="Cidade" />
                    <x-text-input id="cidade" name="cidade" type="text" class="mt-1 block w-full" :value="old('cidade', $user->cidade)" required autocomplete="address-level2" />
                    <x-input-error class="mt-2" :messages="$errors->get('cidade')" />
                </div>

                <div>
                    <x-input-label for="bairro" value="Bairro" />
                    <x-text-input id="bairro" name="bairro" type="text" class="mt-1 block w-full" :value="old('bairro', $user->bairro)" required autocomplete="address-level3" />
                    <x-input-error class="mt-2" :messages="$errors->get('bairro')" />
                </div>

                <div>
                    <x-input-label for="cep" value="CEP" />
                    <x-text-input id="cep" name="cep" type="text" class="mt-1 block w-full" :value="old('cep', $user->cep)" required autocomplete="postal-code" />
                    <x-input-error class="mt-2" :messages="$errors->get('cep')" />
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div>
                <x-input-label for="logradouro" value="Logradouro" />
                <x-text-input id="logradouro" name="logradouro" type="text" class="mt-1 block w-full" :value="old('logradouro', $user->logradouro)" required autocomplete="street-address" />
                <x-input-error class="mt-2" :messages="$errors->get('logradouro')" />
            </div>

            <div>
                <x-input-label for="numero" value="Número" />
                <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full" :value="old('numero', $user->numero)" required autocomplete="off" />
                <x-input-error class="mt-2" :messages="$errors->get('numero')" />
            </div>

            <div>
                <x-input-label for="complemento" value="Complemento" />
                <x-text-input id="complemento" name="complemento" type="text" class="mt-1 block w-full" :value="old('complemento', $user->complemento)" autocomplete="off" />
                <x-input-error class="mt-2" :messages="$errors->get('complemento')" />
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800">
                    Seu endereço de email ainda não foi verificado.

                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Clique aqui para reenviar o email de verificação.
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600">
                        Um novo link de verificação foi enviado para o seu email.
                    </p>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>Salvar</x-primary-button>

            @if (session('status') === 'profile-updated')
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
</section>
