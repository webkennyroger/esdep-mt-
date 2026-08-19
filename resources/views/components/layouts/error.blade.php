<!DOCTYPE html>
<html lang="pt-BR"
    x-data="{ dark: localStorage.getItem('esdep-theme') === 'dark' || (!localStorage.getItem('esdep-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches) }"
    x-init="$watch('dark', v => { localStorage.setItem('esdep-theme', v ? 'dark' : 'light'); if(v) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); }); if(dark) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')"
    :class="{ 'dark': dark }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ESDEP-MT' }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logo-esdep-icone.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-[#f4f8f5] text-[#003f32] antialiased dark:bg-[#081712] dark:text-[#e6f2ec]">
    <x-toast />

    <div class="flex min-h-screen flex-col">

        {{-- Header --}}
        <header class="sticky top-0 z-40 border-b border-[#d8e2dd] bg-[#f4f8f5]/90 backdrop-blur dark:border-[#16352a] dark:bg-[#081712]/90">
            <div class="mx-auto flex h-[73px] max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3 no-underline text-[#003f32] dark:text-[#e6f2ec]">
                    <img src="{{ asset('assets/images/logo-esdep.png') }}" alt="ESDEP-MT" class="h-[42px] w-auto dark:brightness-0 dark:invert">
                    <span class="hidden h-[32px] w-px bg-[#b9c9c2] sm:inline-block dark:bg-[#2c4a3e]"></span>
                    <span class="hidden font-mono text-[10px] uppercase tracking-[0.12em] leading-[14px] text-[#00664f] sm:inline-block dark:text-[#4fd1a5]">
                        Escola Superior<br><strong>Defensoria MT</strong>
                    </span>
                </a>

                <a href="{{ route('home') }}" class="text-xs font-semibold uppercase tracking-wider text-slate-600 transition hover:text-green-700 dark:text-[#8aa39a] dark:hover:text-[#4fd1a5]">
                    Início
                </a>

                <span class="mx-2 text-slate-300 dark:text-[#3d5a4e]">/</span>

                <span class="text-xs font-semibold uppercase tracking-wider text-[#008b5c] dark:text-[#4fd1a5]">
                    {{ $code ?? '' }}
                </span>
            </div>
        </header>

        {{-- Conteúdo --}}
        <main class="flex flex-1 items-center justify-center px-4 py-10">
            {{ $slot }}
        </main>

        <footer class="border-t border-[#d8e2dd] py-8 text-center text-xs text-slate-500 dark:border-[#16352a] dark:text-[#70847d]">
            <p>© {{ date('Y') }} Escola Superior da Defensoria Pública do Estado de Mato Grosso — Todos os direitos reservados.</p>
        </footer>
    </div>

    @livewireScripts
</body>
</html>