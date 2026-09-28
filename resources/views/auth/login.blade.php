<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ESDEP-MT</title>
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
        <div class="mb-6 flex items-center justify-between">
            <span class="w-9"></span>
            <img src="{{ asset('assets/images/logo-esdep.png') }}" alt="ESDEP-MT" class="mx-auto h-16 w-auto dark:brightness-0 dark:invert">
            <x-dark-mode-toggle />
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/50 dark:text-red-400">
                {{ $errors->first() }}
            </div>
        @endif

        <a href="{{ route('sso.redirect') }}"
            class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#003f32] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#008b5c] dark:bg-[#008b5c] dark:hover:bg-[#4fd1a5] dark:hover:text-[#003f32]">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v4h8Z" />
            </svg>
            Entrar com a conta institucional
        </a>

        <div class="my-6 flex items-center gap-3">
            <span class="h-px flex-1 bg-slate-200 dark:bg-[#2c4a3e]"></span>
            <span class="text-xs text-slate-400 dark:text-[#8aa39a]">ou</span>
            <span class="h-px flex-1 bg-slate-200 dark:bg-[#2c4a3e]"></span>
        </div>

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5] dark:focus:ring-[#4fd1a5]"
                    placeholder="seu.email@esdep.mt.gov.br">
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Senha</label>
                <input type="password" name="password" required
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5] dark:focus:ring-[#4fd1a5]"
                    placeholder="Sua senha">
            </div>

            <button type="submit" class="w-full rounded-lg bg-[#003f32] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#008b5c] dark:bg-[#008b5c] dark:hover:bg-[#4fd1a5] dark:hover:text-[#003f32]">
                Entrar
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-slate-700 dark:text-[#8aa39a] dark:hover:text-[#4fd1a5]">Voltar para o início</a>
        </div>
    </div>
</body>
</html>
