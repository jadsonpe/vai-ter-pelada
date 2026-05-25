<footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
        <p class="leading-6">&copy; {{ date('Y') }} Vai Ter Pelada. Todos os direitos reservados.</p>
        <nav class="flex flex-wrap items-center gap-3 leading-6 sm:gap-4" aria-label="Links legais">
            <a href="{{ route('termos') }}" class="font-medium text-slate-600 hover:text-emerald-700">Termos de Uso</a>
            <span class="text-slate-300" aria-hidden="true">|</span>
            <a href="{{ route('privacidade') }}" class="font-medium text-slate-600 hover:text-emerald-700">Politica de Privacidade</a>
        </nav>
    </div>
</footer>
