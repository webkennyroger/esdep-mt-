<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">Painel do Contato</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Bem-vindo(a), {{ auth('contact')->user()->name }}. Aqui estao suas reservas.</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        <div class="border-b border-slate-200 px-6 py-4 dark:border-[#16352a]">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-[#e6f2ec]">Minhas Reservas</h2>
        </div>

        @if($this->reservations->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-sm text-slate-500 dark:text-[#8aa39a]">Nenhuma reserva encontrada.</p>
            </div>
        @else
            <div class="divide-y divide-slate-200 dark:divide-[#16352a]">
                @foreach($this->reservations as $reservation)
                    <div class="flex items-center justify-between px-6 py-4 transition hover:bg-slate-50 dark:hover:bg-[#112a21]">
                        <div class="flex items-center gap-4">
                            <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg bg-green-50 text-slate-600 dark:bg-[#16352a] dark:text-[#4fd1a5]">
                                <span class="text-xs font-bold leading-tight">{{ \Carbon\Carbon::parse($reservation->date)->format('d') }}</span>
                                <span class="text-[10px] uppercase leading-tight">{{ \Carbon\Carbon::parse($reservation->date)->format('M') }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="truncate text-sm font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $reservation->event_name }}</h3>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-[#8aa39a]">
                                    {{ $reservation->room?->name ?? '-' }} &middot; {{ $reservation->start_time }} - {{ $reservation->end_time }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400',
                                    'authorized' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-[#4fd1a5]',
                                    'rejected' => 'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-400',
                                ];
                                $statusLabels = [
                                    'pending' => 'Pendente',
                                    'authorized' => 'Autorizada',
                                    'rejected' => 'Rejeitada',
                                ];
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusColors[$reservation->status->value] ?? '' }}">
                                {{ $statusLabels[$reservation->status->value] ?? $reservation->status->value }}
                            </span>

                            @if($this->canEdit($reservation))
                                <a href="{{ route('contact.reservation.edit', $reservation) }}"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-[#2c4a3e] dark:bg-[#0c221a] dark:text-[#e6f2ec] dark:hover:bg-[#16352a]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Editar
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
