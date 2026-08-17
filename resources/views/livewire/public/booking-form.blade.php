<div>
    @if($showSuccess)
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            Reserva enviada com sucesso! Aguarde aprovação do administrador.
        </div>
    @endif

    @if($showError)
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ $errorMessage }}
        </div>
    @endif

    <form wire:submit="submit" class="space-y-6">
        <div class="grid grid-cols-2 gap-8">
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

        <fieldset>
            <legend class="mb-4 text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Escolha a sala</legend>

            <div class="grid grid-cols-2 gap-4">
                <label class="flex items-center gap-4 border border-[#008b5c] bg-[#eef7f2] p-5 cursor-pointer dark:border-[#00a477] dark:bg-[#0c221a]">
                    <input wire:model="roomId" value="1" type="radio" name="room" checked class="accent-[#008b5c] dark:accent-[#4fd1a5]">
                    <span class="flex flex-1 flex-col">
                        <strong class="text-[13px] text-[#003f32] dark:text-[#e6f2ec]">Sala Horizonte</strong>
                        <small class="mt-1 text-[11px] text-[#70847d] dark:text-[#8aa39a]">Capacidade para 60 pessoas</small>
                    </span>
                    <b class="text-[13px] text-[#008b5c] dark:text-[#4fd1a5]">60</b>
                </label>

                <label class="flex items-center gap-4 border border-[#d2ded9] bg-white p-5 cursor-pointer dark:border-[#16352a] dark:bg-[#0c221a]">
                    <input wire:model="roomId" value="2" type="radio" name="room" class="accent-[#008b5c] dark:accent-[#4fd1a5]">
                    <span class="flex flex-1 flex-col">
                        <strong class="text-[13px] text-[#003f32] dark:text-[#e6f2ec]">Sala Ipê</strong>
                        <small class="mt-1 text-[11px] text-[#70847d] dark:text-[#8aa39a]">Capacidade para 30 pessoas</small>
                    </span>
                    <b class="text-[13px] text-[#008b5c] dark:text-[#4fd1a5]">30</b>
                </label>
            </div>
        </fieldset>

        <div class="grid grid-cols-3 gap-8">
            <label>
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Data do evento</span>
                <input wire:model="date" type="date" class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5] dark:[color-scheme:dark]">
                @error('date') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>

            <label>
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Horário de início</span>
                <input wire:model="startTime" type="time" class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5] dark:[color-scheme:dark]">
                @error('startTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>

            <label>
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Horário de término</span>
                <input wire:model="endTime" type="time" class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5] dark:[color-scheme:dark]">
                @error('endTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>
        </div>

        <div class="flex items-end justify-between border-b border-[#d6e1dc] pb-6 dark:border-[#16352a]">
            <label>
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Previsão de público</span>
                <span class="flex items-center border-b border-[#b8c9c2] dark:border-[#2c4a3e]">
                    <input wire:model="attendeesCount" type="number" min="1" max="60" value="1"
                        class="h-[46px] w-[70px] bg-transparent text-[15px] text-[#003f32] outline-none dark:text-[#e6f2ec]">
                    @error('attendeesCount') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    <small class="pr-2 text-[11px] text-[#70847d] dark:text-[#8aa39a]">pessoas</small>
                </span>
            </label>

            <p class="m-0 text-[11px] text-[#008b5c] dark:text-[#4fd1a5]">
                ✓ Dentro da capacidade da Sala Horizonte
            </p>
        </div>

        <fieldset>
            <legend class="mb-5 text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">
                Serviços adicionais
                <small class="ml-2 font-normal text-[#70847d] dark:text-[#8aa39a]">resposta obrigatória</small>
            </legend>

            <div class="grid grid-cols-3 gap-5">
                <fieldset class="border border-[#d6e1dc] p-5 dark:border-[#16352a] dark:bg-[#0c221a]/40">
                    <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Coffee break</legend>
                    <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="coffeeBreak" type="radio" name="coffee" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="coffeeBreak" type="radio" name="coffee" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Não</label>
                    </div>
                </fieldset>

                <fieldset class="border border-[#d6e1dc] p-5 dark:border-[#16352a] dark:bg-[#0c221a]/40">
                    <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Sonorização</legend>
                    <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="sonorizacao" type="radio" name="sound" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="sonorizacao" type="radio" name="sound" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Não</label>
                    </div>
                </fieldset>

                <fieldset class="border border-[#d6e1dc] p-5 dark:border-[#16352a] dark:bg-[#0c221a]/40">
                    <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Transmissão</legend>
                    <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="transmissao" type="radio" name="streaming" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="transmissao" type="radio" name="streaming" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Não</label>
                    </div>
                </fieldset>
            </div>
        </fieldset>

        <div class="flex items-center justify-between gap-10 border-t border-[#d6e1dc] pt-7 dark:border-[#16352a]">
            <p class="m-0 max-w-[550px] text-[11px] leading-5 text-[#70847d] dark:text-[#8aa39a]">
                Ao enviar, o pedido ficará pendente até a análise do gestor.
                Somente pedidos autorizados aparecem na agenda.
            </p>

            <button type="submit"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-not-allowed"
                class="flex h-[46px] items-center gap-6 bg-[#008b5c] px-7 text-[12px] font-bold text-white hover:bg-[#006e49] transition-colors dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                <span wire:loading.remove>Enviar solicitação <span>→</span></span>
                <span wire:loading>Enviando...</span>
            </button>
        </div>
    </form>
</div>
  