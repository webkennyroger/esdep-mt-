<div class="relative" x-data="{ open: false }">
    <button @click="open = !open"
        class="relative rounded-lg p-2.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1" />
        </svg>
        @if($pendingCount > 0)
            <span class="absolute right-1.5 top-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">{{ $pendingCount }}</span>
        @endif
    </button>
    <div x-show="open" @click.away="open = false" x-transition
        class="absolute right-0 mt-2 w-80 rounded-lg border border-slate-200 bg-white shadow-xl dark:border-slate-600 dark:bg-slate-800">
        <div class="border-b border-slate-200 px-4 py-3 dark:border-slate-700">
            <h3 class="font-bold text-slate-900 dark:text-white">Notificações</h3>
        </div>
        <div class="max-h-64 overflow-y-auto">
            @forelse($notifications as $notif)
                <div class="border-b border-slate-100 px-4 py-3 dark:border-slate-700">
                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $notif['event_name'] }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $notif['requester_name'] }} &middot; {{ $notif['date'] }}</p>
                    <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-xs font-medium
                        {{ $notif['status'] === 'authorized' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : '' }}
                        {{ $notif['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' : '' }}
                        {{ $notif['status'] === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' : '' }}">
                        @if($notif['status'] === 'authorized') Autorizada
                        @elseif($notif['status'] === 'pending') Pendente
                        @elseif($notif['status'] === 'rejected') Rejeitada
                        @endif
                    </span>
                </div>
            @empty
                <p class="px-4 py-6 text-center text-sm text-slate-400">Nenhuma notificação.</p>
            @endforelse
        </div>
        <a href="{{ route('admin.reservations.index') }}"
            class="block px-4 py-3 text-center text-sm font-medium text-green-700 hover:bg-slate-50 dark:text-green-400 dark:hover:bg-slate-700">
            Ver todas
        </a>
    </div>
</div>
