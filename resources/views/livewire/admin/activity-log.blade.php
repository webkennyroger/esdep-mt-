<div>
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-700 text-white dark:bg-[#00a477]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">Histórico de Atividades</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Registro completo de todas as ações realizadas no sistema.</p>
            </div>
        </div>

        {{-- Controles: Periodo / Tipo / Usuario / Exportar --}}
        @php
            $typeLabels = [
                'App\Models\Reservation' => 'Reservas',
                'App\Models\User' => 'Usuários',
                'App\Models\Room' => 'Salas',
                'App\Models\Contact' => 'Contatos',
            ];
            $selectedTypeLabel = $typeLabels[$typeFilter] ?? 'Todos os tipos';
            $selectedUserLabel = $userFilter ? ($users->firstWhere('id', (int) $userFilter)?->name ?? 'Todos os usuários') : 'Todos os usuários';
            $periodLabel = ($dateFrom || $dateTo)
                ? ($dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') : '…').' - '.($dateTo ? \Carbon\Carbon::parse($dateTo)->format('d/m/Y') : '…')
                : 'Todo o período';
        @endphp
        <div class="flex flex-wrap items-center gap-3">
            {{-- Periodo --}}
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button type="button" @click="open = !open"
                    class="flex h-12 items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-4 text-left hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-green-600 dark:border-[#16352a] dark:bg-[#0c221a] dark:hover:bg-[#16352a]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>
                        <span class="block text-xs text-slate-500 dark:text-[#8aa39a]">Período</span>
                        <span class="block text-sm font-semibold text-slate-800 dark:text-[#e6f2ec]">{{ $periodLabel }}</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-400 dark:text-[#5a7068]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2 w-64 rounded-xl border border-slate-200 bg-white p-4 shadow-xl dark:border-[#16352a] dark:bg-[#0c221a]" style="display: none;">
                    <label class="mb-1.5 block text-xs font-medium text-slate-500 dark:text-[#8aa39a]">Data de início</label>
                    <input type="date" wire:model.live="dateFrom"
                        class="mb-3 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-600 focus:outline-none focus:ring-1 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                    <label class="mb-1.5 block text-xs font-medium text-slate-500 dark:text-[#8aa39a]">Data de fim</label>
                    <input type="date" wire:model.live="dateTo"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-600 focus:outline-none focus:ring-1 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                </div>
            </div>

            {{-- Tipo --}}
            <div class="relative flex h-12 items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-4 dark:border-[#16352a] dark:bg-[#0c221a]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>
                    <span class="block text-xs text-slate-500 dark:text-[#8aa39a]">Tipo</span>
                    <span class="block text-sm font-semibold text-slate-800 dark:text-[#e6f2ec]">{{ $selectedTypeLabel }}</span>
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-400 dark:text-[#5a7068]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
                <select wire:model.live="typeFilter" aria-label="Filtrar por tipo"
                    class="absolute inset-0 h-full w-full cursor-pointer appearance-none opacity-0">
                    <option value="">Todos os tipos</option>
                    @foreach($typeLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Usuario --}}
            <div class="relative flex h-12 items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-4 dark:border-[#16352a] dark:bg-[#0c221a]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>
                    <span class="block text-xs text-slate-500 dark:text-[#8aa39a]">Usuário</span>
                    <span class="block text-sm font-semibold text-slate-800 dark:text-[#e6f2ec]">{{ $selectedUserLabel }}</span>
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-400 dark:text-[#5a7068]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
                <select wire:model.live="userFilter" aria-label="Filtrar por usuário"
                    class="absolute inset-0 h-full w-full cursor-pointer appearance-none opacity-0">
                    <option value="">Todos os usuários</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="relative shrink-0" x-data="{ open: false }" @click.away="open = false">
                <button type="button" @click="open = !open"
                    class="inline-flex h-12 items-center gap-1.5 rounded-xl bg-green-700 px-4 text-sm font-semibold text-white shadow-sm hover:bg-green-800 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    Exportar
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                {{-- Visual apenas: ActivityLogController nao possui logica de exportacao implementada --}}
                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2 w-52 rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl dark:border-[#16352a] dark:bg-[#0c221a]" style="display: none;">
                    <button type="button" @click="open = false"
                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                        <x-icons.excel-icon class="h-5 w-5 shrink-0" />
                        Excel (.xlsx)
                    </button>
                    <button type="button" @click="open = false"
                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                        <x-icons.pdf-icon class="h-5 w-5 shrink-0" />
                        PDF (.pdf)
                    </button>
                    <div class="my-1 border-t border-slate-100 dark:border-[#16352a]"></div>
                    <button type="button" @click="open = false"
                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Cópia
                    </button>
                    <button type="button" @click="open = false"
                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-8 4h8v-6H6v6z" />
                        </svg>
                        Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Cards de Estatisticas --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6 ">
        <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#081712]">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-500 dark:text-[#8aa39a]">Total de atividades</p>
                    @if($stats['totalTrend'] >= 0)
                        <span class="inline-flex shrink-0 items-center gap-0.5 rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-950/40 dark:text-[#4fd1a5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            {{ $stats['totalTrend'] }}%
                        </span>
                    @else
                        <span class="inline-flex shrink-0 items-center gap-0.5 rounded-full bg-red-50 px-2 py-1 text-xs font-semibold text-red-600 dark:bg-red-950/40 dark:text-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            {{ abs($stats['totalTrend']) }}%
                        </span>
                    @endif
                </div>
                <div class="flex items-baseline justify-between gap-3">
                    <p class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">{{ number_format($stats['total']) }}</p>
                    <p class="text-xs text-slate-400 dark:text-[#5a7068]">vs. período anterior</p>
                </div>
            </div>
        </div>

        <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#081712]">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-[#4fd1a5]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-500 dark:text-[#8aa39a]">Hoje</p>
                    @if($stats['todayTrend'] >= 0)
                        <span class="inline-flex shrink-0 items-center gap-0.5 rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-950/40 dark:text-[#4fd1a5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            {{ $stats['todayTrend'] }}%
                        </span>
                    @else
                        <span class="inline-flex shrink-0 items-center gap-0.5 rounded-full bg-red-50 px-2 py-1 text-xs font-semibold text-red-600 dark:bg-red-950/40 dark:text-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            {{ abs($stats['todayTrend']) }}%
                        </span>
                    @endif
                </div>
                <div class="flex items-baseline justify-between gap-3">
                    <p class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $stats['today'] }}</p>
                    <p class="text-xs text-slate-400 dark:text-[#5a7068]">vs. mesmo dia anterior</p>
                </div>
            </div>
        </div>

        <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#081712]">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-950/40 dark:text-purple-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-500 dark:text-[#8aa39a]">Esta semana</p>
                    @if($stats['thisWeekTrend'] >= 0)
                        <span class="inline-flex shrink-0 items-center gap-0.5 rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-950/40 dark:text-[#4fd1a5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            {{ $stats['thisWeekTrend'] }}%
                        </span>
                    @else
                        <span class="inline-flex shrink-0 items-center gap-0.5 rounded-full bg-red-50 px-2 py-1 text-xs font-semibold text-red-600 dark:bg-red-950/40 dark:text-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            {{ abs($stats['thisWeekTrend']) }}%
                        </span>
                    @endif
                </div>
                <div class="flex items-baseline justify-between gap-3">
                    <p class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $stats['thisWeek'] }}</p>
                    <p class="text-xs text-slate-400 dark:text-[#5a7068]">vs. semana anterior</p>
                </div>
            </div>
        </div>

        <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#081712]">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-slate-500 dark:text-[#8aa39a]">Reservas</p>
                    @if($stats['reservationsTrend'] >= 0)
                        <span class="inline-flex shrink-0 items-center gap-0.5 rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-950/40 dark:text-[#4fd1a5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            {{ $stats['reservationsTrend'] }}%
                        </span>
                    @else
                        <span class="inline-flex shrink-0 items-center gap-0.5 rounded-full bg-red-50 px-2 py-1 text-xs font-semibold text-red-600 dark:bg-red-950/40 dark:text-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                            {{ abs($stats['reservationsTrend']) }}%
                        </span>
                    @endif
                </div>
                <div class="flex items-baseline justify-between gap-3">
                    <p class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $stats['reservations'] }}</p>
                    <p class="text-xs text-slate-400 dark:text-[#5a7068]">vs. mês anterior</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Busca + pills de filtro por acao --}}
    <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="relative w-full sm:w-64 lg:max-w-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 dark:text-[#5a7068]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Buscar por nome, descrição ou usuário..."
                class="w-full rounded-full border border-slate-300 bg-white py-2.5 pl-10 pr-4 text-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d]">
        </div>

        @php
            $actionFilters = [
                'created' => 'Criações',
                'updated' => 'Atualizações',
                'approved' => 'Aprovações',
                'rejected' => 'Rejeições',
                'deleted' => 'Exclusões',
            ];
            $actionColors = [
                'created' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-[#4fd1a5]',
                'updated' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400',
                'approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-[#4fd1a5]',
                'rejected' => 'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-400',
                'deleted' => 'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-400',
            ];
        @endphp
        <div class="flex flex-wrap items-center gap-2.5">
            <button wire:click="setFilter('')"
                class="inline-flex items-center gap-1.5 rounded-full bg-green-700 px-3.5 py-2 text-sm font-semibold text-white dark:bg-[#00a477]">
                Todos
                <span class="rounded-full bg-white/25 px-1.5 py-0.5 text-xs font-bold">{{ $stats['actionCounts'][''] }}</span>
            </button>
            @foreach($actionFilters as $key => $label)
                <span class="text-slate-300 dark:text-[#2c4a3e]">/</span>
                <button wire:click="setFilter('{{ $key }}')"
                    class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-2 text-sm font-medium {{ $actionFilter === $key ? $actionColors[$key] : 'border border-slate-300 text-slate-600 hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a]' }}">
                    {{ $label }}
                    <span class="rounded-full px-1.5 py-0.5 text-xs font-bold {{ $actionFilter === $key ? 'bg-black/10' : 'bg-slate-100 text-slate-600 dark:bg-[#16352a] dark:text-[#8aa39a]' }}">{{ $stats['actionCounts'][$key] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Tabela de Atividades --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        @if($logs->isEmpty())
            <div class="px-6 py-16 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-slate-400 dark:text-[#5a7068]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-4 text-sm font-medium text-slate-500 dark:text-[#8aa39a]">Nenhuma atividade encontrada.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-slate-50 dark:bg-[#0a1a14]">
                        <tr class="border-b border-slate-200 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:border-[#16352a] dark:text-[#8aa39a]">
                            <th class="w-10 px-6 py-3">
                                <input type="checkbox" class="rounded border-slate-300 text-green-600 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712]">
                            </th>
                            <th class="px-6 py-3">
                                <span class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>Ação</span>
                            </th>
                            <th class="px-6 py-3">
                                <span class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>Descrição</span>
                            </th>
                            <th class="px-6 py-3">
                                <span class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" /></svg>Agenda</span>
                            </th>
                            <th class="px-6 py-3">
                                <span class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>Usuário</span>
                            </th>
                            <th class="px-6 py-3">
                                <span class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>Data/Hora</span>
                            </th>
                            <th class="px-6 py-3 text-right">Detalhes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#16352a]">
                        @foreach($logs as $log)
                            @php
                                $colorMap = [
                                    'emerald' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-[#4fd1a5]',
                                    'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400',
                                    'red' => 'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-400',
                                    'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400',
                                    'purple' => 'bg-purple-100 text-purple-700 dark:bg-purple-950/50 dark:text-purple-400',
                                    'slate' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400',
                                ];
                                $color = $colorMap[$log->action_color] ?? $colorMap['slate'];
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#16352a]/30">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="rounded border-slate-300 text-green-600 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712]">
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $color }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $log->action_icon }}" />
                                        </svg>
                                        {{ $log->action_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-slate-900 dark:text-[#e6f2ec]">{{ $log->description }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 dark:bg-[#16352a] dark:text-[#8aa39a]">
                                        {{ $log->subject_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($log->user)
                                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-green-700 text-[10px] font-bold text-white dark:bg-[#008b5c]">
                                                {{ substr($log->user->name, 0, 2) }}
                                            </div>
                                            <span class="text-sm text-slate-700 dark:text-[#e6f2ec]">{{ $log->user->name }}</span>
                                        @else
                                            <span class="text-sm text-slate-400 italic dark:text-[#5a7068]">Sistema</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 text-sm text-slate-600 dark:text-[#8aa39a]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0 text-slate-400 dark:text-[#5a7068]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <p>{{ $log->created_at->format('d/m/Y') }}</p>
                                            <p class="text-xs text-slate-400 dark:text-[#5a7068]">{{ $log->created_at->format('H:i:s') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button wire:click="openDetailModal({{ $log->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-200 dark:bg-[#16352a] dark:text-[#e6f2ec] dark:hover:bg-[#1f4a3a]">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Ver
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-6 py-4 dark:border-[#16352a]">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    {{-- Modal de Detalhes --}}
    @if($showDetailModal && $selectedLog)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeDetailModal"></div>
            <div class="relative w-full max-w-2xl overflow-y-auto max-h-[90vh] rounded-2xl bg-white shadow-2xl dark:border dark:border-[#16352a] dark:bg-[#0c221a]">

                {{-- Header --}}
                @php
                    $headerColor = match($selectedLog->action_color) {
                        'emerald' => 'from-emerald-500 to-emerald-600',
                        'blue' => 'from-blue-500 to-blue-600',
                        'red' => 'from-red-500 to-red-600',
                        'amber' => 'from-amber-500 to-amber-600',
                        'purple' => 'from-purple-500 to-purple-600',
                        default => 'from-slate-500 to-slate-600',
                    };
                @endphp
                <div class="relative bg-gradient-to-r {{ $headerColor }} px-6 py-5">
                    <button wire:click="closeDetailModal" class="absolute right-4 top-4 rounded-lg bg-white/20 p-2.5 text-white backdrop-blur-sm hover:bg-white/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $selectedLog->action_icon }}" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-xl font-bold text-white">{{ $selectedLog->action_label }}</h2>
                            <div class="mt-1.5 flex items-center gap-2">
                                <span class="inline-flex rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-semibold text-white backdrop-blur-sm">
                                    {{ $selectedLog->subject_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Conteudo --}}
                <div class="px-6 py-5">
                    <div class="space-y-5">
                        {{-- Descricao --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-[#16352a] dark:bg-[#0a1a14]">
                            <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                Descrição
                            </div>
                            <p class="text-sm text-slate-900 dark:text-[#e6f2ec]">{{ $selectedLog->description }}</p>
                        </div>

                        {{-- Usuario --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-[#16352a] dark:bg-[#0a1a14]">
                            <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Usuário
                            </div>
                            @if($selectedLog->user)
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-700 text-sm font-bold text-white dark:bg-[#008b5c]">
                                        {{ substr($selectedLog->user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $selectedLog->user->name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-[#8aa39a]">{{ $selectedLog->user->email }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm text-slate-500 italic dark:text-[#8aa39a]">Ação do sistema</p>
                            @endif
                        </div>

                        {{-- Data/Hora --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-[#16352a] dark:bg-[#0a1a14]">
                            <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Data/Hora
                            </div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $selectedLog->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>

                        {{-- Historico de Alteracoes --}}
                        @if($selectedLog->formatted_changes && count($selectedLog->formatted_changes) > 0)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-[#16352a] dark:bg-[#0a1a14]">
                                <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Histórico de Alterações
                                </div>
                                <div class="space-y-2">
                                    @foreach($selectedLog->formatted_changes as $label => $change)
                                        <div class="flex items-start gap-2">
                                            <span class="inline-flex shrink-0 items-center rounded-md bg-slate-200 px-2 py-0.5 text-[11px] font-semibold text-slate-700 dark:bg-[#16352a] dark:text-[#8aa39a]">
                                                {{ $label }}
                                            </span>
                                            @if(isset($change['old']))
                                                <span class="text-sm text-slate-500 line-through dark:text-[#5a7068]">{{ $change['old'] }}</span>
                                                <span class="text-sm text-slate-400 dark:text-[#5a7068]">→</span>
                                                <span class="text-sm font-medium text-slate-700 dark:text-[#e6f2ec]">{{ $change['new'] }}</span>
                                            @else
                                                <span class="text-sm text-slate-700 dark:text-[#e6f2ec]">{{ $change['value'] ?? '' }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4 dark:border-[#16352a]">
                    <button wire:click="closeDetailModal"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>



