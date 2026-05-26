<x-app-layout>
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">LGPD</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-950 sm:text-4xl">Politica de Privacidade</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">
                Esta politica explica quais dados pessoais o Vai Ter Pelada coleta, por que usamos esses dados e com quem eles podem ser compartilhados.
            </p>
            <p class="mt-4 text-sm text-slate-500">Ultima atualizacao: {{ now()->format('d/m/Y') }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-8 rounded-lg border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div>
                <h2 class="text-xl font-bold text-slate-950">1. Objetivo desta politica</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Esta Politica de Privacidade explica como tratamos dados pessoais no Vai Ter Pelada, de acordo com a Lei Geral de Protecao de Dados
                    Pessoais, Lei 13.709/2018.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">2. Dados pessoais que coletamos</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Conforme as funcionalidades atuais da plataforma, podemos coletar e armazenar os dados abaixo.
                </p>

                <h3 class="mt-5 font-semibold text-slate-900">Dados de cadastro e login</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 leading-7 text-slate-700">
                    <li>Nome, e-mail, senha criptografada e perfil de acesso.</li>
                    <li>Status da conta, data de verificacao de e-mail e tokens de autenticacao ou redefinicao de senha.</li>
                    <li>Dados de sessao, como IP, navegador/dispositivo e ultima atividade.</li>
                </ul>

                <h3 class="mt-5 font-semibold text-slate-900">Dados de perfil</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 leading-7 text-slate-700">
                    <li>Apelido, telefone/WhatsApp, cidade, bairro, logradouro, numero, complemento, estado e CEP.</li>
                    <li>Posicao em campo, nivel, plano, limite de peladas e foto/avatar quando disponivel.</li>
                    <li>Identificador Google e avatar do Google quando o usuario usa login social.</li>
                </ul>

                <h3 class="mt-5 font-semibold text-slate-900">Dados de peladas, membros e jogos</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 leading-7 text-slate-700">
                    <li>Peladas criadas, organizadas ou integradas pelo usuario.</li>
                    <li>Categoria da pelada, data de fundacao, modalidade, local, cidade, bairro, valores, vagas, regras e status.</li>
                    <li>Tipo de membro: mensalista ou diarista.</li>
                    <li>Status na pelada: ativo, pendente, bloqueado, saiu ou inativo.</li>
                    <li>Data de entrada, data desde quando e mensalista, prioridade, observacoes e apelido dentro da pelada.</li>
                    <li>Confirmacoes, cancelamentos, fila de espera, ordem de chegada, presenca no local e ordem de presenca.</li>
                    <li>Nome de jogador avulso adicionado pelo organizador.</li>
                </ul>

                <h3 class="mt-5 font-semibold text-slate-900">Dados de solicitacoes, convites e notificacoes</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 leading-7 text-slate-700">
                    <li>Pedidos para entrar em peladas ou virar mensalista.</li>
                    <li>Convites enviados por organizadores.</li>
                    <li>Mensagens, status da solicitacao, quem respondeu e quando.</li>
                    <li>Titulo, mensagem, link e data de leitura de notificacoes internas.</li>
                </ul>

                <h3 class="mt-5 font-semibold text-slate-900">Dados de sorteio, ranking e avaliacoes</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 leading-7 text-slate-700">
                    <li>Jogadores presentes usados no sorteio, times gerados, ordem nos times e criterio usado.</li>
                    <li>Avaliacoes feitas e recebidas, estrelas/notas, comentarios, media e quantidade de avaliacoes.</li>
                    <li>Pontos, badges, conquistas e presencas usadas no ranking.</li>
                </ul>

                <h3 class="mt-5 font-semibold text-slate-900">Dados financeiros registrados manualmente</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 leading-7 text-slate-700">
                    <li>Mensalidades, diarias, entradas, saidas, categoria, descricao, valor, data, competencia e forma de pagamento.</li>
                    <li>Observacoes, usuario relacionado ao lancamento e usuario que registrou o lancamento.</li>
                </ul>
                <p class="mt-3 leading-7 text-slate-700">
                    A plataforma nao processa pagamentos e nao coleta dados bancarios ou de cartao de credito.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">3. Por que usamos esses dados</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Os dados sao usados estritamente para executar o servico, incluindo:
                </p>
                <ul class="mt-3 list-disc space-y-2 pl-5 leading-7 text-slate-700">
                    <li>Criar e autenticar contas.</li>
                    <li>Identificar jogadores nas listas de membros, solicitacoes, jogos, fila e sorteios.</li>
                    <li>Permitir que o organizador entre em contato com o jogador para confirmar presenca, alinhar regras e tratar cobrancas diretas.</li>
                    <li>Permitir confirmacao de presenca, controle de fila, registro de comparecimento e adicao de jogadores avulsos.</li>
                    <li>Rodar o algoritmo de sorteio e salvar os times gerados.</li>
                    <li>Registrar manualmente mensalidades, diarias, entradas e saidas no caixa da pelada.</li>
                    <li>Exibir avaliacoes, pontos, badges, ranking e notificacoes.</li>
                    <li>Prevenir fraudes, abusos e uso indevido da plataforma.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">4. Bases legais</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Tratamos dados principalmente para execucao do contrato e prestacao do servico. Tambem podemos tratar dados com base em
                    legitimo interesse, cumprimento de obrigacao legal, exercicio regular de direitos e consentimento quando necessario.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">5. Compartilhamento de dados</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Nao vendemos dados pessoais. Compartilhamos dados apenas na medida necessaria para a plataforma funcionar.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    Quando o jogador solicita entrada, aceita convite, participa ou confirma presenca em uma pelada, alguns dados ficam visiveis
                    ao organizador daquela pelada especifica, incluindo nome, apelido, e-mail, telefone/WhatsApp quando informado, tipo de participacao,
                    status, presenca, fila, ordem de chegada, sorteios e registros de pagamento manual relacionados a essa pelada.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    Nome, quantidade de presencas, media de avaliacoes, pontos e badges podem aparecer em areas de ranking da plataforma.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">6. Controle de caixa e dados financeiros</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O caixa e um registro manual feito pelo organizador. A plataforma nao cobra, nao intermedia, nao recebe, nao repassa dinheiro e nao confirma
                    se um pagamento realmente aconteceu. Os dados financeiros servem apenas para organizacao visual da pelada.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">7. Seguranca</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Usamos medidas tecnicas e administrativas razoaveis para proteger os dados contra acessos nao autorizados, perda, alteracao,
                    destruicao ou tratamento inadequado. Senhas sao armazenadas de forma criptografada.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    Mesmo assim, nenhum sistema e totalmente imune a incidentes de seguranca.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">8. Retencao e exclusao</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Mantemos dados enquanto a conta estiver ativa e enquanto forem necessarios para prestar o servico, cumprir obrigacoes legais,
                    prevenir fraudes, resolver disputas ou preservar direitos.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    Quando a conta e excluida, dados vinculados podem ser apagados ou anonimizados, salvo quando a manutencao for necessaria por obrigacao legal,
                    seguranca, auditoria ou exercicio regular de direitos.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">9. Direitos do titular</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Nos termos da LGPD, o usuario pode solicitar confirmacao de tratamento, acesso aos dados, correcao, anonimizacao, bloqueio,
                    eliminacao quando aplicavel, portabilidade quando regulamentada, informacao sobre compartilhamentos e revogacao de consentimento quando cabivel.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">10. Cookies e sessoes</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Podemos usar cookies e mecanismos semelhantes para manter o usuario logado, proteger sessoes, lembrar preferencias e melhorar a navegacao.
                    Tambem podemos registrar IP, navegador, dispositivo e ultima atividade por seguranca e funcionamento do sistema.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">11. Criancas e adolescentes</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    A plataforma e destinada a usuarios capazes de compreender estes termos. Caso haja uso por menores de idade, o responsavel legal deve autorizar
                    e acompanhar o uso.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">12. Servicos de terceiros</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    A plataforma pode usar servicos tecnicos de terceiros, como autenticacao via Google, hospedagem, banco de dados e envio de e-mails.
                    No login social, podemos receber identificador Google e avatar para autenticar e criar a conta.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">13. Alteracoes desta politica</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Esta Politica pode ser atualizada para refletir mudancas legais, tecnicas ou funcionais. Alteracoes relevantes poderao ser comunicadas
                    por aviso no site, e-mail ou outro canal disponivel.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">14. Contato</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Para duvidas ou solicitacoes sobre privacidade e protecao de dados, entre em contato pelo email: vaiterpelada@gmail.com
                </p>
            </div>
        </div>
    </section>
</x-app-layout>
