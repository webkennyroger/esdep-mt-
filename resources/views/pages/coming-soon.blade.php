<x-layouts.public>
    <x-slot name="title">Em breve — ESDEP-MT</x-slot>

    <div class="mx-auto max-w-3xl text-center">
        <div class="mx-auto mb-10 flex h-28 w-28 items-center justify-center rounded-full bg-green-50 text-green-700 dark:bg-[#16352a] dark:text-[#4fd1a5]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h1 class="text-[clamp(2.5rem,8vw,4rem)] font-extrabold tracking-[-0.04em] text-[#006b48] dark:text-[#4fd1a5]">
            Em breve
        </h1>

        <p class="mx-auto mt-4 max-w-md text-sm leading-6 text-slate-500 dark:text-[#8aa39a]">
            Esta funcionalidade está em desenvolvimento e será disponibilizada em breve.
            Fique atento às novidades da ESDEP-MT.
        </p>

        <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <a href="{{ route('home') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#006b48] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-[#00553a] dark:bg-[#008b5c] dark:hover:bg-[#006e49]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2v4l2 2m4-8h8M5 12l2-2v4l2 2" />
                </svg>
                Voltar para o início
            </a>
        </div>
    </div>
</x-layouts.public>

