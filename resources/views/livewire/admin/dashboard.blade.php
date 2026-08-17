<div wire:poll.5s>
    <div class="mb-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-[#e6f2ec]">Visao geral</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Acompanhe as solicitacoes e reservas das salas da ESDEP-MT.</p>
            </div>
            <a href="{{ route('reservations.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                </svg>
                Nova reserva
            </a>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-[#8aa39a]">Pendentes</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $pendingCount }}</p>
                </div>
                <div class="rounded-xl bg-amber-50 p-3 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="mt-4 text-xs font-medium text-amber-600 dark:text-amber-400">Aguardando analise</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-[#8aa39a]">Autorizados</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $authorizedCount }}</p>
                </div>
                <div class="rounded-xl bg-emerald-50 p-3 text-emerald-600 dark:bg-emerald-950/40 dark:text-[#4fd1a5]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <p class="mt-4 text-xs font-medium text-emerald-600 dark:text-[#4fd1a5]">Reservas confirmadas</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-[#8aa39a]">Negados</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $rejectedCount }}</p>
                </div>
                <div class="rounded-xl bg-red-50 p-3 text-red-600 dark:bg-red-950/40 dark:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
            <p class="mt-4 text-xs font-medium text-red-600 dark:text-red-400">Solicitacoes rejeitadas</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500 dark:text-[#8aa39a]">Salas ativas</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $roomsCount }}</p>
                </div>
                <div class="rounded-xl bg-blue-50 p-3 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V5a2 2 0 012-2h10a2 2 0 012 2v16M9 7h2m-2 4h2m2-4h2m-2 4h2" />
                    </svg>
                </div>
            </div>
            <p class="mt-4 text-xs font-medium text-blue-600 dark:text-blue-400">Disponiveis para reserva</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-3">
        <section class="xl:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5 dark:border-[#16352a]">
                    <div>
                        <h2 class="font-bold text-slate-900 dark:text-[#e6f2ec]">Proximas reservas</h2>
                        <p class="mt-1 text-xs text-slate-500 dark:text-[#8aa39a]">Agenda dos proximos eventos autorizados</p>
                    </div>
                </div>

                @if(empty($upcomingReservations))
                    <div class="px-6 py-12 text-center text-sm text-slate-500 dark:text-[#8aa39a]">Nenhuma reserva autorizada.</div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-[#16352a]">
                        @foreach($upcomingReservations as $reservation)
                            <div class="flex items-center gap-4 px-6 py-5 hover:bg-slate-50/50 dark:hover:bg-[#16352a]/30">
                                <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl bg-green-50 text-green-700 dark:bg-[#16352a] dark:text-[#4fd1a5]">
                                    <span class="text-xs font-semibold">{{ \Carbon\Carbon::parse($reservation['date'])->format('M') }}</span>
                                    <span class="text-lg font-bold">{{ \Carbon\Carbon::parse($reservation['date'])->format('d') }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate text-sm font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $reservation['event_name'] }}</h3>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-[#8aa39a]">{{ $reservation['room']['name'] ?? 'Sala nao definida' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $reservation['start_time'] }} - {{ $reservation['end_time'] }}</p>
                                    <span class="mt-1 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-950/50 dark:text-[#4fd1a5]">Autorizado</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section>
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
                <div class="border-b border-slate-200 px-6 py-5 dark:border-[#16352a]">
                    <h2 class="font-bold text-slate-900 dark:text-[#e6f2ec]">Solicitacoes recentes</h2>
                    <p class="mt-1 text-xs text-slate-500 dark:text-[#8aa39a]">Aguardando moderacao</p>
                </div>

                @if(empty($pendingReservations))
                    <div class="px-6 py-12 text-center text-sm text-slate-500 dark:text-[#8aa39a]">Nenhuma solicitacao pendente.</div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-[#16352a]">
                        @foreach($pendingReservations as $reservation)
                            <div class="px-6 py-5 hover:bg-slate-50/50 dark:hover:bg-[#16352a]/30">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $reservation['event_name'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-[#8aa39a]">{{ $reservation['room']['name'] ?? 'Sala nao definida' }} - {{ $reservation['attendees_count'] }} participantes</p>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 dark:bg-amber-950/50 dark:text-amber-400">Pendente</span>
                                </div>
                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-xs text-slate-400 dark:text-[#70847d]">{{ \Carbon\Carbon::parse($reservation['created_at'])->diffForHumans() }}</span>
                                    <a href="{{ route('admin.reservations.index') }}" class="shrink-0 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-950/50 dark:text-blue-400">Analisar</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>
