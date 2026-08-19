<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">Relatorios</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Analise o uso das salas e estatisticas de reservas.</p>
    </div>

    {{-- Filtros --}}
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Data Inicio</label>
                <input type="date" wire:model.live="startDate"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-600 focus:outline-none focus:ring-1 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Data Fim</label>
                <input type="date" wire:model.live="endDate"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-600 focus:outline-none focus:ring-1 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Sala</label>
                <select wire:model.live="roomId"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-600 focus:outline-none focus:ring-1 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                    <option value="">Todas as salas</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>
            <button wire:click="exportCsv"
                class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                Exportar CSV
            </button>
        </div>
    </div>

    {{-- Cards de Estatisticas --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-950/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-[#8aa39a]">Total Reservas</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-950/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600 dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-[#8aa39a]">Autorizadas</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $stats['approved'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-950/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-[#8aa39a]">Rejeitadas</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $stats['rejected'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-950/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 dark:text-[#8aa39a]">Total Participantes</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">{{ number_format($stats['totalAttendees']) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Taxa de Ocupacao --}}
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">Taxa de Ocupacao</h3>
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <div class="h-4 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-[#16352a]">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-600 transition-all duration-500"
                         style="width: {{ $stats['occupancyRate'] }}%"></div>
                </div>
            </div>
            <span class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">{{ $stats['occupancyRate'] }}%</span>
        </div>
        <p class="mt-2 text-sm text-slate-500 dark:text-[#8aa39a]">
            {{ $stats['approved'] }} de {{ $stats['total'] }} reservas foram autorizadas
        </p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Reservas por Sala --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">Reservas por Sala</h3>
            @if(empty($stats['byRoom']))
                <p class="text-sm text-slate-500 dark:text-[#8aa39a]">Nenhum dado disponivel.</p>
            @else
                <div class="space-y-3">
                    @foreach($stats['byRoom'] as $room)
                        @php
                            $maxTotal = max(array_column($stats['byRoom'], 'total'));
                            $width = $maxTotal > 0 ? ($room['total'] / $maxTotal) * 100 : 0;
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between">
                                <span class="text-sm font-medium text-slate-700 dark:text-[#e6f2ec]">{{ $room['name'] }}</span>
                                <span class="text-sm text-slate-500 dark:text-[#8aa39a]">{{ $room['total'] }}</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-[#16352a]">
                                <div class="h-full rounded-full bg-gradient-to-r from-green-500 to-[#4fd1a5] transition-all duration-500"
                                     style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Servicos Mais Solicitados --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">Servicos Mais Solicitados</h3>
            <div class="space-y-4">
                @php
                    $serviceLabels = [
                        'coffee_break' => 'Coffee Break',
                        'sonorizacao' => 'Sonorizacao',
                        'transmissao' => 'Transmissao',
                        'projetor' => 'Projetor',
                    ];
                    $serviceColors = [
                        'coffee_break' => 'from-amber-500 to-amber-600',
                        'sonorizacao' => 'from-blue-500 to-blue-600',
                        'transmissao' => 'from-purple-500 to-purple-600',
                        'projetor' => 'from-cyan-500 to-cyan-600',
                    ];
                    $maxService = max(array_values($stats['byService']));
                @endphp
                @foreach($stats['byService'] as $key => $count)
                    @php
                        $width = $maxService > 0 ? ($count / $maxService) * 100 : 0;
                    @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-700 dark:text-[#e6f2ec]">{{ $serviceLabels[$key] }}</span>
                            <span class="text-sm text-slate-500 dark:text-[#8aa39a]">{{ $count }}</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-[#16352a]">
                            <div class="h-full rounded-full bg-gradient-to-r {{ $serviceColors[$key] }} transition-all duration-500"
                                 style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
