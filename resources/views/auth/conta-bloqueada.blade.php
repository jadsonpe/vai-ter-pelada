<x-guest-layout>
    <div class="text-center">
        <h1 class="text-2xl font-bold text-slate-900">Conta bloqueada</h1>
        <p class="mt-3 text-sm leading-6 text-slate-600">
            Seu acesso foi bloqueado. Se você acredita que isso foi um engano, entre em contato com o suporte para solicitar uma revisão.
        </p>
        <a href="mailto:suporte@vaiterpelada.com" class="mt-5 inline-flex w-full items-center justify-center rounded-md bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-700">
            Falar com suporte
        </a>
        <a href="{{ route('login') }}" class="mt-3 inline-flex text-sm font-semibold text-slate-600 hover:text-emerald-700">
            Voltar para o login
        </a>
    </div>
</x-guest-layout>
