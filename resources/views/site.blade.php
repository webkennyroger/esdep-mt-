<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Salas | ESDEP-MT</title>
    <meta name="description"
        content="Agendamento de salas da Escola Superior da Defensoria PÃºblica do Estado de Mato Grosso.">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-[#f4f8f5] text-[#003f32] antialiased dark:bg-[#081712] dark:text-[#e6f2ec]">
    <main class="min-h-screen">
        <!-- HEADER -->
        <header
            class="h-[73px] border-b border-[#d8e2dd] bg-[#f4f8f5] px-4 sm:px-[3.75vw] flex items-center justify-between dark:border-[#16352a] dark:bg-[#081712]">
            <a href="#inicio" class="flex items-center no-underline text-[#003f32] dark:text-[#e6f2ec]">
                <img src="{{ asset('assets/images/logo-esdep.png') }}"
                    alt="ESDEP â€” Escola Superior da Defensoria PÃºblica do Estado de Mato Grosso"
                    class="h-[45px] w-auto dark:brightness-0 dark:invert">

                <span class="mx-3 sm:mx-4 h-[35px] w-px bg-[#b9c9c2] dark:bg-[#2c4a3e]"></span>

                <span
                    class="font-mono text-[10px] sm:text-[11px] uppercase tracking-[0.12em] leading-[15px] text-[#00664f] dark:text-[#4fd1a5]">
                    Agenda<br><strong>de salas</strong>
                </span>
            </a>

            <div class="flex items-center gap-3 sm:gap-7">
                <!-- DARK MODE TOGGLE -->
                <button type="button" id="theme-toggle"
                    class="flex h-[34px] w-[34px] items-center justify-center border border-[#d8e2dd] text-[#003f32] transition hover:border-[#008b5c] hover:text-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:hover:border-[#4fd1a5] dark:hover:text-[#4fd1a5]"
                    aria-label="Alternar entre modo claro e escuro" title="Alternar tema">
                    <svg class="h-[15px] w-[15px] dark:hidden" fill="none" stroke="currentColor" stroke-width="1.8"
                        viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="4.5"></circle>
                        <path stroke-linecap="round"
                            d="M12 2.5v2.5M12 19v2.5M2.5 12h2.5M19 12h2.5M5.3 5.3l1.8 1.8M16.9 16.9l1.8 1.8M18.7 5.3l-1.8 1.8M7.1 16.9l-1.8 1.8">
                        </path>
                    </svg>
                    <svg class="hidden h-[15px] w-[15px] dark:block" fill="none" stroke="currentColor"
                        stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 12.8A8.5 8.5 0 1 1 11.2 3a6.6 6.6 0 0 0 9.8 9.8Z"></path>
                    </svg>
                </button>

                <div
                    class="hidden md:flex items-center gap-2 text-[11px] uppercase tracking-[0.08em] text-[#63766f] dark:text-[#8aa39a]">
                    <span
                        class="h-[8px] w-[8px] rounded-full bg-[#00b879] shadow-[0_0_0_4px_rgba(0,184,121,.10)]"></span>
                     Sistema disponível
                </div>
            </div>
        </header>

        <!-- HERO -->
        <section id="inicio"
            class="relative min-h-[585px] overflow-hidden border-b-[3px] border-[#008b5c] px-4 sm:px-[3.75vw]">

            <div
                class="pointer-events-none absolute -left-[110px] -top-[180px] -z-10 h-[410px] w-[410px] rounded-full border-2 border-[#dceee7] dark:border-[#16352a]">
            </div>
            <div
                class="pointer-events-none absolute bottom-[-220px] left-[34%] -z-10 h-[390px] w-[390px] rounded-full border-2 border-[#dceee7] dark:border-[#16352a]">
            </div>

            <div
                class="mx-auto flex flex-col lg:flex-row h-auto lg:h-[585px] max-w-[1660px] items-center justify-between gap-10 lg:gap-16 py-12 lg:py-0">

                <!-- COPY -->
                <div class="w-full lg:w-[53%] pb-0 lg:pb-3 text-center lg:text-left">
                    <span class="mb-7 block font-mono text-[10px] font-bold uppercase tracking-[0.22em] text-[#00845c]">
                        ESDEP · RESERVAS DE ESPAÇOS
                    </span>

                    <h1
                        class="m-0 max-w-[760px] text-[clamp(36px,8vw,80px)] font-extrabold leading-[0.96] tracking-[-0.065em] text-[#003f32] dark:text-[#e6f2ec]">
                        Seu evento começa<br>
                        com o
                        <em class="font-serif font-normal tracking-[-0.06em] text-[#008b5c]"> espaço certo.</em>
                    </h1>

                    <p
                        class="mt-8 max-w-[580px] text-[14px] sm:text-[15px] leading-[1.8] text-[#60766e] dark:text-[#8aa39a]">
                        Organize encontros, apresentações e transmissões em poucos passos.<br>
                        Escolha a sala e deixe o restante conosco.
                    </p>

                    <a href="#nova-reserva"
                        class="group mt-7 inline-flex items-center gap-4 text-[12px] font-bold text-[#003f32] dark:text-[#e6f2ec]">
                        Agendar agora
                        <span
                            class="flex h-[40px] w-[40px] items-center justify-center rounded-full bg-[#003f32] text-white transition group-hover:translate-x-1 dark:bg-[#4fd1a5] dark:text-[#081712]">
                            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m4.5 4.5 15 15m0 0H8.25m11.25 0V8.25"></path>
                            </svg>
                        </span>
                    </a>
                </div>

                <!-- ROOMS -->
                <div class="rooms-showcase flex w-full items-center justify-center gap-[17px] pr-0 lg:w-[47%] lg:justify-end lg:pr-1">

                    <!-- HORIZONTE -->
                    <button type="button"
                        class="room-horizonte relative flex shrink-0 flex-col justify-between bg-[#006b48] p-[24px] text-left text-white">
                        <span class="font-mono text-[10px] font-bold tracking-[0.15em]">01</span>
                        <span class="absolute right-[24px] top-[22px] text-[15px]">↗</span>
                        <span class="grid grid-cols-4 gap-[7px] px-[10px]">
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/15 bg-white/20"></i>
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/15 bg-white/20"></i>
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/15 bg-white/20"></i>
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/15 bg-white/20"></i>
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/15 bg-white/20"></i>
                            <i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i>
                        </span>
                        <span class="flex flex-col">
                            <strong class="text-[18px] leading-none tracking-[-0.03em]">Sala Horizonte</strong>
                            <small class="mt-[8px] text-[9px] font-semibold uppercase tracking-[0.12em] text-white/70">
                                ATE 60 PESSOAS
                            </small>
                        </span>
                    </button>

                    <!-- IPE -->
                    <button type="button"
                        class="room-ipe relative flex shrink-0 flex-col justify-between bg-[#00b98b] p-[24px] text-left text-[#003f32]">
                        <span class="font-mono text-[10px] font-bold tracking-[0.15em]">02</span>
                        <span class="absolute right-[24px] top-[22px] text-[15px]">↗</span>
                        <span class="grid grid-cols-4 gap-[7px] px-[1px]">
                            <i class="h-[34px] border border-[#007b61] bg-[#009f78]"></i>
                            <i class="h-[34px] border border-[#007b61]"></i>
                            <i class="h-[34px] border border-[#007b61]"></i>
                            <i class="h-[34px] border border-[#007b61]"></i>
                            <i class="h-[34px] border border-[#007b61] bg-[#009f78]"></i>
                            <i class="h-[34px] border border-[#007b61]"></i>
                            <i class="h-[34px] border border-[#007b61]"></i>
                            <i class="h-[34px] border border-[#007b61]"></i>
                            <i class="h-[34px] border border-[#007b61] bg-[#009f78]"></i>
                            <i class="h-[34px] border border-[#007b61]"></i>
                            <i class="h-[34px] border border-[#007b61]"></i>
                            <i class="h-[34px] border border-[#007b61]"></i>
                        </span>
                        <span class="flex flex-col">
                            <strong class="text-[18px] leading-none tracking-[-0.03em]">Sala Ipe</strong>
                            <small class="mt-[8px] text-[9px] font-semibold uppercase tracking-[0.12em] text-[#00694f]">
                                ATE 30 PESSOAS
                            </small>
                        </span>
                    </button>

                </div>
            </div>
        </section>

        <!-- WORKSPACE -->
        <section id="nova-reserva" class="mx-auto max-w-[1660px] px-4 sm:px-[3.75vw] py-[60px] sm:py-[84px]">
            <div
                class="flex flex-col sm:flex-row sm:items-end justify-between border-b border-[#d6e1dc] pb-[29px] dark:border-[#16352a] gap-4">
                <div class="flex items-center gap-3">
                    <span
                        class="flex h-[28px] w-[28px] items-center justify-center bg-[#004d3c] font-mono text-[9px] font-bold text-white">
                        01
                    </span>
                    <p
                        class="m-0 font-mono text-[10px] font-bold uppercase tracking-[0.14em] text-[#006b53] dark:text-[#4fd1a5]">
                        Novo agendamento
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <span
                        class="flex h-[28px] w-[28px] items-center justify-center bg-[#00a477] font-mono text-[9px] font-bold text-white">02</span>
                    <p class="m-0 font-mono text-[10px] font-bold uppercase tracking-[0.14em]">Proximos eventos
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_380px] gap-[60px] xl:gap-[80px] pt-[45px]">

                <!-- FORM -->
                <div>
                    @livewire('public.booking-form')
                </div>

                <!-- AGENDA -->
                <aside
                    class="border-t xl:border-t-0 xl:border-l border-[#d6e1dc] pt-8 xl:pt-0 pl-0 xl:pl-9 dark:border-[#16352a]">
                    @livewire('public.timeline')
                </aside>
            </div>
        </section>

        <!-- FOOTER -->
        <footer
            class="flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-[#d6e1dc] px-4 sm:px-[3.75vw] py-7 dark:border-[#16352a]">
            <div class="text-[13px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">ESDEP</div>
            <p class="m-0 text-[11px] text-[#70847d] dark:text-[#8aa39a]">Conhecimento, encontro e transformação.</p>
            <a href="#inicio" class="text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Voltar ao topo
                ↑</a>
        </footer>

    </main>

    <script>
        (function () {
            var root = document.documentElement;
            var stored = localStorage.getItem('esdep-theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (stored === 'dark' || (!stored && prefersDark)) {
                root.classList.add('dark');
            }

            var btn = document.getElementById('theme-toggle');
            if (btn) {
                btn.addEventListener('click', function () {
                    var isDark = root.classList.toggle('dark');
                    localStorage.setItem('esdep-theme', isDark ? 'dark' : 'light');
                });
            }
        })();
    </script>
</body>

</html>