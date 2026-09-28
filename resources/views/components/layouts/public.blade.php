<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Agenda de Salas | ESDEP-MT' }}</title>
    <meta name="description" content="{{ $description ?? 'Agendamento de salas da Escola Superior da Defensoria Pública do Estado de Mato Grosso.' }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo-esdep-icone.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="overflow-x-hidden bg-[#f4f8f5] text-[#003f32] antialiased dark:bg-[#081712] dark:text-[#e6f2ec]">
    <x-toast />
    <main class="flex min-h-screen flex-col">
        <!-- HEADER -->
        <header
            class="h-[73px] border-b border-[#d8e2dd] bg-[#f4f8f5] px-3 sm:px-[3.75vw] flex items-center justify-between dark:border-[#16352a] dark:bg-[#081712]">
            <a href="{{ request()->routeIs('home') ? '#inicio' : route('home') }}" class="flex items-center no-underline text-[#003f32] dark:text-[#e6f2ec]">
                <img src="{{ asset('assets/images/logo-esdep-icone.png') }}"
                    alt="ESDEP — Escola Superior da Defensoria Pública do Estado de Mato Grosso"
                    class="h-8 w-auto dark:brightness-0 dark:invert sm:hidden">
                <img src="{{ asset('assets/images/logo-esdep.png') }}"
                    alt="ESDEP — Escola Superior da Defensoria Pública do Estado de Mato Grosso"
                    class="hidden h-[45px] w-auto dark:brightness-0 dark:invert sm:block">

                <span class="mx-3 sm:mx-4 hidden h-[35px] w-px bg-[#b9c9c2] dark:bg-[#2c4a3e] sm:block"></span>

                <span
                    class="hidden font-mono text-[10px] sm:inline sm:text-[11px] uppercase tracking-[0.12em] leading-[15px] text-[#00664f] dark:text-[#4fd1a5]">
                    Agenda<br><strong>de salas</strong>
                </span>
            </a>

            <div class="flex items-center gap-1.5 sm:gap-7">
                <!-- DARK MODE TOGGLE -->
                <x-dark-mode-toggle />

                @if(!($hideHeaderMenu ?? false))
                <a href="{{ route('reservations.check') }}" aria-label="Consultar Reserva" title="Consultar Reserva"
                    class="inline-flex h-[30px] w-[30px] items-center justify-center gap-1.5 rounded-lg border border-[#d8e2dd] text-xs font-semibold text-[#003f32] transition hover:border-[#008b5c] hover:text-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:hover:border-[#4fd1a5] dark:hover:text-[#4fd1a5] sm:h-auto sm:w-auto sm:px-3 sm:py-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span class="hidden sm:inline">Consultar Reserva</span>
                </a>
                @endif

                <a href="{{ route('login') }}" aria-label="Acesso administrativo" title="Acesso administrativo"
                    class="inline-flex h-[30px] w-[30px] items-center justify-center border border-[#d8e2dd] text-[#003f32] transition hover:border-[#008b5c] hover:text-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:hover:border-[#4fd1a5] dark:hover:text-[#4fd1a5] sm:h-[34px] sm:w-[34px]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 sm:h-[16px] sm:w-[16px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3-3H9m0 0l3-3m-3 3l3 3" />
                    </svg>
                </a>

                <div
                    class="hidden md:flex items-center gap-2 text-[11px] uppercase tracking-[0.08em] text-[#63766f] dark:text-[#8aa39a]">
                    <span
                        class="h-[8px] w-[8px] rounded-full bg-[#00b879] shadow-[0_0_0_4px_rgba(0,184,121,.10)]"></span>
                     Sistema disponível
                </div>
            </div>
        </header>

        {{-- Conteudo sem padding, para secoes full-bleed como a Hero da home --}}
        {{ $hero ?? '' }}

        {{-- Main Content --}}
        <section class="flex-1 px-4 sm:px-[3.75vw] py-[60px] sm:py-[84px]">
            {{ $slot }}
        </section>

        <!-- FOOTER -->
        <footer
            class="flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-[#d6e1dc] px-4 sm:px-[3.75vw] py-7 dark:border-[#16352a]">
            <p class="m-0 text-[11px] text-[#70847d] dark:text-[#8aa39a]">© {{ date('Y') }} Defensoria Pública do Estado de Mato Grosso — Todos os direitos reservados.</p>
            <a href="#inicio" class="flex items-center justify-center h-10 w-10 rounded-full bg-[#003f32] text-white transition hover:bg-[#008b5c] dark:bg-[#008b5c] dark:hover:bg-[#4fd1a5]" title="Voltar ao topo">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                </svg>
            </a>
        </footer>

    </main>

    @livewireScripts
</body>

</html>
