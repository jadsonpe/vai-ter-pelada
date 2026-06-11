<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConfirmPendingEmailController extends Controller
{
    public function __invoke(Request $request, User $user, string $hash): RedirectResponse
    {
        if (! $request->user()->is($user)) {
            abort(403);
        }

        if (! $user->pending_email || ! hash_equals(sha1($user->pending_email), $hash)) {
            return redirect()
                ->route('perfil.edit')
                ->withErrors(['email' => 'Este link de confirmacao de email nao e mais valido.']);
        }

        if (User::where('email', $user->pending_email)->whereKeyNot($user->id)->exists()) {
            $user->forceFill(['pending_email' => null])->save();

            return redirect()
                ->route('perfil.edit')
                ->withErrors(['email' => 'Este email ja esta em uso por outra conta.']);
        }

        $user->forceFill([
            'email' => $user->pending_email,
            'pending_email' => null,
            'email_verified_at' => now(),
        ])->save();

        return redirect()
            ->route('perfil.edit')
            ->with('status', 'email-change-confirmed');
    }
}
