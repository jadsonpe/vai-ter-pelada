<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\EsporteController;
use App\Http\Controllers\Admin\PatrocinadorController;
use App\Http\Controllers\Admin\PeladaController as AdminPeladaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Jogador\DashboardController as JogadorDashboardController;
use App\Http\Controllers\Jogador\PeladaController as JogadorPeladaController;
use App\Http\Controllers\Organizador\JogoController;
use App\Http\Controllers\Organizador\MembroController;
use App\Http\Controllers\Organizador\PeladaController as OrganizadorPeladaController;
use App\Http\Controllers\Organizador\SolicitacaoController;
use App\Http\Controllers\Organizador\SorteioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/peladas', [PublicController::class, 'peladas'])->name('peladas.index');
Route::get('/peladas/{pelada:slug}', [PublicController::class, 'pelada'])->name('peladas.show');
Route::get('/ranking', [PublicController::class, 'ranking'])->name('ranking');

Route::get('/dashboard', JogadorDashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('jogador')->name('jogador.')->group(function () {
        Route::get('/minhas-peladas', [JogadorPeladaController::class, 'minhas'])->name('peladas.minhas');
        Route::post('/jogos/{jogo}/confirmar', [JogadorPeladaController::class, 'confirmar'])->name('jogos.confirmar');
        Route::delete('/jogos/{jogo}/cancelar', [JogadorPeladaController::class, 'cancelar'])->name('jogos.cancelar');
        Route::post('/peladas/{pelada}/solicitar-mensalista', [JogadorPeladaController::class, 'solicitarMensalista'])->name('peladas.solicitar-mensalista');
    });

    Route::prefix('organizador')->name('organizador.')->middleware('role:admin,organizador')->group(function () {
        Route::resource('peladas', OrganizadorPeladaController::class)->except(['show']);
        Route::get('peladas/{pelada}/membros', [MembroController::class, 'index'])->name('peladas.membros.index');
        Route::post('peladas/{pelada}/membros', [MembroController::class, 'store'])->name('peladas.membros.store');
        Route::delete('peladas/{pelada}/membros/{membro}', [MembroController::class, 'destroy'])->name('peladas.membros.destroy');
        Route::get('peladas/{pelada}/jogos', [JogoController::class, 'index'])->name('peladas.jogos.index');
        Route::post('peladas/{pelada}/jogos', [JogoController::class, 'store'])->name('peladas.jogos.store');
        Route::get('jogos/{jogo}/participantes', [JogoController::class, 'participantes'])->name('jogos.participantes');
        Route::get('peladas/{pelada}/solicitacoes', [SolicitacaoController::class, 'index'])->name('peladas.solicitacoes.index');
        Route::patch('solicitacoes/{solicitacao}/aprovar', [SolicitacaoController::class, 'aprovar'])->name('solicitacoes.aprovar');
        Route::patch('solicitacoes/{solicitacao}/recusar', [SolicitacaoController::class, 'recusar'])->name('solicitacoes.recusar');
        Route::get('jogos/{jogo}/sorteios', [SorteioController::class, 'show'])->name('jogos.sorteios.show');
        Route::post('jogos/{jogo}/sorteios', [SorteioController::class, 'sortear'])->name('jogos.sorteios.sortear');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::resource('esportes', EsporteController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('peladas', AdminPeladaController::class)->only(['index']);
        Route::resource('banners', BannerController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('patrocinadores', PatrocinadorController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['patrocinadores' => 'patrocinador']);
    });
});

require __DIR__.'/auth.php';
