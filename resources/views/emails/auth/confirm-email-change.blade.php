@component('mail::message')
# Confirme seu novo email

Ola, {{ $user->name }}.

Recebemos uma solicitacao para trocar o email principal da sua conta no **Vai Ter Pelada** para **{{ $newEmail }}**.

@component('mail::button', ['url' => $confirmUrl, 'color' => 'success'])
Confirmar novo email
@endcomponent

Este link expira em **60 minutos**. A troca so sera aplicada depois da confirmacao.

Se voce nao solicitou essa alteracao, ignore este email. Seu email atual continuara valendo.

Se o botao nao funcionar, copie e cole este link no navegador:

{{ $confirmUrl }}

Obrigado,<br>
**Vai Ter Pelada**
@endcomponent
