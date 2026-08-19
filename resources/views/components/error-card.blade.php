@props([
    'code' => '404',
    'title' => 'Erro',
    'message' => 'Algo inesperado aconteceu.',
    'action' => true,
])

<div class="w-full max-w-xl">
    <div class="mx-auto mb-8 flex h-24 w-24 shrink-0 items-center justify-center rounded-full bg-green-50 text-green-700 dark:bg-[#16352a] dark:text-[#4fd1a5]">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9V3.6a2.4 2.4 0 00-3.2-2.2L4 4.2v10.6a2 2 0 002 2h10a2 2 0 002-2V8.4l-4.8-3.2A2.4 2.4 0 0012 3.6V9h0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 14v6a2 2 0 002 2h10a2 2 0 012 2v-2" />
        </svg>
    </div>

    <div class="text-center">
        <span class="font-mono text-sm font-bold tracking-[0.25em] text-[#00664f] dark:text-[#4fd1a5]">
            ESDEP · ERRO {{ $code }}
        </span>

        <h1 class="mt-4 text-[clamp(4rem,14vw,8rem)] font-extrabold leading-none tracking-[-0.06em] text-[#006b48] dark:text-[#4fd1a5]">
            {{ $code }}
        </h1>

        <h2 class="mt-3 text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">
            {{ $title }}
        </h2>

        <p class="mx-auto mt-4 max-w-md text-sm leading-6 text-slate-500 dark:text-[#8aa39a]">
            {{ $message }}
        </p>

        @if($action)
            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#006b48] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-[#00553a] dark:bg-[#008b5c] dark:hover:bg-[#006e49]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2v4l2 2m4-8h8M5 12l2-2v4l2 2" />
                    </svg>
                    Voltar para o início
                </a>

                <a href="{{ route('reservations.check') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-[#d8e2dd] px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-[#16352a] dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                    Consultar reserva
                </a>
            </div>
        @endif

        @isset($slot)
            <div class="mt-6">
                {{ $slot }}
            </div>
        @endisset
    </div>
</div>
