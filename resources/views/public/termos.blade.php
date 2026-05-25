<x-app-layout>
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Documento legal</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-950 sm:text-4xl">Termos de Uso</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">
                Estes termos explicam as regras para usar o Vai Ter Pelada como plataforma de conveniencia e gestao de peladas amadoras.
            </p>
            <p class="mt-4 text-sm text-slate-500">Ultima atualizacao: {{ now()->format('d/m/Y') }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-8 rounded-lg border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div>
                <h2 class="text-xl font-bold text-slate-950">1. Aceitacao dos termos</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Ao criar uma conta, acessar ou usar o Vai Ter Pelada, voce declara que leu, entendeu e concorda com estes Termos de Uso.
                    Se voce nao concordar, nao utilize a plataforma.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">2. O que e a plataforma</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O Vai Ter Pelada e uma ferramenta digital para facilitar a organizacao de peladas, jogos, membros, confirmacoes,
                    fila de espera, sorteios de times, ranking, avaliacoes, notificacoes e controle visual de caixa.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    A plataforma nao organiza fisicamente os jogos, nao administra campos ou arenas, nao garante a realizacao das partidas
                    e nao atua como intermediadora financeira.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">3. Cadastro e conta</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Para usar certas funcionalidades, o usuario deve criar uma conta e manter seus dados corretos e atualizados,
                    especialmente e-mail e telefone quando forem necessarios para contato com o organizador.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    O usuario e responsavel por manter sua senha em sigilo e por todas as atividades feitas em sua conta.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">4. Perfis de usuario</h2>
                <ul class="mt-3 list-disc space-y-2 pl-5 leading-7 text-slate-700">
                    <li><strong>Jogador:</strong> pode solicitar entrada, aceitar convites, confirmar ou cancelar presenca, participar de jogos, avaliacoes e rankings.</li>
                    <li><strong>Organizador:</strong> pode criar peladas, aprovar ou recusar solicitacoes, convidar membros, criar rodadas, controlar presencas, registrar caixa e realizar sorteios.</li>
                    <li><strong>Administrador:</strong> pode gerenciar usuarios, esportes, banners, patrocinadores e dados gerais da plataforma.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">5. Jogos, brigas, lesoes e cancelamentos</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    A pratica esportiva envolve riscos proprios, como quedas, choques, lesoes, discussoes, atrasos, faltas, cancelamentos,
                    indisponibilidade de campo e problemas de terceiros.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    O Vai Ter Pelada nao se responsabiliza por brigas, ofensas, agressoes, lesoes fisicas, danos materiais, objetos perdidos,
                    furtos, roubos, cancelamentos, alteracoes de horario, ausencia de jogadores ou condicoes de campos, quadras, arenas e demais espacos fisicos.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    Cada usuario participa das partidas por sua conta e risco, devendo respeitar as regras da pelada, as leis aplicaveis e os demais participantes.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">6. Responsabilidade do organizador</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O organizador e o unico responsavel por definir regras, valores, horarios, local, vagas, criterios de participacao,
                    aprovacoes, bloqueios, remocoes, cobrancas, comunicados, cancelamentos e conflitos da pelada que administra.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">7. Controle de caixa</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O modulo de caixa e apenas uma ferramenta visual e logica de controle financeiro. Ele permite que o organizador registre manualmente
                    entradas, saidas, mensalidades, diarias, valores, datas, competencias, formas de pagamento e observacoes.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    O Vai Ter Pelada nao processa pagamentos, nao recebe dinheiro, nao guarda saldo, nao faz repasses e nao confirma que um pagamento ocorreu.
                    A relacao financeira e 100% direta entre organizador e jogador.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    A plataforma nao se responsabiliza por calotes, cobrancas indevidas, erros de digitacao, lancamentos incorretos, inadimplencia,
                    estornos, desacordos financeiros ou qualquer disputa entre organizador e jogador.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">8. Sorteio de times</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O sorteio e uma ferramenta de apoio. O sistema pode considerar jogadores presentes, ordem manual, ordem de chegada,
                    mensalistas, diaristas, avulsos e quantidade de jogadores por time.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    O resultado do sorteio nao gera direito adquirido, garantia de escalacao, obrigacao esportiva ou indenizacao a jogadores
                    insatisfeitos com a divisao. O organizador pode refazer, ajustar ou ignorar o sorteio conforme as regras da pelada.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">9. Confirmacoes, fila e presenca</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Confirmar presenca pelo aplicativo nao garante participacao efetiva se houver limite de vagas, fila de espera, atraso,
                    regra interna da pelada ou decisao do organizador. O sistema pode priorizar mensalistas em algumas situacoes.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">10. Avaliacoes, ranking, pontos e badges</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    A plataforma pode exibir rankings com nome, presencas, media de avaliacoes, pontos e conquistas dos jogadores.
                    E proibido usar avaliacoes para ofender, perseguir, difamar ou constranger outros usuarios.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">11. Condutas proibidas</h2>
                <ul class="mt-3 list-disc space-y-2 pl-5 leading-7 text-slate-700">
                    <li>Informar dados falsos ou usar conta de terceiros.</li>
                    <li>Ofender, ameacar, discriminar ou assediar outros usuarios.</li>
                    <li>Praticar golpes, fraudes, cobrancas abusivas ou manipulacao de registros.</li>
                    <li>Tentar acessar areas restritas sem autorizacao.</li>
                    <li>Usar a plataforma para qualquer finalidade ilegal.</li>
                </ul>
                <p class="mt-3 leading-7 text-slate-700">
                    Contas que violem estes termos podem ser limitadas, suspensas ou excluidas.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">12. Conteudos publicados</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Usuarios e organizadores sao responsaveis pelas informacoes que publicam, incluindo nome da pelada, descricao, local,
                    regras, valores, mensagens, observacoes e registros de caixa.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">13. Disponibilidade do servico</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O servico pode passar por manutencoes, instabilidades, atualizacoes ou interrupcoes. Nao garantimos disponibilidade continua,
                    livre de erros ou sem interrupcoes.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">14. Privacidade</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O tratamento de dados pessoais segue a nossa
                    <a href="{{ route('privacidade') }}" class="font-semibold text-emerald-700 hover:text-emerald-800">Politica de Privacidade</a>.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">15. Alteracoes destes termos</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Estes Termos podem ser atualizados para refletir mudancas legais, tecnicas ou operacionais. Alteracoes relevantes poderao ser comunicadas
                    por aviso no site, e-mail ou outro canal disponivel.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">16. Contato</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Para duvidas sobre estes Termos, entre em contato pelo e-mail : vaiterpelada@gmail.com.
                </p>
            </div>
        </div>
    </section>
</x-app-layout>
