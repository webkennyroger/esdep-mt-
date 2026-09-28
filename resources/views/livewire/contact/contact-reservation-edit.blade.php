<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">Editar Reserva</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Atualize os dados da reserva.</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
        <form wire:submit="submit" class="space-y-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8">
                <label class="block">
                    <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Nome do responsável</span>
                    <input wire:model="requesterName" required placeholder="Ex.: Ana Martins"
                        class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent px-0 text-[13px] text-[#003f32] outline-none placeholder:text-[#70847d] focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d] dark:focus:border-[#4fd1a5]">
                    @error('requesterName') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </label>

                <label class="block">
                    <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Nome do evento</span>
                    <input wire:model="eventName" required placeholder="Ex.: Encontro de lideranças"
                        class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent px-0 text-[13px] text-[#003f32] outline-none placeholder:text-[#70847d] focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d] dark:focus:border-[#4fd1a5]">
                    @error('eventName') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </label>
            </div>

            <label class="block">
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">E-mail do solicitante</span>
                <input wire:model="requesterEmail" required type="email" placeholder="nome@instituicao.gov.br"
                    class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent px-0 text-[13px] text-[#003f32] outline-none placeholder:text-[#70847d] focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d] dark:focus:border-[#4fd1a5]">
                @error('requesterEmail') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>

            <label class="block">
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Sala</span>
                <select wire:model="roomId" required
                    class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent px-0 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5]">
                    <option value="">Selecione uma sala</option>
                    @foreach($this->rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->capacity }} pessoas)</option>
                    @endforeach
                </select>
                @error('roomId') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 sm:gap-8">
                <label class="block">
                    <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Data</span>
                    <input wire:model="date" required type="date"
                        class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent px-0 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5]">
                    @error('date') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </label>

                <label class="block">
                    <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Início</span>
                    <input wire:model="startTime" required type="time"
                        class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent px-0 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5]">
                    @error('startTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </label>

                <label class="block">
                    <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Fim</span>
                    <input wire:model="endTime" required type="time"
                        class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent px-0 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5]">
                    @error('endTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </label>
            </div>

            @if($conflictError)
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 dark:border-amber-800 dark:bg-amber-950/50">
                    <p class="text-xs text-amber-700 dark:text-amber-400">{{ $conflictError }}</p>
                </div>
            @endif

            <label class="block">
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Participantes</span>
                <input wire:model="attendeesCount" required type="number" min="1"
                    class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent px-0 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5]">
                @error('attendeesCount') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>

            <label class="block">
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Descrição (opcional)</span>
                <textarea wire:model="description" rows="3" placeholder="Detalhes do evento..."
                    class="w-full border-b border-[#b8c9c2] bg-transparent px-0 text-[13px] text-[#003f32] outline-none placeholder:text-[#70847d] focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d] dark:focus:border-[#4fd1a5]"></textarea>
            </label>

            <div>
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Anexo / Recibo</span>
                @if($hasAttachment)
                    @if($isAttachmentImage)
                        <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener" class="inline-block">
                            <img src="{{ $attachmentUrl }}" alt="{{ $attachmentName }}" class="h-32 w-auto rounded-lg border border-[#b8c9c2] object-cover dark:border-[#2c4a3e]">
                        </a>
                    @else
                        <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener"
                            class="inline-flex items-center gap-2 text-[13px] font-semibold text-[#008b5c] hover:underline dark:text-[#4fd1a5]">
                            <x-icons.pdf-icon class="h-5 w-5" />
                            {{ $attachmentName ?: 'Ver anexo' }}
                        </a>
                    @endif
                @else
                    <p class="text-[13px] text-[#70847d] dark:text-[#8aa39a]">Nenhum anexo enviado.</p>
                @endif
            </div>

            <div>
                <span class="mb-3 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Serviços</span>
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="coffeeBreak" class="h-4 w-4 rounded border-[#b8c9c2] text-[#008b5c] focus:ring-[#008b5c] dark:border-[#2c4a3e]">
                        <span class="text-[12px] text-[#003f32] dark:text-[#e6f2ec]">Coffee Break</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="sonorizacao" class="h-4 w-4 rounded border-[#b8c9c2] text-[#008b5c] focus:ring-[#008b5c] dark:border-[#2c4a3e]">
                        <span class="text-[12px] text-[#003f32] dark:text-[#e6f2ec]">Sonorização</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="transmissao" class="h-4 w-4 rounded border-[#b8c9c2] text-[#008b5c] focus:ring-[#008b5c] dark:border-[#2c4a3e]">
                        <span class="text-[12px] text-[#003f32] dark:text-[#e6f2ec]">Transmissão</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="projetor" class="h-4 w-4 rounded border-[#b8c9c2] text-[#008b5c] focus:ring-[#008b5c] dark:border-[#2c4a3e]">
                        <span class="text-[12px] text-[#003f32] dark:text-[#e6f2ec]">Projetor</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <a href="{{ route('contact.dashboard') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-[#2c4a3e] dark:bg-[#0c221a] dark:text-[#e6f2ec] dark:hover:bg-[#16352a]">
                    Cancelar
                </a>
                <button type="submit" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#008b5c] px-6 py-2 text-xs font-bold text-white transition hover:bg-[#006e49] disabled:cursor-not-allowed disabled:opacity-50 dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                    <span wire:loading.remove>Salvar Alterações</span>
                    <span wire:loading>Salvando...</span>
                </button>
            </div>
        </form>
    </div>
</div>
