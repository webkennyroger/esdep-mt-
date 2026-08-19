<div class="relative" x-data="{
    dropdownOpen: false,
    hasUnread: {{ $pendingCount > 0 ? 'true' : 'false' }},
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
        this.hasUnread = false;
    },
    closeDropdown() {
        this.dropdownOpen = false;
    }
}" @click.away="closeDropdown()">
    <!-- Notification Button -->
    <button class="relative flex items-center justify-center text-slate-500 transition-colors bg-slate-100 border border-slate-200 rounded-full h-11 w-11 hover:bg-slate-200 hover:text-slate-700 dark:bg-[#112a21] dark:border-[#16352a] dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]" @click="toggleDropdown()" type="button">
        <!-- Notification Badge - Orange when unread, Green when all read -->
        <span x-show="hasUnread" class="absolute right-0 top-0.5 z-1 h-2 w-2 rounded-full bg-orange-400">
            <span class="absolute inline-flex w-full h-full bg-orange-400 rounded-full opacity-75 -z-1 animate-ping"></span>
        </span>
        <span x-show="!hasUnread" class="absolute right-0 top-0.5 z-1 h-2 w-2 rounded-full bg-green-500"></span>

        <!-- Bell Icon -->
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z" fill=""></path>
        </svg>
    </button>

    <!-- Dropdown Start -->
    <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute left-4 sm:left-6 xl:left-auto xl:right-0 mt-[17px] flex h-[480px] w-[350px] flex-col rounded-2xl border border-slate-200 bg-white p-3 shadow-xl dark:border-[#16352a] dark:bg-[#0c221a] sm:w-[361px]" style="display: none;">
        <!-- Dropdown Header -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 dark:border-[#16352a]">
            <h5 class="text-lg font-semibold text-slate-900 dark:text-[#e6f2ec]">Notificacoes</h5>

            <button @click="closeDropdown()" class="text-slate-400 dark:text-[#8aa39a]" type="button">
                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z" fill=""></path>
                </svg>
            </button>
        </div>

        <!-- Notification List -->
        <ul class="flex flex-col h-auto overflow-y-auto custom-scrollbar">
            @forelse($notifications as $notif)
                <li wire:click="goToReservation({{ $notif['id'] }})" class="cursor-pointer">
                    <div class="flex gap-3 rounded-lg border-b border-slate-100 p-3 px-4 py-3 hover:bg-slate-50 dark:border-[#16352a] dark:hover:bg-[#112a21]">
                        <span class="relative block h-10 w-10 shrink-0 z-1">
                            <span class="flex h-full w-full items-center justify-center rounded-full
                                {{ $notif['status'] === 'authorized' ? 'bg-green-100 dark:bg-green-900/50' : '' }}
                                {{ $notif['status'] === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/50' : '' }}
                                {{ $notif['status'] === 'rejected' ? 'bg-red-100 dark:bg-red-900/50' : '' }}">
                                <span class="text-sm font-bold
                                    {{ $notif['status'] === 'authorized' ? 'text-green-700 dark:text-green-400' : '' }}
                                    {{ $notif['status'] === 'pending' ? 'text-yellow-700 dark:text-yellow-400' : '' }}
                                    {{ $notif['status'] === 'rejected' ? 'text-red-700 dark:text-red-400' : '' }}">
                                    {{ strtoupper(substr($notif['requester_name'], 0, 2)) }}
                                </span>
                            </span>
                            <span class="absolute bottom-0 right-0 z-10 h-2.5 w-2.5 rounded-full border-[1.5px] border-white dark:border-[#0c221a]
                                {{ $notif['status'] === 'authorized' ? 'bg-green-500' : '' }}
                                {{ $notif['status'] === 'pending' ? 'bg-yellow-500' : '' }}
                                {{ $notif['status'] === 'rejected' ? 'bg-red-500' : '' }}"></span>
                        </span>

                        <span class="block min-w-0 flex-1">
                            <span class="mb-1.5 block text-sm text-slate-500 dark:text-[#8aa39a]">
                                <span class="font-medium text-slate-900 dark:text-[#e6f2ec]">
                                    {{ $notif['requester_name'] }}
                                </span>
                                solicitou
                                <span class="font-medium text-slate-900 dark:text-[#e6f2ec]">
                                    {{ $notif['event_name'] }}
                                </span>
                            </span>

                            <span class="flex items-center gap-2 text-slate-500 text-xs dark:text-[#8aa39a]">
                                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-medium
                                    {{ $notif['status'] === 'authorized' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' : '' }}
                                    {{ $notif['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' : '' }}
                                    {{ $notif['status'] === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' : '' }}">
                                    @if($notif['status'] === 'authorized') Autorizada
                                    @elseif($notif['status'] === 'pending') Pendente
                                    @elseif($notif['status'] === 'rejected') Rejeitada
                                    @endif
                                </span>
                                <span class="h-1 w-1 rounded-full bg-slate-400 dark:bg-[#8aa39a]"></span>
                                <span>{{ $notif['date'] }}</span>
                            </span>
                        </span>
                    </div>
                </li>
            @empty
                <li class="px-4 py-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-10 w-10 text-slate-300 dark:text-[#2c4a3e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="mt-2 text-sm text-slate-500 dark:text-[#8aa39a]">Nenhuma notificacao</p>
                </li>
            @endforelse
        </ul>

        <!-- View All Button -->
        <a href="{{ route('admin.reservations.index') }}" @click="closeDropdown()" class="mt-3 flex justify-center rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 dark:border-[#16352a] dark:bg-[#112a21] dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]">
            Ver todas as notificacoes
        </a>
    </div>
    <!-- Dropdown End -->
</div>
