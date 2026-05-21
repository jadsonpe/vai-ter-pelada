<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\EsporteController;
use App\Http\Controllers\Admin\PatrocinadorController;
use App\Http\Controllers\Admin\PeladaController as AdminPeladaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Jogador\DashboardController as JogadorDashboardController;
use App\Http\Controllers\Jogador\PeladaController as JogadorPeladaController;
use App\Http\Controllers\Organizador\JogoController;
use App\Http\Controllers\Organizador\CaixaController;
use App\Http\Controllers\Organizador\MembroController;
use App\Http\Controllers\Organizador\PeladaController as OrganizadorPeladaController;
use App\Http\Controllers\Organizador\SolicitacaoController;
use App\Http\Controllers\Organizador\SorteioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Public\ArenaController;
use App\Http\Controllers\Public\PatrocinadorController as PublicPatrocinadorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/peladas', [PublicController::class, 'peladas'])->name('peladas.index');
Route::get('/peladas/{pelada:slug}', [PublicController::class, 'pelada'])->name('peladas.show');
Route::get('/ranking', [PublicController::class, 'ranking'])->name('ranking');
Route::get('/arenas', [ArenaController::class, 'index'])->name('arenas.index');
Route::get('/patrocinadores', [PublicPatrocinadorController::class, 'index'])->name('patrocinadores.index');

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
        Route::patch('/solicitacoes/{solicitacao}/aceitar-convite', [JogadorPeladaController::class, 'aceitarConvite'])->name('solicitacoes.aceitar-convite');
        Route::patch('/solicitacoes/{solicitacao}/recusar-convite', [JogadorPeladaController::class, 'recusarConvite'])->name('solicitacoes.recusar-convite');
    });

    Route::prefix('organizador')->name('organizador.')->group(function () {
        Route::resource('peladas', OrganizadorPeladaController::class)->except(['show']);
        Route::get('peladas/{pelada}/membros', [MembroController::class, 'index'])->name('peladas.membros.index');
        Route::post('peladas/{pelada}/membros', [MembroController::class, 'store'])->name('peladas.membros.store');
        Route::patch('peladas/{pelada}/membros', [MembroController::class, 'updateMany'])->name('peladas.membros.update-many');
        Route::delete('peladas/{pelada}/membros/{membro}', [MembroController::class, 'destroy'])->name('peladas.membros.destroy');
        Route::get('peladas/{pelada}/caixa', [CaixaController::class, 'index'])->name('peladas.caixa.index');
        Route::post('peladas/{pelada}/caixa', [CaixaController::class, 'store'])->name('peladas.caixa.store');
        Route::post('peladas/{pelada}/caixa/mensalidades/{membro}', [CaixaController::class, 'registrarMensalidade'])->name('peladas.caixa.mensalidades.store');
        Route::post('peladas/{pelada}/caixa/jogos/{jogo}/diarias/{participante}', [CaixaController::class, 'registrarDiaria'])->name('peladas.caixa.diarias.store');
        Route::delete('peladas/{pelada}/caixa/{movimentacao}', [CaixaController::class, 'destroy'])->name('peladas.caixa.destroy');
        Route::get('peladas/{pelada}/jogos', [JogoController::class, 'index'])->name('peladas.jogos.index');
        Route::post('peladas/{pelada}/jogos', [JogoController::class, 'store'])->name('peladas.jogos.store');
        Route::patch('jogos/{jogo}', [JogoController::class, 'update'])->name('jogos.update');
        Route::get('jogos/{jogo}/participantes', [JogoController::class, 'participantes'])->name('jogos.participantes');
        Route::post('jogos/{jogo}/participantes/mensalistas', [JogoController::class, 'confirmarMensalista'])->name('jogos.participantes.confirmar-mensalista');
        Route::delete('jogos/{jogo}/participantes/{participante}', [JogoController::class, 'removerParticipante'])->name('jogos.participantes.remover');
        Route::get('peladas/{pelada}/solicitacoes', [SolicitacaoController::class, 'index'])->name('peladas.solicitacoes.index');
        Route::patch('solicitacoes/{solicitacao}/aprovar', [SolicitacaoController::class, 'aprovar'])->name('solicitacoes.aprovar');
        Route::patch('solicitacoes/{solicitacao}/recusar', [SolicitacaoController::class, 'recusar'])->name('solicitacoes.recusar');
        Route::get('jogos/{jogo}/sorteios', [SorteioController::class, 'show'])->name('jogos.sorteios.show');
        Route::post('jogos/{jogo}/sorteios', [SorteioController::class, 'sortear'])->name('jogos.sorteios.sortear');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', AdminController::class)->name('dashboard');
        Route::redirect('/usuarios', '/admin/users')->name('usuarios.index');
        Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::redirect('/modalidades', '/admin/esportes')->name('modalidades.index');
        Route::resource('esportes', EsporteController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('peladas', AdminPeladaController::class)->only(['index']);
        Route::resource('banners', BannerController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('patrocinadores', PatrocinadorController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['patrocinadores' => 'patrocinador']);
    });
});

require __DIR__.'/auth.php';
