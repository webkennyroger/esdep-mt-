@if($showViewModal && $viewContact)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeViewModal"></div>
        <div class="relative w-full max-w-2xl overflow-y-auto max-h-[90vh] rounded-2xl bg-white shadow-2xl dark:border dark:border-[#16352a] dark:bg-[#0c221a]">

            {{-- Header --}}
            @php
                $statusColor = $viewContact->is_active ? 'from-emerald-500 to-emerald-600' : 'from-slate-500 to-slate-600';
            @endphp
            <div class="relative bg-gradient-to-r {{ $statusColor }} px-6 py-5">
                <button wire:click="closeViewModal" class="absolute right-4 top-4 rounded-lg bg-white/20 p-2.5 text-white backdrop-blur-sm hover:bg-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-white">{{ $viewContact->name }}</h2>
                        <div class="mt-1.5 flex items-center gap-2">
                            <span class="inline-flex rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-semibold text-white backdrop-blur-sm">
                                {{ $viewContact->tipo ?? 'Sem cargo' }}
                            </span>
                            @if($viewContact->last_login_at)
                                <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-semibold text-white backdrop-blur-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                    Ja logou
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-semibold text-white backdrop-blur-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-300"></span>
                                    Aguardando login
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Conteudo --}}
            <div class="px-6 py-5">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                    {{-- CPF --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-[#16352a] dark:bg-[#0a1a14]">
                        <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                            CPF
                        </div>
                        <p class="text-sm font-semibold font-mono text-slate-900 dark:text-[#e6f2ec]">{{ $viewContact->cpf ?? '-' }}</p>
                    </div>

                    {{-- E-mail --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-[#16352a] dark:bg-[#0a1a14]">
                        <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            E-mail
                        </div>
                        <p class="text-sm font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $viewContact->email }}</p>
                    </div>

                    {{-- Setor --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-[#16352a] dark:bg-[#0a1a14]">
                        <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Setor
                        </div>
                        @php
                            $setorGroup = $groups->firstWhere('name', $viewContact->setor);
                        @endphp
                        @if($setorGroup)
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold" style="background-color: {{ $setorGroup->color }}18; color: {{ $setorGroup->color }};">
                                <span class="h-2 w-2 rounded-full" style="background-color: {{ $setorGroup->color }};"></span>
                                {{ $viewContact->setor }}
                            </span>
                        @else
                            <p class="text-sm font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $viewContact->setor ?? '-' }}</p>
                        @endif
                    </div>

                    {{-- Autorizacao --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-[#16352a] dark:bg-[#0a1a14]">
                        <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Autorizacao
                        </div>
                        @if($viewContact->autorizado_at)
                            <p class="text-sm font-semibold text-green-600 dark:text-green-400">{{ $viewContact->autorizado_at->format('d/m/Y H:i') }}</p>
                        @else
                            <p class="text-sm text-slate-400 dark:text-[#5a7068]">Nao autorizado</p>
                        @endif
                    </div>
                </div>

                {{-- Grupos --}}
                <div class="mt-5">
                    <h4 class="mb-2.5 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#5a7068]">Grupos</h4>
                    <div class="flex flex-wrap gap-2">
                        @forelse($viewContact->groups as $group)
                            <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium" style="background-color: {{ $group->color }}18; color: {{ $group->color }};">
                                <span class="h-2 w-2 rounded-full" style="background-color: {{ $group->color }};"></span>
                                {{ $group->name }}
                            </span>
                        @empty
                            <p class="text-sm text-slate-400 dark:text-[#5a7068]">Nenhum grupo</p>
                        @endforelse
                    </div>
                </div>

                {{-- Acoes --}}
                <div class="mt-5 flex items-center gap-3 border-t border-slate-100 pt-5 dark:border-[#16352a]">
                    <button wire:click="closeViewModal" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-[#8aa39a]">Fechar</button>
                    <button wire:click="closeViewModal; openEditModal({{ $viewContact->id }})" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800 dark:bg-[#00a477]">Editar</button>
                </div>
            </div>
        </div>
    </div>
@endif
