<div>
@if($isPageMode)
    {{-- Pagina cheia: cabecalho igual ao padrao das outras paginas admin --}}
    <div class="mb-6 flex items-center gap-3">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-700 text-white dark:bg-[#00a477]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-[#e6f2ec]">Nova Reserva</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Preencha os dados para realizar uma nova reserva de sala.</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        @include('livewire.admin.partials.reservation-form', ['mode' => 'page'])
    </div>
@else
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="close"></div>
        <div class="relative mx-auto w-full max-w-5xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl dark:border dark:border-[#16352a] dark:bg-[#0c221a]">

            {{-- Cabecalho --}}
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-[#d6e1dc] px-6 py-5 dark:border-[#16352a]">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-700 text-white dark:bg-[#00a477]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-[#e6f2ec]">Editar Reserva</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">
                            Atualize os dados da reserva selecionada.
                            <span class="block text-[11px] text-[#70847d]">Reservas / Editar Reserva</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-3 rounded-lg border border-[#d6e1dc] px-3 py-2 text-[12px] font-medium text-[#003f32] dark:border-[#2c4a3e] dark:text-[#e6f2ec]">
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#008b5c] dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Hoje {{ now()->format('d/m/Y') }}
                        </span>
                        <span class="h-4 w-px bg-[#d6e1dc] dark:bg-[#2c4a3e]"></span>
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#008b5c] dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ now()->format('H:i') }}
                        </span>
                    </div>
                    <button wire:click="close" class="rounded-lg p-2.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#e6f2ec]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            @include('livewire.admin.partials.reservation-form', ['mode' => 'modal'])
        </div>
    </div>
    @endif
@endif
</div>
