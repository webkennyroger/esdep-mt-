<div>
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

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700 dark:bg-green-950/30 dark:border-green-800 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

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
                @endphp
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold" style="background-color: {{ $bg }}; color: {{ $text }};">
                    <span class="h-2 w-2 rounded-full" style="background-color: {{ $group->color }};"></span>
                    {{ $group->name }}
                    <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold" style="background-color: {{ $badgeBg }};">{{ $group->contacts_count }}</span>
                </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Search --}}
    <div class="mb-6">
        <input type="text" wire:model.live="search" placeholder="Buscar contato..."
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
                            <th class="px-6 py-3">Nome</th>
                            <th class="px-6 py-3">E-mail</th>
                            <th class="px-6 py-3">Grupos</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Acoes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#16352a]">
                        @foreach($contacts as $contact)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#16352a]/30">
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-[#e6f2ec]">{{ $contact->name }}</td>
                                <td class="px-6 py-4 text-slate-600 dark:text-[#8aa39a]">{{ $contact->email }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($contact->groups as $group)
                                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold" style="background-color: {{ $group->color }}18; color: {{ $group->color }};">
                                                <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $group->color }};"></span>
                                                {{ $group->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="toggleActive({{ $contact->id }})"
                                        class="rounded-full px-2.5 py-1 text-xs font-medium {{ $contact->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $contact->is_active ? 'Ativo' : 'Inativo' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="deleteContact({{ $contact->id }})"
                                        wire:confirm="Tem certeza que deseja excluir este contato?"
                                        class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-100 dark:bg-red-900/30 dark:text-red-400">
                                        Excluir
                                    </button>
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
            <div class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-[#0c221a]" wire:click.stop>
                <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Novo contato</h3>
                <form wire:submit="createContact" class="mt-4 space-y-4">
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

    {{-- Group Modal --}}
    @if($showGroupModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeGroupModal">
            <div class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-[#0c221a]" wire:click.stop>
                <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Novo grupo</h3>
                <form wire:submit="createGroup" class="mt-4 space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Nome *</label>
                        <input type="text" wire:model="groupName" placeholder="Ex: Defensores"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                        @error('groupName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Descricao</label>
                        <input type="text" wire:model="groupDescription"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Cor *</label>
                        <div class="flex flex-wrap gap-2">
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
</div>
