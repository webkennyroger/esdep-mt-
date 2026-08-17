<div>
@if($showModal)
<div class="fixed inset-0 z-50 flex items-center justify-center" x-data>
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="close"></div>
    <div class="relative mx-4 w-full max-w-2xl rounded-xl bg-white p-6 shadow-2xl dark:border dark:border-[#16352a] dark:bg-[#0c221a] overflow-y-auto max-h-[90vh]">

        <div class="mb-5 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Editar Reserva</h3>
            <button wire:click="close" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#e6f2ec]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form wire:submit="save" class="space-y-4">

            {{-- Evento e Sala --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-[#e6f2ec] mb-1">Nome do Evento <span class="text-red-500">*</span></label>
                    <input wire:model="eventName" type="text" placeholder="Nome do evento"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d]">
                    @error('eventName') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-[#e6f2ec] mb-1">Sala <span class="text-red-500">*</span></label>
                    <select wire:model="roomId"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                        <option value="">Selecione uma sala</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->name }} (cap. {{ $room->capacity }})</option>
                        @endforeach
                    </select>
                    @error('roomId') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Descricao --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-[#e6f2ec] mb-1">Descricao</label>
                <textarea wire:model="description" rows="2" placeholder="Descricao do evento (opcional)"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d]"></textarea>
            </div>

            {{-- Data e Horarios --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-[#e6f2ec] mb-1">Data <span class="text-red-500">*</span></label>
                    <input wire:model="date" type="date"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:[color-scheme:dark]">
                    @error('date') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-[#e6f2ec] mb-1">Inicio <span class="text-red-500">*</span></label>
                    <input wire:model="startTime" type="time"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:[color-scheme:dark]">
                    @error('startTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-[#e6f2ec] mb-1">Termino <span class="text-red-500">*</span></label>
                    <input wire:model="endTime" type="time"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:[color-scheme:dark]">
                    @error('endTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Solicitante --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-[#e6f2ec] mb-1">Solicitante <span class="text-red-500">*</span></label>
                    <input wire:model="requesterName" type="text" placeholder="Nome do solicitante"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d]">
                    @error('requesterName') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-[#e6f2ec] mb-1">E-mail <span class="text-red-500">*</span></label>
                    <input wire:model="requesterEmail" type="email" placeholder="email@exemplo.com"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d]">
                    @error('requesterEmail') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Participantes --}}
            <div class="w-48">
                <label class="block text-sm font-medium text-slate-700 dark:text-[#e6f2ec] mb-1">Participantes <span class="text-red-500">*</span></label>
                <input wire:model="attendeesCount" type="number" min="1"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:outline-none dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                @error('attendeesCount') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Servicos Adicionais --}}
            <div class="rounded-lg border border-slate-200 dark:border-[#16352a] dark:bg-[#081712]/50 p-4">
                <p class="text-sm font-semibold text-slate-700 dark:text-[#e6f2ec] mb-3">Servicos Adicionais</p>
                <div class="grid grid-cols-3 gap-4">
                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-[#8aa39a] cursor-pointer">
                        <input wire:model="coffeeBreak" type="checkbox" class="h-4 w-4 rounded accent-[#008b5c] dark:accent-[#4fd1a5]">
                        Coffee Break
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-[#8aa39a] cursor-pointer">
                        <input wire:model="sonorizacao" type="checkbox" class="h-4 w-4 rounded accent-[#008b5c] dark:accent-[#4fd1a5]">
                        Sonorizacao
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-[#8aa39a] cursor-pointer">
                        <input wire:model="transmissao" type="checkbox" class="h-4 w-4 rounded accent-[#008b5c] dark:accent-[#4fd1a5]">
                        Transmissao
                    </label>
                </div>
            </div>

            {{-- Acoes --}}
            <div class="flex justify-end gap-3 pt-2 border-t border-slate-200 dark:border-[#16352a]">
                <button type="button" wire:click="close"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#e6f2ec]">
                    Cancelar
                </button>
                <button type="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-not-allowed"
                    class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                    <span wire:loading.remove>Salvar Alteracoes</span>
                    <span wire:loading>Salvando...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif
</div>
