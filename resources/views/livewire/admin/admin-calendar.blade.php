<div wire:poll.30s="loadReservations">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-700 text-white dark:bg-[#00a477]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">Agenda</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Visualize as reservas confirmadas no calendário.</p>
            </div>
        </div>
        <div class="flex items-center justify-center gap-2">
            <button wire:click="previousMonth" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:hover:bg-[#16352a]">&lsaquo; Anterior</button>
            <span class="min-w-[170px] text-center text-sm font-bold text-slate-900 dark:text-[#e6f2ec] sm:text-base">{{ $this->month_name }}</span>
            <button wire:click="nextMonth" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:hover:bg-[#16352a]">Próximo &rsaquo;</button>
        </div>
    </div>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-[#8aa39a]">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 dark:border-[#16352a] dark:bg-[#0c221a]"><span class="h-2 w-2 rounded-full bg-green-500"></span> Com reserva</span>
            <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 dark:border-[#16352a] dark:bg-[#0c221a]"><span class="h-2 w-2 rounded-full bg-slate-300 dark:bg-[#2c4a3e]"></span> Sem reserva</span>
            <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 dark:border-[#16352a] dark:bg-[#0c221a]"><span class="h-2 w-2 rounded-full ring-2 ring-green-500"></span> Hoje</span>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 dark:text-[#5a7068]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                <select wire:model.live="roomId"
                    class="rounded-full border border-slate-300 bg-white py-2 pl-9 pr-4 text-sm focus:border-green-600 focus:outline-none focus:ring-1 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                    <option value="">Todas as salas</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>

            @unless(auth('contact')->check())
                <a href="{{ route('admin.reservations.create') }}" wire:navigate
                    class="inline-flex h-10 items-center gap-1.5 rounded-full bg-green-700 px-4 text-sm font-semibold text-white shadow-sm hover:bg-green-800 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Nova Reserva
                </a>
            @endunless
        </div>
    </div>

    <div class="grid grid-cols-7 gap-px rounded-xl border border-slate-200 bg-slate-200 shadow-sm dark:border-[#16352a] dark:bg-[#16352a]">
        @foreach($this->days_of_week as $day)
            <div class="bg-slate-50 py-2 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:bg-[#0c221a] dark:text-[#8aa39a] sm:text-xs">{{ $day }}</div>
        @endforeach

        @foreach($this->calendar_days as $day)
            @if($day === null)
                <div class="min-h-20 bg-white dark:bg-[#0c221a] sm:min-h-28"></div>
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
                    class="relative min-h-20 cursor-pointer bg-white p-1.5 transition-all duration-150 hover:bg-green-50/60 dark:bg-[#0c221a] dark:hover:bg-[#112a21]/50 sm:min-h-28 sm:p-2
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
                        <div class="mt-1 space-y-2 overflow-hidden sm:mt-1.5">
                            @foreach(array_slice($dayRes, 0, 4) as $res)
                                <div
                                    title="{{ $res['start_time'] }} - {{ $res['end_time'] }}"
                                    class="truncate whitespace-nowrap rounded bg-green-50 px-1 py-0.5 text-center leading-none ring-1 ring-green-200 dark:bg-[#008b5c]/20 dark:ring-[#16352a] sm:rounded-md sm:px-1.5 lg:flex lg:items-center lg:justify-center lg:gap-1 lg:overflow-visible lg:whitespace-normal lg:py-1"
                                >
                                    <span class="text-[9px] font-bold text-green-700 dark:text-[#4fd1a5] sm:hidden">{{ substr($res['start_time'], 0, 2) }}-{{ substr($res['end_time'], 0, 2) }}</span>
                                    <span class="hidden text-[10px] font-bold text-green-700 dark:text-[#4fd1a5] sm:inline lg:hidden">{{ $res['start_time'] }} <span class="text-green-400 dark:text-[#4fd1a5]/50">&rarr;</span> {{ $res['end_time'] }}</span>

                                    <div class="hidden text-center leading-none lg:block">
                                        <p class="text-[7px] font-semibold uppercase tracking-wide text-green-500 dark:text-[#4fd1a5]/70">Início</p>
                                        <p class="text-[9px] font-bold text-green-700 dark:text-[#4fd1a5]">{{ $res['start_time'] }}</p>
                                    </div>
                                    <span class="hidden text-green-400 dark:text-[#4fd1a5]/50 lg:inline">&rarr;</span>
                                    <div class="hidden text-center leading-none lg:block">
                                        <p class="text-[7px] font-semibold uppercase tracking-wide text-green-500 dark:text-[#4fd1a5]/70">Término</p>
                                        <p class="text-[9px] font-bold text-green-700 dark:text-[#4fd1a5]">{{ $res['end_time'] }}</p>
                                    </div>
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
                @php
                    $dayCarbon = \Carbon\Carbon::parse($selectedDate);
                    $dayOfWeek = \Illuminate\Support\Str::upper($dayCarbon->translatedFormat('l'));
                    $dayMonthAbbr = \Illuminate\Support\Str::upper(substr($dayCarbon->translatedFormat('F'), 0, 3));
                @endphp
                <div class="space-y-4 p-5">
                    @foreach($dayReservations as $res)
                        <div class="relative flex overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
                            {{-- Painel de data --}}
                            <div class="relative flex w-28 shrink-0 flex-col items-center justify-center gap-1 bg-gradient-to-br from-green-700 to-green-800 px-2 py-6 text-white [clip-path:polygon(0%_0%,100%_0%,100%_75%,0%_100%)] sm:w-36">
                                <div class="flex items-center gap-1 text-[8px] font-bold uppercase tracking-wider sm:text-[9px]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    {{ $dayOfWeek }}
                                </div>
                                <span class="text-3xl font-extrabold leading-none sm:text-4xl">{{ $dayCarbon->format('d') }}</span>
                                <span class="text-[9px] font-semibold uppercase tracking-wide text-white/80 sm:text-[10px]">{{ $dayMonthAbbr }} &bull; {{ $dayCarbon->format('Y') }}</span>
                            </div>

                            <div class="min-w-0 flex-1 p-4 sm:p-5">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="truncate text-base font-bold text-slate-900 dark:text-[#e6f2ec] sm:text-lg">{{ $res['event_name'] }}</h3>
                                        @if(!empty($res['description']))
                                            <p class="mt-0.5 line-clamp-1 text-sm text-slate-400 dark:text-[#70847d]">{{ $res['description'] }}</p>
                                        @endif
                                    </div>
                                    <div class="flex shrink-0 items-center gap-2">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-[#008b5c]/20 dark:text-[#4fd1a5]">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Autorizada
                                        </span>

                                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                            <button type="button" @click="open = !open" title="Adicionar à agenda"
                                                class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-600 transition-colors hover:border-green-200 hover:bg-green-50 hover:text-green-700 hover:shadow-sm dark:border-[#16352a] dark:text-[#8aa39a] dark:hover:border-[#2c4a3e] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12v4m-2-2h4" />
                                                </svg>
                                                Agenda
                                            </button>
                                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-1 w-48 rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl dark:border-[#16352a] dark:bg-[#0c221a]" style="display: none;">
                                                <a href="{{ $res['google_calendar_url'] }}" target="_blank" rel="noopener" @click="open = false"
                                                    class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                    Google Agenda
                                                </a>
                                                <button type="button" wire:click="downloadIcs({{ $res['id'] }})" @click="open = false"
                                                    class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg>
                                                    Baixar .ics
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="my-3 border-t border-slate-100 dark:border-[#16352a]"></div>

                                <div class="flex flex-wrap items-center gap-x-5 gap-y-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-green-50 text-green-600 dark:bg-[#008b5c]/20 dark:text-[#4fd1a5]">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </span>
                                        <div>
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400 dark:text-[#5a7068]">Início</p>
                                            <p class="text-sm font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $res['start_time'] }}</p>
                                        </div>
                                    </div>
                                    <div class="hidden h-8 w-px bg-slate-200 sm:block dark:bg-[#16352a]" aria-hidden="true"></div>
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-green-50 text-green-600 dark:bg-[#008b5c]/20 dark:text-[#4fd1a5]">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </span>
                                        <div>
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400 dark:text-[#5a7068]">Término</p>
                                            <p class="text-sm font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $res['end_time'] }}</p>
                                        </div>
                                    </div>
                                    <div class="hidden h-8 w-px bg-slate-200 sm:block dark:bg-[#16352a]" aria-hidden="true"></div>
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                        </span>
                                        <div>
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400 dark:text-[#5a7068]">Sala</p>
                                            <p class="text-sm font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $res['room_name'] }}</p>
                                        </div>
                                    </div>
                                    <div class="hidden h-8 w-px bg-slate-200 sm:block dark:bg-[#16352a]" aria-hidden="true"></div>
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple-50 text-purple-600 dark:bg-purple-950/40 dark:text-purple-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        </span>
                                        <div>
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400 dark:text-[#5a7068]">Solicitante</p>
                                            <p class="text-sm font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $res['attendees_count'] }} pessoas &middot; {{ $res['requester_name'] }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($res['coffee_break'] || $res['sonorizacao'] || $res['transmissao'])
                                    <div class="mt-4 flex flex-wrap items-center gap-2">
                                        @if($res['coffee_break'])
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">
                                                ☕ Coffee Break
                                            </span>
                                        @endif
                                        @if($res['sonorizacao'])
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/20 dark:text-blue-400">
                                                🎙 Sonorização
                                            </span>
                                        @endif
                                        @if($res['transmissao'])
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-purple-50 px-3 py-1.5 text-xs font-semibold text-purple-700 dark:bg-purple-900/20 dark:text-purple-400">
                                                📡 Transmissão
                                            </span>
                                        @endif
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
