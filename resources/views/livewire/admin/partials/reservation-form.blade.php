<form wire:submit="save">
    <div class="grid grid-cols-1 gap-6 px-6 py-6 lg:grid-cols-3">

        {{-- Coluna esquerda: formulario --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Secao: Dados do responsavel --}}
            <section>
                <div class="mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#008b5c] dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h4 class="text-[13px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Dados do responsável</h4>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Nome do responsável <span class="text-red-500">*</span></label>
                        <input wire:model="requesterName" type="text" placeholder="Ex.: Ana Martins"
                            class="h-10.5 w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none placeholder:text-[#70847d] focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d] dark:focus:border-[#4fd1a5]">
                        @error('requesterName') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Nome do evento <span class="text-red-500">*</span></label>
                        <input wire:model="eventName" type="text" placeholder="Ex.: Encontro de lideranças"
                            class="h-10.5 w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none placeholder:text-[#70847d] focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d] dark:focus:border-[#4fd1a5]">
                        @error('eventName') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">E-mail do solicitante <span class="text-red-500">*</span></label>
                    <input wire:model="requesterEmail" type="email" placeholder="nome@instituicao.gov.br"
                        class="h-10.5 w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none placeholder:text-[#70847d] focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:placeholder:text-[#70847d] dark:focus:border-[#4fd1a5]">
                    @error('requesterEmail') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </section>

            {{-- Secao: Sala e horario --}}
            <section>
                <div class="mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#008b5c] dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h4 class="text-[13px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Sala e horário</h4>
                </div>

                {{-- Slide de salas, igual ao formulario publico de reserva --}}
                <div x-data="{
                    roomStep: 0,
                    totalRooms: {{ count($rooms) }},
                    perPage: 3,
                    get maxStep() { return Math.ceil(this.totalRooms / this.perPage) - 1 },
                    prev() { this.roomStep = this.roomStep > 0 ? this.roomStep - 1 : this.maxStep },
                    next() { this.roomStep = this.roomStep < this.maxStep ? this.roomStep + 1 : 0 }
                }">
                    <label class="mb-2 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Escolha a sala <span class="text-red-500">*</span></label>

                    <div class="relative">
                        <button type="button" @click="prev"
                            class="absolute -left-4 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-[#d2ded9] bg-white text-[#003f32] shadow-sm transition hover:border-[#008b5c] hover:bg-[#eef7f2] dark:border-[#2c4a3e] dark:bg-[#0c221a] dark:text-[#e6f2ec] dark:hover:border-[#00a477] dark:hover:bg-[#16352a]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" @click="next"
                            class="absolute -right-4 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-[#d2ded9] bg-white text-[#003f32] shadow-sm transition hover:border-[#008b5c] hover:bg-[#eef7f2] dark:border-[#2c4a3e] dark:bg-[#0c221a] dark:text-[#e6f2ec] dark:hover:border-[#00a477] dark:hover:bg-[#16352a]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>

                        <div class="overflow-hidden">
                            <div class="flex transition-transform duration-300" :style="'transform: translateX(-' + (roomStep * 100) + '%)'">
                                @foreach($rooms as $index => $room)
                                    @if($index % 3 === 0)
                                        <div class="w-full shrink-0 px-1">
                                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    @endif
                                                <label class="flex items-center gap-3 rounded-lg border border-[#d2ded9] bg-white p-3 cursor-pointer transition-all duration-200 hover:border-[#008b5c] hover:bg-[#eef7f2] has-checked:border-[#008b5c] has-checked:bg-[#eef7f2] has-checked:shadow-md dark:border-[#2c4a3e] dark:bg-[#0c221a] dark:hover:border-[#00a477] dark:hover:bg-[#16352a] dark:has-checked:border-[#00a477] dark:has-checked:bg-[#0c221a]">
                                                    <input wire:model.live="roomId" value="{{ $room->id }}" type="radio" name="admin_room" class="accent-[#008b5c] dark:accent-[#4fd1a5]">
                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg" data-color="{{ $room->theme_color }}" style="background-color: color-mix(in srgb, var(--swatch, {{ $room->theme_color }}) 20%, transparent);">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" data-color="{{ $room->theme_color }}" style="color: var(--swatch, {{ $room->theme_color }});" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                        </svg>
                                                    </div>
                                                    <span class="flex flex-1 flex-col">
                                                        <strong class="text-[13px] text-[#003f32] dark:text-[#e6f2ec]">{{ $room->name }}</strong>
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

                    @if(count($rooms) > 3)
                        <div class="mt-3 flex items-center justify-center gap-2">
                            @foreach(range(0, ceil(count($rooms) / 3) - 1) as $i)
                                <button type="button" @click="roomStep = {{ $i }}" class="h-2 w-2 rounded-full transition-all" :class="roomStep === {{ $i }} ? 'bg-[#008b5c] w-6' : 'bg-[#d2ded9] dark:bg-[#2c4a3e]'"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
                @error('roomId') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Data do evento <span class="text-red-500">*</span></label>
                        <input wire:model.live="date" type="date"
                            class="h-10.5 w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:scheme-dark">
                        @error('date') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Horário de início <span class="text-red-500">*</span></label>
                        <input wire:model.live="startTime" type="time"
                            class="h-10.5 w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:scheme-dark">
                        @error('startTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Horário de término <span class="text-red-500">*</span></label>
                        <input wire:model.live="endTime" type="time"
                            class="h-10.5 w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec] dark:scheme-dark">
                        @error('endTime') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Previsão de público <span class="text-red-500">*</span></label>
                        <input wire:model="attendeesCount" type="number" min="1"
                            class="h-10.5 w-full rounded-lg border border-[#b8c9c2] bg-transparent px-3 text-[13px] text-[#003f32] outline-none focus:border-[#008b5c] focus:ring-1 focus:ring-[#008b5c] dark:border-[#2c4a3e] dark:bg-[#081712] dark:text-[#e6f2ec]">
                        @error('attendeesCount') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                @if($conflictError)
                    <div class="mt-4 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-950/40 dark:text-red-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        {{ $conflictError }}
                    </div>
                @endif
            </section>

            {{-- Secao: Descricao --}}
            <section>
                <div class="mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#008b5c] dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h4 class="text-[13px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Descrição</h4>
                </div>
                <div class="overflow-hidden rounded-lg border border-[#b8c9c2] dark:border-[#2c4a3e]">
                    <div class="flex items-center gap-1 border-b border-[#b8c9c2] bg-slate-50 px-3 py-1.5 dark:border-[#2c4a3e] dark:bg-[#0a1a14]">
                        <button type="button" onclick="document.execCommand('bold')" title="Negrito"
                            class="rounded p-1 text-slate-500 hover:bg-slate-200 dark:text-[#8aa39a] dark:hover:bg-[#16352a]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"/></svg>
                        </button>
                        <button type="button" onclick="document.execCommand('italic')" title="Itálico"
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
                        data-placeholder="Descrição do evento (opcional)"
                        x-data="{ val: @js($description) }"
                        x-init="$el.innerHTML = val"
                        @input="$wire.set('description', $el.innerHTML)"
                        class="min-h-15 max-h-37.5 overflow-y-auto px-3 py-2.5 text-[13px] text-[#003f32] outline-none empty:before:text-[#70847d] empty:before:content-[attr(data-placeholder)] dark:text-[#e6f2ec] dark:empty:before:text-[#5a7068]"></div>
                </div>
            </section>

            {{-- Secao: Servicos Adicionais --}}
            <section>
                <div class="mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#008b5c] dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h4 class="text-[13px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Serviços adicionais</h4>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <fieldset class="border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#0c221a]/40">
                        <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Coffee break</legend>
                        <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="coffeeBreak" type="radio" name="admin_coffee" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="coffeeBreak" type="radio" name="admin_coffee" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Não</label>
                        </div>
                    </fieldset>

                    <fieldset class="border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#0c221a]/40">
                        <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Sonorização</legend>
                        <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="sonorizacao" type="radio" name="admin_sound" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="sonorizacao" type="radio" name="admin_sound" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Não</label>
                        </div>
                    </fieldset>

                    <fieldset class="border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#0c221a]/40">
                        <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Transmissão</legend>
                        <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="transmissao" type="radio" name="admin_streaming" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="transmissao" type="radio" name="admin_streaming" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Não</label>
                        </div>
                    </fieldset>

                    <fieldset class="border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#0c221a]/40">
                        <legend class="px-2 text-[11px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Projetor</legend>
                        <div class="mt-2 flex gap-4 text-[11px] text-[#003f32] dark:text-[#e6f2ec]">
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="projetor" type="radio" name="admin_projector" value="1" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Sim</label>
                            <label class="flex items-center gap-2 cursor-pointer"><input wire:model="projetor" type="radio" name="admin_projector" value="0" class="accent-[#008b5c] dark:accent-[#4fd1a5]"> Não</label>
                        </div>
                    </fieldset>
                </div>
            </section>

            {{-- Secao: Anexo --}}
            <section>
                <div class="mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#008b5c] dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 10-5.656-5.656l-6.415 6.585a6 6 0 108.485 8.486L20.5 13" />
                    </svg>
                    <h4 class="text-[13px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Anexos / Recibo</h4>
                </div>
                <div class="rounded-lg border border-[#d6e1dc] p-4 dark:border-[#16352a] dark:bg-[#081712]/50">
                    @if($existingAttachmentPath && ! $removeExistingAttachment && ! $attachment)
                        <div class="flex items-center justify-between gap-3 rounded-lg border border-[#b8c9c2] bg-white px-3 py-2.5 dark:border-[#2c4a3e] dark:bg-[#081712]">
                            <a href="{{ Storage::disk('public')->url($existingAttachmentPath) }}" target="_blank" class="flex items-center gap-2 text-[13px] font-medium text-[#003f32] hover:underline dark:text-[#4fd1a5]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 10-5.656-5.656l-6.415 6.585a6 6 0 108.485 8.486L20.5 13" />
                                </svg>
                                {{ $existingAttachmentName }}
                            </a>
                            <button type="button" wire:click="markAttachmentForRemoval" class="text-xs font-semibold text-red-600 hover:underline dark:text-red-400">Remover</button>
                        </div>
                    @else
                        <input type="file" wire:model="attachment" accept=".pdf,.png,.jpg,.jpeg"
                            class="w-full text-[13px] text-[#003f32] dark:text-[#e6f2ec] file:mr-3 file:rounded-lg file:border-0 file:bg-[#008b5c] file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white file:hover:bg-[#006e49] dark:file:bg-[#00a477]">
                        <p class="mt-1 text-[11px] text-[#70847d]">PDF, PNG ou JPG, até 5MB.</p>
                        <div wire:loading wire:target="attachment" class="mt-1 text-[11px] text-[#70847d]">Enviando...</div>
                    @endif
                    @error('attachment') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </section>
        </div>

        {{-- Coluna direita: dica + resumo --}}
        <div class="space-y-6">
            {{-- Dica --}}
            <div class="rounded-xl border border-[#dceee7] bg-[#eef7f2] p-4 dark:border-[#16352a] dark:bg-[#0c221a]">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-[#008b5c] dark:bg-[#081712] dark:text-[#4fd1a5]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                    </svg>
                </div>
                <p class="mt-3 text-[12px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Dica</p>
                <p class="mt-1 text-[12px] text-[#3d5a4e] dark:text-[#8aa39a]">Após criar a reserva, o solicitante recebe automaticamente um e-mail de confirmação com todos os detalhes do evento.</p>
            </div>

            {{-- Resumo da reserva --}}
            <div class="rounded-xl border border-[#d6e1dc] bg-white p-4 dark:border-[#16352a] dark:bg-[#081712]">
                <div class="mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#008b5c] dark:text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-5 8l1.5 1.5L15 11" />
                    </svg>
                    <h4 class="text-[13px] font-semibold text-[#003f32] dark:text-[#e6f2ec]">Resumo da reserva</h4>
                </div>
                <dl class="space-y-2.5 text-[12px]">
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-[#70847d] dark:text-[#8aa39a]">Data</dt>
                        <dd class="font-medium text-[#003f32] dark:text-[#e6f2ec]">{{ $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-[#70847d] dark:text-[#8aa39a]">Horário</dt>
                        <dd class="font-medium text-[#003f32] dark:text-[#e6f2ec]">{{ $startTime && $endTime ? "{$startTime} - {$endTime}" : '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-[#70847d] dark:text-[#8aa39a]">Sala</dt>
                        <dd class="font-medium text-[#003f32] dark:text-[#e6f2ec]">{{ $roomId ? optional($rooms->firstWhere('id', (int) $roomId))->name ?? '—' : '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <dt class="text-[#70847d] dark:text-[#8aa39a]">Responsável</dt>
                        <dd class="font-medium text-[#003f32] dark:text-[#e6f2ec]">{{ $requesterName ?: '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-[#d6e1dc] dark:border-[#16352a]">
                        <dt class="text-[#70847d] dark:text-[#8aa39a]">Status</dt>
                        <dd>
                            @if($reservationId === 0)
                                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-medium text-amber-700 dark:bg-amber-950/50 dark:text-amber-400">Pendente</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-medium text-slate-700 dark:bg-[#16352a] dark:text-[#8aa39a]">Em edição</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    {{-- Acoes --}}
    <div class="flex justify-end gap-3 border-t border-[#d6e1dc] px-6 py-4 dark:border-[#16352a]">
        @if($mode === 'page')
            <a href="{{ route('admin.reservations.index') }}"
                class="rounded-lg border border-[#b8c9c2] px-4 py-2 text-sm font-medium text-[#003f32] hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#e6f2ec]">
                Cancelar
            </a>
        @else
            <button type="button" wire:click="close"
                class="rounded-lg border border-[#b8c9c2] px-4 py-2 text-sm font-medium text-[#003f32] hover:bg-slate-50 dark:border-[#2c4a3e] dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#e6f2ec]">
                Cancelar
            </button>
        @endif
        <button type="submit"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-60 cursor-not-allowed"
            @if($conflictError) disabled @endif
            class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white transition-colors @if($conflictError) cursor-not-allowed bg-slate-400 dark:bg-slate-600 @else bg-[#008b5c] hover:bg-[#006e49] dark:bg-[#00a477] dark:hover:bg-[#008b5c] @endif">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span wire:loading.remove>{{ $reservationId === 0 ? 'Criar Reserva' : 'Salvar Alterações' }}</span>
            <span wire:loading>{{ $reservationId === 0 ? 'Criando...' : 'Salvando...' }}</span>
        </button>
    </div>
</form>
