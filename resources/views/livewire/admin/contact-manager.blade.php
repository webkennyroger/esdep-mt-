<div>
    @php
        $colors = \App\Support\TailwindColors::palette();
    @endphp

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-[#e6f2ec]">Contatos</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Gerencie a lista de contatos para divulgação.</p>
        </div>
    </div>

    {{-- Groups Overview Filter --}}
    @unless(auth('contact')->check())
        @include('livewire.admin.contacts.partials.group-chips')
    @endunless

    {{-- Filtros --}}
    <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-50">
                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-[#8aa39a]">Buscar</label>
                <input type="text" wire:model.live="search" placeholder="Buscar por nome, e-mail ou CPF..."
                    class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d]">
            </div>

            @unless(auth('contact')->check())
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" wire:click="openGroupModal"
                    class="inline-flex h-10 items-center gap-1.5 rounded-xl bg-green-700 px-4 text-sm font-semibold text-white shadow-sm hover:bg-green-800 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Novo Grupo
                </button>
            </div>
            @endunless
        </div>
    </div>

    {{-- Contacts Table --}}
    @include('livewire.admin.contacts.partials.contact-table')

    {{-- Modals --}}
    @include('livewire.admin.contacts.modals.edit-contact')
    @include('livewire.admin.contacts.modals.view-contact')
    @include('livewire.admin.contacts.modals.create-group')
    @include('livewire.admin.contacts.modals.edit-group')
    @include('livewire.admin.contacts.modals.delete-contact')
    @include('livewire.admin.contacts.modals.delete-group')
    @include('livewire.admin.contacts.modals.import-csv')
</div>
