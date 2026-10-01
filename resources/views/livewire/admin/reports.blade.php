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
                'chip' => 'rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-[#4fd1a5]',
                'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'note' => $periodStart.' até '.$periodEnd,
                'noteClass' => 'text-slate-500 dark:text-[#8aa39a]',
                'trend' => $stats['trends']['total'],
            ],
            [
                'label' => 'Autorizadas',
                'value' => number_format($stats['approved'], 0, ',', '.'),
                'chip' => 'rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'note' => $stats['pending'].' pendentes de análise',
                'noteClass' => 'text-amber-600 dark:text-amber-400',
                'trend' => $stats['trends']['approved'],
            ],
            [
                'label' => 'Rejeitadas',
                'value' => number_format($stats['rejected'], 0, ',', '.'),
                'chip' => 'rounded-xl bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400',
                'icon' => 'M9.879 9.879l4.242 4.242M14.121 9.879l-4.242 4.242M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'note' => 'Solicitações negadas no período',
                'noteClass' => 'text-red-600 dark:text-red-400',
                'trend' => $stats['trends']['rejected'],
            ],
            [
                'label' => 'Participantes',
                'value' => number_format($stats['totalAttendees'], 0, ',', '.'),
                'chip' => 'rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-950/40 dark:text-purple-400',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                'note' => $stats['total'] > 0
                    ? 'Média de '.number_format($avgAttendees, 0, ',', '.').' por reserva'
                    : 'Sem reservas no período',
                'noteClass' => 'text-purple-600 dark:text-purple-400',
                'trend' => $stats['trends']['totalAttendees'],
            ],
        ];
    @endphp

    {{-- Cabecalho: icone+titulo a esquerda, filtros de periodo/sala + exportar a direita, tudo numa linha --}}
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-700 text-white dark:bg-[#00a477]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-[#e6f2ec]">Relatórios</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Analise o uso das salas e estatísticas de reservas.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Periodo: botao estilo pill mostra o intervalo, abre painel com os dois inputs de data --}}
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button type="button" @click="open = !open"
                    class="flex h-12 items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-4 text-left hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-green-600 dark:border-[#16352a] dark:bg-[#0c221a] dark:hover:bg-[#16352a]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>
                        <span class="block text-xs text-slate-500 dark:text-[#8aa39a]">Período</span>
                        <span class="block text-sm font-semibold text-slate-800 dark:text-[#e6f2ec]">{{ $periodStart }} - {{ $periodEnd }}</span>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-400 dark:text-[#5a7068]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2 w-64 rounded-xl border border-slate-200 bg-white p-4 shadow-xl dark:border-[#16352a] dark:bg-[#0c221a]" style="display: none;">
                    <label class="mb-1.5 block text-xs font-medium text-slate-500 dark:text-[#8aa39a]">Data de início</label>
                    <input type="date" wire:model.live="startDate"
                        class="mb-3 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-600 focus:outline-none focus:ring-1 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                    <label class="mb-1.5 block text-xs font-medium text-slate-500 dark:text-[#8aa39a]">Data de fim</label>
                    <input type="date" wire:model.live="endDate"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-green-600 focus:outline-none focus:ring-1 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                </div>
            </div>

            {{-- Sala: botao estilo pill com select nativo transparente sobreposto, mantem wire:model.live --}}
            <div class="relative flex h-12 items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-4 dark:border-[#16352a] dark:bg-[#0c221a]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>
                    <span class="block text-xs text-slate-500 dark:text-[#8aa39a]">Sala</span>
                    <span class="block text-sm font-semibold text-slate-800 dark:text-[#e6f2ec]">
                        {{ $roomId ? $rooms->firstWhere('id', $roomId)?->name : 'Todas as salas' }}
                    </span>
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-400 dark:text-[#5a7068]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
                <select wire:model.live="roomId" aria-label="Filtrar por sala"
                    class="absolute inset-0 h-full w-full cursor-pointer appearance-none opacity-0">
                    <option value="">Todas as salas</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Exportar --}}
            <div class="relative shrink-0" x-data="{ open: false }" @click.away="open = false">
            <button type="button" @click="open = !open"
                class="inline-flex h-12 items-center gap-1.5 rounded-xl bg-green-700 px-4 text-sm font-semibold text-white hover:bg-green-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-green-600 dark:bg-[#00a477] dark:hover:bg-[#00946b]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                </svg>
                Exportar
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2 w-52 rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl dark:border-[#16352a] dark:bg-[#0c221a]" style="display: none;">
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
    </div>

    {{-- KPIs: 4 cards com total de reservas, aprovadas, rejeitadas e participantes, dentro de um "card de fundo" --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($kpis as $kpi)
            {{-- Icone fica numa coluna propria, a esquerda; label/valor/nota formam UM bloco de texto
                    a direita, alinhados entre si (nao com o icone) --}}
            <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#081712]">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center {{ $kpi['chip'] }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $kpi['icon'] }}" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-medium text-slate-600 dark:text-[#8aa39a]">{{ $kpi['label'] }}</p>
                        @if($kpi['trend'] >= 0)
                            <span class="inline-flex shrink-0 items-center gap-0.5 rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-950/40 dark:text-[#4fd1a5]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                {{ $kpi['trend'] }}%
                            </span>
                        @else
                            <span class="inline-flex shrink-0 items-center gap-0.5 rounded-full bg-red-50 px-2 py-1 text-xs font-semibold text-red-600 dark:bg-red-950/40 dark:text-red-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                                {{ abs($kpi['trend']) }}%
                            </span>
                        @endif
                    </div>
                    <div class="flex items-baseline justify-between gap-3">
                        <p class="text-3xl font-bold tracking-tight text-slate-900 dark:text-[#e6f2ec]">{{ $kpi['value'] }}</p>
                        <p class="text-xs text-slate-400 dark:text-[#5a7068]">vs. mês anterior</p>
                    </div>
                    <p class="mt-1 text-xs font-medium {{ $kpi['noteClass'] }}">{{ $kpi['note'] }}</p>
                </div>
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

        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-[#16352a]">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-[#4fd1a5]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-slate-900 dark:text-[#e6f2ec]">Status de ocupação</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-[#8aa39a]">Solicitações do período foram decididas.</p>
                </div>
            </div>
            @if($stats['total'] > 0)
                <div class="shrink-0 rounded-xl bg-emerald-50 px-4 py-2.5 text-right dark:bg-emerald-950/40">
                    <p class="text-xs font-medium text-emerald-700 dark:text-[#4fd1a5]">Taxa de autorização</p>
                    <p class="mt-0.5 text-xl font-bold tracking-tight text-emerald-700 dark:text-[#4fd1a5]">
                        {{ $fmtPct($stats['occupancyRate']) }}<span class="text-sm font-semibold">%</span>
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
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-[#16352a]">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10m-9 11h4" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-slate-900 dark:text-[#e6f2ec]">Reservas por sala</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-[#8aa39a]">
                            {{ count($stats['byRoom']) }} {{ count($stats['byRoom']) === 1 ? 'sala com reservas' : 'salas com reservas' }}, das mais para a menos usada.
                        </p>
                    </div>
                </div>
                @if($topRoom)
                    <div class="shrink-0 rounded-xl bg-blue-50 px-4 py-2.5 text-right dark:bg-blue-950/40">
                        <p class="max-w-[10rem] truncate text-xs font-medium text-blue-700 dark:text-blue-400">{{ $topRoom['name'] }}</p>
                        <p class="mt-0.5 text-xl font-bold tracking-tight text-blue-700 dark:text-[#60a5fa]">
                            {{ $fmtPct($topRoomPct) }}<span class="text-sm font-semibold">%</span>
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
                $serviceIcons = [
                    'coffee_break' => 'M4 3h12a1 1 0 011 1v3h2a2 2 0 012 2v2a2 2 0 01-2 2h-2v1a4 4 0 01-4 4H8a4 4 0 01-4-4V3zm13 6h2v-2h-2v2z',
                    'sonorizacao' => 'M11 5L6 9H2v6h4l5 4V5zM15.54 8.46a5 5 0 010 7.07M18.07 5.93a9 9 0 010 12.73',
                    'transmissao' => 'M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12 20.25h.008v.008H12v-.008z',
                    'projetor' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
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
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-[#16352a]">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="font-bold text-slate-900 dark:text-[#e6f2ec]">Serviços mais solicitados</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-[#8aa39a]">
                            Uma reserva pode pedir mais de um serviço.
                        </p>
                    </div>
                </div>
                @if($topServiceKey)
                    <div class="shrink-0 rounded-xl bg-amber-50 px-4 py-2.5 text-right dark:bg-amber-950/40">
                        <p class="text-xs font-medium text-amber-700 dark:text-amber-400">{{ $serviceLabels[$topServiceKey] }}</p>
                        <p class="mt-0.5 text-xl font-bold tracking-tight" style="color: {{ $serviceHexColors[$topServiceKey] }}">
                            {{ $fmtPct($topServicePct) }}<span class="text-sm font-semibold">%</span>
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
                                    <span class="h-2 w-2 shrink-0 rounded-full" style="background-color: {{ $slice['hex'] }}"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" style="color: {{ $slice['hex'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $serviceIcons[$key] }}" />
                                    </svg>
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

