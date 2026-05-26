<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\WelcomeUserNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $redirect = $request->query('redirect');

        if (is_string($redirect) && (str_starts_with($redirect, url('/')) || str_starts_with($redirect, '/'))) {
            $request->session()->put('url.intended', $redirect);
        }

        return view('auth.register', [
            'intendedUrl' => $request->session()->get('url.intended'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        $user->notify(new WelcomeUserNotification());

        Auth::login($user);

        return redirect()
            ->route('perfil.edit')
            ->with('status', 'complete-profile');
    }
}
