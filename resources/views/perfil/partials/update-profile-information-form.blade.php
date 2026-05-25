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
                    <x-input-label for="cep" value="CEP" />
                    <div class="mt-1 flex gap-2">
                        <x-text-input id="cep" name="cep" type="text" class="block w-full" :value="old('cep', $user->cep)" required autocomplete="postal-code" inputmode="numeric" placeholder="00000-000" />
                        <button type="button" id="buscar-cep" class="shrink-0 rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            Buscar
                        </button>
                    </div>
                    <p id="cep-status" class="mt-2 text-xs text-slate-500"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('cep')" />
                </div>

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

    <script>
        (() => {
            const cep = document.getElementById('cep');
            const buscar = document.getElementById('buscar-cep');
            const status = document.getElementById('cep-status');
            const fields = {
                logradouro: document.getElementById('logradouro'),
                bairro: document.getElementById('bairro'),
                cidade: document.getElementById('cidade'),
                estado: document.getElementById('estado'),
                numero: document.getElementById('numero'),
            };

            function onlyDigits(value) {
                return String(value || '').replace(/\D/g, '').slice(0, 8);
            }

            function formatCep(value) {
                const digits = onlyDigits(value);
                return digits.length > 5 ? `${digits.slice(0, 5)}-${digits.slice(5)}` : digits;
            }

            function setStatus(message, type = 'info') {
                if (!status) return;
                status.textContent = message;
                status.className = 'mt-2 text-xs ' + {
                    info: 'text-slate-500',
                    success: 'text-emerald-700',
                    error: 'text-red-600',
                }[type];
            }

            async function buscarCep() {
                const digits = onlyDigits(cep?.value);

                if (digits.length !== 8) {
                    setStatus('Informe um CEP com 8 números.', 'error');
                    return;
                }

                buscar.disabled = true;
                buscar.textContent = 'Buscando...';
                setStatus('Consultando endereço pelo CEP...');

                try {
                    const response = await fetch(`https://viacep.com.br/ws/${digits}/json/`);
                    const data = await response.json();

                    if (!response.ok || data.erro) {
                        setStatus('CEP não encontrado. Confira o número informado.', 'error');
                        return;
                    }

                    fields.logradouro.value = data.logradouro || fields.logradouro.value;
                    fields.bairro.value = data.bairro || fields.bairro.value;
                    fields.cidade.value = data.localidade || fields.cidade.value;
                    fields.estado.value = data.uf || fields.estado.value;
                    cep.value = formatCep(digits);

                    setStatus('Endereço preenchido automaticamente.', 'success');

                    if (fields.numero && !fields.numero.value) {
                        fields.numero.focus();
                    }
                } catch (error) {
                    setStatus('Não foi possível consultar o CEP agora. Tente novamente.', 'error');
                } finally {
                    buscar.disabled = false;
                    buscar.textContent = 'Buscar';
                }
            }

            cep?.addEventListener('input', () => {
                cep.value = formatCep(cep.value);
            });

            cep?.addEventListener('blur', () => {
                if (onlyDigits(cep.value).length === 8) {
                    buscarCep();
                }
            });

            buscar?.addEventListener('click', buscarCep);
        })();
    </script>
</section>
