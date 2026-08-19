<!DOCTYPE html>
<html lang="pt-BR"
    x-data="{ dark: localStorage.getItem('esdep-theme') === 'dark' || (!localStorage.getItem('esdep-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches) }"
    x-init="$watch('dark', v => { localStorage.setItem('esdep-theme', v ? 'dark' : 'light'); if(v) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); }); if(dark) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')"
    :class="{ 'dark': dark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Contatos — ESDEP-MT</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logo-esdep-icone.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f4f8f5] antialiased dark:bg-[#081712]">
    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <img src="{{ asset('assets/images/logo-esdep.png') }}" alt="ESDEP-MT" class="mx-auto h-16 w-auto dark:brightness-0 dark:invert">
                <h1 class="mt-4 text-2xl font-bold text-[#003f32] dark:text-[#e6f2ec]">Acesso de Contatos</h1>
                <p class="mt-1 text-sm text-[#60766e] dark:text-[#8aa39a]">Entre com seu e-mail e senha para acessar.</p>
            </div>

            <div class="rounded-2xl border border-[#d8e2dd] bg-white p-8 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
                @if($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-950/50">
                        @foreach($errors->all() as $error)
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.login.submit') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">E-mail</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            class="h-[46px] w-full rounded-lg border border-[#b8c9c2] bg-transparent px-4 text-[13px] text-[#003f32] outline-none placeholder:text-[#70847d] focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d] dark:focus:border-[#4fd1a5] dark:focus:ring-[#4fd1a5]">
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Senha</label>
                        <input type="password" id="password" name="password" required
                            class="h-[46px] w-full rounded-lg border border-[#b8c9c2] bg-transparent px-4 text-[13px] text-[#003f32] outline-none placeholder:text-[#70847d] focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d] dark:focus:border-[#4fd1a5] dark:focus:ring-[#4fd1a5]">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-[#b8c9c2] text-[#008b5c] focus:ring-[#008b5c] dark:border-[#2c4a3e]">
                            <span class="text-[12px] text-[#60766e] dark:text-[#8aa39a]">Lembrar-me</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="h-[46px] w-full rounded-lg bg-[#008b5c] text-[13px] font-bold text-white transition hover:bg-[#006e49] dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                        Entrar
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-[#70847d] dark:text-[#8aa39a]">
                <a href="{{ route('home') }}" class="underline hover:text-[#008b5c] dark:hover:text-[#4fd1a5]">Voltar ao site</a>
            </p>
        </div>
    </div>

    <script>
        (function () {
            var root = document.documentElement;
            var stored = localStorage.getItem('esdep-theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === 'dark' || (!stored && prefersDark)) {
                root.classList.add('dark');
            }
        })();
    </script>
</body>
</html>
