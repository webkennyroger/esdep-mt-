<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
    @if($contacts->isEmpty())
        <div class="px-6 py-12 text-center text-sm text-slate-500 dark:text-[#8aa39a]">Nenhum contato encontrado.</div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs font-medium uppercase text-slate-500 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#8aa39a]">
                    <tr>
                        <th class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6h6m-6 4h6m-7 4h.01M9 16h6M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>Nome</span>
                        </th>
                        <th class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>E-mail</span>
                        </th>
                        <th class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>Grupos</span>
                        </th>
                        <th class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7h-3V5a2 2 0 00-2-2H9a2 2 0 00-2 2v2H4a1 1 0 00-1 1v10a2 2 0 002 2h14a2 2 0 002-2V8a1 1 0 00-1-1zM9 5h6v2H9V5z" /></svg>Cargo</span>
                        </th>
                        @unless(auth('contact')->check())
                        <th class="px-4 py-3 text-right">Ações</th>
                        @endunless
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-[#16352a]" x-data="dragDrop($wire, 'reorderContacts')">
                    @foreach($contacts as $contact)
                        <tr wire:key="contact-{{ $contact->id }}" draggable="false" data-sort-id="{{ $contact->id }}" x-on:dragstart="start($event)" x-on:dragend="end($event)" x-on:dragover.prevent x-on:drop="drop($event)" class="hover:bg-slate-50/50 dark:hover:bg-[#16352a]/30">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2"><x-drag-handle /><div class="flex flex-col">
                                    <span class="text-[11px] text-slate-500 dark:text-[#8aa39a]">{{ $contact->name }}</span>
                                </div></div>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-[#8aa39a]">{{ $contact->email }}</td>
                            <td class="px-4 py-3">
                                @php($visibleGroups = $contact->groups->whereIn('id', $groups->pluck('id')))
                                @forelse($visibleGroups as $group)
                                    <span class="mr-1 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold" data-color="{{ $group->color }}" style="background-color: color-mix(in srgb, var(--swatch, {{ $group->color }}) 18%, transparent); color: var(--swatch, {{ $group->color }});">
                                        <span class="h-1.5 w-1.5 rounded-full" data-color="{{ $group->color }}" style="background-color: var(--swatch, {{ $group->color }});"></span>
                                        {{ $group->name }}
                                    </span>
                                @empty
                                    <span class="text-slate-400 dark:text-[#5a7068]">-</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-[#8aa39a]">{{ $contact->tipo ?? '-' }}</td>
                            @unless(auth('contact')->check())
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
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
