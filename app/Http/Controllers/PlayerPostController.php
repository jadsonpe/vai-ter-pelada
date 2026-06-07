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
        ], [
            'media.required' => 'Escolha uma imagem para publicar.',
            'media.image' => 'A publicação precisa ser uma imagem válida.',
            'media.max' => 'A imagem deve ter no máximo 5 MB.',
            'legenda.max' => 'A legenda deve ter no máximo 220 caracteres.',
        ]);

        [$mediaPath, $thumbnailPath] = $this->storeOptimizedImage($request->file('media'), $user->id);

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
    private function storeOptimizedImage(UploadedFile $file, int $userId): array
    {
        abort_unless(function_exists('imagewebp'), 422, 'O servidor precisa da extensão GD com suporte a WebP para processar imagens.');

        $contents = file_get_contents($file->getRealPath());
        $source = imagecreatefromstring($contents);

        if (! $source) {
            abort(422, 'Não foi possível processar a imagem enviada.');
        }

        $baseDirectory = 'player-posts/'.$userId;
        $baseName = Str::uuid()->toString();
        $mediaPath = "{$baseDirectory}/{$baseName}.webp";
        $thumbnailPath = "{$baseDirectory}/{$baseName}-thumb.webp";

        Storage::disk('public')->makeDirectory($baseDirectory);

        $this->saveResizedWebp($source, $mediaPath, 1400, 84);
        $this->saveSquareWebp($source, $thumbnailPath, 720, 80);

        imagedestroy($source);

        return [$mediaPath, $thumbnailPath];
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

    private function saveSquareWebp($source, string $path, int $size, int $quality): void
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $crop = min($width, $height);
        $sourceX = (int) (($width - $crop) / 2);
        $sourceY = (int) (($height - $crop) / 2);

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
