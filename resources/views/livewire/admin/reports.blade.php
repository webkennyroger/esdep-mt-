<div>
    @php
        // Formatacao pt-BR e o rotulo do periodo, usados em varios blocos da pagina.
        $fmtPct = fn ($value) => number_format((float) $value, 1, ',', '.');
        $periodStart = $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '—';
        $periodEnd = $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '—';
        $avgAttendees = $stats['total'] > 0 ? $stats['totalAttendees'] / $stats['total'] : 0;

        $kpis = [
            [
                'label' => 'Total de reservas',
                'value' => number_format($stats['total'], 0, ',', '.'),
                'chip' => 'rounded-xl bg-blue-50 p-3 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400',
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                'note' => $periodStart.' até '.$periodEnd,
                'noteClass' => 'text-slate-500 dark:text-[#8aa39a]',
            ],
            [
                'label' => 'Autorizadas',
                'value' => number_format($stats['approved'], 0, ',', '.'),
                'chip' => 'rounded-xl bg-emerald-50 p-3 text-emerald-600 dark:bg-emerald-950/40 dark:text-[#4fd1a5]',
                'icon' => 'M5 13l4 4L19 7',
                'note' => $stats['pending'].' pendentes de análise',
                'noteClass' => 'text-amber-600 dark:text-amber-400',
            ],
            [
                'label' => 'Rejeitadas',
                'value' => number_format($stats['rejected'], 0, ',', '.'),
                'chip' => 'rounded-xl bg-red-50 p-3 text-red-600 dark:bg-red-950/40 dark:text-red-400',
                'icon' => 'M6 18L18 6M6 6l12 12',
                'note' => 'Solicitações negadas no período',
                'noteClass' => 'text-red-600 dark:text-red-400',
            ],
            [
                'label' => 'Participantes',
                'value' => number_format($stats['totalAttendees'], 0, ',', '.'),
                'chip' => 'rounded-xl bg-purple-50 p-3 text-purple-600 dark:bg-purple-950/40 dark:text-purple-400',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                'note' => $stats['total'] > 0
                    ? 'Média de '.number_format($avgAttendees, 0, ',', '.').' por reserva'
                    : 'Sem reservas no período',
                'noteClass' => 'text-purple-600 dark:text-purple-400',
            ],
        ];
    @endphp

    {{-- Cabecalho: titulo a esquerda, acao de pagina (exportar) a direita --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-[#e6f2ec]">Relatórios</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Analise o uso das salas e estatísticas de reservas.</p>
        </div>
        <div class="relative shrink-0" x-data="{ open: false }" @click.away="open = false">
            <button type="button" @click="open = !open"
                class="inline-flex h-10 items-center gap-1.5 rounded-full border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:hover:bg-[#16352a]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                </svg>
                Exportar
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 dark:text-[#5a7068]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute left-0 z-10 mt-2 w-52 rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl sm:left-auto sm:right-0 dark:border-[#16352a] dark:bg-[#0c221a]" style="display: none;">
                <button type="button" wire:click="exportExcel" @click="open = false"
                    class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                    <x-icons.excel-icon class="h-5 w-5 shrink-0" />
                    Excel (.xlsx)
                </button>
                <button type="button" wire:click="exportPdf" @click="open = false"
                    class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                    <x-icons.pdf-icon class="h-5 w-5 shrink-0" />
                    PDF (.pdf)
                </button>
                <div class="my-1 border-t border-slate-100 dark:border-[#16352a]"></div>
                <button type="button" @click="open = false; $wire.copyText().then(text => { navigator.clipboard.writeText(text); window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Copiado para a área de transferência.' } })) })"
                    class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Cópia
                </button>
                <button type="button" @click="open = false; $wire.printHtml().then(html => { const w = window.open('', '_blank'); w.document.write(html); w.document.close(); w.focus(); w.print(); })"
                    class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-8 4h8v-6H6v6z" />
                    </svg>
                    Imprimir
                </button>
            </div>
        </div>
    </div>

    {{-- Filtros: periodo e sala agrupados, com o resumo do recorte a direita --}}
    <div class="mb-8 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5 dark:border-[#16352a] dark:bg-[#0c221a]">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex flex-wrap items-end gap-4">
                <div role="group" aria-labelledby="filtro-periodo-label">
                    <span id="filtro-periodo-label" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Período</span>
                    <div class="flex items-center gap-2">
                        <input type="date" wire:model.live="startDate" aria-label="Data de início"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-600 focus:outline-none focus:ring-1 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                        <span class="text-sm text-slate-400 dark:text-[#5a7068]" aria-hidden="true">até</span>
                        <input type="date" wire:model.live="endDate" aria-label="Data de fim"
                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-600 focus:outline-none focus:ring-1 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                    </div>
                </div>

                <div class="mb-1 hidden h-9 w-px bg-slate-200 sm:block dark:bg-[#16352a]" aria-hidden="true"></div>

                <div>
                    <label for="filtro-sala" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Sala</label>
                    <select id="filtro-sala" wire:model.live="roomId"
                        class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-600 focus:outline-none focus:ring-1 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                        <option value="">Todas as salas</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <p class="text-sm text-slate-500 dark:text-[#8aa39a]">
                <span class="font-semibold tabular-nums text-slate-900 dark:text-[#e6f2ec]">{{ number_format($stats['total'], 0, ',', '.') }}</span>
                {{ $stats['total'] === 1 ? 'reserva' : 'reservas' }} no recorte atual
            </p>
        </div>
    </div>

    {{-- Cards de Estatisticas --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($kpis as $kpi)
            <div class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-[#8aa39a]">{{ $kpi['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold tracking-tight tabular-nums text-slate-900 dark:text-[#e6f2ec]">{{ $kpi['value'] }}</p>
                    </div>
                    <div class="{{ $kpi['chip'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $kpi['icon'] }}" />
                        </svg>
                    </div>
                </div>
                <p class="mt-auto pt-4 text-xs font-medium {{ $kpi['noteClass'] }}">{{ $kpi['note'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Rotulo de secao: liga a pizza de status aos dois recortes abaixo --}}
    <div class="mb-4 mt-8 flex items-center gap-4">
        <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400 dark:text-[#5a7068]">Distribuição no período</h2>
        <span class="h-px flex-1 bg-slate-200 dark:bg-[#16352a]" aria-hidden="true"></span>
    </div>

    {{-- Distribuicao no periodo: pizza de status + reservas por sala + servicos, lado a lado --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- Taxa de Ocupacao --}}
    <div class="occupancy-pie flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        <style>
            .occupancy-pie { --slice-approved: #059669; --slice-pending: #f59e0b; --slice-rejected: #e11d48; }
            .dark .occupancy-pie { --slice-pending: #d97706; }
        </style>

        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-[#16352a]">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-[#e6f2ec]">Status de ocupação</h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-[#8aa39a]">Como as solicitações do período foram decididas</p>
            </div>
            @if($stats['total'] > 0)
                <div class="text-right">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400 dark:text-[#5a7068]">Taxa de autorização</p>
                    <p class="mt-0.5 text-3xl font-bold tracking-tight tabular-nums text-green-700 dark:text-[#4fd1a5]">
                        {{ $fmtPct($stats['occupancyRate']) }}<span class="text-lg font-semibold">%</span>
                    </p>
                </div>
            @endif
        </div>

        @if($stats['total'] === 0)
            <p class="px-6 py-14 text-center text-sm text-slate-500 dark:text-[#8aa39a]">Nenhuma reserva no período selecionado.</p>
        @else
            @php
                $icons = [
                    'approved' => 'M5 13l4 4L19 7',
                    'pending' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    'rejected' => 'M6 18L18 6M6 6l12 12',
                ];
                // Raio da pizza + espaço reservado pra linha indicadora e o rotulo (nome + %),
                // pra nao vazar do viewBox como aconteceu na primeira versao.
                $cx = 200;
                $cy = 140;
                $r = 65;
                $labelRadius = $r + 45;
                $point = fn ($angleDeg, $radius) => [
                    $cx + $radius * cos(deg2rad($angleDeg)),
                    $cy + $radius * sin(deg2rad($angleDeg)),
                ];

                $angle = -90;
                $pieSegments = [];
                foreach ($stats['statusBreakdown'] as $key => $s) {
                    $sweep = $s['pct'] / 100 * 360;
                    $start = $angle;
                    $end = $angle + $sweep;
                    $angle = $end;

                    if ($s['pct'] <= 0) {
                        continue;
                    }

                    $mid = ($start + $end) / 2;
                    [$x1, $y1] = $point($start, $r);
                    [$x2, $y2] = $point($end, $r);
                    [$lx1, $ly1] = $point($mid, $r + 3);
                    [$lx2, $ly2] = $point($mid, $labelRadius - 8);
                    [$labelX, $labelY] = $point($mid, $labelRadius);
                    $isRight = cos(deg2rad($mid)) >= 0;

                    $pieSegments[$key] = [
                        'label' => $s['label'],
                        'value' => $s['value'],
                        'pct' => $s['pct'],
                        'icon' => $icons[$key],
                        'fullCircle' => $s['pct'] >= 99.95,
                        'path' => "M{$cx},{$cy} L{$x1},{$y1} A{$r},{$r} 0 ".(($end - $start) > 180 ? 1 : 0).' 1 '."{$x2},{$y2} Z",
                        'line' => "M{$lx1},{$ly1} L{$lx2},{$ly2}",
                        'labelX' => $labelX + ($isRight ? 4 : -4),
                        'labelY' => $labelY,
                        'anchor' => $isRight ? 'start' : 'end',
                    ];
                }
            @endphp

            <div class="flex flex-1 flex-col gap-6 px-6 py-6">
                <div class="flex items-center justify-center">
                    <svg viewBox="0 0 400 280" class="aspect-[10/7] w-full max-w-[320px]" role="img"
                        aria-label="Divisão das reservas do período por status: {{ collect($stats['statusBreakdown'])->map(fn ($s) => $s['label'].' '.$fmtPct($s['pct']).'%')->implode(', ') }}">
                        @foreach($pieSegments as $key => $seg)
                            @if($seg['fullCircle'])
                                <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="var(--slice-{{ $key }})" />
                            @else
                                <path d="{{ $seg['path'] }}" fill="var(--slice-{{ $key }})" stroke="#fff" stroke-width="2" class="dark:stroke-[#0c221a]" />
                            @endif
                        @endforeach
                        @foreach($pieSegments as $key => $seg)
                            @unless($seg['fullCircle'])
                                <path d="{{ $seg['line'] }}" fill="none" stroke="currentColor" stroke-width="1" class="text-slate-300 dark:text-[#2c4a3e]" />
                                <text x="{{ $seg['labelX'] }}" y="{{ $seg['labelY'] - 4 }}" text-anchor="{{ $seg['anchor'] }}" class="fill-slate-700 text-[11px] font-semibold dark:fill-[#e6f2ec]">{{ $seg['label'] }}</text>
                                <text x="{{ $seg['labelX'] }}" y="{{ $seg['labelY'] + 9 }}" text-anchor="{{ $seg['anchor'] }}" class="fill-slate-400 text-[11px] dark:fill-[#8aa39a]">{{ number_format($seg['pct'], 1) }}%</text>
                            @endunless
                        @endforeach
                    </svg>
                </div>

                {{-- Leitura tabular da pizza: mesma anatomia dos recortes ao lado (rotulo, valor, barra) --}}
                <div class="flex flex-col gap-4 border-t border-slate-100 pt-6 dark:border-[#16352a]">
                    @foreach($stats['statusBreakdown'] as $key => $status)
                        <div>
                            <div class="flex items-center gap-2.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" style="color: var(--slice-{{ $key }})" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$key] }}" />
                                </svg>
                                <span class="flex-1 truncate text-sm font-medium text-slate-700 dark:text-[#e6f2ec]">{{ $status['label'] }}</span>
                                <span class="w-10 text-right text-sm font-semibold tabular-nums text-slate-900 dark:text-[#e6f2ec]">{{ $status['value'] }}</span>
                                <span class="w-16 text-right text-sm tabular-nums text-slate-500 dark:text-[#8aa39a]">{{ $fmtPct($status['pct']) }}%</span>
                            </div>
                            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-[#16352a]">
                                <div class="h-full rounded-full transition-all duration-500"
                                     style="width: {{ min(100, $status['pct']) }}%; background-color: var(--slice-{{ $key }})"></div>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex items-center justify-between border-t border-slate-100 pt-3 dark:border-[#16352a]">
                        <span class="text-xs font-semibold uppercase tracking-[0.12em] text-slate-400 dark:text-[#5a7068]">Total</span>
                        <span class="text-sm font-semibold tabular-nums text-slate-900 dark:text-[#e6f2ec]">{{ number_format($stats['total'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Reservas por Sala e Servicos: mesmo denominador da pizza, % do total de reservas do periodo --}}
        {{-- Reservas por Sala --}}
        <div class="room-pie flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <style>
                .room-pie {
                    --room-slice-0: #2563eb; --room-slice-1: #0d9488; --room-slice-2: #a855f7;
                    --room-slice-3: #db2777; --room-slice-4: #6366f1; --room-slice-other: #94a3b8;
                }
            </style>
            @php
                // sortByDesc mantem as chaves originais (nao reindexa), entao o "primeiro"
                // (maior total) pode nao estar na chave 0 — usar reset() pra achar de verdade.
                $topRoom = ! empty($stats['byRoom']) ? reset($stats['byRoom']) : null;
                $topRoomGrandTotal = array_sum(array_column($stats['byRoom'], 'total'));
                $topRoomPct = $topRoom && $topRoomGrandTotal > 0 ? round(($topRoom['total'] / $topRoomGrandTotal) * 100, 1) : 0;
            @endphp
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-[#16352a]">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-[#e6f2ec]">Reservas por sala</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-[#8aa39a]">
                        {{ count($stats['byRoom']) }} {{ count($stats['byRoom']) === 1 ? 'sala com reservas' : 'salas com reservas' }} · da mais para a menos usada
                    </p>
                </div>
                @if($topRoom)
                    <div class="text-right">
                        <p class="max-w-[10rem] truncate text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400 dark:text-[#5a7068]">{{ $topRoom['name'] }}</p>
                        <p class="mt-0.5 text-3xl font-bold tracking-tight tabular-nums text-blue-700 dark:text-[#60a5fa]">
                            {{ $fmtPct($topRoomPct) }}<span class="text-lg font-semibold">%</span>
                        </p>
                    </div>
                @endif
            </div>
            @if(empty($stats['byRoom']))
                <p class="px-6 py-14 text-center text-sm text-slate-500 dark:text-[#8aa39a]">Nenhum dado disponível.</p>
            @else
                @php
                    // No maximo 5 salas na pizza; o restante entra em "Outros" pra nao passar
                    // do teto de fatias que ainda se le a olho (ver dataviz: <=6 segmentos).
                    $topRooms = array_slice($stats['byRoom'], 0, 5);
                    $otherRooms = array_slice($stats['byRoom'], 5);
                    $roomTotal = array_sum(array_column($stats['byRoom'], 'total'));

                    $roomSlices = collect($topRooms)->map(fn ($room, $i) => [
                        'label' => $room['name'],
                        'value' => $room['total'],
                        'pct' => $roomTotal > 0 ? round(($room['total'] / $roomTotal) * 100, 1) : 0,
                        'colorVar' => "--room-slice-{$i}",
                    ])->values()->all();

                    if (count($otherRooms) > 0) {
                        $otherTotal = array_sum(array_column($otherRooms, 'total'));
                        $roomSlices[] = [
                            'label' => 'Outros ('.count($otherRooms).')',
                            'value' => $otherTotal,
                            'pct' => $roomTotal > 0 ? round(($otherTotal / $roomTotal) * 100, 1) : 0,
                            'colorVar' => '--room-slice-other',
                        ];
                    }

                    // Mesma formula de arco/rotulo da pizza de status, aplicada a esse conjunto de fatias.
                    $rcx = 200;
                    $rcy = 140;
                    $rr = 65;
                    $rLabelRadius = $rr + 45;
                    $rPoint = fn ($angleDeg, $radius) => [
                        $rcx + $radius * cos(deg2rad($angleDeg)),
                        $rcy + $radius * sin(deg2rad($angleDeg)),
                    ];

                    $rAngle = -90;
                    foreach ($roomSlices as $i => $slice) {
                        $sweep = $slice['pct'] / 100 * 360;
                        $start = $rAngle;
                        $end = $rAngle + $sweep;
                        $rAngle = $end;

                        if ($slice['pct'] <= 0) {
                            $roomSlices[$i]['skip'] = true;

                            continue;
                        }

                        $mid = ($start + $end) / 2;
                        [$x1, $y1] = $rPoint($start, $rr);
                        [$x2, $y2] = $rPoint($end, $rr);
                        [$lx1, $ly1] = $rPoint($mid, $rr + 3);
                        [$lx2, $ly2] = $rPoint($mid, $rLabelRadius - 8);
                        [$labelX, $labelY] = $rPoint($mid, $rLabelRadius);
                        $isRight = cos(deg2rad($mid)) >= 0;

                        $roomSlices[$i]['fullCircle'] = $slice['pct'] >= 99.95;
                        $roomSlices[$i]['path'] = "M{$rcx},{$rcy} L{$x1},{$y1} A{$rr},{$rr} 0 ".(($end - $start) > 180 ? 1 : 0).' 1 '."{$x2},{$y2} Z";
                        $roomSlices[$i]['line'] = "M{$lx1},{$ly1} L{$lx2},{$ly2}";
                        $roomSlices[$i]['labelX'] = $labelX + ($isRight ? 4 : -4);
                        $roomSlices[$i]['labelY'] = $labelY;
                        $roomSlices[$i]['anchor'] = $isRight ? 'start' : 'end';
                    }
                @endphp

                <div class="flex flex-1 flex-col gap-6 px-6 py-6">
                    <div class="flex items-center justify-center">
                        <svg viewBox="0 0 400 280" class="aspect-[10/7] w-full max-w-[320px]" role="img"
                            aria-label="Reservas por sala: {{ collect($roomSlices)->map(fn ($s) => $s['label'].' '.$fmtPct($s['pct']).'%')->implode(', ') }}">
                            @foreach($roomSlices as $slice)
                                @unless($slice['skip'] ?? false)
                                    @if($slice['fullCircle'])
                                        <circle cx="{{ $rcx }}" cy="{{ $rcy }}" r="{{ $rr }}" fill="var({{ $slice['colorVar'] }})" />
                                    @else
                                        <path d="{{ $slice['path'] }}" fill="var({{ $slice['colorVar'] }})" stroke="#fff" stroke-width="2" class="dark:stroke-[#0c221a]" />
                                    @endif
                                @endunless
                            @endforeach
                            @foreach($roomSlices as $slice)
                                @if(!($slice['skip'] ?? false) && !$slice['fullCircle'])
                                    <path d="{{ $slice['line'] }}" fill="none" stroke="currentColor" stroke-width="1" class="text-slate-300 dark:text-[#2c4a3e]" />
                                    <text x="{{ $slice['labelX'] }}" y="{{ $slice['labelY'] - 4 }}" text-anchor="{{ $slice['anchor'] }}" class="fill-slate-700 text-[11px] font-semibold dark:fill-[#e6f2ec]">{{ $slice['label'] }}</text>
                                    <text x="{{ $slice['labelX'] }}" y="{{ $slice['labelY'] + 9 }}" text-anchor="{{ $slice['anchor'] }}" class="fill-slate-400 text-[11px] dark:fill-[#8aa39a]">{{ number_format($slice['pct'], 1) }}%</text>
                                @endif
                            @endforeach
                        </svg>
                    </div>

                    {{-- Leitura tabular: mesma anatomia dos outros recortes (rotulo, valor, barra) --}}
                    <div class="flex flex-col gap-4 border-t border-slate-100 pt-6 dark:border-[#16352a]">
                        @foreach($roomSlices as $slice)
                            <div>
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-4 w-4 shrink-0 items-center justify-center">
                                        <span class="h-2 w-2 rounded-full" style="background-color: var({{ $slice['colorVar'] }})"></span>
                                    </span>
                                    <span class="flex-1 truncate text-sm font-medium text-slate-700 dark:text-[#e6f2ec]">{{ $slice['label'] }}</span>
                                    <span class="w-10 text-right text-sm font-semibold tabular-nums text-slate-900 dark:text-[#e6f2ec]">{{ $slice['value'] }}</span>
                                    <span class="w-16 text-right text-sm tabular-nums text-slate-500 dark:text-[#8aa39a]">{{ $fmtPct($slice['pct']) }}%</span>
                                </div>
                                <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-[#16352a]">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         style="width: {{ min(100, $slice['pct']) }}%; background-color: var({{ $slice['colorVar'] }})"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Servicos Mais Solicitados --}}
        <div class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            @php
                $serviceLabels = [
                    'coffee_break' => 'Coffee Break',
                    'sonorizacao' => 'Sonorização',
                    'transmissao' => 'Transmissão',
                    'projetor' => 'Projetor',
                ];
                // Cor em hexadecimal direto (sem custom property de CSS), pra tirar qualquer
                // duvida de herenca/escopo entre a pizza e a legenda.
                $serviceHexColors = [
                    'coffee_break' => '#c2410c',
                    'sonorizacao' => '#2563eb',
                    'transmissao' => '#db2777',
                    'projetor' => '#0891b2',
                ];
                $topServiceGrandTotal = array_sum($stats['byService']);
                $topServiceKey = $topServiceGrandTotal > 0 ? array_search(max($stats['byService']), $stats['byService']) : null;
                $topServicePct = $topServiceKey && $topServiceGrandTotal > 0 ? round(($stats['byService'][$topServiceKey] / $topServiceGrandTotal) * 100, 1) : 0;
            @endphp
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-[#16352a]">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-[#e6f2ec]">Serviços mais solicitados</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-[#8aa39a]">
                        Uma reserva pode pedir mais de um serviço.
                    </p>
                </div>
                @if($topServiceKey)
                    <div class="text-right">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-400 dark:text-[#5a7068]">{{ $serviceLabels[$topServiceKey] }}</p>
                        <p class="mt-0.5 text-3xl font-bold tracking-tight tabular-nums" style="color: {{ $serviceHexColors[$topServiceKey] }}">
                            {{ $fmtPct($topServicePct) }}<span class="text-lg font-semibold">%</span>
                        </p>
                    </div>
                @endif
            </div>
            @php
                $serviceTotal = $topServiceGrandTotal;

                $serviceSlices = [];
                foreach ($stats['byService'] as $key => $count) {
                    $serviceSlices[$key] = [
                        'label' => $serviceLabels[$key],
                        'value' => $count,
                        'pct' => $serviceTotal > 0 ? round(($count / $serviceTotal) * 100, 1) : 0,
                        'hex' => $serviceHexColors[$key],
                    ];
                }

                // Mesma formula de arco/rotulo das outras pizzas da pagina.
                $scx = 200;
                $scy = 140;
                $sr = 65;
                $sLabelRadius = $sr + 45;
                $sPoint = fn ($angleDeg, $radius) => [
                    $scx + $radius * cos(deg2rad($angleDeg)),
                    $scy + $radius * sin(deg2rad($angleDeg)),
                ];

                $sAngle = -90;
                foreach ($serviceSlices as $key => $slice) {
                    $sweep = $slice['pct'] / 100 * 360;
                    $start = $sAngle;
                    $end = $sAngle + $sweep;
                    $sAngle = $end;

                    if ($slice['pct'] <= 0) {
                        $serviceSlices[$key]['skip'] = true;

                        continue;
                    }

                    $mid = ($start + $end) / 2;
                    [$x1, $y1] = $sPoint($start, $sr);
                    [$x2, $y2] = $sPoint($end, $sr);
                    [$lx1, $ly1] = $sPoint($mid, $sr + 3);
                    [$lx2, $ly2] = $sPoint($mid, $sLabelRadius - 8);
                    [$labelX, $labelY] = $sPoint($mid, $sLabelRadius);
                    $isRight = cos(deg2rad($mid)) >= 0;

                    $serviceSlices[$key]['fullCircle'] = $slice['pct'] >= 99.95;
                    $serviceSlices[$key]['path'] = "M{$scx},{$scy} L{$x1},{$y1} A{$sr},{$sr} 0 ".(($end - $start) > 180 ? 1 : 0).' 1 '."{$x2},{$y2} Z";
                    $serviceSlices[$key]['line'] = "M{$lx1},{$ly1} L{$lx2},{$ly2}";
                    $serviceSlices[$key]['labelX'] = $labelX + ($isRight ? 4 : -4);
                    $serviceSlices[$key]['labelY'] = $labelY;
                    $serviceSlices[$key]['anchor'] = $isRight ? 'start' : 'end';
                }
            @endphp

            @if($serviceTotal === 0)
                <p class="px-6 py-14 text-center text-sm text-slate-500 dark:text-[#8aa39a]">Nenhum serviço solicitado no período.</p>
            @else
                <div class="flex flex-1 flex-col gap-6 px-6 py-6">
                    <div class="flex items-center justify-center">
                        <svg viewBox="0 0 400 280" class="aspect-[10/7] w-full max-w-[320px]" role="img"
                            aria-label="Serviços mais solicitados: {{ collect($serviceSlices)->map(fn ($s) => $s['label'].' '.$fmtPct($s['pct']).'%')->implode(', ') }}">
                            @foreach($serviceSlices as $slice)
                                @unless($slice['skip'] ?? false)
                                    @if($slice['fullCircle'])
                                        <circle cx="{{ $scx }}" cy="{{ $scy }}" r="{{ $sr }}" fill="{{ $slice['hex'] }}" />
                                    @else
                                        <path d="{{ $slice['path'] }}" fill="{{ $slice['hex'] }}" stroke="#fff" stroke-width="2" class="dark:stroke-[#0c221a]" />
                                    @endif
                                @endunless
                            @endforeach
                            @foreach($serviceSlices as $slice)
                                @if(!($slice['skip'] ?? false) && !$slice['fullCircle'])
                                    <path d="{{ $slice['line'] }}" fill="none" stroke="currentColor" stroke-width="1" class="text-slate-300 dark:text-[#2c4a3e]" />
                                    <text x="{{ $slice['labelX'] }}" y="{{ $slice['labelY'] - 4 }}" text-anchor="{{ $slice['anchor'] }}" class="fill-slate-700 text-[11px] font-semibold dark:fill-[#e6f2ec]">{{ $slice['label'] }}</text>
                                    <text x="{{ $slice['labelX'] }}" y="{{ $slice['labelY'] + 9 }}" text-anchor="{{ $slice['anchor'] }}" class="fill-slate-400 text-[11px] dark:fill-[#8aa39a]">{{ number_format($slice['pct'], 1) }}%</text>
                                @endif
                            @endforeach
                        </svg>
                    </div>

                    {{-- Leitura tabular: % aqui e sobre o total de pedidos de servico, nao sobre o total de reservas --}}
                    <div class="flex flex-col gap-4 border-t border-slate-100 pt-6 dark:border-[#16352a]">
                        @foreach($serviceSlices as $key => $slice)
                            <div>
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-4 w-4 shrink-0 items-center justify-center">
                                        <span class="h-2 w-2 rounded-full" style="background-color: {{ $slice['hex'] }}"></span>
                                    </span>
                                    <span class="flex-1 truncate text-sm font-medium text-slate-700 dark:text-[#e6f2ec]">{{ $slice['label'] }}</span>
                                    <span class="w-10 text-right text-sm font-semibold tabular-nums text-slate-900 dark:text-[#e6f2ec]">{{ $slice['value'] }}</span>
                                    <span class="w-16 text-right text-sm tabular-nums text-slate-500 dark:text-[#8aa39a]">{{ $fmtPct($slice['pct']) }}%</span>
                                </div>
                                <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-[#16352a]">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         style="width: {{ min(100, $slice['pct']) }}%; background-color: {{ $slice['hex'] }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
