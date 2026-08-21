@if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeCreateModal">
        <div class="mx-4 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-[#0c221a]" wire:click.stop>
            <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Novo contato</h3>
            <form wire:submit="createContact" class="mt-4 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Nome *</label>
                        <input type="text" wire:model="newName"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                        @error('newName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">E-mail *</label>
                        <input type="email" wire:model="newEmail"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                        @error('newEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Senha (para login do contato)</label>
                    <input type="password" wire:model="newPassword" placeholder="Deixe vazio para sem acesso"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">CPF</label>
                        <input type="text" wire:model="newCpf" placeholder="000.000.000-00"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Cargo</label>
                        <select wire:model="newTipo"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]">
                            <option value="">Selecione</option>
                            @foreach($cargos as $cargo)
                                <option value="{{ $cargo }}">{{ $cargo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Setor</label>
                        <select wire:model="newSetor"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]">
                            <option value="">Selecione</option>
                            @foreach($sectors as $sector)
                                <option value="{{ $sector }}">{{ $sector }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="newAutorizado" class="rounded text-green-600 focus:ring-green-500">
                            <span class="text-sm text-slate-700 dark:text-[#8aa39a]">Autorizado</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Grupos</label>
                    <div class="max-h-32 overflow-y-auto rounded-lg border border-slate-200 p-2 dark:border-[#16352a]">
                        @foreach($groups as $group)
                            <label class="flex items-center gap-2 py-1 text-sm text-slate-600 dark:text-[#8aa39a]">
                                <input type="checkbox" wire:model="newGroupIds" value="{{ $group->id }}" class="rounded text-green-600 focus:ring-green-500" />
                                <span class="h-2 w-2 rounded-full" style="background-color: {{ $group->color }};"></span>
                                {{ $group->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                @if(!empty($newGroupIds))
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Responsaveis</label>
                        <p class="mb-2 text-xs text-slate-500 dark:text-[#70847d]">Marque os grupos onde este contato e responsavel (pode editar reservas).</p>
                        <div class="max-h-32 overflow-y-auto rounded-lg border border-slate-200 p-2 dark:border-[#16352a]">
                            @foreach($groups as $group)
                                @if(in_array($group->id, $newGroupIds))
                                    <label class="flex items-center gap-2 py-1 text-sm text-slate-600 dark:text-[#8aa39a]">
                                        <input type="checkbox" wire:model="newResponsibleGroupIds" value="{{ $group->id }}" class="rounded text-green-600 focus:ring-green-500" />
                                        <span class="h-2 w-2 rounded-full" style="background-color: {{ $group->color }};"></span>
                                        {{ $group->name }}
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="closeCreateModal" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-[#8aa39a]">Cancelar</button>
                    <button type="submit" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800 dark:bg-[#00a477]">Criar</button>
                </div>
            </form>
        </div>
    </div>
@endif
