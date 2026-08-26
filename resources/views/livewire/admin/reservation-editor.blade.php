<div x-data="{ roomStep: 0, totalSteps: {{ count($rooms) }} }">
@if($showModal)
<div class="fixed inset-0 z-50 flex items-center justify-center" x-data>
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="close"></div>
    <div class="relative mx-4 w-full max-w-2xl rounded-xl bg-white p-6 shadow-2xl dark:border dark:border-[#16352a] dark:bg-[#0c221a] overflow-y-auto max-h-[90vh]">

        <div class="mb-5 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900 dark:text-[#e6f2ec]">Editar Reserva</h3>
            <button wire:click="close" class="rounded-lg p-2.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#e6f2ec]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form wire:submit="save" class="space-y-5">

            {{-- Nome do Responsavel e Evento --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Nome do responsavel <span class="text-red-500">*</span></label>
                    <input wire:model="requesterName" type="text" placeholder="Ex.: Ana Martins"
                        class="h-[42px] w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none placeholder:text-[#70847d] focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d] dark:focus:border-[#4fd1a5]">
                    @error('requesterName') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Nome do evento <span class="text-red-500">*</span></label>
                    <input wire:model="eventName" type="text" placeholder="Ex.: Encontro de liderancas"
                        class="h-[42px] w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none placeholder:text-[#70847d] focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d] dark:focus:border-[#4fd1a5]">
                    @error('eventName') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- E-mail --}}
            <div>
                <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">E-mail do solicitante <span class="text-red-500">*</span></label>
                <input wire:model="requesterEmail" type="email" placeholder="nome@instituicao.gov.br"
                    class="h-[42px] w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none placeholder:text-[#70847d] focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d] dark:focus:border-[#4fd1a5]">
                @error('requesterEmail') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Sala --}}
            <div>
                <label class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Escolha a sala <span class="text-red-500">*</span></label>
                <div class="relative overflow-hidden">
                    <div class="flex transition-transform duration-300" :style="'transform: translateX(-' + (roomStep * 100) + '%)'">
                        @foreach($rooms as $index => $room)
                            <div class="w-full shrink-0 px-1 sm:w-1/3">
                                <label class="flex items-center gap-3 border border-[#d2ded9] bg-white p-4 cursor-pointer transition-all duration-200 hover:border-[#008b5c] hover:bg-[#eef7f2] has-[:checked]:border-[#008b5c] has-[:checked]:bg-[#eef7f2] has-[:checked]:shadow-md dark:border-[#2c4a3e] dark:bg-[#0c221a] dark:hover:border-[#00a477] dark:hover:bg-[#16352a] dark:has-[:checked]:border-[#00a477] dark:has-[:checked]:bg-[#0c221a]">
                                    <input wire:model.live="roomId" value="{{ $room->id }}" type="radio" name="admin_room" class="accent-[#008b5c] dark:accent-[#4fd1a5]">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg" style="background-color: {{ $room->theme_color }}20;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" style="color: {{ $room->theme_color }};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <span class="flex flex-1 flex-col">
                                        <strong class="text-[13px] text-[#003f32] dark:text-[#e6f2ec]">{{ $room->name }}</strong>
                                        <small class="mt-0.5 text-[11px] text-[#70847d] dark:text-[#8aa39a]">{{ $room->description }}</small>
                                        <small class="text-[10px] text-[#008b5c] dark:text-[#4fd1a5]">{{ $room->capacity }} pessoas</small>
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-2 flex items-center justify-center gap-2">
                    @foreach($rooms as $index => $room)
                        <button type="button" @click="roomStep = {{ $index }}" class="h-2 w-2 rounded-full transition-all" :class="roomStep === {{ $index }} ? 'bg-[#008b5c] w-6' : 'bg-[#d2ded9]'"></button>
                    @endforeach
                </div>
                @error('roomId') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Data e Horarios --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Data do evento <span class="text-red-500">*</span></label>
                    <input wire:model.live="date" type="date"
                        class="h-[42px] w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:[color-scheme:dark]">
                    @error('date') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Horario de inicio <span class="text-red-500">*</span></label>
                    <input wire:model.live="startTime" type="time"
                        class="h-[42px] w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:[color-scheme:dark]">
                    @error('startTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Horario de termino <span class="text-red-500">*</span></label>
                    <input wire:model.live="endTime" type="time"
                        class="h-[42px] w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:[color-scheme:dark]">
                    @error('endTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            @if($conflictError)
                <div class="flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    {{ $conflictError }}
                </div>
            @endif

            {{-- Participantes --}}
            <div>
                <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Previsao de publico <span class="text-red-500">*</span></label>
                <input wire:model="attendeesCount" type="number" min="1"
                    class="h-[42px] w-[120px] rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                <span class="ml-2 text-[11px] text-[#70847d] dark:text-[#8aa39a]">pessoas</span>
                @error('attendeesCount') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- Descricao --}}
            <div>
                <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Descricao</label>
                <div class="overflow-hidden rounded-lg border border-[#b8c9c2] dark:border-[#2c4a3e]">
                    <div class="flex items-center gap-1 border-b border-[#b8c9c2] bg-slate-50 px-3 py-1.5 dark:border-[#2c4a3e] dark:bg-[#0a1a14]">
                        <button type="button" onclick="document.execCommand('bold')" title="Negrito"
                            class="rounded p-1 text-slate-500 hover:bg-slate-200 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"/></svg>
                        </button>
                        <button type="button" onclick="document.execCommand('italic')" title="Italico"
                            class="rounded p-1 text-slate-500 hover:bg-slate-200 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 4h4m-2 0l-4 16m-2 0h4m2-16l4 16"/></svg>
                        </button>
                        <button type="button" onclick="document.execCommand('underline')" title="Sublinhado"
                            class="rounded p-1 text-slate-500 hover:bg-slate-200 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 4v7a5 5 0 0010 0V4M5 21h14"/></svg>
                        </button>
                        <div class="mx-1 h-4 w-px bg-slate-300 dark:bg-[#2c4a3e]"></div>
                        <button type="button" onclick="document.execCommand('insertUnorderedList')" title="Lista"
                            class="rounded p-1 text-slate-500 hover:bg-slate-200 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
                        </button>
                    </div>
                    <div id="description-editor"
                        wire:ignore
                        contenteditable="true"
                        data-placeholder="Descricao do evento (opcional)"
                        x-data="{ val: @js($description) }"
                        x-init="$el.innerHTML = val"
                        @input="$wire.set('description', $el.innerHTML)"
                        class="min-h-[60px] max-h-[150px] overflow-y-auto px-3 py-2.5 text-[13px] text-[#003f32] outline-none empty:before:text-[#70847d] empty:before:content-[attr(data-placeholder)] dark:text-[#e6f2ec] dark:empty:before:text-[#5a7068]"></div>
                </div>
            </div>

            {{-- Servicos Adicionais --}}
            <fieldset class="rounded-lg border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#081712]/50">
                <legend class="px-2 text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Servicos adicionais</legend>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <fieldset class="border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#0c221a]/40">
                        <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Coffee break</legend>
                        <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="coffeeBreak" type="radio" name="admin_coffee" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="coffeeBreak" type="radio" name="admin_coffee" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Nao</label>
                        </div>
                    </fieldset>

                    <fieldset class="border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#0c221a]/40">
                        <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Sonorizacao</legend>
                        <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="sonorizacao" type="radio" name="admin_sound" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="sonorizacao" type="radio" name="admin_sound" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Nao</label>
                        </div>
                    </fieldset>

                    <fieldset class="border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#0c221a]/40">
                        <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Transmissao</legend>
                        <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="transmissao" type="radio" name="admin_streaming" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="transmissao" type="radio" name="admin_streaming" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Nao</label>
                        </div>
                    </fieldset>

                    <fieldset class="border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#0c221a]/40">
                        <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Projetor</legend>
                        <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="projetor" type="radio" name="admin_projector" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="projetor" type="radio" name="admin_projector" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Nao</label>
                        </div>
                    </fieldset>
                </div>
            </fieldset>

            {{-- Acoes --}}
            <div class="flex justify-end gap-3 pt-3 border-t border-[#d6e1dc] dark:border-[#16352a]">
                <button type="button" wire:click="close"
                    class="rounded-lg border border-[#b8c9c2] px-4 py-2 text-sm font-medium text-[#003f32] hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#e6f2ec]">
                    Cancelar
                </button>
                <button type="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-not-allowed"
                    @if($conflictError) disabled @endif
                    class="rounded-lg px-4 py-2 text-sm font-semibold text-white transition-colors @if($conflictError) cursor-not-allowed bg-slate-400 dark:bg-slate-600 @else bg-[#008b5c] hover:bg-[#006e49] dark:bg-[#00a477] dark:hover:bg-[#008b5c] @endif">
                    <span wire:loading.remove>Salvar Alteracoes</span>
                    <span wire:loading>Salvando...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif
</div>
