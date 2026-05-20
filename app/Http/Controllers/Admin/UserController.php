<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', ['users' => User::latest()->paginate(20)]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $user->update($request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:admin,organizador,jogador'],
            'phone' => ['nullable', 'max:30'],
            'active' => ['nullable', 'boolean'],
        ]) + ['active' => $request->boolean('active')]);

        return redirect()->route('admin.users.index')->with('status', 'Usuario atualizado.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 422);
        $user->delete();

        return back()->with('status', 'Usuario removido.');
    }
}
