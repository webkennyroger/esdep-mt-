@if($showGroupModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeGroupModal">
        <div class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-[#0c221a]" wire:click.stop>
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Novo grupo</h3>
                <button type="button" wire:click="closeGroupModal" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#e6f2ec]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form wire:submit="createGroup" class="mt-4 space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Sigla *</label>
                    <input type="text" wire:model="groupName" maxlength="20" placeholder="Ex: DEF"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                    <p class="mt-1 text-[11px] text-slate-400 dark:text-[#5a7068]">Sera convertida automaticamente para MAIUSCULA</p>
                    @error('groupName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Descricao</label>
                    <input type="text" wire:model="groupDescription"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                    <p class="mt-1 text-[11px] text-slate-400 dark:text-[#5a7068]">Nome completo do setor (aparecera nas listagens)</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Cor *</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($colors as $hex => $label)
                            <button type="button" wire:key="create-group-color-{{ $hex }}" wire:click="$set('groupColor', '{{ $hex }}')"
                                aria-pressed="{{ $groupColor === $hex ? 'true' : 'false' }}"
                                class="group relative h-8 w-8 rounded-full transition-transform {{ $groupColor === $hex ? 'ring-2 ring-offset-2 ring-slate-400 scale-110' : 'hover:scale-110' }}"
                                style="background-color: {{ $hex }};"
                                title="{{ $label }}">
                                @if($groupColor === $hex)
                                    <svg class="absolute inset-0 m-auto h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    @error('groupColor') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">E-mail de notificação de agendamento</label>
                    <x-email-tags-input model="groupNotificationEmail" placeholder="Digite um e-mail e pressione Enter" />
                    <p class="mt-1 text-[11px] text-slate-400 dark:text-[#5a7068]">Recebe o aviso de "Nova Solicitação" das salas deste grupo que não tiverem um e-mail próprio definido. Pode adicionar mais de um e-mail.</p>
                    @error('groupNotificationEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Salas gerenciadas</label>
            <div class="max-h-44 space-y-2 overflow-y-auto rounded-lg border border-slate-200 p-3 dark:border-[#16352a]">
                @forelse($rooms as $room)
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="groupRoomIds" value="{{ $room->id }}" class="rounded text-green-600"><span>{{ $room->name }}</span>@if($room->contactGroup && (!auth('contact')->check() || $room->contactGroup->owner_contact_id === auth('contact')->id()))<small class="text-amber-600">atual: {{ $room->contactGroup->name }}</small>@endif</label>
                @empty <span class="text-xs text-slate-500">Nenhuma sala cadastrada.</span> @endforelse
            </div>
            @error('groupRoomIds.*') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Contatos do grupo</label>
            <div class="max-h-44 space-y-2 overflow-y-auto rounded-lg border border-slate-200 p-3 dark:border-[#16352a]">
                @forelse($contactsForGroups as $groupContact)
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="groupContactIds" value="{{ $groupContact->id }}" class="rounded text-green-600"><span>{{ $groupContact->name }}</span> <small class="text-slate-400">{{ $groupContact->email }}</small></label>
                @empty <span class="text-xs text-slate-500">Nenhum contato disponivel.</span> @endforelse
            </div>
            @error('groupContactIds.*') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
        <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="closeGroupModal" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-[#8aa39a]">Cancelar</button>
                    <button type="submit" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800 dark:bg-[#00a477]">Criar grupo</button>
                </div>
            </form>
        </div>
    </div>
@endif
