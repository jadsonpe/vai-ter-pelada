<?php

namespace App\Http\Controllers;

use App\Models\PlayerPost;
use App\Models\PlayerProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlayerPostController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load([
            'posts' => fn ($query) => $query
                ->publicado()
                ->withCount('likes')
                ->latest('publicado_em')
                ->latest(),
        ]);

        return view('jogador.publicacoes.index', [
            'user' => $user,
            'postCategoryLabels' => self::categoryLabels(),
            'maxPlayerPosts' => PlayerPost::MAX_ACTIVE_POSTS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = PlayerProfile::ensureForUser($user);

        $data = $request->validate([
            'media' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'categoria' => ['required', Rule::in(array_keys($this->categoryLabels()))],
            'legenda' => ['nullable', 'string', 'max:220'],
            'crop_x' => ['nullable', 'numeric', 'min:0'],
            'crop_y' => ['nullable', 'numeric', 'min:0'],
            'crop_size' => ['nullable', 'numeric', 'min:1'],
            'image_width' => ['nullable', 'integer', 'min:1'],
            'image_height' => ['nullable', 'integer', 'min:1'],
        ], [
            'media.required' => 'Escolha uma imagem para publicar.',
            'media.image' => 'A publicação precisa ser uma imagem válida.',
            'media.max' => 'A imagem deve ter no máximo 5 MB.',
            'legenda.max' => 'A legenda deve ter no máximo 220 caracteres.',
        ]);

        $crop = $this->validatedCropData($data);
        [$mediaPath, $thumbnailPath] = $this->storeOptimizedImage($request->file('media'), $user->id, $crop);

        PlayerPost::create([
            'user_id' => $user->id,
            'player_profile_id' => $profile->id,
            'tipo' => 'image',
            'categoria' => $data['categoria'],
            'legenda' => $data['legenda'] ?? null,
            'media_path' => $mediaPath,
            'thumbnail_path' => $thumbnailPath,
            'mime_type' => 'image/webp',
            'tamanho_bytes' => Storage::disk('public')->size($mediaPath),
            'status' => PlayerPost::STATUS_PUBLICADO,
            'publicado_em' => now(),
        ]);

        $this->removeOldestPostsOverLimit($user);

        return back()->with('status', 'Publicação criada. Se você passou do limite de 5 fotos, a mais antiga foi removida automaticamente.');
    }

    public function destroy(Request $request, PlayerPost $post): RedirectResponse
    {
        abort_unless($post->canBeManagedBy($request->user()), 403);

        $post->removeMedia();
        $post->update(['status' => PlayerPost::STATUS_REMOVIDO]);

        return back()->with('status', 'Publicação removida.');
    }

    public function toggleLike(Request $request, PlayerPost $post): RedirectResponse|JsonResponse
    {
        abort_unless($post->status === PlayerPost::STATUS_PUBLICADO, 404);

        $liked = $post->likes()->where('user_id', $request->user()->id)->exists();

        if ($liked) {
            $post->likes()->detach($request->user()->id);
            $liked = false;
        } else {
            $post->likes()->attach($request->user()->id);
            $liked = true;
        }

        $likesCount = $post->likes()->count();

        if ($request->expectsJson()) {
            return response()->json([
                'liked' => $liked,
                'likes_count' => $likesCount,
                'likes_label' => $likesCount.' curtida(s)',
            ]);
        }

        return back();
    }

    public static function categoryLabels(): array
    {
        return [
            'momento' => 'Momento',
            'gol' => 'Gol bonito',
            'penalti' => 'Pênalti',
            'ataque' => 'Ataque',
            'chute' => 'Chute',
            'cartao' => 'Cartão',
            'defesa' => 'Defesaça',
            'resenha' => 'Resenha',
            'conquista' => 'Conquista',
            'convite' => 'Convite',
            'falta' => 'Falta boba',
            'drible' => 'Drible',
            'time' => 'Time',
            'jogo' => 'Jogo',
        ];
    }

    /** @return array{0:string,1:string} */
    private function storeOptimizedImage(UploadedFile $file, int $userId, ?array $crop = null): array
    {
        abort_unless(function_exists('imagewebp'), 422, 'O servidor precisa da extensão GD com suporte a WebP para processar imagens.');

        $contents = file_get_contents($file->getRealPath());
        $source = imagecreatefromstring($contents);

        if (! $source) {
            abort(422, 'Não foi possível processar a imagem enviada.');
        }

        $source = $this->normalizeImageOrientation($source, $file);

        $baseDirectory = 'player-posts/'.$userId;
        $baseName = Str::uuid()->toString();
        $mediaPath = "{$baseDirectory}/{$baseName}.webp";
        $thumbnailPath = "{$baseDirectory}/{$baseName}-thumb.webp";

        Storage::disk('public')->makeDirectory($baseDirectory);

        $this->saveResizedWebp($source, $mediaPath, 1400, 84);
        $this->saveSquareWebp($source, $thumbnailPath, 720, 80, $crop);

        imagedestroy($source);

        return [$mediaPath, $thumbnailPath];
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

    private function saveResizedWebp($source, string $path, int $maxSize, int $quality): void
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, $maxSize / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        ob_start();
        imagewebp($target, null, $quality);
        $webp = ob_get_clean();
        imagedestroy($target);

        Storage::disk('public')->put($path, $webp);
    }

    private function saveSquareWebp($source, string $path, int $size, int $quality, ?array $cropData = null): void
    {
        $width = imagesx($source);
        $height = imagesy($source);
        [$sourceX, $sourceY, $crop] = $this->squareCropFromData($cropData, $width, $height);

        $target = imagecreatetruecolor($size, $size);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        imagecopyresampled($target, $source, 0, 0, $sourceX, $sourceY, $size, $size, $crop, $crop);

        ob_start();
        imagewebp($target, null, $quality);
        $webp = ob_get_clean();
        imagedestroy($target);

        Storage::disk('public')->put($path, $webp);
    }

    private function validatedCropData(array $data): ?array
    {
        $keys = ['crop_x', 'crop_y', 'crop_size', 'image_width', 'image_height'];

        foreach ($keys as $key) {
            if (! isset($data[$key])) {
                return null;
            }
        }

        return [
            'x' => (float) $data['crop_x'],
            'y' => (float) $data['crop_y'],
            'size' => (float) $data['crop_size'],
            'image_width' => (int) $data['image_width'],
            'image_height' => (int) $data['image_height'],
        ];
    }

    /** @return array{0:int,1:int,2:int} */
    private function squareCropFromData(?array $cropData, int $width, int $height): array
    {
        if (! $cropData) {
            $crop = min($width, $height);

            return [
                (int) (($width - $crop) / 2),
                (int) (($height - $crop) / 2),
                $crop,
            ];
        }

        $scaleX = $width / max(1, $cropData['image_width']);
        $scaleY = $height / max(1, $cropData['image_height']);
        $scale = min($scaleX, $scaleY);
        $crop = max(1, (int) round($cropData['size'] * $scale));
        $crop = min($crop, $width, $height);
        $sourceX = (int) round($cropData['x'] * $scaleX);
        $sourceY = (int) round($cropData['y'] * $scaleY);

        $sourceX = max(0, min($sourceX, $width - $crop));
        $sourceY = max(0, min($sourceY, $height - $crop));

        return [$sourceX, $sourceY, $crop];
    }

    private function removeOldestPostsOverLimit(User $user): void
    {
        $overflowPosts = $user->posts()
            ->publicado()
            ->orderByDesc('publicado_em')
            ->orderByDesc('created_at')
            ->skip(PlayerPost::MAX_ACTIVE_POSTS)
            ->take(50)
            ->get();

        foreach ($overflowPosts as $post) {
            $post->removeMedia();
            $post->update(['status' => PlayerPost::STATUS_REMOVIDO]);
        }
    }
}
