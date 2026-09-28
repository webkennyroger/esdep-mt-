<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aguardando liberação | ESDEP-MT</title>
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function () {
            var stored = localStorage.getItem('esdep-theme');
            if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center dark:bg-[#081712]">
    <div class="w-full max-w-md rounded-xl border border-slate-200 bg-white p-8 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        <div class="mb-6 text-center">
            <img src="{{ asset('assets/images/logo-esdep.png') }}" alt="ESDEP-MT" class="mx-auto mb-3 h-16 w-auto dark:brightness-0 dark:invert">
        </div>

        <div class="mb-5 flex justify-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/40">
                <svg class="h-7 w-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z" />
                </svg>
            </div>
        </div>

        <h1 class="mb-2 text-center text-lg font-semibold text-slate-800 dark:text-[#e6f2ec]">
            Acesso aguardando liberação
        </h1>

        <p class="text-center text-sm leading-relaxed text-slate-600 dark:text-[#8aa39a]">
            @if(session('sso_pending_name'))
                <span class="font-medium text-slate-700 dark:text-[#e6f2ec]">{{ session('sso_pending_name') }}</span>,
                sua
            @else
                Sua
            @endif
            identificação institucional foi reconhecida, mas o acesso ao sistema
            ainda precisa ser liberado por um administrador.
        </p>

        <p class="mt-4 text-center text-sm text-slate-500 dark:text-[#8aa39a]">
            Você será avisado assim que a liberação for concluída.
        </p>

        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:text-[#8aa39a] dark:hover:text-[#4fd1a5]">Voltar para o início</a>
        </div>
    </div>
</body>
</html>
