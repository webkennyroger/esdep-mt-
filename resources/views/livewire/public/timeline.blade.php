<div class="space-y-3">
    @forelse($reservations as $res)
        @php
            $date = \Carbon\Carbon::parse($res->date);
        @endphp
        <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-[#16352a] dark:bg-[#0c221a]">
            <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg bg-green-50 text-slate-600 dark:bg-[#16352a] dark:text-[#4fd1a5]">
                <span class="text-xs font-bold leading-tight">{{ $date->format('d') }}</span>
                <span class="text-[10px] uppercase leading-tight">{{ $date->format('M') }}</span>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $res->event_name }}</h3>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-[#70847d]">{{ $res->room?->name ?? '-' }}</p>
                <p class="mt-0.5 text-xs text-slate-400 dark:text-[#70847d]">{{ $res->requester_name }}</p>
            </div>
           
            <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg bg-slate-100 text-slate-600 dark:bg-[#16352a] dark:text-slate-300">
                <span class="text-xs font-semibold leading-tight">{{ $res->start_time }}</span>
                <span class="text-xs font-semibold leading-tight">-</span>
                <span class="text-xs font-semibold leading-tight">{{ $res->end_time }}</span>
            </div> 
        </div>
    @empty
        <div class="flex min-h-[270px] items-center justify-center text-center">
            <div>
                <span class="mx-auto flex h-[46px] w-[46px] items-center justify-center border border-dashed border-[#aebfb8] text-[23px] text-[#008b5c] dark:border-[#2c4a3e] dark:text-[#4fd1a5]">+</span>
                <strong class="mt-5 block text-[13px] text-[#003f32] dark:text-[#e6f2ec]">A agenda esta livre</strong>
                <p class="mx-auto mt-2 max-w-[220px] text-[11px] leading-5 text-[#70847d]">
                    O proximo evento confirmado aparecera aqui.
                </p>
            </div>
        </div>
    @endforelse

    {{-- Paginacao --}}
    @if($totalPages > 1)
        <div class="flex items-center justify-center gap-3 pt-4">
            <button wire:click="prevPage" {{ $currentPage <= 1 ? 'disabled' : '' }}
                class="flex h-9 w-9 items-center justify-center rounded-lg border transition
                    {{ $currentPage <= 1
                        ? 'border-slate-200 text-slate-300 cursor-not-allowed dark:border-[#1e3a2f] dark:text-[#3a5a4a]'
                        : 'border-slate-300 text-slate-600 hover:bg-slate-100 hover:border-slate-400 dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#e6f2ec]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>

            <span class="text-xs font-medium text-slate-400 dark:text-[#5a7068]">
                {{ $currentPage }} / {{ $totalPages }}
            </span>

            <button wire:click="nextPage" {{ $currentPage >= $totalPages ? 'disabled' : '' }}
                class="flex h-9 w-9 items-center justify-center rounded-lg border transition
                    {{ $currentPage >= $totalPages
                        ? 'border-slate-200 text-slate-300 cursor-not-allowed dark:border-[#1e3a2f] dark:text-[#3a5a4a]'
                        : 'border-slate-300 text-slate-600 hover:bg-slate-100 hover:border-slate-400 dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#e6f2ec]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </div>
    @endif
</div>
