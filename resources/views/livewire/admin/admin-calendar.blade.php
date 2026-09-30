<div wire:poll.30s="loadReservations">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">Agenda</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Visualize as reservas confirmadas no calendário.</p>
        </div>
        <div class="flex items-center justify-center gap-2">
            <button wire:click="previousMonth" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:hover:bg-[#16352a]">&lsaquo; Anterior</button>
            <span class="min-w-[170px] text-center text-sm font-bold text-slate-900 dark:text-[#e6f2ec] sm:text-base">{{ $this->month_name }}</span>
            <button wire:click="nextMonth" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:hover:bg-[#16352a]">Próximo &rsaquo;</button>
        </div>
    </div>

    <div class="mb-4 flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-[#8aa39a]">
        <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-green-500"></span> Com reserva</span>
        <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-slate-300 dark:bg-[#2c4a3e]"></span> Sem reserva</span>
        <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full ring-2 ring-green-500"></span> Hoje</span>
    </div>

    <div class="grid grid-cols-7 gap-px rounded-xl border border-slate-200 bg-slate-200 shadow-sm dark:border-[#16352a] dark:bg-[#16352a]">
        @foreach($this->days_of_week as $day)
            <div class="bg-slate-50 py-2 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:bg-[#0c221a] dark:text-[#8aa39a] sm:text-xs">{{ $day }}</div>
        @endforeach

        @foreach($this->calendar_days as $day)
            @if($day === null)
                <div class="min-h-16 bg-white dark:bg-[#0c221a] sm:min-h-24"></div>
            @else
                @php
                    $dateStr = \Carbon\Carbon::create($currentYear, $currentMonth, $day)->format('Y-m-d');
                    $dayRes = $reservations[$dateStr] ?? [];
                    $isToday = $dateStr === now()->format('Y-m-d');
                    $hasEvents = count($dayRes) > 0;
                    $isSelected = $selectedDate === $dateStr;
                @endphp
                <div
                    wire:click="selectDate('{{ $dateStr }}')"
                    class="relative min-h-16 cursor-pointer bg-white p-1.5 transition-all duration-150 hover:bg-green-50/60 dark:bg-[#0c221a] dark:hover:bg-[#112a21]/50 sm:min-h-24 sm:p-2
                        {{ $isSelected ? 'ring-2 ring-inset ring-green-500 bg-green-50/50 dark:bg-[#008b5c]/10' : '' }}
                        {{ $isToday && !$isSelected ? 'ring-2 ring-inset ring-green-400/50' : '' }}"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold {{ $isToday ? 'flex h-6 w-6 items-center justify-center rounded-full bg-green-600 text-white sm:h-7 sm:w-7' : 'text-slate-700 dark:text-[#e6f2ec]' }}">
                            {{ $day }}
                        </span>
                        @if($hasEvents)
                            <span class="h-1.5 w-1.5 rounded-full bg-green-500 sm:h-2 sm:w-2"></span>
                        @endif
                    </div>

                    @if($hasEvents)
                        <div class="mt-1 space-y-0.5 overflow-hidden sm:mt-1.5">
                            @foreach(array_slice($dayRes, 0, 4) as $res)
                                <div class="truncate rounded bg-green-100 px-1 py-px text-[9px] font-bold leading-tight text-green-800 dark:bg-[#008b5c]/20 dark:text-[#4fd1a5] sm:px-1.5 sm:py-0.5 sm:text-[10px]">
                                    {{ $res['start_time'] }}–{{ $res['end_time'] }}
                                </div>
                            @endforeach
                            @if(count($dayRes) > 4)
                                <div class="text-center text-[9px] font-semibold text-green-600 dark:text-[#4fd1a5] sm:text-[10px]">+{{ count($dayRes) - 4 }}</div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    @if($selectedDate)
        <div class="mt-6 rounded-xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4 dark:border-[#16352a] dark:bg-[#112a21]">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-[#e6f2ec] sm:text-lg">{{ $this->selected_date_label }}</h2>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-[#8aa39a]">{{ count($dayReservations) }} evento(s) autorizado(s)</p>
                </div>
                <button wire:click="$set('selectedDate', '')" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-200 hover:text-slate-600 dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            @if(empty($dayReservations))
                <div class="px-6 py-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-slate-300 dark:text-[#2c4a3e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <p class="mt-3 text-sm font-medium text-slate-500 dark:text-[#8aa39a]">Nenhuma reserva neste dia.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-[#16352a]">
                    @foreach($dayReservations as $res)
                        <div class="px-5 py-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg bg-green-50 text-green-700 ring-1 ring-green-200 dark:bg-[#008b5c]/20 dark:text-[#4fd1a5] dark:ring-[#16352a]">
                                        <span class="text-xs font-bold leading-tight">{{ $res['start_time'] }}</span>
                                        <span class="text-[8px] leading-tight text-green-500">↓</span>
                                        <span class="text-xs font-bold leading-tight">{{ $res['end_time'] }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900 dark:text-[#e6f2ec] sm:text-base">{{ $res['event_name'] }}</h3>
                                        @if(!empty($res['description']))
                                            <p class="mt-0.5 text-xs text-slate-400 dark:text-[#70847d]">{{ $res['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="shrink-0 self-start rounded-full bg-green-100 px-2.5 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-[#008b5c]/20 dark:text-[#4fd1a5] sm:text-xs">Autorizada</span>
                            </div>

                            <div class="mt-3 grid grid-cols-1 gap-2 text-xs text-slate-600 dark:text-[#8aa39a] sm:grid-cols-3">
                                <div class="flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0 text-slate-400 dark:text-[#70847d]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    {{ $res['room_name'] }}
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0 text-slate-400 dark:text-[#70847d]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    {{ $res['attendees_count'] }} pessoas
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0 text-slate-400 dark:text-[#70847d]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    {{ $res['requester_name'] }}
                                </div>
                            </div>

                            @if($res['coffee_break'] || $res['sonorizacao'] || $res['transmissao'])
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    @if($res['coffee_break'])
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                            ☕ Coffee Break
                                        </span>
                                    @endif
                                    @if($res['sonorizacao'])
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                            🎙 Sonorização
                                        </span>
                                    @endif
                                    @if($res['transmissao'])
                                        <span class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                            📡 Transmissão
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
