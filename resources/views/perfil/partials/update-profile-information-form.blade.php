<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Informacoes do jogador
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Atualize seus dados de contato e endereco. Esses dados sao importantes para organizar as peladas corretamente.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('perfil.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <x-user-avatar :user="$user" size="xl" />
                <div class="flex-1">
                    <x-input-label for="avatar" value="Foto do jogador" />
                    <input id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
                    <p class="mt-1 text-xs text-slate-500">JPG, PNG ou WebP. Maximo 2 MB. Essa foto aparecera nas listas de membros, avaliacoes e sorteios.</p>
                    <x-input-error class="mt-2" :messages="$errors->get('avatar')" />

                    @if($user->avatar_path)
                        <label class="mt-3 flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="remover_avatar" value="1" @checked(old('remover_avatar'))>
                            Remover foto enviada
                        </label>
                    @endif
                </div>
            </div>
        </div>

        @php
            $playerProfile = $user->playerProfile ?: new \App\Models\PlayerProfile();
            $socialLinks = $playerProfile->socialLinks?->pluck('url', 'platform') ?? collect();
        @endphp

        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
            <div>
                <h3 class="font-semibold text-slate-900">Perfil publico do peladeiro</h3>
                <p class="mt-1 text-sm text-slate-600">Essas informacoes aparecem na pagina compartilhavel do jogador.</p>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="esporte_principal_id" value="Esporte principal" />
                    <select id="esporte_principal_id" name="player_profile[esporte_principal_id]" class="mt-1 block w-full rounded-md border-slate-300">
                        <option value="">Selecione</option>
                        @foreach($esportes as $esporte)
                            <option value="{{ $esporte->id }}" @selected(old('player_profile.esporte_principal_id', $playerProfile->esporte_principal_id) == $esporte->id)>{{ $esporte->nome }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('player_profile.esporte_principal_id')" />
                </div>

                <div>
                    <x-input-label for="posicao_favorita" value="Posicao favorita" />
                    <x-text-input id="posicao_favorita" name="player_profile[posicao_favorita]" type="text" class="mt-1 block w-full" :value="old('player_profile.posicao_favorita', $playerProfile->posicao_favorita)" placeholder="Ex: Atacante, armador, ponteiro..." />
                    <x-input-error class="mt-2" :messages="$errors->get('player_profile.posicao_favorita')" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="headline" value="Frase de destaque" />
                    <x-text-input id="headline" name="player_profile[headline]" type="text" class="mt-1 block w-full" :value="old('player_profile.headline', $playerProfile->headline)" placeholder="Ex: Raca, resenha e gol no fim." />
                    <x-input-error class="mt-2" :messages="$errors->get('player_profile.headline')" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="bio" value="Bio esportiva" />
                    <textarea id="bio" name="player_profile[bio]" rows="3" class="mt-1 block w-full rounded-md border-slate-300" placeholder="Conte rapidamente seu estilo de jogo.">{{ old('player_profile.bio', $playerProfile->bio) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('player_profile.bio')" />
                </div>

                <div class="sm:col-span-2">
                    @php
                        $selectedMode = old('player_profile.cover_mode', $playerProfile->banner_preset ? 'image' : 'gradient');
                        $selectedTheme = old('player_profile.banner_theme', $playerProfile->banner_theme ?: $playerProfile->defaultCoverTheme());
                        $selectedCover = old('player_profile.banner_preset', $playerProfile->banner_preset);
                        $selectedGradient = $gradientCoverOptions[$selectedTheme] ?? $gradientCoverOptions[$playerProfile->defaultCoverTheme()];
                        $selectedImageUrl = $selectedCover ? asset('images/player-covers/'.$selectedCover) : null;
                        $previewStyle = $selectedMode === 'image' && $selectedImageUrl
                            ? "background-image: linear-gradient(90deg, rgba(2,6,23,.82), rgba(2,6,23,.22)), url('{$selectedImageUrl}'); background-size: cover; background-position: center;"
                            : "background-image: {$selectedGradient['style']};";
                    @endphp
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex flex-col gap-4 lg:flex-row">
                            <div class="lg:w-80">
                                <x-input-label value="Capa do perfil publico" />
                                <div data-cover-preview class="mt-3 flex h-36 items-end overflow-hidden rounded-xl border border-slate-200 p-4 shadow-inner" style="{{ $previewStyle }}">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-200">Vai Ter Pelada</p>
                                        <p class="mt-1 text-xl font-black text-white">{{ $user->apelido ?: $user->name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="inline-flex rounded-lg border border-slate-200 bg-slate-100 p-1">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="player_profile[cover_mode]" value="gradient" class="peer sr-only js-cover-mode" @checked($selectedMode === 'gradient')>
                                        <span class="block rounded-md px-4 py-2 text-sm font-bold text-slate-600 peer-checked:bg-white peer-checked:text-emerald-700 peer-checked:shadow-sm">Gradiente</span>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="player_profile[cover_mode]" value="image" class="peer sr-only js-cover-mode" @checked($selectedMode === 'image')>
                                        <span class="block rounded-md px-4 py-2 text-sm font-bold text-slate-600 peer-checked:bg-white peer-checked:text-emerald-700 peer-checked:shadow-sm">Imagem</span>
                                    </label>
                                </div>

                                <div data-gradient-panel class="mt-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Cores</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach($gradientCoverOptions as $themeKey => $theme)
                                            <label class="cursor-pointer" title="{{ $theme['label'] }}">
                                                <input type="radio" name="player_profile[banner_theme]" value="{{ $themeKey }}" class="peer sr-only js-cover-gradient" data-cover-style="background-image: {{ $theme['style'] }};" @checked($selectedTheme === $themeKey)>
                                                <span class="block h-11 w-11 rounded-full border-2 border-white shadow ring-1 ring-slate-300 transition peer-checked:scale-105 peer-checked:ring-4 peer-checked:ring-emerald-300" style="background-image: {{ $theme['style'] }}"></span>
                                                <span class="sr-only">{{ $theme['label'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div data-image-panel class="{{ $selectedMode === 'image' ? '' : 'hidden' }} mt-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Imagens prontas</p>
                                    <div class="mt-2 grid max-h-80 gap-2 overflow-y-auto pr-1 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($imageCoverOptions as $coverFile => $coverLabel)
                                <label class="group cursor-pointer">
                                    @php($imageUrl = asset('images/player-covers/'.$coverFile))
                                    <input type="radio" name="player_profile[banner_preset]" value="{{ $coverFile }}" class="peer sr-only js-cover-image" data-cover-style="background-image: linear-gradient(90deg, rgba(2,6,23,.82), rgba(2,6,23,.22)), url('{{ $imageUrl }}'); background-size: cover; background-position: center;" @checked($selectedCover === $coverFile)>
                                    <span class="block overflow-hidden rounded-lg border-2 border-slate-200 bg-white shadow-sm transition peer-checked:border-emerald-500 peer-checked:ring-2 peer-checked:ring-emerald-200 group-hover:border-emerald-300">
                                        <img src="{{ $imageUrl }}" alt="{{ $coverLabel }}" class="h-20 w-full object-cover" loading="lazy">
                                        <span class="block truncate px-2 py-1.5 text-xs font-semibold text-slate-700">{{ $coverLabel }}</span>
                                    </span>
                                </label>
                            @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-input-error class="mt-2" :messages="$errors->get('player_profile.cover_mode')" />
                    <x-input-error class="mt-2" :messages="$errors->get('player_profile.banner_theme')" />
                    <x-input-error class="mt-2" :messages="$errors->get('player_profile.banner_preset')" />
                </div>
            </div>
        </div>

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

                <div>
                    <x-input-label for="data_nascimento" value="Data de nascimento" />
                    <x-text-input id="data_nascimento" name="data_nascimento" type="date" class="mt-1 block w-full" :value="old('data_nascimento', optional($user->data_nascimento)->format('Y-m-d'))" max="{{ now()->subDay()->toDateString() }}" />
                    <p class="mt-1 text-xs text-slate-500">Opcional. Se preenchida, outros jogadores poderao ver sua idade no perfil publico.</p>
                    <x-input-error class="mt-2" :messages="$errors->get('data_nascimento')" />
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
                <x-input-label for="numero" value="Numero" />
                <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full" :value="old('numero', $user->numero)" required autocomplete="off" />
                <x-input-error class="mt-2" :messages="$errors->get('numero')" />
            </div>

            <div>
                <x-input-label for="complemento" value="Complemento" />
                <x-text-input id="complemento" name="complemento" type="text" class="mt-1 block w-full" :value="old('complemento', $user->complemento)" autocomplete="off" />
                <x-input-error class="mt-2" :messages="$errors->get('complemento')" />
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
            <div>
                <h3 class="font-semibold text-slate-900">Posicoes por esporte</h3>
                <p class="mt-1 text-sm text-slate-600">Opcional. Preencha apenas as modalidades em que voce quer mostrar sua posicao no perfil publico.</p>
            </div>

            @php($perfisPorEsporte = $user->esportePerfis->keyBy('esporte_id'))
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @foreach($esportes as $index => $esporte)
                    @php($perfil = $perfisPorEsporte->get($esporte->id))
                    <div>
                        <input type="hidden" name="esporte_perfis[{{ $index }}][esporte_id]" value="{{ $esporte->id }}">
                        <x-input-label :for="'esporte-posicao-'.$esporte->id" :value="$esporte->nome" />
                        <x-text-input
                            :id="'esporte-posicao-'.$esporte->id"
                            name="esporte_perfis[{{ $index }}][posicao]"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('esporte_perfis.'.$index.'.posicao', $perfil?->posicao)"
                            placeholder="Ex: Goleiro, atacante, levantador..."
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('esporte_perfis.'.$index.'.posicao')" />
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-4">
            <div>
                <h3 class="font-semibold text-slate-900">Redes sociais</h3>
                <p class="mt-1 text-sm text-slate-600">Mostramos apenas os links preenchidos no perfil publico.</p>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="instagram" value="Instagram" />
                    <x-text-input id="instagram" name="social_links[instagram]" type="url" class="mt-1 block w-full" :value="old('social_links.instagram', $socialLinks->get('instagram'))" placeholder="https://instagram.com/seuuser" />
                </div>
                <div>
                    <x-input-label for="tiktok" value="TikTok" />
                    <x-text-input id="tiktok" name="social_links[tiktok]" type="url" class="mt-1 block w-full" :value="old('social_links.tiktok', $socialLinks->get('tiktok'))" placeholder="https://tiktok.com/@seuuser" />
                </div>
                <div>
                    <x-input-label for="youtube" value="YouTube" />
                    <x-text-input id="youtube" name="social_links[youtube]" type="url" class="mt-1 block w-full" :value="old('social_links.youtube', $socialLinks->get('youtube'))" placeholder="https://youtube.com/@seucanal" />
                </div>
                <div>
                    <x-input-label for="whatsapp" value="WhatsApp" />
                    <x-text-input id="whatsapp" name="social_links[whatsapp]" type="text" class="mt-1 block w-full" :value="old('social_links.whatsapp', $socialLinks->get('whatsapp'))" placeholder="81999999999" />
                </div>
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800">
                    Seu endereco de email ainda nao foi verificado.

                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Clique aqui para reenviar o email de verificacao.
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600">
                        Um novo link de verificacao foi enviado para o seu email.
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
                    setStatus('Informe um CEP com 8 numeros.', 'error');
                    return;
                }

                buscar.disabled = true;
                buscar.textContent = 'Buscando...';
                setStatus('Consultando endereco pelo CEP...');

                try {
                    const response = await fetch(`https://viacep.com.br/ws/${digits}/json/`);
                    const data = await response.json();

                    if (!response.ok || data.erro) {
                        setStatus('CEP nao encontrado. Confira o numero informado.', 'error');
                        return;
                    }

                    fields.logradouro.value = data.logradouro || fields.logradouro.value;
                    fields.bairro.value = data.bairro || fields.bairro.value;
                    fields.cidade.value = data.localidade || fields.cidade.value;
                    fields.estado.value = data.uf || fields.estado.value;
                    cep.value = formatCep(digits);

                    setStatus('Endereco preenchido automaticamente.', 'success');

                    if (fields.numero && !fields.numero.value) {
                        fields.numero.focus();
                    }
                } catch (error) {
                    setStatus('Nao foi possivel consultar o CEP agora. Tente novamente.', 'error');
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

        (() => {
            const modeInputs = document.querySelectorAll('input[name="player_profile[cover_mode]"]');
            const gradientInputs = document.querySelectorAll('.js-cover-gradient');
            const imageInputs = document.querySelectorAll('.js-cover-image');
            const preview = document.querySelector('[data-cover-preview]');
            const imagePanel = document.querySelector('[data-image-panel]');
            const gradientPanel = document.querySelector('[data-gradient-panel]');

            function setMode(mode) {
                const input = document.querySelector(`input[name="player_profile[cover_mode]"][value="${mode}"]`);
                if (input) {
                    input.checked = true;
                }

                imagePanel?.classList.toggle('hidden', mode !== 'image');
                gradientPanel?.classList.toggle('opacity-50', mode === 'image');
            }

            function updatePreview(input) {
                if (!preview || !input?.dataset.coverStyle) {
                    return;
                }

                preview.style.cssText = input.dataset.coverStyle;
            }

            gradientInputs.forEach((input) => {
                input.addEventListener('change', () => {
                    setMode('gradient');
                    updatePreview(input);
                    imageInputs.forEach((image) => image.checked = false);
                });
            });

            imageInputs.forEach((input) => {
                input.addEventListener('change', () => {
                    setMode('image');
                    updatePreview(input);
                });
            });

            modeInputs.forEach((input) => {
                input.addEventListener('change', () => {
                    if (input.value === 'gradient') {
                        imageInputs.forEach((image) => image.checked = false);
                        updatePreview(document.querySelector('.js-cover-gradient:checked'));
                    }

                    setMode(input.value);
                });
            });

            setMode(document.querySelector('input[name="player_profile[cover_mode]"]:checked')?.value || 'gradient');
        })();
    </script>
</section>

