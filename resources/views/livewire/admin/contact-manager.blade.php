<div>
    @php
        $colors = [
            '#059669' => 'Esmeralda',
            '#2563eb' => 'Azul',
            '#d97706' => 'Ambar',
            '#7c3aed' => 'Violeta',
            '#dc2626' => 'Vermelho',
            '#0891b2' => 'Ciano',
            '#c026d3' => 'Magenta',
            '#ea580c' => 'Laranja',
            '#65a30d' => 'Lima',
            '#475569' => 'Cinza',
        ];
    @endphp

    <div class="mb-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-[#e6f2ec]">Contatos</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Gerencie a lista de contatos para divulgacao.</p>
            </div>
            <div class="flex gap-2">
                <button wire:click="openGroupModal"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-[#16352a] dark:bg-[#0c221a] dark:text-[#8aa39a]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Grupos
                </button>
                <button wire:click="openImportModal"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-[#16352a] dark:bg-[#0c221a] dark:text-[#8aa39a]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Importar
                </button>
                <button wire:click="openCreateModal"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14" />
                    </svg>
                    Novo contato
                </button>
            </div>
        </div>
    </div>

    {{-- Groups Overview --}}
    @if($groups->isNotEmpty())
    <div class="mb-6">
        <h2 class="mb-3 text-sm font-semibold text-slate-700 dark:text-[#8aa39a]">Grupos</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($groups as $group)
                @php
                    $bg = $group->color . '18';
                    $text = $group->color;
                    $badgeBg = $group->color . '30';
                    $isActive = $filterGroupId === $group->id;
                @endphp
                <button wire:click="setFilterGroup({{ $group->id }})"
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold transition-all {{ $isActive ? 'ring-2 ring-offset-1 dark:ring-offset-[#0c221a]' : 'hover:scale-105' }}"
                    style="background-color: {{ $bg }}; color: {{ $text }}; {{ $isActive ? 'ring-color: ' . $group->color : '' }}">
                    <span class="h-2 w-2 rounded-full" style="background-color: {{ $group->color }};"></span>
                    {{ $group->name }}
                    <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold" style="background-color: {{ $badgeBg }};">{{ $group->contacts_count }}</span>
                    <span wire:click.stop="openEditGroupModal({{ $group->id }})" title="Editar grupo"
                        class="ml-0.5 rounded-full p-0.5 opacity-60 hover:opacity-100 hover:bg-black/10 dark:hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </span>
                    <span wire:click.stop="confirmDeleteGroup({{ $group->id }})" title="Excluir grupo"
                        class="rounded-full p-0.5 opacity-60 hover:opacity-100 hover:bg-black/10 dark:hover:bg-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                </button>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Search --}}
    <div class="mb-6">
        <input type="text" wire:model.live="search" placeholder="Buscar por nome, e-mail ou CPF..."
            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0c221a] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d]" />
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        @if($contacts->isEmpty())
            <div class="px-6 py-12 text-center text-sm text-slate-500 dark:text-[#8aa39a]">Nenhum contato encontrado.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs font-medium uppercase text-slate-500 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#8aa39a]">
                        <tr>
                            <th class="px-4 py-3">CPF / Nome</th>
                            <th class="px-4 py-3">E-mail</th>
                            <th class="px-4 py-3">Setor</th>
                            <th class="px-4 py-3">Tipo</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Autorizado</th>
                            <th class="px-4 py-3 text-right">Acoes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#16352a]">
                        @foreach($contacts as $contact)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#16352a]/30">
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-mono text-xs font-medium text-slate-900 dark:text-[#e6f2ec]">{{ $contact->cpf ?? '-' }}</span>
                                        <span class="text-[11px] text-slate-500 dark:text-[#8aa39a]">{{ $contact->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-[#8aa39a]">{{ $contact->email }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $setorGroup = $groups->firstWhere('name', $contact->setor);
                                    @endphp
                                    @if($setorGroup)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold" style="background-color: {{ $setorGroup->color }}18; color: {{ $setorGroup->color }};">
                                            <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $setorGroup->color }};"></span>
                                            {{ $contact->setor }}
                                        </span>
                                    @elseif($contact->setor)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 dark:bg-[#16352a] dark:text-[#8aa39a]">
                                            {{ $contact->setor }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 dark:text-[#5a7068]">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-[#8aa39a]">{{ $contact->tipo ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($contact->last_login_at)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                            Ja logou
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Aguardando login
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($contact->autorizado_at)
                                        <span class="text-xs text-green-600 dark:text-green-400">{{ $contact->autorizado_at->format('d/m/Y H:i') }}</span>
                                    @else
                                        <span class="text-xs text-slate-400 dark:text-[#5a7068]">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="openViewModal({{ $contact->id }})" title="Ver detalhes"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:text-[#5a7068] dark:hover:bg-[#16352a] dark:hover:text-[#8aa39a]">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <button wire:click="openEditModal({{ $contact->id }})" title="Editar"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-blue-600 dark:text-[#5a7068] dark:hover:bg-[#16352a] dark:hover:text-blue-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        <button wire:click="confirmDeleteContact({{ $contact->id }})" title="Excluir"
                                            class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 dark:text-[#5a7068] dark:hover:bg-red-900/20 dark:hover:text-red-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200 px-6 py-4 dark:border-[#16352a]">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>

    {{-- Create Contact Modal --}}
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
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">CPF</label>
                            <input type="text" wire:model="newCpf" placeholder="000.000.000-00"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Tipo</label>
                            <select wire:model="newTipo"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]">
                                <option value="">Selecione</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo }}">{{ $tipo }}</option>
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
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeCreateModal" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-[#8aa39a]">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800 dark:bg-[#00a477]">Criar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Edit Contact Modal --}}
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
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">CPF</label>
                            <input type="text" wire:model="editCpf" placeholder="000.000.000-00"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Tipo</label>
                            <select wire:model="editTipo"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]">
                                <option value="">Selecione</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo }}">{{ $tipo }}</option>
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
                            @foreach($groups as $group)
                                <label class="flex items-center gap-2 py-1 text-sm text-slate-600 dark:text-[#8aa39a]">
                                    <input type="checkbox" wire:model="editGroupIds" value="{{ $group->id }}" class="rounded text-green-600 focus:ring-green-500" />
                                    <span class="h-2 w-2 rounded-full" style="background-color: {{ $group->color }};"></span>
                                    {{ $group->name }}
                                </label>
                            @endforeach
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

    {{-- View Contact Modal --}}
    @if($showViewModal && $viewContact)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeViewModal"></div>
            <div class="relative w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:border dark:border-[#16352a] dark:bg-[#0c221a]">

                {{-- Header --}}
                @php
                    $statusColor = $viewContact->is_active ? 'from-emerald-500 to-emerald-600' : 'from-slate-500 to-slate-600';
                @endphp
                <div class="relative bg-gradient-to-r {{ $statusColor }} px-6 py-5">
                    <button wire:click="closeViewModal" class="absolute right-4 top-4 rounded-lg bg-white/20 p-1.5 text-white backdrop-blur-sm hover:bg-white/30">
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
                                    {{ $viewContact->tipo ?? 'Sem tipo' }}
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
                    <div class="grid grid-cols-2 gap-5">

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

    {{-- Import Modal --}}
    @if($showImportModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="$set('showImportModal', false)">
            <div class="mx-4 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-[#0c221a]" wire:click.stop>
                <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Importar contatos (CSV)</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Cole o conteudo CSV no formato: nome,email</p>
                <form wire:submit="importContacts" class="mt-4 space-y-4">
                    <div>
                        <textarea wire:model="importCsv" rows="8" placeholder="nome,email&#10;Carlos Souza,carlos@exemplo.gov.br&#10;Maria Silva,maria@exemplo.gov.br"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 font-mono text-sm text-slate-900 placeholder:text-slate-400 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]"></textarea>
                        @error('importCsv') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="$set('showImportModal', false)" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-[#8aa39a]">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800 dark:bg-[#00a477]">Importar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Create Group Modal --}}
    @if($showGroupModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeGroupModal">
            <div class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-[#0c221a]" wire:click.stop>
                <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Novo grupo</h3>
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
                                <button type="button" wire:click="groupColor = '{{ $hex }}'"
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
                        <input type="hidden" wire:model="groupColor" />
                        @error('groupColor') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeGroupModal" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-[#8aa39a]">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800 dark:bg-[#00a477]">Criar grupo</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Edit Group Modal --}}
    @if($showEditGroupModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeEditGroupModal">
            <div class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-[#0c221a]" wire:click.stop>
                <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Editar grupo</h3>
                <form wire:submit="updateGroup" class="mt-4 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Sigla *</label>
                        <input type="text" wire:model="editGroupName" maxlength="20"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                        <p class="mt-1 text-[11px] text-slate-400 dark:text-[#5a7068]">Sera convertida automaticamente para MAIUSCULA</p>
                        @error('editGroupName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Descricao</label>
                        <input type="text" wire:model="editGroupDescription"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                        <p class="mt-1 text-[11px] text-slate-400 dark:text-[#5a7068]">Nome completo do setor (aparecera nas listagens)</p>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Cor *</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($colors as $hex => $label)
                                <button type="button" wire:click="editGroupColor = '{{ $hex }}'"
                                    class="group relative h-8 w-8 rounded-full transition-transform {{ $editGroupColor === $hex ? 'ring-2 ring-offset-2 ring-slate-400 scale-110' : 'hover:scale-110' }}"
                                    style="background-color: {{ $hex }};"
                                    title="{{ $label }}">
                                    @if($editGroupColor === $hex)
                                        <svg class="absolute inset-0 m-auto h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" wire:model="editGroupColor" />
                        @error('editGroupColor') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeEditGroupModal" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-[#8aa39a]">Cancelar</button>
                        <button type="submit" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800 dark:bg-[#00a477]">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteGroupModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeDeleteGroupModal">
            <div class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-[#0c221a]" wire:click.stop>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Excluir</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Tem certeza que deseja excluir <strong>{{ $deleteGroupName }}</strong>?</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button wire:click="closeDeleteGroupModal" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-[#8aa39a]">Cancelar</button>
                    <button wire:click="deleteContact({{ $deleteGroupId }})" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Excluir</button>
                </div>
            </div>
        </div>
    @endif
</div>
