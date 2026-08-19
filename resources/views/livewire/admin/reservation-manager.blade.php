<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">Gerenciar Reservas</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Aprove, rejeite ou gerencie as solicitacoes de reserva.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live="search" placeholder="Buscar..."
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d]">
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        <div class="border-b border-slate-200 px-6 py-4 dark:border-[#16352a]">
            <div class="flex flex-wrap items-center gap-3">
                @php
                    $filters = ['all' => 'Todas', 'pending' => 'Pendentes', 'authorized' => 'Autorizadas', 'rejected' => 'Rejeitadas'];
                    $filterColors = [
                        'all' => 'bg-green-100 text-green-700 dark:bg-[#16352a] dark:text-[#4fd1a5]',
                        'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400',
                        'authorized' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-[#4fd1a5]',
                        'rejected' => 'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-400',
                    ];
                @endphp
                @foreach($filters as $key => $label)
                    <a href="#" wire:click.prevent="setFilter('{{ $key }}')"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium {{ $activeFilter === $key ? $filterColors[$key] : 'text-slate-600 hover:bg-slate-100 dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#e6f2ec]' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        @if($reservations->isEmpty())
            <div class="px-6 py-16 text-center">
                <p class="text-sm font-medium text-slate-500 dark:text-[#8aa39a]">Nenhuma reserva encontrada.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:border-[#16352a] dark:text-[#8aa39a]">
                            <th class="px-6 py-3">Evento</th>
                            <th class="px-6 py-3">Sala</th>
                            <th class="px-6 py-3">Data</th>
                            <th class="px-6 py-3">Horario</th>
                            <th class="px-6 py-3">Solicitante</th>
                            <th class="px-6 py-3">Servicos</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Acoes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#16352a]">
                        @foreach($reservations as $reservation)
                            @php
                                $startTime = $reservation->start_time;
                                $endTime = $reservation->end_time;
                                $isRejected = $reservation->status === \App\Enums\ReservationStatus::REJECTED;
                                $isPending = $reservation->status === \App\Enums\ReservationStatus::PENDING;
                                $isAuthorized = $reservation->status === \App\Enums\ReservationStatus::AUTHORIZED;
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#16352a]/30 {{ $isRejected ? 'opacity-60' : '' }}">
                                <td class="px-6 py-4 text-sm font-medium">
                                    <button wire:click="openDetailModal({{ $reservation->id }})"
                                        class="text-left text-green-700 hover:text-green-900 hover:underline dark:text-[#4fd1a5] dark:hover:text-[#7de8c8] {{ $isRejected ? 'line-through text-slate-400 dark:text-[#5a7068]' : '' }}">
                                        {{ $reservation->event_name }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-[#8aa39a]">{{ $reservation->room?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-[#8aa39a]">{{ \Carbon\Carbon::parse($reservation->date)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-[#8aa39a]">
                                    @if($isRejected)
                                        <span class="line-through">{{ $startTime }} - {{ $endTime }}</span>
                                    @else
                                        {{ $startTime }} - {{ $endTime }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-[#8aa39a]">{{ $reservation->requester_name }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @if($reservation->coffee_break)
                                            <span title="Coffee Break" class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-medium text-amber-700 dark:bg-amber-950/50 dark:text-amber-400">CB</span>
                                        @endif
                                        @if($reservation->sonorizacao)
                                            <span title="Sonorizacao" class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-medium text-blue-700 dark:bg-blue-950/50 dark:text-blue-400">Son</span>
                                        @endif
                                        @if($reservation->transmissao)
                                            <span title="Transmissao" class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-medium text-purple-700 dark:bg-purple-950/50 dark:text-purple-400">Transm</span>
                                        @endif
                                        @if($reservation->projetor)
                                            <span title="Projetor" class="inline-flex items-center rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-medium text-cyan-700 dark:bg-cyan-950/50 dark:text-cyan-400">Proj</span>
                                        @endif
                                        @if(!$reservation->coffee_break && !$reservation->sonorizacao && !$reservation->transmissao && !$reservation->projetor)
                                            <span class="text-[11px] text-slate-400 italic dark:text-[#5a7068]">Nenhum servico</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $status = $reservation->status;
                                        $sc = match($status) {
                                            \App\Enums\ReservationStatus::PENDING => 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400',
                                            \App\Enums\ReservationStatus::AUTHORIZED => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-[#4fd1a5]',
                                            \App\Enums\ReservationStatus::REJECTED => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400',
                                            default => 'bg-slate-100 text-slate-600 dark:bg-[#16352a] dark:text-[#8aa39a]',
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $sc }}">
                                        {{ $status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="$dispatch('openReservationEditor', { id: {{ $reservation->id }} })"
                                            class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-200 dark:bg-[#16352a] dark:text-[#e6f2ec] dark:hover:bg-[#1f4a3a]">
                                            Editar
                                        </button>
                                        @if($isAuthorized)
                                            <button wire:click="openAnnounceModal({{ $reservation->id }}, '{{ addslashes($reservation->event_name) }}')"
                                                class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                                                Divulgar
                                            </button>
                                        @endif
                                        @if($isPending)
                                            <button wire:click="openApproveModal({{ $reservation->id }}, '{{ addslashes($reservation->event_name) }}')"
                                                class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                                                Autorizar
                                            </button>
                                            <button wire:click="openRejectModal({{ $reservation->id }}, '{{ addslashes($reservation->event_name) }}')"
                                                class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200 dark:bg-red-950/50 dark:text-red-400 dark:hover:bg-red-900/50">
                                                Rejeitar
                                            </button>
                                        @elseif($isAuthorized)
                                            <button wire:click="openRejectModal({{ $reservation->id }}, '{{ addslashes($reservation->event_name) }}')"
                                                class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200 dark:bg-red-950/50 dark:text-red-400 dark:hover:bg-red-900/50">
                                                Rejeitar
                                            </button>
                                        @elseif($isRejected)
                                            <button wire:click="openApproveModal({{ $reservation->id }}, '{{ addslashes($reservation->event_name) }}')"
                                                class="rounded-lg bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-950/50 dark:text-[#4fd1a5] dark:hover:bg-emerald-900/50">
                                                Autorizar
                                            </button>
                                            <button wire:click="reopenReservation({{ $reservation->id }})"
                                                class="rounded-lg bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-200 dark:bg-amber-950/50 dark:text-amber-400 dark:hover:bg-amber-900/50">
                                                Reabrir
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-6 py-4 dark:border-[#16352a]">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Detalhes do Evento --}}
    @if($showDetailModal && $detailReservation)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeDetailModal"></div>
            <div class="relative w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:border dark:border-[#16352a] dark:bg-[#0c221a]">

                {{-- Header --}}
                @php
                    $statusColor = match($detailReservation->status) {
                        \App\Enums\ReservationStatus::PENDING => 'from-amber-500 to-amber-600',
                        \App\Enums\ReservationStatus::AUTHORIZED => 'from-emerald-500 to-emerald-600',
                        \App\Enums\ReservationStatus::REJECTED => 'from-red-500 to-red-600',
                        default => 'from-slate-500 to-slate-600',
                    };
                    $statusBg = match($detailReservation->status) {
                        \App\Enums\ReservationStatus::PENDING => 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400',
                        \App\Enums\ReservationStatus::AUTHORIZED => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-[#4fd1a5]',
                        \App\Enums\ReservationStatus::REJECTED => 'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-400',
                        default => 'bg-slate-100 text-slate-600',
                    };
                @endphp
                <div class="relative bg-gradient-to-r {{ $statusColor }} px-6 py-5">
                    <button wire:click="closeDetailModal" class="absolute right-4 top-4 rounded-lg bg-white/20 p-1.5 text-white backdrop-blur-sm hover:bg-white/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-xl font-bold text-white">{{ $detailReservation->event_name }}</h2>
                            <div class="mt-1.5 flex items-center gap-2">
                                <span class="inline-flex rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-semibold text-white backdrop-blur-sm">
                                    {{ $detailReservation->status->label() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Conteudo --}}
                <div class="px-6 py-5">
                    <div class="grid grid-cols-2 gap-5">

                        {{-- Data e Horario --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-[#16352a] dark:bg-[#0a1a14]">
                            <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Data e Horario
                            </div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ \Carbon\Carbon::parse($detailReservation->date)->format('d/m/Y') }}</p>
                            <p class="text-sm text-slate-600 dark:text-[#8aa39a]">{{ $detailReservation->start_time }} - {{ $detailReservation->end_time }}</p>
                        </div>

                        {{-- Sala --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-[#16352a] dark:bg-[#0a1a14]">
                            <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Sala
                            </div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $detailReservation->room?->name ?? 'Nao informada' }}</p>
                            <p class="text-sm text-slate-600 dark:text-[#8aa39a]">Capacidade: {{ $detailReservation->room?->capacity ?? '-' }} pessoas</p>
                        </div>

                        {{-- Solicitante --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-[#16352a] dark:bg-[#0a1a14]">
                            <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Solicitante
                            </div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $detailReservation->requester_name }}</p>
                            <p class="text-sm text-slate-600 dark:text-[#8aa39a]">{{ $detailReservation->requester_email }}</p>
                        </div>

                        {{-- Publico --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-[#16352a] dark:bg-[#0a1a14]">
                            <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Publico Estimado
                            </div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $detailReservation->attendees_count }} pessoas</p>
                            @if($detailReservation->room && $detailReservation->attendees_count <= $detailReservation->room->capacity)
                                <p class="text-xs text-emerald-600 dark:text-[#4fd1a5]">Dentro da capacidade</p>
                            @else
                                <p class="text-xs text-red-500 dark:text-red-400">Acima da capacidade</p>
                            @endif
                        </div>
                    </div>

                    {{-- Servicos --}}
                    <div class="mt-5">
                        <h4 class="mb-2.5 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">Servicos Solicitados</h4>
                        <div class="flex flex-wrap gap-2">
                            @if($detailReservation->coffee_break)
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-100 px-3 py-2 text-sm font-medium text-amber-700 dark:bg-amber-950/50 dark:text-amber-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 1v3M10 1v3M14 1v3" /></svg>
                                    Coffee Break
                                </span>
                            @endif
                            @if($detailReservation->sonorizacao)
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-100 px-3 py-2 text-sm font-medium text-blue-700 dark:bg-blue-950/50 dark:text-blue-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" /></svg>
                                    Sonorizacao
                                </span>
                            @endif
                            @if($detailReservation->transmissao)
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-purple-100 px-3 py-2 text-sm font-medium text-purple-700 dark:bg-purple-950/50 dark:text-purple-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                    Transmissao
                                </span>
                            @endif
                            @if($detailReservation->projetor)
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-100 px-3 py-2 text-sm font-medium text-cyan-700 dark:bg-cyan-950/50 dark:text-cyan-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    Projetor
                                </span>
                            @endif
                            @if(!$detailReservation->coffee_break && !$detailReservation->sonorizacao && !$detailReservation->transmissao && !$detailReservation->projetor)
                                <span class="text-sm text-slate-400 italic dark:text-[#5a7068]">Nenhum servico solicitado</span>
                            @endif
                        </div>
                    </div>

                    {{-- Descricao --}}
                    @if($detailReservation->description)
                        <div class="mt-5">
                            <h4 class="mb-2.5 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">Descricao</h4>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#8aa39a]">
                                {{ $detailReservation->description }}
                            </div>
                        </div>
                    @endif

                    {{-- Motivo da Rejeicao --}}
                    @if($detailReservation->reject_reason)
                        <div class="mt-5">
                            <h4 class="mb-2.5 text-xs font-semibold uppercase tracking-wider text-red-400 dark:text-red-400">Motivo da Rejeicao</h4>
                            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/30 dark:text-red-300">
                                {!! $detailReservation->reject_reason !!}
                            </div>
                        </div>
                    @endif

                    {{-- Historico de Alteracoes --}}
                    @if($detailReservation->logs->isNotEmpty())
                        <div class="mt-5">
                            <h4 class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">Historico de Alteracoes</h4>
                            <div class="space-y-3">
                                @foreach($detailReservation->logs as $log)
                                    @php
                                        $actionConfig = match($log->action) {
                                            'created' => ['icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6', 'color' => 'text-blue-500 bg-blue-100 dark:bg-blue-950/50 dark:text-blue-400'],
                                            'approved' => ['icon' => 'M5 13l4 4L19 7', 'color' => 'text-emerald-500 bg-emerald-100 dark:bg-emerald-950/50 dark:text-[#4fd1a5]'],
                                            'rejected' => ['icon' => 'M6 18L18 6M6 6l12 12', 'color' => 'text-red-500 bg-red-100 dark:bg-red-950/50 dark:text-red-400'],
                                            'updated' => ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'color' => 'text-amber-500 bg-amber-100 dark:bg-amber-950/50 dark:text-amber-400'],
                                            default => ['icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'text-slate-500 bg-slate-100 dark:bg-slate-800 dark:text-slate-400'],
                                        };
                                    @endphp
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $actionConfig['color'] }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $actionConfig['icon'] }}" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-slate-900 dark:text-[#e6f2ec]">{{ $log->action_label }}</span>
                                                <span class="text-xs text-slate-400 dark:text-[#5a7068]">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            @if($log->user)
                                                <p class="text-xs text-slate-500 dark:text-[#8aa39a]">por {{ $log->user->name }}</p>
                                            @endif
                                            @if($log->reason)
                                                <p class="mt-1 text-sm text-slate-600 dark:text-[#8aa39a]">{!! $log->reason !!}</p>
                                            @endif
                                            @if($log->changes && count($log->changes) > 0)
                                                <div class="mt-1.5 flex flex-wrap gap-1.5">
                                                    @foreach($log->changes as $field => $value)
                                                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600 dark:bg-[#16352a] dark:text-[#8aa39a]">
                                                            {{ $field }}: {{ is_array($value) ? json_encode($value) : $value }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Footer --}}
                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-200 pt-5 dark:border-[#16352a]">
                        <button wire:click="closeDetailModal"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                            Fechar
                        </button>
                        <button wire:click="$dispatch('openReservationEditor', { id: {{ $detailReservation->id }} })"
                            class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-600 dark:bg-orange-500 dark:hover:bg-orange-600">
                            Editar Reserva
                        </button>
                        @if($detailReservation->status === \App\Enums\ReservationStatus::PENDING)
                            @if($detailConflictError)
                                <div class="flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    Conflito de horario detectado
                                </div>
                            @endif
                            <button wire:click="closeDetailModal; openApproveModal({{ $detailReservation->id }}, '{{ addslashes($detailReservation->event_name) }}')"
                                @if($detailConflictError) disabled @endif
                                class="rounded-lg px-4 py-2 text-sm font-semibold text-white @if($detailConflictError) cursor-not-allowed bg-slate-400 dark:bg-slate-600 @else bg-emerald-600 hover:bg-emerald-700 dark:bg-[#00a477] dark:hover:bg-[#008b5c] @endif">
                                Autorizar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Autorizar --}}
    @if($showApproveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center" x-data>
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeApproveModal"></div>
            <div class="relative mx-4 w-full max-w-md rounded-xl bg-white p-6 shadow-2xl dark:border dark:border-[#16352a] dark:bg-[#0c221a]">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-[#16352a]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Autorizar Reserva</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-[#8aa39a]">
                    Tem certeza que deseja autorizar a reserva <strong class="text-slate-700 dark:text-[#e6f2ec]">{{ $selectedName }}</strong>?
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeApproveModal"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                        Cancelar
                    </button>
                    <button wire:click="confirmApprove"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                        Sim, Autorizar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Rejeitar --}}
    @if($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center" x-data="{ editor: null }" x-init="$nextTick(() => { editor = document.getElementById('reject-editor'); editor.focus(); })">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeRejectModal"></div>
            <div class="relative mx-4 w-full max-w-lg rounded-xl bg-white p-6 shadow-2xl dark:border dark:border-[#16352a] dark:bg-[#0c221a]">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-950/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Rejeitar Reserva</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-[#8aa39a]">
                    Tem certeza que deseja rejeitar a reserva <strong class="text-slate-700 dark:text-[#e6f2ec]">{{ $selectedName }}</strong>?
                </p>

                <div class="mt-4">
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">
                        Motivo da rejeicao <span class="text-slate-400">(opcional)</span>
                    </label>
                    <textarea wire:model="rejectReason" rows="4" placeholder="Descreva o motivo da rejeicao..."
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]"></textarea>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeRejectModal"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                        Cancelar
                    </button>
                    <button wire:click="confirmReject"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800">
                        Sim, Rejeitar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Divulgar Evento --}}
    @if($showAnnounceModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center" x-data>
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeAnnounceModal"></div>
            <div class="relative mx-4 w-full max-w-lg rounded-xl bg-white p-6 shadow-2xl dark:border dark:border-[#16352a] dark:bg-[#0c221a]">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-950/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Divulgar Evento</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">
                    Enviar comunicado sobre <strong class="text-slate-700 dark:text-[#e6f2ec]">{{ $announceReservationName }}</strong>
                </p>

                <form wire:submit="sendAnnouncement" class="mt-5 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Mensagem complementar</label>
                        <textarea wire:model="announceMessage" rows="3" placeholder="Mensagem opcional para os destinatarios..."
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]"></textarea>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Destinatarios</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-[#8aa39a]">
                                <input type="radio" wire:model="announceRecipientType" value="all" class="text-blue-600 focus:ring-blue-500" />
                                Todos os contatos ativos
                            </label>
                            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-[#8aa39a]">
                                <input type="radio" wire:model="announceRecipientType" value="groups" class="text-blue-600 focus:ring-blue-500" />
                                Por grupos
                            </label>
                            @if($announceRecipientType === 'groups')
                                <div class="ml-6 space-y-1">
                                    @foreach($announceGroups as $group)
                                        <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-[#8aa39a]">
                                            <input type="checkbox" wire:model="announceSelectedGroupIds" value="{{ $group->id }}" class="rounded text-blue-600 focus:ring-blue-500" />
                                            {{ $group->name }}
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-[#8aa39a]">
                                <input type="radio" wire:model="announceRecipientType" value="contacts" class="text-blue-600 focus:ring-blue-500" />
                                Contatos especificos
                            </label>
                            @if($announceRecipientType === 'contacts')
                                <div class="ml-6 max-h-40 overflow-y-auto rounded-lg border border-slate-200 p-2 dark:border-[#16352a]">
                                    @foreach($announceContacts as $contact)
                                        <label class="flex items-center gap-2 py-1 text-sm text-slate-600 dark:text-[#8aa39a]">
                                            <input type="checkbox" wire:model="announceSelectedContactIds" value="{{ $contact->id }}" class="rounded text-blue-600 focus:ring-blue-500" />
                                            {{ $contact->name }} ({{ $contact->email }})
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-200 dark:border-[#16352a]">
                        <button type="button" wire:click="closeAnnounceModal"
                            class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800">
                            Enviar Divulgacao
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Edicao --}}
    <livewire:admin.reservation-editor />

</div>
