@if($showImportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click="closeImportModal">
        <div class="mx-4 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-[#0c221a]" wire:click.stop>
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Importar contatos (CSV)</h3>
                <button type="button" wire:click="closeImportModal" class="rounded-lg p-2.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#e6f2ec]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Cole o conteudo CSV no formato: nome,email</p>
            <form wire:submit="importContacts" class="mt-4 space-y-4">
                <div>
                    <textarea wire:model="importCsv" rows="8" placeholder="nome,email&#10;Carlos Souza,carlos@exemplo.gov.br&#10;Maria Silva,maria@exemplo.gov.br"
                        class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 font-mono text-sm text-slate-900 placeholder:text-slate-400 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20 dark:border-[#16352a] dark:bg-[#0a1a14] dark:text-[#e6f2ec]"></textarea>
                    @error('importCsv') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="closeImportModal" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-[#8aa39a]">Cancelar</button>
                    <button type="submit" class="rounded-lg bg-green-700 px-4 py-2 text-sm font-medium text-white hover:bg-green-800 dark:bg-[#00a477]">Importar</button>
                </div>
            </form>
        </div>
    </div>
@endif
