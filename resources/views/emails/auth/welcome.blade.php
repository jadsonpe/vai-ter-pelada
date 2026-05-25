@component('mail::message')
# Bem-vindo, {{ $user->name }}!

Sua conta no **Vai Ter Pelada** foi criada com sucesso.

Agora você pode encontrar peladas, pedir para participar, confirmar presença nas rodadas e acompanhar seus convites em um só lugar.

@component('mail::button', ['url' => $dashboardUrl, 'color' => 'success'])
Acessar minha conta
@endcomponent

Também dá para explorar as peladas disponíveis por região, modalidade e valores.

@component('mail::button', ['url' => $peladasUrl])
Ver peladas disponíveis
@endcomponent

Se você não criou esta conta, ignore este e-mail.

Obrigado,<br>
**Vai Ter Pelada**
@endcomponent
