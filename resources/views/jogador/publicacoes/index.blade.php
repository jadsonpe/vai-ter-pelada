<x-app-layout>
    @php
        $activePosts = $user->posts ?? collect();
    @endphp

    <div class="bg-slate-100">
        <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            @include('shared.status')

            <x-page-header
                title="Minhas publicações"
                description="Publique momentos da pelada e gerencie o que aparece no seu perfil público."
                eyebrow="Perfil do jogador"
            />

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">Publicar no perfil</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Você pode manter até {{ $maxPlayerPosts }} imagens ativas. Ao publicar uma nova foto acima desse limite, a mais antiga é removida automaticamente.
                        </p>
                    </div>

                    <span class="inline-flex w-fit rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-700">
                        {{ $activePosts->count() }}/{{ $maxPlayerPosts }} ativas
                    </span>
                </header>

                <form method="post" action="{{ route('player-posts.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 lg:grid-cols-[1fr_220px]">
                        @csrf

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="player-post-media" value="Imagem" />
                                <input
                                    id="player-post-media"
                                    name="media"
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="mt-1 block w-full rounded-md border border-gray-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800"
                                >
                                <input type="hidden" name="crop_x" data-crop-x>
                                <input type="hidden" name="crop_y" data-crop-y>
                                <input type="hidden" name="crop_size" data-crop-size>
                                <input type="hidden" name="image_width" data-image-width>
                                <input type="hidden" name="image_height" data-image-height>
                                <p class="mt-1 text-xs text-slate-500">JPG, PNG ou WebP até 5 MB. O sistema otimiza a imagem automaticamente.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('media')" />
                            </div>

                            <div data-post-cropper class="hidden overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                                <div class="grid gap-0 lg:grid-cols-[minmax(0,1fr)_230px]">
                                    <div class="bg-slate-950 p-3 sm:p-4">
                                        <div class="mx-auto max-w-[560px]">
                                            <canvas
                                                data-crop-canvas
                                                width="960"
                                                height="960"
                                                class="aspect-square w-full cursor-grab touch-none select-none rounded-md bg-slate-900 shadow-inner active:cursor-grabbing"
                                                aria-label="Previa do corte da publicacao"
                                            ></canvas>
                                        </div>
                                    </div>

                                    <div class="space-y-4 border-t border-slate-200 p-4 lg:border-l lg:border-t-0">
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">Ajuste da imagem</p>
                                            <p class="mt-1 text-xs leading-5 text-slate-500">Arraste para enquadrar. Use o zoom para aproximar sem perder a proporcao do feed.</p>
                                        </div>

                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between gap-3">
                                                <label for="player-post-crop-zoom" class="text-xs font-bold uppercase tracking-wide text-slate-500">Zoom</label>
                                                <span data-crop-zoom-label class="text-xs font-semibold text-slate-500">100%</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button type="button" data-crop-zoom-out class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-slate-300 text-sm font-bold text-slate-700 hover:bg-slate-50" aria-label="Diminuir zoom">-</button>
                                                <input
                                                    id="player-post-crop-zoom"
                                                    data-crop-zoom
                                                    type="range"
                                                    min="1"
                                                    max="4"
                                                    step="0.01"
                                                    value="1"
                                                    class="min-w-0 flex-1 accent-emerald-600"
                                                >
                                                <button type="button" data-crop-zoom-in class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-slate-300 text-sm font-bold text-slate-700 hover:bg-slate-50" aria-label="Aumentar zoom">+</button>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <button type="button" data-crop-center class="inline-flex items-center justify-center rounded-md border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                                Centralizar
                                            </button>
                                            <button type="button" data-crop-reset class="inline-flex items-center justify-center rounded-md border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">
                                                Reajustar
                                            </button>
                                        </div>

                                        <p class="rounded-md bg-emerald-50 px-3 py-2 text-xs font-medium leading-5 text-emerald-800">
                                            A imagem publicada usa exatamente este enquadramento quadrado.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="player-post-legenda" value="Legenda" />
                                <textarea
                                    id="player-post-legenda"
                                    name="legenda"
                                    rows="3"
                                    maxlength="220"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Ex: gol no último minuto, resenha depois da rodada..."
                                >{{ old('legenda') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('legenda')" />
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="player-post-categoria" value="Tipo" />
                                <select
                                    id="player-post-categoria"
                                    name="categoria"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    @foreach($postCategoryLabels as $value => $label)
                                        <option value="{{ $value }}" @selected(old('categoria', 'momento') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('categoria')" />
                            </div>

                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                                Publicar
                            </button>
                        </div>
                </form>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">Minhas publicações</h2>
                        <p class="mt-1 text-sm text-slate-600">Essas imagens aparecem no seu perfil público.</p>
                    </div>
                    <a href="{{ route('peladeiros.show', $user->publicProfile()) }}" class="inline-flex w-fit items-center justify-center rounded-md border border-emerald-200 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                        Ver perfil
                    </a>
                </div>

                @if($activePosts->isEmpty())
                    <div class="mt-5 rounded-lg border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">
                        Nenhuma publicação ainda.
                    </div>
                @else
                    <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($activePosts as $post)
                            <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                                <img src="{{ $post->thumbnailUrl() }}" alt="Publicação do jogador" class="aspect-square w-full object-cover">

                                <div class="space-y-3 p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">
                                            {{ $postCategoryLabels[$post->categoria] ?? 'Momento' }}
                                        </span>
                                        <span class="text-xs font-semibold text-slate-500">{{ $post->likes_count }} curtida(s)</span>
                                    </div>

                                    @if($post->legenda)
                                        <p class="text-sm text-slate-700">{{ $post->legenda }}</p>
                                    @endif

                                    <form method="post" action="{{ route('player-posts.destroy', $post) }}" onsubmit="return confirm('Remover esta publicação do seu perfil?')">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">
                                            Remover
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
    <script>
        (() => {
            const input = document.getElementById('player-post-media');
            const cropper = document.querySelector('[data-post-cropper]');
            const canvas = document.querySelector('[data-crop-canvas]');
            const zoomInput = document.querySelector('[data-crop-zoom]');
            const zoomLabel = document.querySelector('[data-crop-zoom-label]');
            const zoomInButton = document.querySelector('[data-crop-zoom-in]');
            const zoomOutButton = document.querySelector('[data-crop-zoom-out]');
            const centerButton = document.querySelector('[data-crop-center]');
            const resetButton = document.querySelector('[data-crop-reset]');
            const fields = {
                x: document.querySelector('[data-crop-x]'),
                y: document.querySelector('[data-crop-y]'),
                size: document.querySelector('[data-crop-size]'),
                width: document.querySelector('[data-image-width]'),
                height: document.querySelector('[data-image-height]'),
            };

            if (! input || ! cropper || ! canvas || ! zoomInput) {
                return;
            }

            const ctx = canvas.getContext('2d');
            const state = {
                image: null,
                cropX: 0,
                cropY: 0,
                cropSize: 1,
                dragging: false,
                dragStartX: 0,
                dragStartY: 0,
                startCropX: 0,
                startCropY: 0,
                activePointers: new Map(),
                pinchStartDistance: 0,
                pinchStartZoom: 1,
            };

            const getZoom = () => Number(zoomInput.value) || 1;

            const updateZoomLabel = () => {
                if (zoomLabel) {
                    zoomLabel.textContent = `${Math.round(getZoom() * 100)}%`;
                }
            };

            const clampCrop = () => {
                if (! state.image) {
                    return;
                }

                const maxX = Math.max(0, state.image.naturalWidth - state.cropSize);
                const maxY = Math.max(0, state.image.naturalHeight - state.cropSize);
                state.cropX = Math.max(0, Math.min(state.cropX, maxX));
                state.cropY = Math.max(0, Math.min(state.cropY, maxY));
            };

            const syncFields = () => {
                if (! state.image) {
                    Object.values(fields).forEach((field) => field.value = '');
                    return;
                }

                fields.x.value = Math.round(state.cropX * 100) / 100;
                fields.y.value = Math.round(state.cropY * 100) / 100;
                fields.size.value = Math.round(state.cropSize * 100) / 100;
                fields.width.value = state.image.naturalWidth;
                fields.height.value = state.image.naturalHeight;
            };

            const draw = () => {
                if (! state.image) {
                    return;
                }

                clampCrop();
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(
                    state.image,
                    state.cropX,
                    state.cropY,
                    state.cropSize,
                    state.cropSize,
                    0,
                    0,
                    canvas.width,
                    canvas.height
                );

                ctx.save();
                ctx.strokeStyle = 'rgba(255, 255, 255, .36)';
                ctx.lineWidth = 2;

                for (let i = 1; i <= 2; i += 1) {
                    const position = (canvas.width / 3) * i;
                    ctx.beginPath();
                    ctx.moveTo(position, 0);
                    ctx.lineTo(position, canvas.height);
                    ctx.moveTo(0, position);
                    ctx.lineTo(canvas.width, position);
                    ctx.stroke();
                }

                ctx.strokeStyle = 'rgba(255, 255, 255, .9)';
                ctx.lineWidth = 5;
                ctx.strokeRect(2.5, 2.5, canvas.width - 5, canvas.height - 5);
                ctx.restore();

                syncFields();
            };

            const setZoom = (zoom, keepCenter = true) => {
                if (! state.image) {
                    return;
                }

                const currentCenterX = state.cropX + state.cropSize / 2;
                const currentCenterY = state.cropY + state.cropSize / 2;
                const normalizedZoom = Math.max(1, Math.min(4, zoom));

                zoomInput.value = normalizedZoom.toString();
                updateZoomLabel();
                state.cropSize = Math.min(state.image.naturalWidth, state.image.naturalHeight) / normalizedZoom;

                if (keepCenter) {
                    state.cropX = currentCenterX - state.cropSize / 2;
                    state.cropY = currentCenterY - state.cropSize / 2;
                } else {
                    state.cropX = (state.image.naturalWidth - state.cropSize) / 2;
                    state.cropY = (state.image.naturalHeight - state.cropSize) / 2;
                }

                draw();
            };

            const changeZoom = (amount) => {
                setZoom(getZoom() + amount);
            };

            const loadImage = (file) => {
                if (! file) {
                    state.image = null;
                    cropper.classList.add('hidden');
                    state.activePointers.clear();
                    syncFields();
                    return;
                }

                const image = new Image();
                const url = URL.createObjectURL(file);

                image.onload = () => {
                    URL.revokeObjectURL(url);
                    state.image = image;
                    zoomInput.value = '1';
                    cropper.classList.remove('hidden');
                    updateZoomLabel();
                    setZoom(1, false);
                };

                image.src = url;
            };

            input.addEventListener('change', () => loadImage(input.files?.[0]));
            zoomInput.addEventListener('input', () => setZoom(Number(zoomInput.value)));
            zoomInButton?.addEventListener('click', () => changeZoom(.15));
            zoomOutButton?.addEventListener('click', () => changeZoom(-.15));
            centerButton?.addEventListener('click', () => setZoom(Number(zoomInput.value), false));
            resetButton?.addEventListener('click', () => setZoom(1, false));

            canvas.addEventListener('wheel', (event) => {
                if (! state.image) {
                    return;
                }

                event.preventDefault();
                changeZoom(event.deltaY < 0 ? .08 : -.08);
            }, { passive: false });

            canvas.addEventListener('pointerdown', (event) => {
                if (! state.image) {
                    return;
                }

                state.activePointers.set(event.pointerId, {
                    x: event.clientX,
                    y: event.clientY,
                });
                state.dragging = true;
                state.dragStartX = event.clientX;
                state.dragStartY = event.clientY;
                state.startCropX = state.cropX;
                state.startCropY = state.cropY;
                canvas.setPointerCapture(event.pointerId);

                if (state.activePointers.size === 2) {
                    const [first, second] = [...state.activePointers.values()];
                    state.pinchStartDistance = Math.hypot(second.x - first.x, second.y - first.y);
                    state.pinchStartZoom = getZoom();
                }
            });

            canvas.addEventListener('pointermove', (event) => {
                if (! state.dragging || ! state.image || ! state.activePointers.has(event.pointerId)) {
                    return;
                }

                state.activePointers.set(event.pointerId, {
                    x: event.clientX,
                    y: event.clientY,
                });

                if (state.activePointers.size === 2 && state.pinchStartDistance > 0) {
                    const [first, second] = [...state.activePointers.values()];
                    const distance = Math.hypot(second.x - first.x, second.y - first.y);
                    setZoom(state.pinchStartZoom * (distance / state.pinchStartDistance));
                    return;
                }

                const rect = canvas.getBoundingClientRect();
                const pixelsPerSource = state.cropSize / Math.max(1, rect.width);
                state.cropX = state.startCropX - (event.clientX - state.dragStartX) * pixelsPerSource;
                state.cropY = state.startCropY - (event.clientY - state.dragStartY) * pixelsPerSource;
                draw();
            });

            const stopDragging = (event) => {
                state.activePointers.delete(event.pointerId);
                state.dragging = state.activePointers.size > 0;
                state.pinchStartDistance = 0;
                if (canvas.hasPointerCapture?.(event.pointerId)) {
                    canvas.releasePointerCapture(event.pointerId);
                }
            };

            canvas.addEventListener('pointerup', stopDragging);
            canvas.addEventListener('pointercancel', stopDragging);
        })();
    </script>
</x-app-layout>
