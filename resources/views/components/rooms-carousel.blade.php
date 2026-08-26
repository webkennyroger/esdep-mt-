{{-- Versao mobile: lista com rolagem horizontal, sem o efeito 3D --}}
<div class="flex gap-4 overflow-x-auto pb-2 -mx-4 px-4 snap-x snap-mandatory lg:hidden">
    @foreach([
        ['name' => 'Sala Horizonte', 'capacity' => 'ATÉ 60 PESSOAS', 'from' => '#006b48', 'to' => '#004d3c'],
        ['name' => 'Sala Ipê', 'capacity' => 'ATÉ 30 PESSOAS', 'from' => '#00b98b', 'to' => '#00856b'],
        ['name' => 'Sala 165', 'capacity' => 'ATÉ 10 PESSOAS', 'from' => '#00856b', 'to' => '#004d3c'],
        ['name' => 'Sede Auditório', 'capacity' => 'ATÉ 10 PESSOAS', 'from' => '#4fd1a5', 'to' => '#00856b'],
        ['name' => 'Sede Reuniões', 'capacity' => 'ATÉ 60 PESSOAS', 'from' => '#004d3c', 'to' => '#003f32'],
        ['name' => 'Sala Conselho', 'capacity' => 'ATÉ 15 PESSOAS', 'from' => '#2c4a3e', 'to' => '#1a2e26'],
    ] as $room)
        <div class="relative h-56 w-48 shrink-0 snap-start overflow-hidden rounded-2xl ring-1 ring-white/10"
            style="background: linear-gradient(to bottom right, {{ $room['from'] }}, {{ $room['to'] }});">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
            <div class="absolute bottom-4 left-4 right-4">
                <p class="text-base font-semibold text-white tracking-tight mb-1">{{ $room['name'] }}</p>
                <p class="text-xs text-white/70">{{ $room['capacity'] }}</p>
            </div>
        </div>
    @endforeach
</div>

<div class="hidden lg:flex lg:justify-end">
    <div class="relative flex w-full max-w-[700px] items-center justify-center" style="perspective: 1200px;">

        <!-- SETA ESQUERDA -->
        <button type="button" id="room-prev"
            class="absolute left-2 z-20 inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/50 ring-1 ring-white/10 hover:bg-white/5 transition dark:bg-white/10"
            title="Anterior">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <!-- CAROUSEL -->
        <div class="relative w-full h-[500px] flex items-center justify-center overflow-visible">
            <div id="rooms-carousel" class="flex transition-transform duration-500 ease-out absolute top-0 right-0 bottom-0 left-0 items-center justify-center" style="transform-style: preserve-3d;">

                <!-- 01 — HORIZONTE -->
                <div class="neon-card absolute w-80 h-[460px] rounded-2xl overflow-hidden ring-1 ring-white/10 transition-all duration-500" data-index="0">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#006b48] to-[#004d3c]"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                    <div class="absolute top-6 left-6">
                        <span class="font-mono text-[10px] font-bold tracking-[0.15em] text-white/70">01</span>
                    </div>
                    <div class="absolute top-6 right-6">
                        <span class="text-[15px] text-white/70">↗</span>
                    </div>
                    <div class="absolute top-16 left-6 right-6">
                        <div class="grid grid-cols-4 gap-[7px]">
                            <i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/15 bg-white/20"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/15 bg-white/20"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/15 bg-white/20"></i>
                            <i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/15 bg-white/20"></i><i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/15 bg-white/20"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i>
                        </div>
                    </div>
                    <div class="absolute bottom-8 left-8 right-8">
                        <p class="text-xl font-semibold text-white tracking-tight mb-1">Sala Horizonte</p>
                        <p class="text-sm text-white/70">ATÉ 60 PESSOAS</p>
                    </div>
                </div>

                <!-- 02 — IPE -->
                <div class="neon-card absolute w-80 h-[460px] rounded-2xl overflow-hidden ring-1 ring-white/10 transition-all duration-500" data-index="1">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#00b98b] to-[#00856b]"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                    <div class="absolute top-6 left-6">
                        <span class="font-mono text-[10px] font-bold tracking-[0.15em] text-[#003f32]/70">02</span>
                    </div>
                    <div class="absolute top-6 right-6">
                        <span class="text-[15px] text-[#003f32]/70">↗</span>
                    </div>
                    <div class="absolute top-16 left-6 right-6">
                        <div class="grid grid-cols-4 gap-[7px]">
                            <i class="h-[34px] border border-[#003f32]/40 bg-[#003f32]/20"></i><i class="h-[34px] border border-[#003f32]/40"></i><i class="h-[34px] border border-[#003f32]/40"></i><i class="h-[34px] border border-[#003f32]/40"></i>
                            <i class="h-[34px] border border-[#003f32]/40 bg-[#003f32]/20"></i><i class="h-[34px] border border-[#003f32]/40"></i><i class="h-[34px] border border-[#003f32]/40"></i><i class="h-[34px] border border-[#003f32]/40"></i>
                            <i class="h-[34px] border border-[#003f32]/40 bg-[#003f32]/20"></i><i class="h-[34px] border border-[#003f32]/40"></i><i class="h-[34px] border border-[#003f32]/40"></i><i class="h-[34px] border border-[#003f32]/40"></i>
                        </div>
                    </div>
                    <div class="absolute bottom-8 left-8 right-8">
                        <p class="text-xl font-semibold text-white tracking-tight mb-1">Sala Ipê</p>
                        <p class="text-sm text-white/70">ATÉ 30 PESSOAS</p>
                    </div>
                </div>

                <!-- 03 — SALA 165 -->
                <div class="neon-card absolute w-80 h-[460px] rounded-2xl overflow-hidden ring-1 ring-white/10 transition-all duration-500" data-index="2">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#00856b] to-[#004d3c]"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                    <div class="absolute top-6 left-6">
                        <span class="font-mono text-[10px] font-bold tracking-[0.15em] text-white/70">03</span>
                    </div>
                    <div class="absolute top-6 right-6">
                        <span class="text-[15px] text-white/70">↗</span>
                    </div>
                    <div class="absolute top-16 left-6 right-6">
                        <div class="grid grid-cols-3 gap-[7px]">
                            <i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/15 bg-white/20"></i><i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/15 bg-white/20"></i>
                        </div>
                    </div>
                    <div class="absolute bottom-8 left-8 right-8">
                        <p class="text-xl font-semibold text-white tracking-tight mb-1">Sala 165</p>
                        <p class="text-sm text-white/70">ATÉ 10 PESSOAS</p>
                    </div>
                </div>

                <!-- 04 — SEDE AUDITÓRIO -->
                <div class="neon-card absolute w-80 h-[460px] rounded-2xl overflow-hidden ring-1 ring-white/10 transition-all duration-500" data-index="3">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#4fd1a5] to-[#00856b]"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                    <div class="absolute top-6 left-6">
                        <span class="font-mono text-[10px] font-bold tracking-[0.15em] text-[#003f32]/70">04</span>
                    </div>
                    <div class="absolute top-6 right-6">
                        <span class="text-[15px] text-[#003f32]/70">↗</span>
                    </div>
                    <div class="absolute top-16 left-6 right-6">
                        <div class="grid grid-cols-3 gap-[7px]">
                            <i class="h-[34px] border border-[#003f32]/40 bg-[#003f32]/20"></i><i class="h-[34px] border border-[#003f32]/40"></i><i class="h-[34px] border border-[#003f32]/40"></i>
                            <i class="h-[34px] border border-[#003f32]/40"></i><i class="h-[34px] border border-[#003f32]/40 bg-[#003f32]/20"></i><i class="h-[34px] border border-[#003f32]/40"></i>
                        </div>
                    </div>
                    <div class="absolute bottom-8 left-8 right-8">
                        <p class="text-xl font-semibold text-white tracking-tight mb-1">Sede Auditório</p>
                        <p class="text-sm text-white/70">ATÉ 10 PESSOAS</p>
                    </div>
                </div>

                <!-- 05 — SEDE REUNIÕES -->
                <div class="neon-card absolute w-80 h-[460px] rounded-2xl overflow-hidden ring-1 ring-white/10 transition-all duration-500" data-index="4">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#004d3c] to-[#003f32]"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                    <div class="absolute top-6 left-6">
                        <span class="font-mono text-[10px] font-bold tracking-[0.15em] text-white/70">05</span>
                    </div>
                    <div class="absolute top-6 right-6">
                        <span class="text-[15px] text-white/70">↗</span>
                    </div>
                    <div class="absolute top-16 left-6 right-6">
                        <div class="grid grid-cols-4 gap-[7px]">
                            <i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/15 bg-white/20"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/15 bg-white/20"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/15 bg-white/20"></i>
                            <i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/15 bg-white/20"></i><i class="h-[39px] border border-white/65"></i>
                            <i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/15 bg-white/20"></i><i class="h-[39px] border border-white/65"></i><i class="h-[39px] border border-white/65"></i>
                        </div>
                    </div>
                    <div class="absolute bottom-8 left-8 right-8">
                        <p class="text-xl font-semibold text-white tracking-tight mb-1">Sede Reuniões</p>
                        <p class="text-sm text-white/70">ATÉ 60 PESSOAS</p>
                    </div>
                </div>

                <!-- 06 — CONSELHO -->
                <div class="neon-card absolute w-80 h-[460px] rounded-2xl overflow-hidden ring-1 ring-white/10 transition-all duration-500" data-index="5">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#2c4a3e] to-[#1a2e26]"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                    <div class="absolute top-6 left-6">
                        <span class="font-mono text-[10px] font-bold tracking-[0.15em] text-[#4fd1a5]/70">06</span>
                    </div>
                    <div class="absolute top-6 right-6">
                        <span class="text-[15px] text-[#4fd1a5]/70">↗</span>
                    </div>
                    <div class="absolute top-16 left-6 right-6">
                        <div class="grid grid-cols-3 gap-[7px]">
                            <i class="h-[39px] border border-[#4fd1a5]/50"></i><i class="h-[39px] border border-[#4fd1a5]/30 bg-[#4fd1a5]/15"></i><i class="h-[39px] border border-[#4fd1a5]/50"></i>
                            <i class="h-[39px] border border-[#4fd1a5]/50"></i><i class="h-[39px] border border-[#4fd1a5]/50"></i><i class="h-[39px] border border-[#4fd1a5]/30 bg-[#4fd1a5]/15"></i>
                            <i class="h-[39px] border border-[#4fd1a5]/50"></i><i class="h-[39px] border border-[#4fd1a5]/30 bg-[#4fd1a5]/15"></i><i class="h-[39px] border border-[#4fd1a5]/50"></i>
                            <i class="h-[39px] border border-[#4fd1a5]/50"></i><i class="h-[39px] border border-[#4fd1a5]/50"></i><i class="h-[39px] border border-[#4fd1a5]/30 bg-[#4fd1a5]/15"></i>
                            <i class="h-[39px] border border-[#4fd1a5]/50"></i><i class="h-[39px] border border-[#4fd1a5]/50"></i><i class="h-[39px] border border-[#4fd1a5]/30 bg-[#4fd1a5]/15"></i>
                        </div>
                    </div>
                    <div class="absolute bottom-8 left-8 right-8">
                        <p class="text-xl font-semibold text-white tracking-tight mb-1">Sala Conselho</p>
                        <p class="text-sm text-white/70">ATÉ 15 PESSOAS</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- SETA DIREITA -->
        <button type="button" id="room-next"
            class="absolute right-2 z-20 inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/50 ring-1 ring-white/10 hover:bg-white/5 transition dark:bg-white/10"
            title="Próximo">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#4fd1a5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var carousel = document.getElementById('rooms-carousel');
        var prevBtn = document.getElementById('room-prev');
        var nextBtn = document.getElementById('room-next');

        if (carousel && prevBtn && nextBtn) {
            var cards = carousel.querySelectorAll('.neon-card');
            var currentIndex = 2;
            var total = cards.length;

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
