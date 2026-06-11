<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Esporte;
use App\Models\PlayerProfile;
use App\Notifications\ConfirmEmailChangeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('perfil.edit', [
            'user' => $request->user()->load([
                'esportePerfis.esporte',
                'playerProfile.socialLinks',
            ]),
            'esportes' => Esporte::ensureFootballModalities(),
            'imageCoverOptions' => PlayerProfile::imageCoverOptions(),
            'gradientCoverOptions' => PlayerProfile::gradientCoverOptions(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = collect($request->validated())
            ->except(['avatar', 'remover_avatar', 'esporte_perfis', 'player_profile', 'social_links'])
            ->all();
        $requestedEmail = $data['email'];
        unset($data['email']);

        $user->fill($data);
        $user->forceFill([
            'cep' => null,
            'logradouro' => null,
            'numero' => null,
            'complemento' => null,
        ]);

        $emailChangeRequested = ! $user->google_id
            && str($requestedEmail)->lower()->toString() !== str($user->email)->lower()->toString();

        if ($request->boolean('remover_avatar') && $user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->avatar_path = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        if ($user->google_id) {
            $user->pending_email = null;
        } elseif ($emailChangeRequested) {
            $user->pending_email = $requestedEmail;
        }

        $user->save();
        $this->syncEsportePerfis($request, $user);
        $this->syncPlayerProfile($request, $user);

        if ($emailChangeRequested) {
            Notification::route('mail', $user->pending_email)
                ->notify(new ConfirmEmailChangeNotification($user));

            return Redirect::route('perfil.edit')->with('status', 'email-change-verification-sent');
        }

        return Redirect::route('perfil.edit')->with('status', 'profile-updated');
    }

    private function syncEsportePerfis(ProfileUpdateRequest $request, $user): void
    {
        foreach (($request->validated()['esporte_perfis'] ?? []) as $perfil) {
            $posicao = trim((string) ($perfil['posicao'] ?? ''));

            if ($posicao === '') {
                $user->esportePerfis()
                    ->where('esporte_id', $perfil['esporte_id'])
                    ->delete();

                continue;
            }

            $user->esportePerfis()->updateOrCreate(
                ['esporte_id' => $perfil['esporte_id']],
                ['posicao' => $posicao]
            );
        }
    }

    private function syncPlayerProfile(ProfileUpdateRequest $request, $user): void
    {
        $profile = PlayerProfile::ensureForUser($user);
        $profileData = $request->validated()['player_profile'] ?? [];

        $coverMode = $profileData['cover_mode'] ?? ($profileData['banner_preset'] ?? null ? 'image' : 'gradient');

        $profile->fill([
            'esporte_principal_id' => $profileData['esporte_principal_id'] ?? null,
            'posicao_favorita' => $profileData['posicao_favorita'] ?? null,
            'headline' => $profileData['headline'] ?? null,
            'bio' => $profileData['bio'] ?? null,
            'banner_theme' => $coverMode === 'gradient'
                ? ($profileData['banner_theme'] ?? $profile->defaultCoverTheme())
                : null,
            'banner_preset' => $coverMode === 'image'
                ? ($profileData['banner_preset'] ?? null)
                : null,
            'slug' => $user->username ?: PlayerProfile::uniqueSlug($user->apelido ?: $user->name ?: 'peladeiro', $profile->id),
        ]);

        if ($profile->banner_path) {
            Storage::disk('public')->delete($profile->banner_path);
            $profile->banner_path = null;
        }

        $profile->save();

        foreach (($request->validated()['social_links'] ?? []) as $platform => $url) {
            $value = trim((string) $url);

            if ($value === '') {
                $profile->socialLinks()->where('platform', $platform)->delete();
                continue;
            }

            if ($platform === 'whatsapp') {
                if (! str_starts_with($value, 'http')) {
                    $digits = preg_replace('/\D+/', '', $value);
                    $value = $digits ? 'https://wa.me/'.(str_starts_with($digits, '55') ? $digits : '55'.$digits) : '';
                }
            }

            if ($value !== '') {
                $profile->socialLinks()->updateOrCreate(
                    ['platform' => $platform],
                    ['url' => $value]
                );
            }
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        if ($user->playerProfile?->banner_path) {
            Storage::disk('public')->delete($user->playerProfile->banner_path);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
