<?php

namespace Database\Seeders;

use App\Models\PlayerFollow;
use App\Models\PlayerPost;
use App\Models\PlayerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PlayerPostsDemoSeeder extends Seeder
{
    private array $categories = ['momento', 'gol', 'defesa', 'resenha', 'conquista', 'convite'];

    private array $captions = [
        'Rodada boa demais, elenco completo e jogo pegado até o fim.',
        'Aquele registro depois da vitória suada.',
        'Noite de quadra cheia, bola rolando e resenha garantida.',
        'Treino forte, jogo limpo e parceria de sempre.',
        'Dia de gol bonito e comemoração com a turma.',
        'Quando a pelada vira compromisso sagrado da semana.',
        'Partida equilibrada, ninguém quis perder dividida.',
        'Mais uma rodada para atualizar o card de peladeiro.',
    ];

    public function run(): void
    {
        if (! Schema::hasTable('player_posts') || ! Schema::hasTable('player_post_likes')) {
            return;
        }

        $images = $this->demoImages();
        if ($images->isEmpty()) {
            return;
        }

        $users = User::query()
            ->with('playerProfile')
            ->where('status', 'ativo')
            ->where(fn ($query) => $query->where('active', true)->orWhereNull('active'))
            ->orderBy('id')
            ->get()
            ->values();

        if ($users->isEmpty()) {
            return;
        }

        $this->ensureSocialGraph($users);
        $this->createPosts($users, $images);
        $this->createLikes();
    }

    private function demoImages()
    {
        $directory = public_path('assets/img/demo/player-posts');

        if (! File::isDirectory($directory)) {
            return collect();
        }

        return collect(File::files($directory))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp', 'jfif'], true))
            ->values();
    }

    private function ensureSocialGraph($users): void
    {
        if ($users->count() < 2) {
            return;
        }

        foreach ($users as $index => $user) {
            for ($offset = 1; $offset <= 6; $offset++) {
                $followed = $users->get(($index + $offset) % $users->count());

                if ($followed && $followed->id !== $user->id) {
                    PlayerFollow::firstOrCreate([
                        'follower_id' => $user->id,
                        'followed_id' => $followed->id,
                    ]);
                }
            }
        }
    }

    private function createPosts($users, $images): void
    {
        foreach ($users as $userIndex => $user) {
            $profile = $user->playerProfile ?: PlayerProfile::ensureForUser($user);
            $postsToCreate = min(PlayerPost::MAX_ACTIVE_POSTS, 2 + ($userIndex % 3));

            for ($postIndex = 0; $postIndex < $postsToCreate; $postIndex++) {
                $image = $images->get(($userIndex + $postIndex) % $images->count());
                $extension = strtolower($image->getExtension());
                $baseName = Str::slug($user->username ?: $user->apelido ?: $user->name ?: 'peladeiro').'-'.($postIndex + 1).'.'.$extension;
                $path = 'player-posts/demo/'.$user->id.'/'.$baseName;

                if (! Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->put($path, File::get($image->getPathname()));
                }

                PlayerPost::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'media_path' => $path,
                    ],
                    [
                        'player_profile_id' => $profile->id,
                        'tipo' => 'image',
                        'categoria' => $this->categories[($userIndex + $postIndex) % count($this->categories)],
                        'legenda' => $this->captions[($userIndex + $postIndex) % count($this->captions)],
                        'thumbnail_path' => $path,
                        'mime_type' => $this->mimeType($extension),
                        'tamanho_bytes' => Storage::disk('public')->size($path),
                        'status' => PlayerPost::STATUS_PUBLICADO,
                        'publicado_em' => now()->subDays(($userIndex % 10) + $postIndex)->subMinutes($userIndex * 3),
                    ]
                );
            }
        }
    }

    private function createLikes(): void
    {
        $posts = PlayerPost::query()
            ->publicado()
            ->with('user.followers')
            ->latest('publicado_em')
            ->get();

        foreach ($posts as $postIndex => $post) {
            $likers = $post->user->followers
                ->where('id', '!=', $post->user_id)
                ->values()
                ->take(3 + ($postIndex % 5));

            foreach ($likers as $liker) {
                $post->likes()->syncWithoutDetaching([$liker->id]);
            }
        }
    }

    private function mimeType(string $extension): string
    {
        return match ($extension) {
            'webp' => 'image/webp',
            'png' => 'image/png',
            default => 'image/jpeg',
        };
    }
}
