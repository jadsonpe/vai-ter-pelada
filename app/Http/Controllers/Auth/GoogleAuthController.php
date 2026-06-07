<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\WelcomeUserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()->route('login')->with('status', 'Não foi possível autenticar com o Google.');
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        $created = false;

        if ($user) {
            $user->update([
                'google_id' => $user->google_id ?: $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Jogador',
                'username' => User::uniqueUsernameFrom($googleUser->getName() ?: $googleUser->getNickname() ?: 'Jogador'),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(32)),
                'role' => 'jogador',
                'active' => true,
                'email_verified_at' => now(),
            ]);
            $created = true;
        }

        if ($created) {
            $user->notify(new WelcomeUserNotification());
        }

        Auth::login($user, true);

        if ($created || ! $user->perfilCompleto()) {
            return redirect()
                ->route('perfil.edit')
                ->with('status', 'Complete o seu perfil para aproveitar ao máximo a plataforma!');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
