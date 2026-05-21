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
            'apelido' => ['nullable', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:admin,organizador,jogador'],
            'status' => ['required', 'in:ativo,bloqueado,inativo'],
            'plano' => ['required', 'in:gratis,plus,ilimitado'],
            'limite_peladas' => ['required', 'integer', 'min:0'],
            'phone' => ['nullable', 'max:30'],
            'cidade' => ['nullable', 'max:255'],
            'bairro' => ['nullable', 'max:255'],
            'posicao' => ['nullable', 'max:255'],
            'nivel' => ['nullable', 'integer', 'between:1,5'],
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
