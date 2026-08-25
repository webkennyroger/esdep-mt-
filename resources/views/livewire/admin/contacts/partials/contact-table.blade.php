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
                        <th class="px-4 py-3">Grupos</th>
                        @if($isSuperAdmin)
                        <th class="px-4 py-3">Papel</th>
                        @endif
                        <th class="px-4 py-3">Cargo</th>
                        <th class="px-4 py-3">Reservas</th>
                        <th class="px-4 py-3">Acesso ao sistema</th>
                        <th class="px-4 py-3">Cadastro autorizado</th>
                        @unless(auth('contact')->check())
                        <th class="px-4 py-3 text-right">Acoes</th>
                        @endunless
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#16352a]" x-data="dragDrop($wire, 'reorderContacts')">
                    @foreach($contacts as $contact)
                        <tr wire:key="contact-{{ $contact->id }}" draggable="false" data-sort-id="{{ $contact->id }}" x-on:dragstart="start($event)" x-on:dragend="end($event)" x-on:dragover.prevent x-on:drop="drop($event)" class="hover:bg-slate-50/50 dark:hover:bg-[#16352a]/30">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2"><x-drag-handle /><div class="flex flex-col">
                                    <span class="font-mono text-xs font-medium text-slate-900 dark:text-[#e6f2ec]">{{ $contact->cpf ?? '-' }}</span>
                                    <span class="text-[11px] text-slate-500 dark:text-[#8aa39a]">{{ $contact->name }}</span>
                                </div></div>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-[#8aa39a]">{{ $contact->email }}</td>
                            <td class="px-4 py-3">
                                @php($visibleGroups = $contact->groups->whereIn('id', $groups->pluck('id')))
                                @forelse($visibleGroups as $group)
                                    <span class="mr-1 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold" style="background-color: {{ $group->color }}18; color: {{ $group->color }};">
                                        <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $group->color }};"></span>
                                        {{ $group->name }}
                                    </span>
                                @empty
                                    <span class="text-slate-400 dark:text-[#5a7068]">-</span>
                                @endforelse
                            </td>
                            @if($isSuperAdmin)
                            <td class="px-4 py-3">
                                @if($contact->groups->contains(fn ($group) => (bool) $group->pivot->is_responsible))
                                    <span class="inline-flex items-center gap-1 rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-medium text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">Gerente</span>
                                @else
                                    <span class="text-slate-400 dark:text-[#5a7068]">-</span>
                                @endif
                            </td>
                            @endif
                            <td class="px-4 py-3 text-slate-600 dark:text-[#8aa39a]">{{ $contact->tipo ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($contact->last_login_at)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Login realizado
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Aguardando primeiro login
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs font-semibold text-slate-600 dark:text-[#8aa39a]">
                                {{ $contact->reservations_count ?? 0 }}
                            </td>
                            <td class="px-4 py-3">
                                @if($contact->autorizado_at)
                                    <span class="text-xs text-green-600 dark:text-green-400">{{ $contact->autorizado_at->format('d/m/Y H:i') }}</span>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-[#5a7068]">-</span>
                                @endif
                            </td>
                            @unless(auth('contact')->check())
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
                            @endunless
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
