<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">Meu Perfil</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Gerencie suas informacoes pessoais e senha.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Informacoes Pessoais --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-950/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-[#e6f2ec]">Informacoes Pessoais</h2>
            </div>
            
            <form wire:submit="updateProfile" class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Nome</label>
                    <input type="text" wire:model="name"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                    @error('name') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">E-mail</label>
                    <input type="email" wire:model="email"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                    @error('email') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                        Salvar
                    </button>
                </div>
            </form>
        </div>

        {{-- Alterar Senha --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-950/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-[#e6f2ec]">Alterar Senha</h2>
            </div>
            
            <form wire:submit="updatePassword" class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Senha Atual</label>
                    <input type="password" wire:model="currentPassword"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                    @error('currentPassword') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Nova Senha</label>
                    <input type="password" wire:model="newPassword"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                    @error('newPassword') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Confirmar Nova Senha</label>
                    <input type="password" wire:model="newPasswordConfirmation"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                        Atualizar
                    </button>
                </div>
            </form>
        </div>

        {{-- Zona de Perigo --}}
        <div class="rounded-2xl border border-red-200 bg-red-50 p-6 dark:border-red-800 dark:bg-red-950/30">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-red-700 dark:text-red-400">Zona de Perigo</h2>
            </div>
            <p class="mb-4 text-sm text-red-600 dark:text-red-300">
                Excluir sua conta e uma acao irreversivel. Todos os seus dados serao removidos permanentemente.
            </p>
            <button wire:click="openDeleteModal"
                class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800">
                Excluir Minha Conta
            </button>
        </div>
    </div>

    {{-- Modal Confirmacao Exclusao --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeDeleteModal"></div>
            <div class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl dark:border dark:border-[#16352a] dark:bg-[#0c221a]">
                <div class="px-6 py-5">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-950/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Excluir Conta</h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-[#8aa39a]">
                        Tem certeza que deseja excluir sua conta? Esta acao e irreversivel e todos os seus dados serao removidos permanentemente.
                    </p>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4 dark:border-[#16352a]">
                    <button wire:click="closeDeleteModal"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                        Cancelar
                    </button>
                    <button wire:click="deleteAccount"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800">
                        Sim, Excluir Conta
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
