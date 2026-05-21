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
            ->with('status', 'Voce nao tem permissao para gerenciar essa pelada.')
            ->send();

        exit;
    }
}
