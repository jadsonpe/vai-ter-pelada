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

                <form method="post" action="{{ route('player-posts.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 md:grid-cols-[1fr_180px]">
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
                                <p class="mt-1 text-xs text-slate-500">JPG, PNG ou WebP até 5 MB. O sistema otimiza a imagem automaticamente.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('media')" />
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
</x-app-layout>
