<x-app-layout>
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Documento legal</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-950 sm:text-4xl">Termos de Uso</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">
                Estes termos explicam as regras para usar o Vai Ter Pelada como plataforma de conveniência e gestão de peladas amadoras.
            </p>
            <p class="mt-4 text-sm text-slate-500">Ultima atualizacao: {{ now()->format('d/m/Y') }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-8 rounded-lg border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div>
                <h2 class="text-xl font-bold text-slate-950">1. Aceitacao dos termos</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Ao criar uma conta, acessar ou usar o Vai Ter Pelada, você declara que leu, entendeu e concorda com estes Termos de Uso.
                    Se você não concordar, não utilize a plataforma.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">2. O que é a plataforma</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O Vai Ter Pelada é uma ferramenta digital para facilitar a organização de peladas, jogos, membros, confirmações,
                    fila de espera, sorteios de times, ranking, avaliações, notificações e controle visual de caixa.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    A plataforma não organiza fisicamente os jogos, não administra campos ou arenas, não garante a realização das partidas
                    e não atua como intermediadora financeira.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">3. Cadastro e conta</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Para usar certas funcionalidades, o usuário deve criar uma conta e manter seus dados corretos e atualizados,
                    especialmente e-mail e telefone quando forem necessários para contato com o organizador.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    O usuário é responsável por manter sua senha em sigilo e por todas as atividades feitas em sua conta.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">4. Perfis de usuário</h2>
                <ul class="mt-3 list-disc space-y-2 pl-5 leading-7 text-slate-700">
                    <li><strong>Jogador:</strong> pode solicitar entrada, aceitar convites, confirmar ou cancelar presença, participar de jogos, avaliações e rankings.</li>
                    <li><strong>Organizador:</strong> pode criar peladas, aprovar ou recusar solicitações, convidar membros, criar rodadas, controlar presenças, registrar caixa e realizar sorteios.</li>
                    <li><strong>Administrador:</strong> pode gerenciar usuários, esportes, banners, patrocinadores e dados gerais da plataforma.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">5. Jogos, brigas, lesoes e cancelamentos</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    A prática esportiva envolve riscos próprios, como quedas, choques, lesões, discussões, atrasos, faltas, cancelamentos,
                    indisponibilidade de campo e problemas de terceiros.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    O Vai Ter Pelada não se responsabiliza por brigas, ofensas, agressões, lesões físicas, danos materiais, objetos perdidos,
                    furtos, roubos, cancelamentos, alterações de horário, ausência de jogadores ou condições de campos, quadras, arenas e demais espaços físicos.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    Cada usuário participa das partidas por sua conta e risco, devendo respeitar as regras da pelada, as leis aplicáveis e os demais participantes.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">6. Responsabilidade do organizador</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O organizador é o único responsável por definir regras, valores, horários, local, vagas, critérios de participação,
                    aprovações, bloqueios, remoções, cobranças, comunicados, cancelamentos e conflitos da pelada que administra.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">7. Controle de caixa</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O módulo de caixa é apenas uma ferramenta visual e lógica de controle financeiro. Ele permite que o organizador registre manualmente
                    entradas, saídas, mensalidades, diárias, valores, datas, competências, formas de pagamento e observações.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    O Vai Ter Pelada não processa pagamentos, não recebe dinheiro, não guarda saldo, não faz repasses e não confirma que um pagamento ocorreu.
                    A relação financeira é 100% direta entre organizador e jogador.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    A plataforma não se responsabiliza por calotes, cobranças indevidas, erros de digitação, lançamentos incorretos, inadimplência,
                    estornos, desacordos financeiros ou qualquer disputa entre organizador e jogador.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">8. Sorteio de times</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O sorteio é uma ferramenta de apoio. O sistema pode considerar jogadores presentes, ordem manual, ordem de chegada,
                    mensalistas, diaristas, avulsos e quantidade de jogadores por time.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    O resultado do sorteio não gera direito adquirido, garantia de escalação, obrigação esportiva ou indenização a jogadores
                    insatisfeitos com a divisão. O organizador pode refazer, ajustar ou ignorar o sorteio conforme as regras da pelada.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">9. Confirmações, fila e presença</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Confirmar presença pelo aplicativo não garante participação efetiva se houver limite de vagas, fila de espera, atraso,
                    regra interna da pelada ou decisão do organizador. O sistema pode priorizar mensalistas em algumas situações.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">10. Avaliações, ranking, pontos e badges</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    A plataforma pode exibir rankings com nome, presenças, média de avaliações, pontos e conquistas dos jogadores.
                    É proibido usar avaliações para ofender, perseguir, difamar ou constranger outros usuários.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">11. Condutas proibidas</h2>
                <ul class="mt-3 list-disc space-y-2 pl-5 leading-7 text-slate-700">
                    <li>Informar dados falsos ou usar conta de terceiros.</li>
                    <li>Ofender, ameaçar, discriminar ou assediar outros usuários.</li>
                    <li>Praticar golpes, fraudes, cobranças abusivas ou manipulação de registros.</li>
                    <li>Tentar acessar áreas restritas sem autorização.</li>
                    <li>Usar a plataforma para qualquer finalidade ilegal.</li>
                </ul>
                <p class="mt-3 leading-7 text-slate-700">
                    Contas que violem estes termos podem ser limitadas, suspensas ou excluidas.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">12. Conteudos publicados</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Usuários e organizadores são responsáveis pelas informações que publicam, incluindo nome da pelada, descrição, local,
                    regras, valores, mensagens, observações e registros de caixa.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">13. Disponibilidade do serviço</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O serviço pode passar por manutenções, instabilidades, atualizações ou interrupções. Não garantimos disponibilidade contínua,
                    livre de erros ou sem interrupções.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">14. Privacidade</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O tratamento de dados pessoais segue a nossa
                    <a href="{{ route('privacidade') }}" class="font-semibold text-emerald-700 hover:text-emerald-800">Política de Privacidade</a>.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">15. Alterações destes termos</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Estes Termos podem ser atualizados para refletir mudanças legais, técnicas ou operacionais. Alterações relevantes poderão ser comunicadas
                    por aviso no site, e-mail ou outro canal disponível.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">16. Contato</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Para dúvidas sobre estes Termos, entre em contato pelo e-mail: vaiterpelada@gmail.com.
                </p>
            </div>
        </div>
    </section>
</x-app-layout>
