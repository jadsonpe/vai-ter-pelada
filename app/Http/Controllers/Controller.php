<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function redirectIfNotPeladaOwner($pelada): void
    {
        $user = auth()->user();

        if ($user?->isAdmin() || $pelada->isOwner($user)) {
            return;
        }

        redirect()
            ->route('home')
            ->with('status', 'Você não tem permissão para alterar as configurações desta pelada.')
            ->send();

        exit;
    }

    protected function redirectIfNotPeladaManager($pelada): void
    {
        if ($pelada->isManagedBy(auth()->user())) {
            return;
        }

        redirect()
            ->route('home')
            ->with('status', 'Você não tem permissão para gerenciar essa pelada.')
            ->send();

        exit;
    }
}
