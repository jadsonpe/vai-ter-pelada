<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function redirectIfNotPeladaOwner($pelada): void
    {
        if (auth()->user()?->isAdmin() || $pelada->organizador_id === auth()->id()) {
            return;
        }

        redirect()
            ->route('home')
            ->with('status', 'Você não tem permissão para gerenciar essa pelada.')
            ->send();

        exit;
    }
}
