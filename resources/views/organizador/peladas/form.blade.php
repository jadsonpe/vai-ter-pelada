<x-app-layout>
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        @include('shared.status')

        <h1 class="text-3xl font-bold text-slate-900">{{ $pelada->exists ? 'Editar pelada' : 'Nova pelada' }}</h1>

        <form method="POST" enctype="multipart/form-data" action="{{ $pelada->exists ? route('organizador.peladas.update', $pelada) : route('organizador.peladas.store') }}" class="mt-6 grid gap-4 rounded-lg border border-slate-200 bg-white p-5">
            @csrf
            @if($pelada->exists)
                @method('PUT')
            @endif

            <div>
                <label class="text-sm font-medium">Imagem da pelada</label>
                @if($pelada->temImagemPropria())
                    <x-pelada-imagem variant="preview" :src="$pelada->imagemPropriaUrl()" :alt="$pelada->nome" class="mt-2" />
                    <label class="mt-2 flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remover_imagem" value="1" @checked(old('remover_imagem'))>
                        Remover imagem enviada
                    </label>
                @elseif($pelada->exists && ($padrao = $pelada->esporte?->imagemPadraoUrl()))
                    <x-pelada-imagem variant="preview" :src="$padrao" :alt="'Imagem padrão do '.$pelada->esporte->nome" class="mt-2 opacity-90" />
                    <p class="mt-1 text-xs text-slate-500">Imagem padrão do esporte. Envie um arquivo abaixo para substituir.</p>
                @endif
                <input id="pelada-image-input" type="file" name="imagem" accept="image/jpeg,image/png,image/webp" class="mt-2 w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
                <input type="hidden" name="image_crop_x" data-image-crop-x>
                <input type="hidden" name="image_crop_y" data-image-crop-y>
                <input type="hidden" name="image_crop_width" data-image-crop-width>
                <input type="hidden" name="image_crop_height" data-image-crop-height>
                <input type="hidden" name="image_width" data-image-width>
                <input type="hidden" name="image_height" data-image-height>
                <input type="hidden" name="image_crop_dirty" value="0" data-image-crop-dirty>
                <p class="mt-1 text-xs text-slate-500">JPG, PNG ou WebP. Máximo 2 MB. Sem envio, usa a imagem padrão do esporte (public/images/esportes/).</p>
                <x-input-error :messages="$errors->get('imagem')" class="mt-2" />

                <div data-pelada-image-cropper data-current-image-url="{{ $pelada->temImagemPropria() ? $pelada->imagemPropriaUrl() : '' }}" class="mt-4 hidden overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="bg-slate-950 p-3 sm:p-4">
                        <canvas
                            data-pelada-image-canvas
                            width="960"
                            height="540"
                            class="aspect-video w-full cursor-grab touch-none select-none rounded-md bg-slate-900 shadow-inner active:cursor-grabbing"
                            aria-label="Previa do corte da imagem da pelada"
                        ></canvas>
                    </div>

                    <div class="space-y-4 p-4">
                        <div>
                            <p class="text-sm font-bold text-slate-900">Enquadramento da pelada</p>
                            <p class="mt-1 text-xs leading-5 text-slate-500">Arraste a imagem para ajustar o corte 16:9 usado nos cards e na pagina publica.</p>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-3">
                                <label for="pelada-image-zoom" class="text-xs font-bold uppercase tracking-wide text-slate-500">Zoom</label>
                                <span data-pelada-image-zoom-label class="text-xs font-semibold text-slate-500">100%</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" data-pelada-image-zoom-out class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-slate-300 text-sm font-bold text-slate-700 hover:bg-slate-50" aria-label="Diminuir zoom">-</button>
                                <input
                                    id="pelada-image-zoom"
                                    data-pelada-image-zoom
                                    type="range"
                                    min="1"
                                    max="4"
                                    step="0.01"
                                    value="1"
                                    class="min-w-0 flex-1 accent-emerald-600"
                                >
                                <button type="button" data-pelada-image-zoom-in class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-slate-300 text-sm font-bold text-slate-700 hover:bg-slate-50" aria-label="Aumentar zoom">+</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 sm:flex">
                            <button type="button" data-pelada-image-center class="inline-flex items-center justify-center rounded-md border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                Centralizar
                            </button>
                            <button type="button" data-pelada-image-reset class="inline-flex items-center justify-center rounded-md border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                Reajustar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <label class="text-sm font-medium">
                Esporte
                <select name="esporte_id" class="mt-1 w-full rounded-md border-slate-300">
                    @foreach($esportes as $esporte)
                        <option value="{{ $esporte->id }}" @selected(old('esporte_id', $pelada->esporte_id) == $esporte->id)>{{ $esporte->nome }}</option>
                    @endforeach
                </select>
            </label>

            <label class="text-sm font-medium">
                Nome da Pelada
                <input name="nome" value="{{ old('nome', $pelada->nome) }}" class="mt-1 w-full rounded-md border-slate-300">
                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium">
                    Data de fundacao
                    <input type="date" name="data_fundacao" value="{{ old('data_fundacao', optional($pelada->data_fundacao)->format('Y-m-d')) }}" max="{{ now()->toDateString() }}" class="mt-1 w-full rounded-md border-slate-300">
                    <x-input-error :messages="$errors->get('data_fundacao')" class="mt-2" />
                </label>

                <label class="text-sm font-medium">
                    Categoria
                    <select name="categoria" class="mt-1 w-full rounded-md border-slate-300">
                        @foreach(\App\Models\Pelada::CATEGORIAS as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected(old('categoria', $pelada->categoria ?: 'adulto') === $valor)>{{ $rotulo }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('categoria')" class="mt-2" />
                </label>
            </div>

            <div>
                <label for="descricao" class="text-sm font-medium">Descrição da Pelada</label>
                <textarea id="descricao" name="descricao" maxlength="200" rows="3" class="mt-1 w-full rounded-md border-slate-300" placeholder="Ex: Pelada semanal para quem curte jogo organizado, ambiente respeitoso e boa resenha depois da partida.">{{ old('descricao', $pelada->descricao) }}</textarea>
                <div class="mt-1 flex items-start justify-between gap-3 text-xs text-slate-500">
                    <x-input-error :messages="$errors->get('descricao')" />
                    <span class="shrink-0"><span id="descricao-count">0</span>/200 caracteres</span>
                </div>
            </div>

            <div class="font-semibold">ONDE FICA SUA PELADA?</div>

            <div>
                <label class="text-sm font-medium text-emerald-700">
                    Digite o CEP para preencher o endereço automaticamente
                    <input type="text" id="cep" name="cep" placeholder="00000-000" maxlength="9" class="mt-1 w-full rounded-md border-emerald-300 bg-emerald-50 focus:border-emerald-500 focus:ring-emerald-500">
                </label>
                <span id="cep-erro" class="text-xs text-red-500 hidden">CEP não encontrado.</span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium">
                    Cidade
                    <input id="cidade" name="cidade" value="{{ old('cidade', $pelada->cidade) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>
                <label class="text-sm font-medium">
                    Bairro
                    <input id="bairro" name="bairro" value="{{ old('bairro', $pelada->bairro) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>
            </div>

            <label class="text-sm font-medium">
                Nome do local/arena/quadra
                <input name="local_nome" value="{{ old('local_nome', $pelada->local_nome ?: $pelada->local) }}" class="mt-1 w-full rounded-md border-slate-300">
            </label>

            <label class="text-sm font-medium">
                Endereço (Rua e Número)
                <input id="endereco" name="endereco" value="{{ old('endereco', $pelada->endereco) }}" class="mt-1 w-full rounded-md border-slate-300">
            </label>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium">
                    Horário
                    <input type="time" name="horario" value="{{ old('horario', optional($pelada->horario)->format('H:i')) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>
                <label class="text-sm font-medium">
                    Vagas totais
                    <input type="number" name="vagas_totais" value="{{ old('vagas_totais', $pelada->vagas_totais ?: $pelada->capacidade ?: 20) }}" class="mt-1 w-full rounded-md border-slate-300">
                    <x-input-error :messages="$errors->get('vagas_totais')" class="mt-2" />
                </label>
            </div>

            <div class="grid gap-4 {{ $pelada->exists ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }}">
                <label class="text-sm font-medium">
                    Vagas diaristas
                    <input type="number" name="vagas_diaristas" value="{{ old('vagas_diaristas', $pelada->vagas_diaristas ?: 0) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>

                @if($pelada->exists)
                    <label class="text-sm font-medium">
                        Status
                        <select name="status" class="mt-1 w-full rounded-md border-slate-300">
                            @foreach(['ativa', 'pausada', 'encerrada'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $pelada->status ?: 'ativa') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                <label class="text-sm font-medium">
                    WhatsApp
                    <input name="whatsapp_contato" value="{{ old('whatsapp_contato', $pelada->whatsapp_contato) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-medium">
                    Valor mensalista
                    <input name="valor_mensalista" value="{{ old('valor_mensalista', $pelada->valor_mensalista) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>
                <label class="text-sm font-medium">
                    Valor diarista
                    <input name="valor_diarista" value="{{ old('valor_diarista', $pelada->valor_diarista) }}" class="mt-1 w-full rounded-md border-slate-300">
                </label>
            </div>

            <div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <label for="regras" class="text-sm font-medium">Regras</label>
                    <button type="button" id="usar-regras-padrao" class="inline-flex items-center justify-center rounded-md border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">
                        Usar regras padrão
                    </button>
                </div>
                <textarea id="regras" name="regras" rows="8" class="mt-1 w-full rounded-md border-slate-300" placeholder="Ex: Informe regras de pontualidade, respeito, pagamento, prioridade de mensalistas, sorteio de times e cuidado com materiais.">{{ old('regras', $pelada->regras) }}</textarea>
                <x-input-error :messages="$errors->get('regras')" class="mt-2" />
            </div>

            <button class="rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white">Salvar</button>
        </form>
    </div>

    <script>
        (() => {
            const imageInput = document.getElementById('pelada-image-input');
            const imageCropper = document.querySelector('[data-pelada-image-cropper]');
            const imageCanvas = document.querySelector('[data-pelada-image-canvas]');
            const imageZoom = document.querySelector('[data-pelada-image-zoom]');
            const imageZoomLabel = document.querySelector('[data-pelada-image-zoom-label]');
            const imageZoomIn = document.querySelector('[data-pelada-image-zoom-in]');
            const imageZoomOut = document.querySelector('[data-pelada-image-zoom-out]');
            const imageCenter = document.querySelector('[data-pelada-image-center]');
            const imageReset = document.querySelector('[data-pelada-image-reset]');
            const imageFields = {
                x: document.querySelector('[data-image-crop-x]'),
                y: document.querySelector('[data-image-crop-y]'),
                width: document.querySelector('[data-image-crop-width]'),
                height: document.querySelector('[data-image-crop-height]'),
                imageWidth: document.querySelector('[data-image-width]'),
                imageHeight: document.querySelector('[data-image-height]'),
            };
            const imageCropDirty = document.querySelector('[data-image-crop-dirty]');

            if (imageInput && imageCropper && imageCanvas && imageZoom) {
                const ratio = 16 / 9;
                const ctx = imageCanvas.getContext('2d');
                const state = {
                    image: null,
                    cropX: 0,
                    cropY: 0,
                    cropWidth: 1,
                    cropHeight: 1,
                    dragging: false,
                    dragStartX: 0,
                    dragStartY: 0,
                    startCropX: 0,
                    startCropY: 0,
                    activePointers: new Map(),
                    pinchStartDistance: 0,
                    pinchStartZoom: 1,
                };

                const zoomValue = () => Number(imageZoom.value) || 1;

                const markImageCropDirty = () => {
                    if (imageCropDirty) {
                        imageCropDirty.value = '1';
                    }
                };

                const updateZoomLabel = () => {
                    if (imageZoomLabel) {
                        imageZoomLabel.textContent = `${Math.round(zoomValue() * 100)}%`;
                    }
                };

                const baseCrop = () => {
                    if (! state.image) {
                        return { width: 1, height: 1 };
                    }

                    if (state.image.naturalWidth / Math.max(1, state.image.naturalHeight) > ratio) {
                        return {
                            width: state.image.naturalHeight * ratio,
                            height: state.image.naturalHeight,
                        };
                    }

                    return {
                        width: state.image.naturalWidth,
                        height: state.image.naturalWidth / ratio,
                    };
                };

                const clampCrop = () => {
                    if (! state.image) {
                        return;
                    }

                    state.cropX = Math.max(0, Math.min(state.cropX, state.image.naturalWidth - state.cropWidth));
                    state.cropY = Math.max(0, Math.min(state.cropY, state.image.naturalHeight - state.cropHeight));
                };

                const syncImageFields = () => {
                    if (! state.image) {
                        Object.values(imageFields).forEach((field) => {
                            if (field) {
                                field.value = '';
                            }
                        });
                        return;
                    }

                    imageFields.x.value = Math.round(state.cropX * 100) / 100;
                    imageFields.y.value = Math.round(state.cropY * 100) / 100;
                    imageFields.width.value = Math.round(state.cropWidth * 100) / 100;
                    imageFields.height.value = Math.round(state.cropHeight * 100) / 100;
                    imageFields.imageWidth.value = state.image.naturalWidth;
                    imageFields.imageHeight.value = state.image.naturalHeight;
                };

                const drawImageCrop = () => {
                    if (! state.image) {
                        return;
                    }

                    clampCrop();
                    ctx.clearRect(0, 0, imageCanvas.width, imageCanvas.height);
                    ctx.drawImage(
                        state.image,
                        state.cropX,
                        state.cropY,
                        state.cropWidth,
                        state.cropHeight,
                        0,
                        0,
                        imageCanvas.width,
                        imageCanvas.height
                    );

                    ctx.save();
                    ctx.strokeStyle = 'rgba(255,255,255,.34)';
                    ctx.lineWidth = 2;
                    for (let i = 1; i <= 2; i += 1) {
                        const x = (imageCanvas.width / 3) * i;
                        const y = (imageCanvas.height / 3) * i;
                        ctx.beginPath();
                        ctx.moveTo(x, 0);
                        ctx.lineTo(x, imageCanvas.height);
                        ctx.moveTo(0, y);
                        ctx.lineTo(imageCanvas.width, y);
                        ctx.stroke();
                    }
                    ctx.strokeStyle = 'rgba(255,255,255,.9)';
                    ctx.lineWidth = 5;
                    ctx.strokeRect(2.5, 2.5, imageCanvas.width - 5, imageCanvas.height - 5);
                    ctx.restore();

                    syncImageFields();
                };

                const setImageZoom = (zoom, keepCenter = true) => {
                    if (! state.image) {
                        return;
                    }

                    const normalizedZoom = Math.max(1, Math.min(4, zoom));
                    const centerX = state.cropX + state.cropWidth / 2;
                    const centerY = state.cropY + state.cropHeight / 2;
                    const crop = baseCrop();
                    imageZoom.value = normalizedZoom.toString();
                    updateZoomLabel();
                    state.cropWidth = crop.width / normalizedZoom;
                    state.cropHeight = crop.height / normalizedZoom;

                    if (keepCenter) {
                        state.cropX = centerX - state.cropWidth / 2;
                        state.cropY = centerY - state.cropHeight / 2;
                    } else {
                        state.cropX = (state.image.naturalWidth - state.cropWidth) / 2;
                        state.cropY = (state.image.naturalHeight - state.cropHeight) / 2;
                    }

                    drawImageCrop();
                };

                const changeImageZoom = (amount) => setImageZoom(zoomValue() + amount);

                const loadImageCrop = (file) => {
                    if (! file) {
                        state.image = null;
                        imageCropper.classList.add('hidden');
                        state.activePointers.clear();
                        syncImageFields();
                        return;
                    }

                    const image = new Image();
                    const url = URL.createObjectURL(file);
                    image.onload = () => {
                        URL.revokeObjectURL(url);
                        state.image = image;
                        imageZoom.value = '1';
                        imageCropper.classList.remove('hidden');
                        updateZoomLabel();
                        setImageZoom(1, false);
                    };
                    image.src = url;
                };

                const loadCurrentImageCrop = () => {
                    const currentImageUrl = imageCropper.dataset.currentImageUrl;

                    if (! currentImageUrl) {
                        return;
                    }

                    const image = new Image();
                    image.onload = () => {
                        state.image = image;
                        imageZoom.value = '1';
                        imageCropper.classList.remove('hidden');
                        updateZoomLabel();
                        setImageZoom(1, false);
                    };
                    image.src = currentImageUrl;
                };

                imageInput.addEventListener('change', () => loadImageCrop(imageInput.files?.[0]));
                imageInput.addEventListener('change', () => markImageCropDirty());
                imageZoom.addEventListener('input', () => {
                    markImageCropDirty();
                    setImageZoom(Number(imageZoom.value));
                });
                imageZoomIn?.addEventListener('click', () => {
                    markImageCropDirty();
                    changeImageZoom(.15);
                });
                imageZoomOut?.addEventListener('click', () => {
                    markImageCropDirty();
                    changeImageZoom(-.15);
                });
                imageCenter?.addEventListener('click', () => {
                    markImageCropDirty();
                    setImageZoom(zoomValue(), false);
                });
                imageReset?.addEventListener('click', () => {
                    markImageCropDirty();
                    setImageZoom(1, false);
                });

                imageCanvas.addEventListener('wheel', (event) => {
                    if (! state.image) {
                        return;
                    }

                    event.preventDefault();
                    markImageCropDirty();
                    changeImageZoom(event.deltaY < 0 ? .08 : -.08);
                }, { passive: false });

                imageCanvas.addEventListener('pointerdown', (event) => {
                    if (! state.image) {
                        return;
                    }

                    state.activePointers.set(event.pointerId, { x: event.clientX, y: event.clientY });
                    markImageCropDirty();
                    state.dragging = true;
                    state.dragStartX = event.clientX;
                    state.dragStartY = event.clientY;
                    state.startCropX = state.cropX;
                    state.startCropY = state.cropY;
                    imageCanvas.setPointerCapture(event.pointerId);

                    if (state.activePointers.size === 2) {
                        const [first, second] = [...state.activePointers.values()];
                        state.pinchStartDistance = Math.hypot(second.x - first.x, second.y - first.y);
                        state.pinchStartZoom = zoomValue();
                    }
                });

                imageCanvas.addEventListener('pointermove', (event) => {
                    if (! state.dragging || ! state.image || ! state.activePointers.has(event.pointerId)) {
                        return;
                    }

                    state.activePointers.set(event.pointerId, { x: event.clientX, y: event.clientY });

                    if (state.activePointers.size === 2 && state.pinchStartDistance > 0) {
                        const [first, second] = [...state.activePointers.values()];
                        const distance = Math.hypot(second.x - first.x, second.y - first.y);
                        setImageZoom(state.pinchStartZoom * (distance / state.pinchStartDistance));
                        return;
                    }

                    const rect = imageCanvas.getBoundingClientRect();
                    const pixelsPerSource = state.cropWidth / Math.max(1, rect.width);
                    state.cropX = state.startCropX - (event.clientX - state.dragStartX) * pixelsPerSource;
                    state.cropY = state.startCropY - (event.clientY - state.dragStartY) * pixelsPerSource;
                    drawImageCrop();
                });

                const stopImageDrag = (event) => {
                    state.activePointers.delete(event.pointerId);
                    state.dragging = state.activePointers.size > 0;
                    state.pinchStartDistance = 0;
                    if (imageCanvas.hasPointerCapture?.(event.pointerId)) {
                        imageCanvas.releasePointerCapture(event.pointerId);
                    }
                };

                imageCanvas.addEventListener('pointerup', stopImageDrag);
                imageCanvas.addEventListener('pointercancel', stopImageDrag);
                loadCurrentImageCrop();
            }

            const descricao = document.getElementById('descricao');
            const contador = document.getElementById('descricao-count');
            const regras = document.getElementById('regras');
            const usarRegrasPadrao = document.getElementById('usar-regras-padrao');
          const regrasPadrao = `1. Respeito e Fair Play: o respeito entre todos os participantes é obrigatório. Não serão toleradas ofensas, discriminação, ameaças ou agressões físicas. Atitudes antidesportivas podem gerar advertência, suspensão ou exclusão da pelada.

2. Pontualidade: os jogos começam no horário agendado. Atrasos prejudicam o tempo de quadra/campo de todos e podem resultar em perda da vaga ou redução do tempo de jogo.

3. Confirmação de presença: os participantes devem confirmar presença dentro do prazo definido pela organização. Ausências sem aviso podem gerar perda de prioridade em futuras peladas.

4. Divisão de times: as equipes serão sorteadas ou definidas pela organização para manter o equilíbrio e a competitividade saudável entre os participantes.

5. Tempo de jogo: cada partida terá a duração padrão definida pela organização (tempo corrido, número de gols ou sistema de rodízio), garantindo a participação justa de todos.

6. Substituições: as trocas de jogadores devem ocorrer de forma organizada, sem prejudicar o andamento das partidas.

7. Faltas e conduta em campo: entradas violentas, agressões ou atitudes que coloquem outros participantes em risco não serão toleradas.

8. Cartões disciplinares: a organização poderá registrar cartões amarelos e vermelhos. Expulsões e reincidências podem resultar em suspensão temporária ou definitiva.

9. Mensalistas e diaristas: mensalistas possuem prioridade nas vagas. Diaristas devem confirmar presença e realizar o pagamento antes do início da pelada.

10. W.O. (ausência): participantes que confirmarem presença e não comparecerem sem justificativa poderão receber advertência e perder prioridade em futuras inscrições.

11. Cuidado com o espaço e materiais: zele pela quadra, campo, coletes, bolas e demais equipamentos utilizados pela turma.

12. Segurança: cada participante é responsável por suas condições físicas para a prática esportiva e pelo uso adequado dos equipamentos.

13. Estatísticas: a organização poderá registrar gols, assistências, cartões, presenças, vitórias e demais dados para rankings e histórico dos participantes.

14. Decisões da organização: situações não previstas nestas regras serão resolvidas pela organização, visando o bom andamento da pelada.

15. Objetivo principal: promover diversão, amizade, integração, saúde e espírito esportivo entre todos os participantes.`;

function atualizarContador() {
                if (!descricao || !contador) return;
                contador.textContent = descricao.value.length;
            }

            descricao?.addEventListener('input', atualizarContador);
            usarRegrasPadrao?.addEventListener('click', () => {
                if (!regras) return;
                regras.value = regrasPadrao;
                regras.focus();
            });

            atualizarContador();
        })();

        // Busca de CEP Gratuita (ViaCEP)
    const inputCep = document.getElementById('cep');
    const erroCep = document.getElementById('cep-erro');

    inputCep?.addEventListener('blur', function() {
        // Remove caracteres especiais deixando apenas números
        const cep = this.value.replace(/\D/g, "");

        // Valida se o CEP tem 8 dígitos
        if (cep.length === 8) {
            erroCep.classList.add('hidden');
            
            // Alerta visual de carregando nos campos
            document.getElementById('endereco').value = "...";
            document.getElementById('bairro').value = "...";
            document.getElementById('cidade').value = "...";

            // Consulta a API gratuita ViaCEP
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(dados => {
                    if (!dados.erro) {
                        // Preenche os campos com os dados retornados
                        document.getElementById('endereco').value = dados.logradouro;
                        document.getElementById('bairro').value = dados.bairro;
                        // Salva no formato "Cidade - UF" (Ex: São Paulo - SP)
                        document.getElementById('cidade').value = `${dados.localidade} - ${dados.uf}`;
                    } else {
                        // CEP pesquisado não existe
                        limparCamposEndereco();
                        erroCep.classList.remove('hidden');
                    }
                })
                .catch(() => {
                    limparCamposEndereco();
                    alert("Erro ao buscar o CEP. Tente digitar manualmente.");
                });
        }
    });

    // Máscara simples para o input de CEP (adiciona o hífen enquanto digita)
    inputCep?.addEventListener('input', function() {
        let v = this.value.replace(/\D/g, "");
        if (v.length > 5) {
            v = v.substring(0, 5) + "-" + v.substring(5, 8);
        }
        this.value = v;
    });

    function limparCamposEndereco() {
        document.getElementById('endereco').value = "";
        document.getElementById('bairro').value = "";
        document.getElementById('cidade').value = "";
    }
    </script>
</x-app-layout>
