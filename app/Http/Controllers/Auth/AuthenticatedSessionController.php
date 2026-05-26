<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $this->storeIntendedUrl($request);

        return view('auth.login', [
            'intendedUrl' => $request->session()->get('url.intended'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function storeIntendedUrl(Request $request): void
    {
        $redirect = $request->query('redirect');

        if (! is_string($redirect) || $redirect === '') {
            return;
        }

        if (str_starts_with($redirect, url('/')) || str_starts_with($redirect, '/')) {
            $request->session()->put('url.intended', $redirect);
        }
    }
}
