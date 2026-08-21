@if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeEditModal">
        <div class="mx-4 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-[#0c221a]" wire:click.stop>
            <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Editar contato</h3>
            <form wire:submit="updateContact" class="mt-4 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Nome *</label>
                        <input type="text" wire:model="editName"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                        @error('editName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">E-mail *</label>
                        <input type="email" wire:model="editEmail"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                        @error('editEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Senha (deixe vazio para manter)</label>
                    <input type="password" wire:model="editPassword" placeholder="Nova senha"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">CPF</label>
                        <input type="text" wire:model="editCpf" placeholder="000.000.000-00"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Cargo</label>
                        <select wire:model="editTipo"
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
                        <select wire:model="editSetor"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]">
                            <option value="">Selecione</option>
                            @foreach($sectors as $sector)
                                <option value="{{ $sector }}">{{ $sector }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="editAutorizado" class="rounded text-green-600 focus:ring-green-500">
                            <span class="text-sm text-slate-700 dark:text-[#8aa39a]">Autorizado</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Grupos</label>
                    <div class="max-h-32 overflow-y-auto rounded-lg border border-slate-200 p-2 dark:border-[#16352a]">
                        @php
                            $editableGroups = auth('contact')->check()
                                ? $groups->where('owner_contact_id', auth('contact')->id())
                                : $groups;
                        @endphp
                        @forelse($editableGroups as $group)
                            <label wire:key="edit-contact-group-{{ $group->id }}" class="flex items-center gap-2 py-1 text-sm text-slate-600 dark:text-[#8aa39a]">
                                <input type="checkbox" wire:model.live="editGroupIds" value="{{ $group->id }}" class="rounded text-green-600 focus:ring-green-500" />
                                <span class="h-2 w-2 rounded-full" style="background-color: {{ $group->color }};"></span>
                                {{ $group->name }}
                            </label>
                        @empty
                            <p class="py-1 text-xs text-slate-500 dark:text-[#70847d]">O grupo de acesso desta conta é definido pelo Super Admin.</p>
                        @endforelse
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="closeEditModal" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-[#8aa39a]">Cancelar</button>
                    <button type="submit" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800 dark:bg-[#00a477]">Salvar</button>
                </div>
            </form>
        </div>
    </div>
@endif
