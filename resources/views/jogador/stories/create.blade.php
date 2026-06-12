<x-app-layout>
    <div class="bg-slate-100">
        <div class="mx-auto max-w-3xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            @include('shared.status')

            <x-page-header
                title="Novo story"
                description="Publique uma foto ou video que fica disponivel por 24 horas."
                eyebrow="Stories"
            />

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">Publicar story</h2>
                        <p class="mt-1 text-sm text-slate-600">Voce pode manter ate {{ $maxActiveStories }} stories ativos. Os mais antigos saem automaticamente.</p>
                    </div>
                    <span class="inline-flex w-fit rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-700">
                        {{ $activeStoriesCount }}/{{ $maxActiveStories }} ativos
                    </span>
                </div>

                <form method="post" action="{{ route('player-stories.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="story-media" value="Foto ou video" />
                        <input
                            id="story-media"
                            name="media"
                            type="file"
                            accept="image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime"
                            class="mt-1 block w-full rounded-md border border-gray-300 bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800"
                            required
                        >
                        <p class="mt-1 text-xs text-slate-500">Fotos em JPG, PNG ou WebP. Videos em MP4, WebM ou MOV ate 30 MB.</p>
                        <x-input-error class="mt-2" :messages="$errors->get('media')" />
                    </div>

                    <div>
                        <x-input-label for="story-caption" value="Legenda" />
                        <textarea
                            id="story-caption"
                            name="caption"
                            rows="3"
                            maxlength="220"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Ex: aquecimento, gol bonito, resenha depois do jogo..."
                        >{{ old('caption') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('caption')" />
                    </div>

                    <div>
                        <x-input-label for="story-visibility" value="Quem pode ver" />
                        <select
                            id="story-visibility"
                            name="visibility"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="public" @selected(old('visibility', 'public') === 'public')>Todos os jogadores</option>
                            <option value="followers" @selected(old('visibility') === 'followers')>Apenas seguidores</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('visibility')" />
                    </div>

                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Cancelar
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                            Publicar story
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-app-layout>
