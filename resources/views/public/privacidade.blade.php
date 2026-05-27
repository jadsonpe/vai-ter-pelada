<x-app-layout>
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">LGPD</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-950 sm:text-4xl">Política de Privacidade</h1>
            <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">
                Esta política explica quais dados pessoais o Vai Ter Pelada coleta, por que usamos esses dados e com quem eles podem ser compartilhados.
            </p>
            <p class="mt-4 text-sm text-slate-500">Última atualização: {{ now()->format('d/m/Y') }}</p>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="space-y-8 rounded-lg border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div>
                <h2 class="text-xl font-bold text-slate-950">1. Objetivo desta política</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Esta Política de Privacidade explica como tratamos dados pessoais no Vai Ter Pelada, de acordo com a Lei Geral de Proteção de Dados
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
                    <li>Status da conta, data de verificação de e-mail e tokens de autenticação ou redefinição de senha.</li>
                    <li>Dados de sessão, como IP, navegador/dispositivo e última atividade.</li>
                </ul>

                <h3 class="mt-5 font-semibold text-slate-900">Dados de perfil</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 leading-7 text-slate-700">
                    <li>Apelido, telefone/WhatsApp, estado, cidade e bairro.</li>
                    <li>Data de nascimento, idade exibida, posições por esporte, esporte principal, bio esportiva, capa do perfil, links sociais, nível, plano, limite de peladas e foto/avatar quando disponível.</li>
                    <li>Identificador Google e avatar do Google quando o usuário usa login social.</li>
                </ul>

                <h3 class="mt-5 font-semibold text-slate-900">Dados de peladas, membros e jogos</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 leading-7 text-slate-700">
                    <li>Peladas criadas, organizadas ou integradas pelo usuário.</li>
                    <li>Categoria da pelada, data de fundacao, modalidade, local, cidade, bairro, valores, vagas, regras e status.</li>
                    <li>Tipo de membro: mensalista ou diarista.</li>
                    <li>Status na pelada: ativo, pendente, bloqueado, saiu ou inativo.</li>
                    <li>Data de entrada, data desde quando é mensalista, prioridade, observações e apelido dentro da pelada.</li>
                    <li>Confirmações, cancelamentos, fila de espera, ordem de chegada, presença no local e ordem de presença.</li>
                    <li>Nome de jogador avulso adicionado pelo organizador.</li>
                </ul>

                <h3 class="mt-5 font-semibold text-slate-900">Dados de solicitações, convites e notificações</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 leading-7 text-slate-700">
                    <li>Pedidos para entrar em peladas ou virar mensalista.</li>
                    <li>Convites enviados por organizadores.</li>
                    <li>Mensagens, status da solicitacao, quem respondeu e quando.</li>
                    <li>Titulo, mensagem, link e data de leitura de notificacoes internas.</li>
                </ul>

                <h3 class="mt-5 font-semibold text-slate-900">Dados de sorteio, ranking e avaliacoes</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 leading-7 text-slate-700">
                    <li>Jogadores presentes usados no sorteio, times gerados, ordem nos times e criterio usado.</li>
                    <li>Avaliações feitas e recebidas, estrelas/notas, comentários, média e quantidade de avaliações.</li>
                    <li>Estatísticas esportivas, votos de destaque, rankings sociais, conquistas e reputação do perfil público do peladeiro.</li>
                    <li>Pontos, badges, conquistas e presenças usadas no ranking.</li>
                </ul>

                <h3 class="mt-5 font-semibold text-slate-900">Dados financeiros registrados manualmente</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 leading-7 text-slate-700">
                    <li>Mensalidades, diárias, entradas, saídas, categoria, descrição, valor, data, competência e forma de pagamento.</li>
                    <li>Observações, usuário relacionado ao lançamento e usuário que registrou o lançamento.</li>
                </ul>
                <p class="mt-3 leading-7 text-slate-700">
                    A plataforma não processa pagamentos e não coleta dados bancários ou de cartão de crédito.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">3. Por que usamos esses dados</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Os dados são usados estritamente para executar o serviço, incluindo:
                </p>
                <ul class="mt-3 list-disc space-y-2 pl-5 leading-7 text-slate-700">
                    <li>Criar e autenticar contas.</li>
                    <li>Identificar jogadores nas listas de membros, solicitações, jogos, fila e sorteios.</li>
                    <li>Permitir que o organizador entre em contato com o jogador para confirmar presença, alinhar regras e tratar cobranças diretas.</li>
                    <li>Permitir confirmação de presença, controle de fila, registro de comparecimento e adição de jogadores avulsos.</li>
                    <li>Rodar o algoritmo de sorteio e salvar os times gerados.</li>
                    <li>Registrar manualmente mensalidades, diárias, entradas e saídas no caixa da pelada.</li>
                    <li>Exibir avaliações, pontos, badges, ranking e notificações.</li>
                    <li>Prevenir fraudes, abusos e uso indevido da plataforma.</li>
                </ul>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">4. Bases legais</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Tratamos dados principalmente para execução do contrato e prestação do serviço. Também podemos tratar dados com base em
                    legítimo interesse, cumprimento de obrigação legal, exercício regular de direitos e consentimento quando necessário.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">5. Compartilhamento de dados</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Não vendemos dados pessoais. Compartilhamos dados apenas na medida necessária para a plataforma funcionar.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    Quando o jogador solicita entrada, aceita convite, participa ou confirma presença em uma pelada, alguns dados ficam visíveis
                    ao organizador daquela pelada específica, incluindo nome, apelido, e-mail, telefone/WhatsApp quando informado, tipo de participação,
                    status, presença, fila, ordem de chegada, sorteios e registros de pagamento manual relacionados a essa pelada.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    Nome, quantidade de presenças, média de avaliações, pontos e badges podem aparecer em áreas de ranking da plataforma.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">6. Controle de caixa e dados financeiros</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    O caixa é um registro manual feito pelo organizador. A plataforma não cobra, não intermedia, não recebe, não repassa dinheiro e não confirma
                    se um pagamento realmente aconteceu. Os dados financeiros servem apenas para organização visual da pelada.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">7. Seguranca</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Usamos medidas técnicas e administrativas razoáveis para proteger os dados contra acessos não autorizados, perda, alteração,
                    destruição ou tratamento inadequado. Senhas são armazenadas de forma criptografada.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    Mesmo assim, nenhum sistema é totalmente imune a incidentes de segurança.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">8. Retenção e exclusão</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Mantemos dados enquanto a conta estiver ativa e enquanto forem necessários para prestar o serviço, cumprir obrigações legais,
                    prevenir fraudes, resolver disputas ou preservar direitos.
                </p>
                <p class="mt-3 leading-7 text-slate-700">
                    Quando a conta é excluída, dados vinculados podem ser apagados ou anonimizados, salvo quando a manutenção for necessária por obrigação legal,
                    segurança, auditoria ou exercício regular de direitos.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">9. Direitos do titular</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Nos termos da LGPD, o usuário pode solicitar confirmação de tratamento, acesso aos dados, correção, anonimização, bloqueio,
                    eliminação quando aplicável, portabilidade quando regulamentada, informação sobre compartilhamentos e revogação de consentimento quando cabível.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">10. Cookies e sessões</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Podemos usar cookies e mecanismos semelhantes para manter o usuário logado, proteger sessões, lembrar preferências e melhorar a navegação.
                    Também podemos registrar IP, navegador, dispositivo e última atividade por segurança e funcionamento do sistema.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">11. Crianças e adolescentes</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    A plataforma é destinada a usuários capazes de compreender estes termos. Caso haja uso por menores de idade, o responsável legal deve autorizar
                    e acompanhar o uso.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">12. Serviços de terceiros</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    A plataforma pode usar serviços técnicos de terceiros, como autenticação via Google, hospedagem, banco de dados e envio de e-mails.
                    No login social, podemos receber identificador Google e avatar para autenticar e criar a conta.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">13. Alterações desta política</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Esta Política pode ser atualizada para refletir mudanças legais, técnicas ou funcionais. Alterações relevantes poderão ser comunicadas
                    por aviso no site, e-mail ou outro canal disponível.
                </p>
            </div>

            <div>
                <h2 class="text-xl font-bold text-slate-950">14. Contato</h2>
                <p class="mt-3 leading-7 text-slate-700">
                    Para dúvidas ou solicitações sobre privacidade e proteção de dados, entre em contato pelo e-mail: vaiterpelada@gmail.com
                </p>
            </div>
        </div>
    </section>
</x-app-layout>
