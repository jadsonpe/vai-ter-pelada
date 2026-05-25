@component('mail::message')
# Redefinição de senha

Olá, {{ $user->name }}.

Recebemos uma solicitação para redefinir a senha da sua conta no **Vai Ter Pelada**.

@component('mail::button', ['url' => $resetUrl, 'color' => 'success'])
Redefinir minha senha
@endcomponent

Este link expira em **{{ $expireMinutes }} minutos**.

Se você não solicitou a redefinição, nenhuma ação é necessária e sua senha atual continuará válida.

Se o botão não funcionar, copie e cole este link no navegador:

{{ $resetUrl }}

Obrigado,<br>
**Vai Ter Pelada**
@endcomponent
