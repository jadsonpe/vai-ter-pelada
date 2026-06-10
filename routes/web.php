<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\EsporteController;
use App\Http\Controllers\Admin\PatrocinadorController;
use App\Http\Controllers\Admin\PeladaController as AdminPeladaController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
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
use App\Http\Controllers\Organizador\TorneioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JogadorSearchController;
use App\Http\Controllers\PlayerPostController;
use App\Http\Controllers\PlayerProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Public\ArenaController;
use App\Http\Controllers\Public\PatrocinadorController as PublicPatrocinadorController;
use App\Http\Controllers\Public\TorneioController as PublicTorneioController;
use App\Http\Controllers\Jogador\AvaliacaoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/peladas', [PublicController::class, 'peladas'])->name('peladas.index');
Route::get('/peladas/{pelada:slug}', [PublicController::class, 'pelada'])->middleware('auth')->name('peladas.show');
Route::get('/torneios/{torneio:slug}', [PublicTorneioController::class, 'show'])->name('torneios.public.show');
Route::get('/ranking', [PublicController::class, 'ranking'])->name('ranking');
Route::get('/arenas', [ArenaController::class, 'index'])->name('arenas.index');
Route::get('/patrocinadores', [PublicPatrocinadorController::class, 'index'])->name('patrocinadores.index');
Route::get('/p/{pelada:slug}', [PublicController::class, 'peladaPublic'])->name('peladas.public.show');
Route::get('/peladeiro/{profile:slug}/card.png', [PlayerProfileController::class, 'card'])->name('peladeiros.card');
Route::get('/peladeiro/{profile:slug}/card.svg', [PlayerProfileController::class, 'legacyCard'])->name('peladeiros.card.legacy');
Route::get('/peladeiro/{profile:slug}/seguidores', [PlayerProfileController::class, 'followers'])->name('peladeiros.followers');
Route::get('/peladeiro/{profile:slug}/seguindo', [PlayerProfileController::class, 'following'])->name('peladeiros.following');
Route::get('/peladeiro/{profile:slug}', [PlayerProfileController::class, 'show'])->name('peladeiros.show');
Route::get('/jogadores/{user}', [PlayerProfileController::class, 'legacy'])->name('jogadores.show');
Route::get('/termos-de-uso', [PublicController::class, 'termos'])->name('termos');
Route::get('/politica-de-privacidade', [PublicController::class, 'privacidade'])->name('privacidade');
Route::view('/conta-bloqueada', 'auth.conta-bloqueada')->name('conta.bloqueada');

Route::get('/dashboard', JogadorDashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/jogadores', JogadorSearchController::class)->name('jogadores.index');

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('perfil.edit');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('perfil.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('perfil.destroy');
    Route::get('/jogador/publicacoes', [PlayerPostController::class, 'index'])->name('player-posts.index');
    Route::post('/jogador/publicacoes', [PlayerPostController::class, 'store'])->name('player-posts.store');
    Route::delete('/publicacoes/{post}', [PlayerPostController::class, 'destroy'])->name('player-posts.destroy');
    Route::post('/publicacoes/{post}/curtir', [PlayerPostController::class, 'toggleLike'])->name('player-posts.likes.toggle');
    Route::post('/peladeiro/{profile:slug}/seguir', [PlayerProfileController::class, 'follow'])->name('peladeiros.follow');
    Route::delete('/peladeiro/{profile:slug}/seguir', [PlayerProfileController::class, 'unfollow'])->name('peladeiros.unfollow');
    Route::post('/denuncias/peladas/{pelada:slug}', [ReportController::class, 'storePelada'])->name('denuncias.peladas.store');
    Route::post('/denuncias/peladeiros/{profile:slug}', [ReportController::class, 'storePlayer'])->name('denuncias.peladeiros.store');
    Route::post('/denuncias/publicacoes/{post}', [ReportController::class, 'storePlayerPost'])->name('denuncias.player-posts.store');

    Route::prefix('jogador')->name('jogador.')->group(function () {
        Route::get('/avaliacoes', fn () => redirect()->route('dashboard', ['aba' => 'avaliacoes']))->name('avaliacoes.index');
        Route::post('/avaliacoes', [AvaliacaoController::class, 'store'])->name('avaliacoes.store');
        Route::post('/votos', [AvaliacaoController::class, 'vote'])->name('votos.store');

        Route::get('/minhas-peladas', fn () => redirect()->route('dashboard', ['aba' => 'peladas']))->name('peladas.minhas');
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
        Route::post('jogos/{jogo}/finalizar', [JogoController::class, 'finalizar'])->name('jogos.finalizar');
        Route::post('jogos/{jogo}/cancelar', [JogoController::class, 'cancelar'])->name('jogos.cancelar');
        Route::get('jogos/{jogo}', [JogoController::class, 'show'])->name('jogos.show');
        Route::get('jogos/{jogo}/participantes', [JogoController::class, 'participantes'])->name('jogos.participantes');
        Route::post('jogos/{jogo}/participantes/membros', [JogoController::class, 'confirmarMembro'])->name('jogos.participantes.confirmar-membro');
        Route::post('jogos/{jogo}/participantes/mensalistas', [JogoController::class, 'confirmarMembro'])->name('jogos.participantes.confirmar-mensalista');
        Route::delete('jogos/{jogo}/participantes/{participante}', [JogoController::class, 'removerParticipante'])->name('jogos.participantes.remover');
        Route::get('peladas/{pelada}/solicitacoes', [SolicitacaoController::class, 'index'])->name('peladas.solicitacoes.index');
        Route::patch('solicitacoes/{solicitacao}/aprovar', [SolicitacaoController::class, 'aprovar'])->name('solicitacoes.aprovar');
        Route::patch('solicitacoes/{solicitacao}/recusar', [SolicitacaoController::class, 'recusar'])->name('solicitacoes.recusar');
        Route::get('peladas/{pelada}/torneios', [TorneioController::class, 'index'])->name('peladas.torneios.index');
        Route::get('peladas/{pelada}/torneios/create', [TorneioController::class, 'create'])->name('peladas.torneios.create');
        Route::post('peladas/{pelada}/torneios', [TorneioController::class, 'store'])->name('peladas.torneios.store');
        Route::get('torneios/{torneio:slug}', [TorneioController::class, 'show'])->name('torneios.show');
        Route::get('torneios/{torneio:slug}/edit', [TorneioController::class, 'edit'])->name('torneios.edit');
        Route::patch('torneios/{torneio:slug}', [TorneioController::class, 'update'])->name('torneios.update');
        Route::post('torneios/{torneio:slug}/participantes', [TorneioController::class, 'addParticipantes'])->name('torneios.participantes.store');
        Route::patch('torneios/{torneio:slug}/participantes', [TorneioController::class, 'updateParticipantesMany'])->name('torneios.participantes.update-many');
        Route::patch('torneios/participantes/{participante}', [TorneioController::class, 'updateParticipante'])->name('torneios.participantes.update');
        Route::delete('torneios/participantes/{participante}', [TorneioController::class, 'removeParticipante'])->name('torneios.participantes.destroy');
        Route::post('torneios/{torneio:slug}/sortear-times', [TorneioController::class, 'sortearTimes'])->name('torneios.times.sortear');
        Route::patch('torneios/times/{time}', [TorneioController::class, 'updateTime'])->name('torneios.times.update');
        Route::post('torneios/times/{time}/jogadores', [TorneioController::class, 'addJogadorTime'])->name('torneios.times.jogadores.store');
        Route::post('torneios/{torneio:slug}/gerar-jogos', [TorneioController::class, 'gerarJogos'])->name('torneios.jogos.gerar');
        Route::patch('torneios/jogos/{jogo}', [TorneioController::class, 'resultado'])->name('torneios.jogos.resultado');
        Route::get('jogos/{jogo}/sorteios', [SorteioController::class, 'show'])->name('jogos.sorteios.show');
        Route::post('jogos/{jogo}/sorteios/presencas', [SorteioController::class, 'salvarPresencas'])->name('jogos.sorteios.presencas');
        Route::post('jogos/{jogo}/sorteios/avulsos', [SorteioController::class, 'adicionarAvulso'])->name('jogos.sorteios.avulsos');
        Route::post('jogos/{jogo}/sorteios', [SorteioController::class, 'sortear'])->name('jogos.sorteios.sortear');
        Route::patch('jogos/{jogo}/sorteios/{sorteio}/times', [SorteioController::class, 'atualizarTimes'])->name('jogos.sorteios.times.update');
        Route::post('jogos/{jogo}/estatisticas', [JogoController::class, 'salvarEstatisticas'])->name('jogos.estatisticas.store');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', AdminController::class)->name('dashboard');
        Route::redirect('/usuarios', '/admin/users')->name('usuarios.index');
        Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::redirect('/modalidades', '/admin/esportes')->name('modalidades.index');
        Route::resource('esportes', EsporteController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('peladas', AdminPeladaController::class)->only(['index']);
        Route::get('denuncias', [AdminReportController::class, 'index'])->name('reports.index');
        Route::patch('denuncias/{report}', [AdminReportController::class, 'update'])->name('reports.update');
        Route::resource('banners', BannerController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('patrocinadores', PatrocinadorController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['patrocinadores' => 'patrocinador']);
    });
});

require __DIR__.'/auth.php';
