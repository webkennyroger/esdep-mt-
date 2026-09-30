@php
    $rooms = \App\Models\Room::where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get()
        ->each(fn ($room) => $room->theme_color ??= '#008b5c');
@endphp

@if($rooms->isEmpty())
    <div class="flex h-56 items-center justify-center rounded-2xl bg-[#0c221a] ring-1 ring-white/10">
        <p class="text-sm text-white/60">Nenhuma sala disponível no momento.</p>
    </div>
@else
    {{-- Versao mobile: lista com rolagem horizontal, sem o efeito 3D --}}
    <div class="flex gap-4 overflow-x-auto pb-2 -mx-4 px-4 snap-x snap-mandatory lg:hidden">
        @foreach($rooms as $room)
            <div class="relative h-56 w-48 shrink-0 snap-start overflow-hidden rounded-2xl ring-1 ring-white/10"
                style="background: linear-gradient(to bottom right, {{ $room->theme_color }}, {{ \App\Support\TailwindColors::darken($room->theme_color, 0.35) }});">
                <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/30 to-transparent"></div>
                <div class="absolute bottom-4 left-4 right-4">
                    <p class="text-base font-semibold text-white tracking-tight mb-1">{{ $room->name }}</p>
                    <p class="text-xs text-white/70">ATÉ {{ $room->capacity }} PESSOAS</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="hidden lg:flex lg:justify-end">
        <div class="relative flex w-full max-w-175 items-center justify-center" style="perspective: 1200px;">
            <!-- SETA ESQUERDA -->
            <button type="button" id="room-prev"
                class="absolute left-2 z-20 inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/50 ring-1 ring-white/10 hover:bg-white/5 transition dark:bg-white/10"
                title="Anterior">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- SETA DIREITA -->
            <button type="button" id="room-next"
                class="absolute right-2 z-20 inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/50 ring-1 ring-white/10 hover:bg-white/5 transition dark:bg-white/10"
                title="Próximo">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- CAROUSEL -->
            <div class="relative w-full h-125 flex items-center justify-center overflow-visible">
                <div id="rooms-carousel" class="flex transition-transform duration-500 ease-out absolute top-0 right-0 bottom-0 left-0 items-center justify-center" style="transform-style: preserve-3d;">

                    @foreach($rooms as $room)
                        <div class="neon-card absolute w-80 h-115 rounded-2xl overflow-hidden ring-1 ring-white/10 transition-all duration-500" data-index="{{ $loop->index }}">
                            <div class="absolute inset-0" style="background: linear-gradient(to bottom right, {{ $room->theme_color }}, {{ \App\Support\TailwindColors::darken($room->theme_color, 0.35) }});"></div>
                            <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/30 to-transparent"></div>
                            <div class="absolute top-6 left-6">
                                <span class="font-mono text-[10px] font-bold tracking-[0.15em] text-white/70">{{ sprintf('%02d', $loop->iteration) }}</span>
                            </div>
                            <div class="absolute top-6 right-6">
                                <span class="text-[15px] text-white/70">↗</span>
                            </div>
                            <div class="absolute top-16 left-6 right-6">
                                <div class="grid grid-cols-4 gap-1.75">
                                    @for($i = 0; $i < 16; $i++)
                                        <i class="h-9.75 border {{ in_array($i, [5, 8, 11, 14]) ? 'border-white/15 bg-white/20' : 'border-white/65' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <div class="absolute bottom-8 left-8 right-8">
                                <p class="text-xl font-semibold text-white tracking-tight mb-1">{{ $room->name }}</p>
                                <p class="text-sm text-white/70">ATÉ {{ $room->capacity }} PESSOAS</p>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var carousel = document.getElementById('rooms-carousel');
            var prevBtn = document.getElementById('room-prev');
            var nextBtn = document.getElementById('room-next');

            if (carousel && prevBtn && nextBtn) {
                var cards = carousel.querySelectorAll('.neon-card');
                var total = cards.length;
                var currentIndex = Math.min(2, Math.max(0, total - 1));

                function updateCarousel() {
                    cards.forEach(function(card, i) {
                        var relativePos = i - currentIndex;
                        var transform = '';
                        var opacity = 1;
                        var filter = 'brightness(1)';
                        var zIndex = 1;
                        var ringClass = 'ring-1 ring-white/10';
                        var shadow = '';

                        if (relativePos === 0) {
                            transform = 'translateX(0) scale(1) rotateY(0deg)';
                            opacity = 1;
                            zIndex = 10;
                            ringClass = 'ring-2 ring-[#4fd1a5]/40';
                            shadow = '0 20px 60px rgba(79,209,165,0.3)';
                        } else if (relativePos === -1) {
                            transform = 'translateX(-160px) scale(0.9) rotateY(12deg)';
                            opacity = 0.65;
                            filter = 'brightness(0.75)';
                            zIndex = 5;
                        } else if (relativePos === 1) {
                            transform = 'translateX(160px) scale(0.9) rotateY(-12deg)';
                            opacity = 0.65;
                            filter = 'brightness(0.75)';
                            zIndex = 5;
                        } else if (relativePos === -2) {
                            transform = 'translateX(-300px) scale(0.82) rotateY(22deg)';
                            opacity = 0.35;
                            filter = 'brightness(0.6)';
                            zIndex = 2;
                        } else if (relativePos === 2) {
                            transform = 'translateX(300px) scale(0.82) rotateY(-22deg)';
                            opacity = 0.35;
                            filter = 'brightness(0.6)';
                            zIndex = 2;
                        } else {
                            transform = 'translateX(' + (relativePos * 160) + 'px) scale(0.75) rotateY(' + (-relativePos * 15) + 'deg)';
                            opacity = 0;
                            filter = 'brightness(0.5)';
                            zIndex = 1;
                        }

                        card.style.transform = transform;
                        card.style.opacity = opacity;
                        card.style.filter = filter;
                        card.style.zIndex = zIndex;
                        card.style.boxShadow = shadow;
                        card.className = 'neon-card absolute w-80 h-[460px] rounded-2xl overflow-hidden ' + ringClass + ' ' + (relativePos === 0 ? 'bg-[#4fd1a5]/10' : '') + ' transition-all duration-500';
                    });
                }

                prevBtn.addEventListener('click', function() {
                    currentIndex = (currentIndex - 1 + total) % total;
                    updateCarousel();
                });

                nextBtn.addEventListener('click', function() {
                    currentIndex = (currentIndex + 1) % total;
                    updateCarousel();
                });

                updateCarousel();
            }
        });
    </script>
@endif
