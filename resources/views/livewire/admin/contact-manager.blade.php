<div @if(auth('contact')->check()) wire:poll.5s @endif>
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

    {{-- Page Header --}}
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

    {{-- Groups Overview Filter --}}
    @include('livewire.admin.contacts.partials.group-chips')

    {{-- Search Bar --}}
    <div class="mb-6">
        <input type="text" wire:model.live="search" placeholder="Buscar por nome, e-mail ou CPF..."
            class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0c221a] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d]" />
    </div>

    {{-- Contacts Table --}}
    @include('livewire.admin.contacts.partials.contact-table')

    {{-- Modals --}}
    @include('livewire.admin.contacts.modals.create-contact')
    @include('livewire.admin.contacts.modals.edit-contact')
    @include('livewire.admin.contacts.modals.view-contact')
    @include('livewire.admin.contacts.modals.create-group')
    @include('livewire.admin.contacts.modals.edit-group')
    @include('livewire.admin.contacts.modals.delete-contact')
    @include('livewire.admin.contacts.modals.delete-group')
    @include('livewire.admin.contacts.modals.import-csv')
</div>
