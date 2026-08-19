<x-layouts.error title="Sessão expirada — ESDEP-MT" code="419">
    <x-error-card
        code="419"
        title="Sua sessão expirou"
        message="Sua sessão expirou por inatividade. Atualize a página e tente novamente."
        action="false">
        <div class="flex justify-center">
            <a href="{{ route('home') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#006b48] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-[#00553a] dark:bg-[#008b5c] dark:hover:bg-[#006e49]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v2h2m14 0v2H6V4m0 4h12v12a2 2 0 01-2 2H8a2 2 0 01-2-2V8z" />
                </svg>
                Atualizar página
            </a>
        </div>
    </x-error-card>
</x-layouts.error>