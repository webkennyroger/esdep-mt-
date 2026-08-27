<div>
    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-[#e6f2ec]">Usuários & Acessos</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Libere o acesso de quem entrou pelo login institucional e defina o perfil de cada operador.</p>
    </div>

    {{-- Pendentes de liberação --}}
    @if($pending->isNotEmpty())
        <div class="mb-8 overflow-hidden rounded-2xl border border-amber-200 bg-amber-50/50 shadow-sm dark:border-amber-900/50 dark:bg-amber-950/20">
            <div class="flex items-center gap-2 border-b border-amber-200 px-6 py-4 dark:border-amber-900/50">
                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z" />
                </svg>
                <h2 class="text-sm font-semibold text-amber-900 dark:text-amber-300">
                    Aguardando liberação
                    <span class="ml-1 rounded-full bg-amber-200 px-2 py-0.5 text-xs font-bold text-amber-900 dark:bg-amber-900 dark:text-amber-200">{{ $pending->count() }}</span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-amber-200 text-xs font-medium uppercase text-amber-800 dark:border-amber-900/50 dark:text-amber-400">
                        <tr>
                            <th class="px-6 py-3">Pessoa</th>
                            <th class="px-4 py-3">Origem</th>
                            <th class="px-4 py-3">Perfil a conceder</th>
                            <th class="px-4 py-3 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-200/60 dark:divide-amber-900/40">
                        @foreach($pending as $user)
                            <tr wire:key="pending-{{ $user->id }}">
                                <td class="px-6 py-3">
                                    <p class="font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-[#8aa39a]">{{ $user->email }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($user->isSsoUser())
                                        <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-[#16352a] dark:text-[#4fd1a5]">Login institucional</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 dark:bg-[#16352a] dark:text-[#8aa39a]">Cadastro local</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <select wire:model="roleChoice.{{ $user->id }}"
                                        class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-900 focus:border-green-600 focus:outline-none focus:ring-1 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#0c221a] dark:text-[#e6f2ec]">
                                        @foreach($roles as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="approve({{ $user->id }})" wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-green-700 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-green-800 disabled:opacity-50 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                                        </svg>
                                        Liberar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Busca --}}
    <div class="mb-6">
        <input type="text" wire:model.live="search" placeholder="Buscar por nome ou e-mail..."
            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0c221a] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d]" />
    </div>

    {{-- Usuários com acesso --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        @if($users->isEmpty())
            <div class="px-6 py-12 text-center text-sm text-slate-500 dark:text-[#8aa39a]">Nenhum usuário encontrado.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs font-medium uppercase text-slate-500 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#8aa39a]">
                        <tr>
                            <th class="px-6 py-3">Pessoa</th>
                            <th class="px-4 py-3">Origem</th>
                            <th class="px-4 py-3">Último acesso</th>
                            <th class="px-4 py-3">Perfil</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#16352a]">
                        @foreach($users as $user)
                            <tr wire:key="user-{{ $user->id }}" class="hover:bg-slate-50/50 dark:hover:bg-[#16352a]/30">
                                <td class="px-6 py-3">
                                    <p class="font-semibold text-slate-900 dark:text-[#e6f2ec]">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="ml-1 text-xs font-normal text-slate-400 dark:text-[#70847d]">(você)</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-[#8aa39a]">{{ $user->email }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    @if($user->isSsoUser())
                                        <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-[#16352a] dark:text-[#4fd1a5]">Institucional</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 dark:bg-[#16352a] dark:text-[#8aa39a]">Local</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-[#8aa39a]">
                                    {{ $user->last_login_at?->diffForHumans() ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <select wire:model="roleChoice.{{ $user->id }}" wire:change="changeRole({{ $user->id }})"
                                        class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm text-slate-900 focus:border-green-600 focus:outline-none focus:ring-1 focus:ring-green-600 dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                                        @foreach($roles as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($user->id !== auth()->id())
                                        <button wire:click="revoke({{ $user->id }})"
                                            wire:confirm="Revogar o acesso de {{ $user->name }}?"
                                            class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                                            Revogar acesso
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-4 py-3 dark:border-[#16352a]">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
