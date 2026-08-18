<div x-data="{
    roomStep: 0,
    totalRooms: {{ count($rooms) }},
    perPage: 3,
    get maxStep() { return Math.ceil(this.totalRooms / this.perPage) - 1 },
    prev() { this.roomStep = this.roomStep > 0 ? this.roomStep - 1 : this.maxStep },
    next() { this.roomStep = this.roomStep < this.maxStep ? this.roomStep + 1 : 0 }
}">
    <form wire:submit="submit" class="space-y-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8">
            <label class="block">
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Nome do responsavel</span>
                <input wire:model="requesterName" required placeholder="Ex.: Ana Martins"
                    class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent px-0 text-[13px] text-[#003f32] outline-none placeholder:text-[#70847d] focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d] dark:focus:border-[#4fd1a5]">
                    @error('requesterName') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>

            <label class="block">
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Nome do evento</span>
                <input wire:model="eventName" required placeholder="Ex.: Encontro de liderancas"
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

        {{-- Sala Slider --}}
        <fieldset>
            <legend class="mb-4 text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Escolha a sala</legend>

            <div class="relative">
                <button type="button" @click="prev"
                    class="absolute -left-4 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-[#d2ded9] bg-white text-[#003f32] shadow-sm transition hover:bg-[#eef7f2] hover:border-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#0c221a] dark:text-[#e6f2ec] dark:hover:bg-[#16352a] dark:hover:border-[#00a477]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button" @click="next"
                    class="absolute -right-4 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-[#d2ded9] bg-white text-[#003f32] shadow-sm transition hover:bg-[#eef7f2] hover:border-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#0c221a] dark:text-[#e6f2ec] dark:hover:bg-[#16352a] dark:hover:border-[#00a477]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>

                <div class="overflow-hidden">
                    <div class="flex transition-transform duration-300" :style="'transform: translateX(-' + (roomStep * 100) + '%)'">
                        @foreach($rooms as $index => $room)
                            @if($index % 3 === 0)
                                <div class="w-full shrink-0 px-1">
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            @endif
                                        <label class="flex items-center gap-3 rounded-xl border border-[#d2ded9] bg-white p-4 cursor-pointer transition-all duration-200 has-[:checked]:border-[#008b5c] has-[:checked]:bg-[#eef7f2] has-[:checked]:shadow-md dark:border-[#2c4a3e] dark:bg-[#0c221a] dark:has-[:checked]:border-[#00a477] dark:has-[:checked]:bg-[#0c221a]">
                                            <input wire:model="roomId" value="{{ $room->id }}" type="radio" name="room" class="accent-[#008b5c] dark:accent-[#4fd1a5]">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg" style="background-color: {{ $room->theme_color }}20;">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="color: {{ $room->theme_color }};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                            </div>
                                            <span class="flex flex-1 flex-col">
                                                <strong class="text-[13px] text-[#003f32] dark:text-[#e6f2ec]">{{ $room->name }}</strong>
                                                <small class="mt-0.5 text-[11px] text-[#70847d] dark:text-[#8aa39a]">{{ $room->description }}</small>
                                                <small class="text-[10px] text-[#008b5c] dark:text-[#4fd1a5]">{{ $room->capacity }} pessoas</small>
                                            </span>
                                        </label>
                            @if($index % 3 === 2 || $loop->last)
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-3 flex items-center justify-center gap-2">
                @foreach(range(0, ceil(count($rooms) / 3) - 1) as $i)
                    <button type="button" @click="roomStep = {{ $i }}" class="h-2 w-2 rounded-full transition-all" :class="roomStep === {{ $i }} ? 'bg-[#008b5c] w-6' : 'bg-[#d2ded9] dark:bg-[#2c4a3e]'"></button>
                @endforeach
            </div>
        </fieldset>

        {{-- Data, Horarios, Publico --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-4 sm:gap-4">
            <label>
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Data do evento</span>
                <input wire:model="date" type="date"
                    class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5] dark:[color-scheme:dark]">
                @error('date') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>

            <label>
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Horario de inicio</span>
                <input wire:model="startTime" type="time"
                    class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5] dark:[color-scheme:dark]">
                @error('startTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>

            <label>
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Horario de termino</span>
                <input wire:model="endTime" type="time"
                    class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5] dark:[color-scheme:dark]">
                @error('endTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>

            <label>
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Previsao de publico</span>
                <span class="flex h-[46px] items-center border-b border-[#b8c9c2] dark:border-[#2c4a3e]">
                    <input wire:model="attendeesCount" type="number" min="1" max="60" value="1"
                        class="h-full w-[70px] bg-transparent text-[15px] text-[#003f32] outline-none dark:text-[#e6f2ec]">
                    <small class="pr-2 text-[11px] text-[#70847d] dark:text-[#8aa39a]">pessoas</small>
                </span>
                @error('attendeesCount') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>
        </div>

        @if($conflictError)
            <div class="flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                {{ $conflictError }}
            </div>
        @endif

        <label class="block">
            <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Descricao do evento</span>
            <textarea wire:model="description" rows="3" placeholder="Descricao do evento (opcional)"
                class="w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 py-2.5 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#5a7068] dark:focus:border-[#4fd1a5]"></textarea>
        </label>

        <fieldset>
            <legend class="mb-5 text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">
                Servicos adicionais
                <small class="ml-2 font-normal text-[#70847d] dark:text-[#8aa39a]">resposta obrigatoria</small>
            </legend>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-4">
                <fieldset class="border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#0c221a]/40 sm:p-5">
                    <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Coffee break</legend>
                    <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="coffeeBreak" type="radio" name="coffee" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="coffeeBreak" type="radio" name="coffee" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Nao</label>
                    </div>
                </fieldset>

                <fieldset class="border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#0c221a]/40 sm:p-5">
                    <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Sonorizacao</legend>
                    <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="sonorizacao" type="radio" name="sound" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="sonorizacao" type="radio" name="sound" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Nao</label>
                    </div>
                </fieldset>

                <fieldset class="border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#0c221a]/40 sm:p-5">
                    <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Transmissao</legend>
                    <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="transmissao" type="radio" name="streaming" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="transmissao" type="radio" name="streaming" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Nao</label>
                    </div>
                </fieldset>

                <fieldset class="border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#0c221a]/40 sm:p-5">
                    <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Projetor</legend>
                    <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="projetor" type="radio" name="only_projector" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                        <label class="flex items-center gap-2 cursor-pointer"><input wire:model="projetor" type="radio" name="only_projector" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Nao</label>
                    </div>
                </fieldset>
            </div>
        </fieldset>

        <div class="flex flex-col items-start justify-between gap-4 border-t border-[#d6e1dc] pt-7 dark:border-[#16352a] sm:flex-row sm:items-center sm:gap-10">
            <p class="m-0 max-w-[550px] text-[11px] leading-5 text-[#70847d] dark:text-[#8aa39a]">
                Ao enviar, o pedido ficara pendente ate a analise do gestor.
                Somente pedidos autorizados aparecem na agenda.
            </p>

            <button type="submit"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-not-allowed"
                @if($conflictError) disabled @endif
                class="flex h-[46px] w-full items-center justify-center gap-6 px-7 text-[12px] font-bold text-white transition-colors sm:w-auto @if($conflictError) cursor-not-allowed bg-slate-400 dark:bg-slate-600 @else bg-[#008b5c] hover:bg-[#006e49] dark:bg-[#00a477] dark:hover:bg-[#008b5c] @endif">
                <span wire:loading.remove>Enviar solicitacao <span>&rarr;</span></span>
                <span wire:loading>Enviando...</span>
            </button>
        </div>
    </form>
</div>
