<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.navigation', function ($view) {
            $user = auth()->user();

            $view->with('notificacoesNaoLidas', $user
                ? Cache::remember(
                    "users.{$user->id}.notificacoes_nao_lidas",
                    now()->addMinute(),
                    fn () => $user->notificacoes()->whereNull('lida_em')->count()
                )
                : 0
            );
        });
    }
}
