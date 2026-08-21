<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    {{-- Top Heading --}}
    <div class="text-center mb-8">
        <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-green-800 dark:bg-green-900/30 dark:text-[#4fd1a5]">
            Autoatendimento
        </span>
        <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-4xl">
            Consultar Status da disponibilidade 
        </h1>
        <p class="mx-auto mt-3 max-w-xl text-sm text-slate-600 dark:text-[#8aa39a]">
            Busque por protocolo, evento, sala, data, horário ou solicitante.
        </p>
    </div>

    {{-- Search Card --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        <form wire:submit="search" class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 dark:text-[#5a7068]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model="searchQuery" required placeholder="Ex: RES-2026-0001, Workshop, Sala Horizonte, 20/08, Joao..."
                    class="h-12 w-full rounded-xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-green-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-600/20 dark:border-[#16352a] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#5a7068] dark:focus:border-[#00a477]" />
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="inline-flex h-12 items-center justify-center gap-2 rounded-xl bg-green-700 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Consultar
                </button>
                @if($hasSearched)
                    <button type="button" wire:click="clear"
                        class="inline-flex h-12 items-center justify-center rounded-xl border border-slate-200 px-4 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:border-[#16352a] dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                        Limpar
                    </button>
                @endif
            </div>
        </form>
    </div>

    {{-- Results Section --}}
    @if($hasSearched)
        <div class="mt-8 space-y-4">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-[#70847d]">
                Resultados encontrados ({{ $results->count() }})
            </h2>

            @if($results->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center dark:border-[#16352a] dark:bg-[#0c221a]">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-[#16352a] dark:text-[#70847d]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-base font-semibold text-slate-900 dark:text-[#e6f2ec]">Nenhuma reserva encontrada</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-[#8aa39a]">
                        Tente buscar por protocolo, nome do evento, sala, data ou nome do solicitante.
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($results as $res)
                        @php
                            $status = $res->status->value ?? (string) $res->status;
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                'authorized' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800',
                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-800',
                            ];
                            $statusLabels = [
                                'pending' => 'Pendente de Aprovação',
                                'authorized' => 'Autorizada / Confirmada',
                                'rejected' => 'Não Autorizada',
                            ];
                        @endphp

                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md dark:border-[#16352a] dark:bg-[#0c221a]">
                            {{-- Card Header --}}
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-slate-50/60 px-6 py-4 dark:border-[#16352a] dark:bg-[#0a1a14]/60">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-xs font-bold text-slate-900 dark:text-[#4fd1a5]">
                                        {{ $res->protocol ?? 'RES-'.date('Y', strtotime($res->date)).'-'.str_pad($res->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="text-xs text-slate-400 dark:text-[#5a7068]">•</span>
                                    <span class="text-xs text-slate-500 dark:text-[#8aa39a]">
                                        Solicitado em {{ $res->created_at ? $res->created_at->format('d/m/Y H:i') : '-' }}
                                    </span>
                                </div>
                                <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold {{ $statusColors[$status] ?? 'bg-slate-100 text-slate-700' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $status === 'authorized' ? 'bg-green-500' : ($status === 'pending' ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                                    {{ $statusLabels[$status] ?? ucfirst($status) }}
                                </span>
                            </div>

                            {{-- Card Body --}}
                            <div class="p-6 space-y-5">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $res->event_name }}</h3>
                                    @if($res->description)
                                        <p class="mt-1 text-xs text-slate-600 dark:text-[#8aa39a]">{{ $res->description }}</p>
                                    @endif
                                </div>

                                {{-- Details Grid --}}
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 dark:border-[#16352a] dark:bg-[#081712]">
                                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">Espaço</span>
                                        <p class="mt-1 font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $res->room?->name ?? 'Sala não informada' }}</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 dark:border-[#16352a] dark:bg-[#081712]">
                                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">Data & Horário</span>
                                        <p class="mt-1 font-semibold text-slate-900 dark:text-[#e6f2ec]">
                                            {{ \Carbon\Carbon::parse($res->date)->format('d/m/Y') }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-[#8aa39a]">{{ $res->start_time }} às {{ $res->end_time }}</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 dark:border-[#16352a] dark:bg-[#081712]">
                                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">Solicitante</span>
                                        <p class="mt-1 font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $res->requester_name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-[#8aa39a]">{{ $res->attendees_count }} participantes</p>
                                    </div>
                                </div>

                                {{-- Services & Equipment --}}
                                <div class="flex flex-wrap items-center gap-2 pt-1">
                                    <span class="text-xs font-semibold text-slate-500 dark:text-[#70847d]">Recursos:</span>
                                    @if($res->coffee_break)
                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-[#16352a] dark:text-[#8aa39a]">☕ Coffee Break</span>
                                    @endif
                                    @if($res->sonorizacao)
                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-[#16352a] dark:text-[#8aa39a]">🎤 Sonorização</span>
                                    @endif
                                    @if($res->transmissao)
                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-[#16352a] dark:text-[#8aa39a]">📡 Transmissão</span>
                                    @endif
                                    @if($res->projetor)
                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-[#16352a] dark:text-[#8aa39a]">📽️ Projetor</span>
                                    @endif
                                    @if(!$res->coffee_break && !$res->sonorizacao && !$res->transmissao && !$res->projetor)
                                        <span class="text-xs text-slate-400 dark:text-[#5a7068]">Nenhum recurso adicional solicitado</span>
                                    @endif
                                </div>

                                {{-- Rejection Reason Notice --}}
                                @if($status === 'rejected' && $res->reject_reason)
                                    <div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-950/20">
                                        <p class="text-xs font-bold text-red-800 dark:text-red-300">Motivo da Não Autorização:</p>
                                        <p class="mt-1 text-xs text-red-700 dark:text-red-400">{{ $res->reject_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
