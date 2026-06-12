<?php

namespace App\Http\Controllers;

use App\Models\PlayerProfile;
use App\Models\PlayerStory;
use App\Models\PlayerStoryItem;
use App\Models\PlayerStoryView;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlayerStoryController extends Controller
{
    public function create(Request $request): View
    {
        return view('jogador.stories.create', [
            'activeStoriesCount' => $request->user()->stories()->active()->count(),
            'maxActiveStories' => PlayerStory::MAX_ACTIVE_STORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'media' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime', 'max:30720'],
            'caption' => ['nullable', 'string', 'max:220'],
            'visibility' => ['required', Rule::in([PlayerStory::VISIBILITY_PUBLIC, PlayerStory::VISIBILITY_FOLLOWERS])],
        ], [
            'media.required' => 'Escolha uma foto ou video para publicar.',
            'media.mimetypes' => 'Use JPG, PNG, WebP, MP4, WebM ou MOV.',
            'media.max' => 'O story deve ter no maximo 30 MB.',
            'caption.max' => 'A legenda deve ter no maximo 220 caracteres.',
        ]);

        $user = $request->user();
        $profile = PlayerProfile::ensureForUser($user);
        $file = $request->file('media');
        $type = str_starts_with((string) $file->getMimeType(), 'video/') ? 'video' : 'image';

        [$mediaPath, $thumbnailPath, $mimeType, $size] = $type === 'image'
            ? $this->storeImage($file, $user->id)
            : $this->storeVideo($file, $user->id);

        $story = PlayerStory::create([
            'user_id' => $user->id,
            'player_profile_id' => $profile->id,
            'caption' => $data['caption'] ?? null,
            'visibility' => $data['visibility'],
            'status' => PlayerStory::STATUS_PUBLISHED,
            'published_at' => now(),
            'expires_at' => now()->addDay(),
        ]);

        $story->items()->create([
            'type' => $type,
            'media_path' => $mediaPath,
            'thumbnail_path' => $thumbnailPath,
            'mime_type' => $mimeType,
            'size_bytes' => $size,
            'sort_order' => 0,
        ]);

        $this->removeOldestStoriesOverLimit($user);

        return redirect()->route('dashboard')->with('status', 'Story publicado por 24 horas.');
    }

    public function feed(Request $request): JsonResponse
    {
        return response()->json([
            'groups' => $this->storyGroups($request->user()),
        ]);
    }

    public function view(Request $request, PlayerStoryItem $item): JsonResponse
    {
        $item->load('story');
        abort_unless($item->story?->status === PlayerStory::STATUS_PUBLISHED && $item->story->expires_at?->isFuture(), 404);
        abort_unless($this->canView($item->story, $request->user()), 403);

        PlayerStoryView::updateOrCreate(
            [
                'player_story_item_id' => $item->id,
                'viewer_id' => $request->user()->id,
            ],
            ['viewed_at' => now()]
        );

        return response()->json(['viewed' => true]);
    }

    public function destroy(Request $request, PlayerStory $story): RedirectResponse|JsonResponse
    {
        abort_unless($story->canBeManagedBy($request->user()), 403);

        $story->load('items');
        $story->removeMedia();
        $story->update(['status' => PlayerStory::STATUS_REMOVED]);

        if ($request->expectsJson()) {
            return response()->json(['removed' => true]);
        }

        return back()->with('status', 'Story removido.');
    }

    public static function groupsForUser(User $user)
    {
        return app(self::class)->storyGroups($user);
    }

    private function storyGroups(User $user)
    {
        $followingIds = $user->following()->pluck('users.id');
        $storyUserIds = $followingIds->push($user->id)->unique()->values();

        $stories = PlayerStory::query()
            ->active()
            ->visibleTo($user)
            ->with(['user.playerProfile', 'items.views' => fn ($query) => $query->where('viewer_id', $user->id)])
            ->whereIn('user_id', $storyUserIds)
            ->latest('published_at')
            ->take(80)
            ->get()
            ->groupBy('user_id');

        return $stories
            ->sortBy(fn ($group, int $userId) => $userId === $user->id ? 0 : 1)
            ->map(function ($stories) use ($user) {
                $author = $stories->first()->user;
                $profile = $author?->playerProfile ?: $author?->publicProfile();
                $items = $stories->flatMap->items->values();
                $seen = $items->every(fn (PlayerStoryItem $item) => $item->views->isNotEmpty() || (int) $author?->id === (int) $user->id);

                return [
                    'user_id' => $author?->id,
                    'name' => $author?->apelido ?: $author?->name ?: 'Peladeiro',
                    'avatar' => $author?->avatarUrl(),
                    'initials' => $author?->initials(),
                    'profile_url' => $profile ? route('peladeiros.show', $profile) : null,
                    'seen' => $seen,
                    'items' => $items->map(fn (PlayerStoryItem $item) => [
                        'id' => $item->id,
                        'story_id' => $item->player_story_id,
                        'type' => $item->type,
                        'media_url' => $item->mediaUrl(),
                        'thumbnail_url' => $item->thumbnailUrl(),
                        'caption' => $item->story->caption,
                        'published_at' => optional($item->story->published_at)->diffForHumans(),
                        'view_url' => route('player-stories.items.view', $item),
                        'delete_url' => $item->story->canBeManagedBy($user) ? route('player-stories.destroy', $item->story) : null,
                        'report_url' => ! $item->story->canBeManagedBy($user) ? route('denuncias.stories.store', $item->story) : null,
                    ])->values(),
                ];
            })
            ->values();
    }

    private function canView(PlayerStory $story, User $user): bool
    {
        if ((int) $story->user_id === (int) $user->id || $story->visibility === PlayerStory::VISIBILITY_PUBLIC) {
            return true;
        }

        return $story->visibility === PlayerStory::VISIBILITY_FOLLOWERS
            && $user->isFollowing($story->user);
    }

    /** @return array{0:string,1:string,2:string,3:int} */
    private function storeImage(UploadedFile $file, int $userId): array
    {
        abort_unless(function_exists('imagewebp'), 422, 'O servidor precisa da extensao GD com suporte a WebP para processar imagens.');

        $source = imagecreatefromstring(file_get_contents($file->getRealPath()));
        abort_unless($source, 422, 'Nao foi possivel processar a imagem enviada.');

        $baseDirectory = 'player-stories/'.$userId;
        $baseName = Str::uuid()->toString();
        $mediaPath = "{$baseDirectory}/{$baseName}.webp";
        $thumbnailPath = "{$baseDirectory}/{$baseName}-thumb.webp";

        Storage::disk('public')->makeDirectory($baseDirectory);
        $this->saveResizedWebp($source, $mediaPath, 1600, 84);
        $this->saveResizedWebp($source, $thumbnailPath, 480, 78);
        imagedestroy($source);

        return [$mediaPath, $thumbnailPath, 'image/webp', Storage::disk('public')->size($mediaPath)];
    }

    /** @return array{0:string,1:null,2:string|null,3:int} */
    private function storeVideo(UploadedFile $file, int $userId): array
    {
        $baseDirectory = 'player-stories/'.$userId;
        $extension = strtolower($file->getClientOriginalExtension() ?: 'mp4');
        $mediaPath = $file->storeAs($baseDirectory, Str::uuid()->toString().'.'.$extension, 'public');

        return [$mediaPath, null, $file->getMimeType(), Storage::disk('public')->size($mediaPath)];
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

    private function removeOldestStoriesOverLimit(User $user): void
    {
        $overflowStories = $user->stories()
            ->active()
            ->with('items')
            ->orderByDesc('published_at')
            ->skip(PlayerStory::MAX_ACTIVE_STORIES)
            ->take(50)
            ->get();

        foreach ($overflowStories as $story) {
            $story->removeMedia();
            $story->update(['status' => PlayerStory::STATUS_REMOVED]);
        }
    }
}
