<div>
    <h1 class="text-2xl font-bold">Minhas Salas</h1><p class="mt-1 text-sm text-slate-500">Espaços dos grupos pelos quais você é responsável.</p>
    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($rooms as $room)<article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-[#16352a] dark:bg-[#0c221a]">
            <div class="flex items-start justify-between"><div><p class="text-xs font-semibold uppercase text-slate-400">{{ $room->contactGroup?->name }}</p><h2 class="mt-1 text-lg font-bold">{{ $room->name }}</h2><p class="font-mono text-xs text-slate-400">{{ $room->code }}</p></div><span class="h-4 w-4 rounded-full" style="background:{{ $room->theme_color }}"></span></div>
            <p class="mt-4 text-sm text-slate-500">{{ $room->description ?: 'Sem descrição.' }}</p><div class="mt-4 flex gap-2 text-xs"><span class="rounded-full bg-slate-100 px-2.5 py-1 dark:bg-[#16352a]">{{ $room->capacity }} pessoas</span><span class="rounded-full bg-slate-100 px-2.5 py-1 dark:bg-[#16352a]">{{ $room->reservations_count }} reservas</span></div>
        </article>@empty<p class="text-sm text-slate-500">Você ainda não é responsável por nenhuma sala.</p>@endforelse
    </div>
</div>
