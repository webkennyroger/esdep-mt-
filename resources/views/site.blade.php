<x-layouts.public description="Agendamento de salas da Escola Superior da Defensoria Pública do Estado de Mato Grosso.">
    <x-slot:hero>
        <!-- HERO -->
        <section id="inicio" class="relative overflow-hidden border-b-[3px] border-[#008b5c] px-4 sm:px-[3.75vw]">
            <div class="pointer-events-none absolute -left-[110px] -top-[180px] -z-10 h-[410px] w-[410px] rounded-full border-2 border-[#dceee7] dark:border-[#16352a]"></div>
            <div class="pointer-events-none absolute bottom-[-220px] left-[34%] -z-10 h-[390px] w-[390px] rounded-full border-2 border-[#dceee7] dark:border-[#16352a]"></div>
            <div class="container">
                <div class="mx-auto flex flex-col lg:flex-row items-center justify-between gap-10 py-12">
                    <!-- COPY -->
                    <div class="w-full text-center lg:text-left">
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

                    <!-- ROOMS CAROUSEL -->
                    <x-rooms-carousel />
                </div>
            </div>
        </section>
    </x-slot:hero>

    <!-- WORKSPACE -->
    <div id="nova-reserva">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between border-b border-[#d6e1dc] pb-[29px] dark:border-[#16352a] gap-4">
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

        <div class="grid grid-cols-1 gap-8 pt-[45px] xl:grid-cols-[minmax(0,1fr)_380px] xl:gap-[80px]">

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
    </div>
</x-layouts.public>
