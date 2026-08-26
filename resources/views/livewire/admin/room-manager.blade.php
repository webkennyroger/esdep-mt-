<div>
    {{-- Page Header --}}
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-[#e6f2ec]">Salas & Espaços</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Gerencie as salas disponíveis para agendamentos e eventos.</p>
        </div>
        <button wire:click="openCreateModal"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
            </svg>
            Nova Sala
        </button>
    </div>

    {{-- Search Bar --}}
    <div class="mb-6">
        <input type="text" wire:model.live="search" placeholder="Buscar por nome da sala, código ou descrição..."
            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0c221a] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d]" />
    </div>

    {{-- Rooms Table --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        @if($rooms->isEmpty())
            <div class="px-6 py-12 text-center text-sm text-slate-500 dark:text-[#8aa39a]">Nenhuma sala encontrada.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs font-medium uppercase text-slate-500 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#8aa39a]">
                        <tr>
                            <th class="px-4 py-3">Sala / Código</th>
                            <th class="px-4 py-3">Capacidade</th>
                            <th class="px-4 py-3">Grupo responsável</th>
                            <th class="px-4 py-3">Cor no Calendário</th>
                            <th class="px-4 py-3">Reservas</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#16352a]" x-data="dragDrop($wire, 'reorderRooms')">
                        @foreach($rooms as $room)
                            <tr wire:key="room-{{ $room->id }}" draggable="false" data-sort-id="{{ $room->id }}" x-on:dragstart="start($event)" x-on:dragend="end($event)" x-on:dragover.prevent x-on:drop="drop($event)" class="hover:bg-slate-50/50 dark:hover:bg-[#16352a]/30">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3"><x-drag-handle />
                                        <span class="h-3.5 w-3.5 shrink-0 rounded-full" style="background-color: {{ $room->theme_color ?? '#008b5c' }};"></span>
                                        <div>
                                            <p class="font-semibold text-slate-900 dark:text-[#e6f2ec]">{{ $room->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                @php
                                    $visibleGroup = $room->contactGroup;
                                    if (auth('contact')->check() && $visibleGroup?->owner_contact_id !== auth('contact')->id()) $visibleGroup = null;
                                    if (auth()->check() && $visibleGroup?->owner_contact_id !== null) $visibleGroup = null;
                                @endphp
                                <td class="px-4 py-3 text-xs text-slate-600 dark:text-[#8aa39a]">{{ $visibleGroup?->name ?? 'Não atribuído' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 dark:bg-[#16352a] dark:text-[#8aa39a]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $room->capacity }} pessoas
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="h-4 w-4 rounded-md" style="background-color: {{ $room->theme_color ?? '#008b5c' }};"></span>
                                        <span class="font-mono text-xs text-slate-600 dark:text-[#8aa39a]">{{ $room->theme_color ?? '#008b5c' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs font-semibold text-slate-600 dark:text-[#8aa39a]">
                                    {{ $room->reservations_count }} registros
                                </td>
                                <td class="px-4 py-3">
                                    <button wire:click="toggleActive({{ $room->id }})"
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold transition
                                            {{ $room->is_active
                                                ? 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400'
                                                : 'bg-slate-100 text-slate-500 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400' }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $room->is_active ? 'bg-green-500' : 'bg-slate-400' }}"></span>
                                        {{ $room->is_active ? 'Ativa' : 'Inativa' }}
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="openEditModal({{ $room->id }})" title="Editar Sala"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-blue-600 dark:text-[#5a7068] dark:hover:bg-[#16352a] dark:hover:text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $room->id }})" title="Excluir Sala"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 dark:text-[#5a7068] dark:hover:bg-red-900/20 dark:hover:text-red-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-6 py-4 dark:border-[#16352a]">
                {{ $rooms->links() }}
            </div>
        @endif
    </div>

    {{-- Create Room Modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeCreateModal">
            <div class="mx-4 w-full max-w-lg overflow-y-auto max-h-[90vh] rounded-2xl bg-white p-6 shadow-xl dark:bg-[#0c221a]" wire:click.stop>
                <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Nova Sala</h3>
                <form wire:submit="createRoom" class="mt-4 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Nome da Sala *</label>
                        <input type="text" wire:model="name" placeholder="Ex: Auditório Principal"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Grupo responsável</label>
                        <select wire:model="contactGroupId" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-[#16352a] dark:bg-[#0a1a14]">
                            <option value="">Não atribuído</option>
                            @foreach($contactGroups as $group)<option value="{{ $group->id }}">{{ $group->name }}</option>@endforeach
                        </select>
                        @error('contactGroupId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">E-mail para notificações (dono da sala)</label>
                        <x-email-tags-input model="notificationEmail" placeholder="Digite um e-mail e pressione Enter" />
                        <p class="mt-1 text-[11px] text-slate-400 dark:text-[#5a7068]">Recebe o aviso de "Nova Solicitação" para esta sala. Pode adicionar mais de um e-mail. Deixe vazio para usar o e-mail do grupo ou o padrão do sistema.</p>
                        @error('notificationEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Capacidade (Pessoas) *</label>
                            <input type="number" min="1" wire:model="capacity"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                            @error('capacity') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-end pb-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="rounded text-green-600 focus:ring-green-500">
                                <span class="text-sm text-slate-700 dark:text-[#8aa39a]">Sala Ativa para Reservas</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Cor no Calendário *</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($colors as $hex => $label)
                                <button type="button" wire:click="$set('theme_color', '{{ $hex }}')"
                                    class="group relative h-8 w-8 rounded-full transition-transform {{ $theme_color === $hex ? 'ring-2 ring-offset-2 ring-slate-400 scale-110' : 'hover:scale-110' }}"
                                    style="background-color: {{ $hex }};"
                                    title="{{ $label }}">
                                    @if($theme_color === $hex)
                                        <svg class="absolute inset-0 m-auto h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                        @error('theme_color') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Descrição / Recursos</label>
                        <textarea wire:model="description" rows="3" placeholder="Ex: Equipado com projetor 4K, 2 microfones sem fio e ar-condicionado central."
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]"></textarea>
                        @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeCreateModal" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-[#8aa39a]">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800 dark:bg-[#00a477]">Criar Sala</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Edit Room Modal --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeEditModal">
            <div class="mx-4 w-full max-w-lg overflow-y-auto max-h-[90vh] rounded-2xl bg-white p-6 shadow-xl dark:bg-[#0c221a]" wire:click.stop>
                <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Editar Sala</h3>
                <form wire:submit="updateRoom" class="mt-4 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Nome da Sala *</label>
                        <input type="text" wire:model="editName"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                        @error('editName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Grupo responsável</label>
                        <select wire:model="editContactGroupId" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm dark:border-[#16352a] dark:bg-[#0a1a14]">
                            <option value="">Não atribuído</option>
                            @foreach($contactGroups as $group)<option value="{{ $group->id }}">{{ $group->name }}</option>@endforeach
                        </select>
                        @error('editContactGroupId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">E-mail para notificações (dono da sala)</label>
                        <x-email-tags-input model="editNotificationEmail" placeholder="Digite um e-mail e pressione Enter" />
                        <p class="mt-1 text-[11px] text-slate-400 dark:text-[#5a7068]">Recebe o aviso de "Nova Solicitação" para esta sala. Pode adicionar mais de um e-mail. Deixe vazio para usar o e-mail do grupo ou o padrão do sistema.</p>
                        @error('editNotificationEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Capacidade (Pessoas) *</label>
                            <input type="number" min="1" wire:model="editCapacity"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                            @error('editCapacity') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-end pb-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="editIsActive" class="rounded text-green-600 focus:ring-green-500">
                                <span class="text-sm text-slate-700 dark:text-[#8aa39a]">Sala Ativa</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Cor no Calendário *</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($colors as $hex => $label)
                                <button type="button" wire:click="$set('editThemeColor', '{{ $hex }}')"
                                    class="group relative h-8 w-8 rounded-full transition-transform {{ $editThemeColor === $hex ? 'ring-2 ring-offset-2 ring-slate-400 scale-110' : 'hover:scale-110' }}"
                                    style="background-color: {{ $hex }};"
                                    title="{{ $label }}">
                                    @if($editThemeColor === $hex)
                                        <svg class="absolute inset-0 m-auto h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                        @error('editThemeColor') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Descrição / Recursos</label>
                        <textarea wire:model="editDescription" rows="3"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]"></textarea>
                        @error('editDescription') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeEditModal" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-[#8aa39a]">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800 dark:bg-[#00a477]">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeDeleteModal">
            <div class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-[#0c221a]" wire:click.stop>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Excluir Sala</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Tem certeza que deseja excluir a sala <strong>{{ $deleteRoomName }}</strong>?</p>
                    </div>
                </div>
                @if($deleteReservationsCount > 0)
                    <div class="mt-4 rounded-lg bg-amber-50 p-3 text-xs text-amber-800 dark:bg-amber-900/20 dark:text-amber-300">
                        Atenção: Esta sala possui <strong>{{ $deleteReservationsCount }} reservas</strong> associadas. Ela não poderá ser excluída, apenas desativada.
                    </div>
                @endif
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeDeleteModal" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-[#8aa39a]">Cancelar</button>
                    <button wire:click="deleteRoom" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Excluir</button>
                </div>
            </div>
        </div>
    @endif
</div>
