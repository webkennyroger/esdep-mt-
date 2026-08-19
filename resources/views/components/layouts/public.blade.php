<!DOCTYPE html>
<html lang="pt-BR"
    x-data="{ dark: localStorage.getItem('esdep-theme') === 'dark' || (!localStorage.getItem('esdep-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches) }"
    x-init="$watch('dark', v => { localStorage.setItem('esdep-theme', v ? 'dark' : 'light'); if(v) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); }); if(dark) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')"
    :class="{ 'dark': dark }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ESDEP-MT — Agenda de Salas' }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logo-esdep-icone.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-[#f4f8f5] text-[#003f32] antialiased dark:bg-[#081712] dark:text-[#e6f2ec]">
    <x-toast />

    {{-- Public Header --}}
    <header class="sticky top-0 z-40 border-b border-[#d8e2dd] bg-[#f4f8f5]/90 backdrop-blur dark:border-[#16352a] dark:bg-[#081712]/90">
        <div class="mx-auto flex h-[73px] max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3 no-underline text-[#003f32] dark:text-[#e6f2ec]">
                <img src="{{ asset('assets/images/logo-esdep.png') }}"
                    alt="ESDEP-MT"
                    class="h-[42px] w-auto dark:brightness-0 dark:invert">
                <span class="hidden sm:inline-block h-[32px] w-px bg-[#b9c9c2] dark:bg-[#2c4a3e]"></span>
                <span class="hidden sm:inline-block font-mono text-[10px] uppercase tracking-[0.12em] leading-[14px] text-[#00664f] dark:text-[#4fd1a5]">
                    Escola Superior<br><strong>Defensoria MT</strong>
                </span>
            </a>

            <div class="flex items-center gap-3 sm:gap-6">
                <a href="{{ route('home') }}" class="text-xs font-semibold uppercase tracking-wider text-slate-600 transition hover:text-green-700 dark:text-[#8aa39a] dark:hover:text-[#4fd1a5]">
                    Início
                </a>
                <a href="{{ route('reservations.check') }}" class="text-xs font-semibold uppercase tracking-wider text-slate-600 transition hover:text-green-700 dark:text-[#8aa39a] dark:hover:text-[#4fd1a5]">
                    Consultar Reserva
                </a>

                {{-- Dark Mode Toggle --}}
                <button @click="dark = !dark"
                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-[#d8e2dd] text-[#003f32] transition hover:border-[#008b5c] hover:text-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:hover:border-[#4fd1a5] dark:hover:text-[#4fd1a5]"
                    title="Alternar tema">
                    <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="4.5"></circle>
                        <path stroke-linecap="round" d="M12 2.5v2.5M12 19v2.5M2.5 12h2.5M19 12h2.5M5.3 5.3l1.8 1.8M16.9 16.9l1.8 1.8M18.7 5.3l-1.8 1.8M7.1 16.9l-1.8 1.8"></path>
                    </svg>
                    <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.8A8.5 8.5 0 1 1 11.2 3a6.6 6.6 0 0 0 9.8 9.8Z"></path>
                    </svg>
                </button>

                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg bg-green-700 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-green-800 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                        Painel
                    </a>
                @else
                    @auth('contact')
                        <a href="{{ route('contact.dashboard') }}" class="rounded-lg bg-green-700 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-green-800 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                            Meu Painel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-[#16352a] dark:bg-[#0c221a] dark:text-[#8aa39a]">
                            Acesso Restrito
                        </a>
                        <a href="{{ route('contact.login') }}" class="rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-[#16352a] dark:bg-[#0c221a] dark:text-[#8aa39a]">
                            Contato
                        </a>
                    @endauth
                @endauth
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="py-10">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="border-t border-[#d8e2dd] py-8 text-center text-xs text-slate-500 dark:border-[#16352a] dark:text-[#70847d]">
        <p>© {{ date('Y') }} Escola Superior da Defensoria Pública do Estado de Mato Grosso — Todos os direitos reservados.</p>
    </footer>

    @livewireScripts
</body>
</html>
