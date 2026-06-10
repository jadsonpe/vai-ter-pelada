<?php

namespace App\Http\Controllers\Organizador;

use App\Http\Controllers\Controller;
use App\Models\Esporte;
use App\Models\Pelada;
use App\Models\PeladaMembro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PeladaController extends Controller
{
    public function index(Request $request): View
    {
        return view('organizador.peladas.index', [
            'peladas' => Pelada::with('esporte')
                ->where(function ($query) use ($request) {
                    $query->where('organizador_id', $request->user()->id)
                        ->orWhereHas('membros', function ($membros) use ($request) {
                            $membros->where('user_id', $request->user()->id)
                                ->where('status', 'ativo')
                                ->whereIn('papel', [PeladaMembro::PAPEL_ORGANIZADOR, PeladaMembro::PAPEL_DIRETOR]);
                        });
                })
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        if (! auth()->user()->podeCriarPelada()) {
            return view('organizador.peladas.upgrade', [
                'peladasCriadas' => auth()->user()->peladasOrganizadas()->count(),
                'limite' => auth()->user()->limite_peladas ?: 1,
            ]);
        }

        return view('organizador.peladas.form', [
            'pelada' => new Pelada(),
            'esportes' => Esporte::ensureFootballModalities(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $request->user()->podeCriarPelada()) {
            return redirect()
                ->route('organizador.peladas.index')
                ->with('status', 'Seu plano atual permite criar apenas uma pelada. Atualize seu plano para criar mais.');
        }

        $data = $this->peladaData($this->validateData($request));
        $data['organizador_id'] = $request->user()->id;
        $data['slug'] = Str::slug($data['nome']).'-'.Str::lower(Str::random(5));
        $data['local'] = $data['local_nome'];
        $data['capacidade'] = $data['vagas_totais'];
        $data['status'] = 'ativa';
        $data['ativa'] = true;

        $pelada = Pelada::create($data);
        $this->syncImagem($request, $pelada);

        PeladaMembro::updateOrCreate(
            ['pelada_id' => $pelada->id, 'user_id' => $request->user()->id],
            [
                'tipo' => 'mensalista',
                'papel' => PeladaMembro::PAPEL_ORGANIZADOR,
                'status' => 'ativo',
                'prioridade' => 100,
                'data_entrada' => now()->toDateString(),
                'mensalista_desde' => now()->toDateString(),
                'observacao' => 'Organizador da pelada',
            ]
        );

        if ($request->user()->role === 'jogador') {
            $request->user()->update(['role' => 'organizador']);
        }

        return redirect()->route('organizador.peladas.index')->with('status', 'Pelada criada.');
    }

    public function edit(Pelada $pelada): View
    {
        $this->authorizeOwner($pelada);

        return view('organizador.peladas.form', [
            'pelada' => $pelada,
            'esportes' => Esporte::ensureFootballModalities(),
        ]);
    }

    public function update(Request $request, Pelada $pelada): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        $data = $this->peladaData($this->validateData($request));
        $data['local'] = $data['local_nome'];
        $data['capacidade'] = $data['vagas_totais'];
        $data['ativa'] = $data['status'] === 'ativa';
        $pelada->update($data);
        $this->syncImagem($request, $pelada);

        return redirect()->route('organizador.peladas.index')->with('status', 'Pelada atualizada.');
    }

    public function destroy(Pelada $pelada): RedirectResponse
    {
        $this->authorizeOwner($pelada);
        $this->deleteImagem($pelada);
        $pelada->delete();

        return back()->with('status', 'Pelada removida.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'esporte_id' => [
                'required',
                Rule::exists('esportes', 'id')->where(fn ($query) => $query->whereIn('slug', Esporte::ALLOWED_SLUGS)->where('ativo', true)),
            ],
            'nome' => ['required', 'max:255'],
            'descricao' => ['nullable', 'max:200'],
            'data_fundacao' => ['nullable', 'date', 'before_or_equal:today'],
            'categoria' => ['required', 'in:adulto,infantil'],
            'cidade' => ['nullable', 'max:255'],
            'bairro' => ['nullable', 'max:255'],
            'local_nome' => ['required', 'max:255'],
            'endereco' => ['nullable', 'max:255'],
            'horario' => ['nullable', 'date_format:H:i'],
            'vagas_totais' => ['required', 'integer', 'min:2'],
            'vagas_diaristas' => ['nullable', 'integer', 'min:0'],
            'valor_mensalista' => ['nullable', 'numeric', 'min:0'],
            'valor_diarista' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:ativa,pausada,encerrada'],
            'regras' => ['nullable'],
            'whatsapp_contato' => ['nullable', 'max:30'],
            'imagem' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'remover_imagem' => ['nullable', 'boolean'],
            'image_crop_x' => ['nullable', 'numeric', 'min:0'],
            'image_crop_y' => ['nullable', 'numeric', 'min:0'],
            'image_crop_width' => ['nullable', 'numeric', 'min:1'],
            'image_crop_height' => ['nullable', 'numeric', 'min:1'],
            'image_width' => ['nullable', 'integer', 'min:1'],
            'image_height' => ['nullable', 'integer', 'min:1'],
            'image_crop_dirty' => ['nullable', 'boolean'],
        ]) + [
            'aceita_diarista' => true,
            'requer_aprovacao' => true,
            'vagas_diaristas' => (int) $request->input('vagas_diaristas', 0),
        ];
    }

    private function authorizeOwner(Pelada $pelada): void
    {
        $this->redirectIfNotPeladaOwner($pelada);
    }

    private function peladaData(array $data): array
    {
        return collect($data)
            ->except([
                'imagem',
                'remover_imagem',
                'image_crop_x',
                'image_crop_y',
                'image_crop_width',
                'image_crop_height',
                'image_width',
                'image_height',
                'image_crop_dirty',
            ])
            ->all();
    }

    private function syncImagem(Request $request, Pelada $pelada): void
    {
        if ($request->boolean('remover_imagem')) {
            $this->deleteImagem($pelada);
            $pelada->update(['imagem' => null]);

            return;
        }

        if (! $request->hasFile('imagem')) {
            if ($request->boolean('image_crop_dirty') && $pelada->temImagemPropria() && $this->validatedCropData($request)) {
                $this->recropImagemAtual($pelada, $this->validatedCropData($request));
            }

            return;
        }

        $this->deleteImagem($pelada);
        $pelada->update([
            'imagem' => $this->storeImagem($request->file('imagem'), $this->validatedCropData($request)),
        ]);
    }

    private function storeImagem(UploadedFile $file, ?array $crop = null): string
    {
        abort_unless(function_exists('imagewebp'), 422, 'O servidor precisa da extensão GD com suporte a WebP para processar imagens.');

        $source = imagecreatefromstring(file_get_contents($file->getRealPath()));

        if (! $source) {
            abort(422, 'Não foi possível processar a imagem enviada.');
        }

        $source = $this->normalizeImageOrientation($source, $file);
        [$sourceX, $sourceY, $sourceWidth, $sourceHeight] = $this->cropFromData($crop, imagesx($source), imagesy($source));

        $targetWidth = 1600;
        $targetHeight = 900;
        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagecopyresampled($target, $source, 0, 0, $sourceX, $sourceY, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        ob_start();
        imagewebp($target, null, 84);
        $webp = ob_get_clean();

        imagedestroy($target);
        imagedestroy($source);

        $path = 'peladas/'.Str::uuid()->toString().'.webp';
        Storage::disk('public')->put($path, $webp);

        return $path;
    }

    private function recropImagemAtual(Pelada $pelada, array $crop): void
    {
        $disk = Storage::disk('public');

        if (! $pelada->imagem || ! $disk->exists($pelada->imagem)) {
            return;
        }

        $source = imagecreatefromstring($disk->get($pelada->imagem));

        if (! $source) {
            return;
        }

        [$sourceX, $sourceY, $sourceWidth, $sourceHeight] = $this->cropFromData($crop, imagesx($source), imagesy($source));

        $target = imagecreatetruecolor(1600, 900);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagecopyresampled($target, $source, 0, 0, $sourceX, $sourceY, 1600, 900, $sourceWidth, $sourceHeight);

        ob_start();
        imagewebp($target, null, 84);
        $webp = ob_get_clean();

        imagedestroy($target);
        imagedestroy($source);

        $newPath = 'peladas/'.Str::uuid()->toString().'.webp';
        $disk->put($newPath, $webp);
        $this->deleteImagem($pelada);
        $pelada->update(['imagem' => $newPath]);
    }

    private function validatedCropData(Request $request): ?array
    {
        $keys = ['image_crop_x', 'image_crop_y', 'image_crop_width', 'image_crop_height', 'image_width', 'image_height'];

        foreach ($keys as $key) {
            if (! $request->filled($key)) {
                return null;
            }
        }

        return [
            'x' => (float) $request->input('image_crop_x'),
            'y' => (float) $request->input('image_crop_y'),
            'width' => (float) $request->input('image_crop_width'),
            'height' => (float) $request->input('image_crop_height'),
            'image_width' => (int) $request->input('image_width'),
            'image_height' => (int) $request->input('image_height'),
        ];
    }

    /** @return array{0:int,1:int,2:int,3:int} */
    private function cropFromData(?array $cropData, int $width, int $height): array
    {
        if (! $cropData) {
            $ratio = 16 / 9;

            if ($width / max(1, $height) > $ratio) {
                $cropHeight = $height;
                $cropWidth = (int) round($height * $ratio);
            } else {
                $cropWidth = $width;
                $cropHeight = (int) round($width / $ratio);
            }

            return [
                (int) (($width - $cropWidth) / 2),
                (int) (($height - $cropHeight) / 2),
                $cropWidth,
                $cropHeight,
            ];
        }

        $scaleX = $width / max(1, $cropData['image_width']);
        $scaleY = $height / max(1, $cropData['image_height']);
        $sourceWidth = max(1, (int) round($cropData['width'] * $scaleX));
        $sourceHeight = max(1, (int) round($cropData['height'] * $scaleY));
        $sourceWidth = min($sourceWidth, $width);
        $sourceHeight = min($sourceHeight, $height);
        $sourceX = (int) round($cropData['x'] * $scaleX);
        $sourceY = (int) round($cropData['y'] * $scaleY);

        $sourceX = max(0, min($sourceX, $width - $sourceWidth));
        $sourceY = max(0, min($sourceY, $height - $sourceHeight));

        return [$sourceX, $sourceY, $sourceWidth, $sourceHeight];
    }

    private function normalizeImageOrientation($source, UploadedFile $file)
    {
        if (! function_exists('exif_read_data') || ! in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg'], true)) {
            return $source;
        }

        $exif = @exif_read_data($file->getRealPath());
        $orientation = (int) ($exif['Orientation'] ?? 1);

        if ($orientation === 1) {
            return $source;
        }

        $oriented = match ($orientation) {
            2 => $this->flipImage($source, IMG_FLIP_HORIZONTAL),
            3 => imagerotate($source, 180, 0),
            4 => $this->flipImage($source, IMG_FLIP_VERTICAL),
            5 => $this->flipImage(imagerotate($source, -90, 0), IMG_FLIP_HORIZONTAL),
            6 => imagerotate($source, -90, 0),
            7 => $this->flipImage(imagerotate($source, 90, 0), IMG_FLIP_HORIZONTAL),
            8 => imagerotate($source, 90, 0),
            default => $source,
        };

        if ($oriented && $oriented !== $source) {
            imagedestroy($source);
        }

        return $oriented ?: $source;
    }

    private function flipImage($source, int $mode)
    {
        if (! function_exists('imageflip')) {
            return $source;
        }

        imageflip($source, $mode);

        return $source;
    }

    private function deleteImagem(Pelada $pelada): void
    {
        if ($pelada->imagem) {
            Storage::disk('public')->delete($pelada->imagem);
        }
    }
}
