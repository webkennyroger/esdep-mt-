@if($groups->isNotEmpty())
<div class="mb-6">
    <h2 class="mb-3 text-sm font-semibold text-slate-700 dark:text-[#8aa39a]">Grupos</h2>
    <div class="flex flex-wrap gap-2" x-data="dragDrop($wire, 'reorderGroups')" :class="saving ? 'pointer-events-none opacity-70' : ''">
        @foreach($groups as $group)
            @php
                $isActive = $filterGroupId === $group->id;
            @endphp
            <div draggable="false" data-sort-id="{{ $group->id }}" data-color="{{ $group->color }}"
                x-on:dragstart.stop="start($event)" x-on:dragend.stop="end($event)" x-on:dragover.prevent.stop x-on:drop.stop="drop($event)"
                class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-semibold transition-all {{ $isActive ? 'ring-2 ring-offset-1 dark:ring-offset-[#0c221a]' : 'hover:scale-105' }}"
                style="background-color: color-mix(in srgb, var(--swatch, {{ $group->color }}) 18%, transparent); color: var(--swatch, {{ $group->color }}); {{ $isActive ? 'ring-color: var(--swatch, ' . $group->color . ')' : '' }}">
                <span class="h-2 w-2 rounded-full" style="background-color: var(--swatch, {{ $group->color }});"></span>
                <x-drag-handle class="-ml-1" />
                <button type="button" wire:click="setFilterGroup({{ $group->id }})" class="font-semibold">{{ $group->name }}</button>
                <span class="rounded-full px-1.5 py-0.5 text-[10px] font-bold" style="background-color: color-mix(in srgb, var(--swatch, {{ $group->color }}) 30%, transparent);">{{ $group->contacts_count }} contatos · {{ $group->rooms_count }} salas</span>
                <button type="button" wire:click.stop="openEditGroupModal({{ $group->id }})" title="Editar grupo"
                    class="ml-0.5 rounded-full p-0.5 opacity-60 hover:opacity-100 hover:bg-black/10 dark:hover:bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                @unless(auth('contact')->check())
                <button type="button" wire:click.stop="confirmDeleteGroup({{ $group->id }})" title="Excluir grupo"
                    class="rounded-full p-0.5 opacity-60 hover:opacity-100 hover:bg-black/10 dark:hover:bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                @endunless
            </div>
        @endforeach
    </div>
</div>
@endif
