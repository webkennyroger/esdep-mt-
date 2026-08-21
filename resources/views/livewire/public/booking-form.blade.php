<div x-data="{
    roomStep: 0,
    totalRooms: {{ count($rooms) }},
    perPage: 3,
    get maxStep() { return Math.ceil(this.totalRooms / this.perPage) - 1 },
    prev() { this.roomStep = this.roomStep > 0 ? this.roomStep - 1 : this.maxStep },
    next() { this.roomStep = this.roomStep < this.maxStep ? this.roomStep + 1 : 0 }
}">
    @if($standalone)
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-[#e6f2ec]">Nova Reserva</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-[#8aa39a]">Preencha os dados para solicitar uma reserva de sala.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
    @endif

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
                                            <input wire:model.live="roomId" value="{{ $room->id }}" type="radio" name="room" class="accent-[#008b5c] dark:accent-[#4fd1a5]">
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
                <input wire:model.live="date" type="date"
                    class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5] dark:[color-scheme:dark]">
                @error('date') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>

            <label>
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Horario de inicio</span>
                <input wire:model.live="startTime" type="time"
                    class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5] dark:[color-scheme:dark]">
                @error('startTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>

            <label>
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Horario de termino</span>
                <input wire:model.live="endTime" type="time"
                    class="h-[46px] w-full border-b border-[#b8c9c2] bg-transparent text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] dark:border-[#2c4a3e] dark:text-[#e6f2ec] dark:focus:border-[#4fd1a5] dark:[color-scheme:dark]">
                @error('endTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </label>

            <label>
                <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Previsao de publico</span>
                <span class="flex h-[46px] items-center border-b border-[#b8c9c2] dark:border-[#2c4a3e]">
                    <input wire:model.live="attendeesCount" type="number" min="1"
                        @if($roomId) max="{{ $rooms->firstWhere('id', $roomId)?->capacity ?? 999 }}" @endif
                        value="1"
                        class="h-full w-[70px] bg-transparent text-[15px] text-[#003f32] outline-none dark:text-[#e6f2ec]">
                    <small class="pr-2 text-[11px] text-[#70847d] dark:text-[#8aa39a]">pessoas</small>
                </span>
                @if($roomId)
                    @php $selectedRoom = $rooms->firstWhere('id', $roomId); @endphp
                    @if($selectedRoom)
                        <small class="mt-1 block text-[10px] text-[#70847d] dark:text-[#8aa39a]">Capacidade maxima: {{ $selectedRoom->capacity }} pessoas</small>
                    @endif
                @endif
                @error('attendeesCount') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                @if($capacityError)
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $capacityError }}</p>
                @endif
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

        <div class="block">
            <span class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Anexo (opcional)</span>
            <span class="mb-2 block text-[11px] text-[#70847d] dark:text-[#8aa39a]">Oficio de solicitacao ou cronograma do evento (PDF, PNG, JPG - max 5MB)</span>
            <div class="flex items-center gap-3">
                <label class="flex h-[46px] cursor-pointer items-center gap-2 rounded-lg border border-[#b8c9c2] bg-transparent px-3 transition-colors hover:border-[#008b5c] dark:border-[#2c4a3e] dark:hover:border-[#4fd1a5]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#70847d] dark:text-[#8aa39a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span class="text-[13px] text-[#003f32] dark:text-[#e6f2ec]">{{ $attachmentName ?: 'Selecionar arquivo' }}</span>
                    <input type="file" wire:model="attachment" accept=".pdf,.png,.jpg,.jpeg" class="hidden">
                </label>
                @if($attachment)
                    <button type="button" wire:click="$set('attachment', null); $set('attachmentName', '')" class="text-red-500 hover:text-red-700 dark:text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>
            @error('attachment') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

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
            <div class="flex-1">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="termsAccepted" @checked($termsAccepted) class="mt-1 accent-[#008b5c] dark:accent-[#4fd1a5]">
                    <span class="text-[12px] leading-5 text-[#70847d] dark:text-[#8aa39a]">
                        Li e aceito os 
                        <button type="button" wire:click="openTermsModal" class="font-semibold text-[#008b5c] hover:underline dark:text-[#4fd1a5]">
                            Termos de Compromisso e Regras de Uso
                        </button>
                        da ESDEP-MT.
                    </span>
                </label>
                @error('termsAccepted') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-not-allowed"
                @if($conflictError || $capacityError) disabled @endif
                class="flex h-[46px] w-full items-center justify-center gap-6 px-7 text-[12px] font-bold text-white transition-colors sm:w-auto @if($conflictError || $capacityError) cursor-not-allowed bg-slate-400 dark:bg-slate-600 @else bg-[#008b5c] hover:bg-[#006e49] dark:bg-[#00a477] dark:hover:bg-[#008b5c] @endif">
                <span wire:loading.remove>Enviar solicitacao <span>&rarr;</span></span>
                <span wire:loading>Enviando...</span>
            </button>
        </div>
    </form>
    @if($standalone)
        </div>
    @endif

    {{-- Modal Termos de Compromisso --}}
    @if($showTermsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeTermsModal"></div>
            <div class="relative w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:border dark:border-[#16352a] dark:bg-[#0c221a]">
                
                {{-- Header --}}
                <div class="relative bg-gradient-to-r from-[#003f32] to-[#008b5c] px-6 py-5">
                    <button wire:click="closeTermsModal" class="absolute right-4 top-4 rounded-lg bg-white/20 p-1.5 text-white backdrop-blur-sm hover:bg-white/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-xl font-bold text-white">Termos de Compromisso e Regras de Uso</h2>
                            <p class="mt-1 text-sm text-white/80">ESDEP-MT - Espaços de Eventos</p>
                        </div>
                    </div>
                </div>

                {{-- Conteudo --}}
                <div class="max-h-[60vh] overflow-y-auto px-6 py-5">
                    <div class="space-y-6">
                        
                        {{-- Conservacao de Equipamentos --}}
                        <div>
                            <h3 class="mb-2 flex items-center gap-2 text-sm font-bold text-[#003f32] dark:text-[#e6f2ec]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#008b5c] dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                Conservacao de Equipamentos
                            </h3>
                            <ul class="ml-7 list-disc space-y-1 text-[13px] text-[#70847d] dark:text-[#8aa39a]">
                                <li>Os equipamentos de sonorizacao, projetor e outros dispositivos devem ser utilizados com cuidado.</li>
                                <li>Qualquer dano causado por uso indevido sera de responsabilidade do solicitante.</li>
                                <li>Nao e permitido desligar ou religar equipamentos sem autorizacao do responsavel.</li>
                            </ul>
                        </div>

                        {{-- Cancelamento --}}
                        <div>
                            <h3 class="mb-2 flex items-center gap-2 text-sm font-bold text-[#003f32] dark:text-[#e6f2ec]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#008b5c] dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Cancelamento e Remarcacao
                            </h3>
                            <ul class="ml-7 list-disc space-y-1 text-[13px] text-[#70847d] dark:text-[#8aa39a]">
                                <li>Caso necessite cancelar, avise com no minimo 24 horas de antecedencia.</li>
                                <li>Cancelamentos de ultima hora podem impedir futuras reservas.</li>
                                <li>Remarcacoes estao sujeitas a disponibilidade da sala.</li>
                            </ul>
                        </div>

                        {{-- Pontualidade --}}
                        <div>
                            <h3 class="mb-2 flex items-center gap-2 text-sm font-bold text-[#003f32] dark:text-[#e6f2ec]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#008b5c] dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Pontualidade e Uso do Espaco
                            </h3>
                            <ul class="ml-7 list-disc space-y-1 text-[13px] text-[#70847d] dark:text-[#8aa39a]">
                                <li>O solicitante deve chegar no horario agendado.</li>
                                <li>A sala deve ser entregue nas condicoes em que foi encontrada.</li>
                                <li>Nao e permitido exceder o horario reservado sem autorizacao previa.</li>
                            </ul>
                        </div>

                        {{-- Limpeza --}}
                        <div>
                            <h3 class="mb-2 flex items-center gap-2 text-sm font-bold text-[#003f32] dark:text-[#e6f2ec]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#008b5c] dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Limpeza e Organizacao
                            </h3>
                            <ul class="ml-7 list-disc space-y-1 text-[13px] text-[#70847d] dark:text-[#8aa39a]">
                                <li>Mantenha o espaco limpo e organizado durante e apos o evento.</li>
                                <li>Retire todo o lixo e materiais utilizados.</li>
                                <li>O uso de alimentos e permitido apenas em areas designadas.</li>
                            </ul>
                        </div>

                        {{-- Cafe Break --}}
                        <div>
                            <h3 class="mb-2 flex items-center gap-2 text-sm font-bold text-[#003f32] dark:text-[#e6f2ec]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#008b5c] dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
                                </svg>
                                Coffee Break
                            </h3>
                            <ul class="ml-7 list-disc space-y-1 text-[13px] text-[#70847d] dark:text-[#8aa39a]">
                                <li>O servico de coffee break deve ser solicitado com antecedencia minima de 48 horas.</li>
                                <li>Alteracoes no cardapio devem ser comunicadas com 24 horas de antecedencia.</li>
                                <li>Restricoes alimentares devem ser informadas no momento da reserva.</li>
                            </ul>
                        </div>

                        {{-- Disposicoes Finais --}}
                        <div class="rounded-xl border border-[#d6e1dc] bg-[#f8faf9] p-4 dark:border-[#2c4a3e] dark:bg-[#0a1a14]">
                            <p class="text-[12px] leading-5 text-[#70847d] dark:text-[#8aa39a]">
                                <strong class="text-[#003f32] dark:text-[#e6f2ec]">Disposicoes Finais:</strong> 
                                A ESDEP-MT reserva-se o direito de cancelar reservas em caso de necessidade institucional. 
                                O descumprimento dos termos pode resultar na negacao de futuras solicitacoes.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-[#d6e1dc] px-6 py-4 dark:border-[#16352a]">
                    <button wire:click="closeTermsModal"
                        class="rounded-lg border border-[#b8c9c2] px-4 py-2 text-sm font-medium text-[#70847d] hover:bg-[#f0f5f3] dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                        Fechar
                    </button>
                    <button wire:click="acceptTerms"
                        class="rounded-lg bg-[#008b5c] px-4 py-2 text-sm font-semibold text-white hover:bg-[#006e49] dark:bg-[#00a477] dark:hover:bg-[#008b5c]">
                        Aceitar Termos
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
