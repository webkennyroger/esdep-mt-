<!DOCTYPE html>
<html lang="pt-BR"
    x-data="{ dark: localStorage.getItem('esdep-theme') === 'dark' || (!localStorage.getItem('esdep-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches), sidebarOpen: false }"
    x-init="$watch('dark', v => { localStorage.setItem('esdep-theme', v ? 'dark' : 'light'); if(v) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); }); if(dark) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark')"
    :class="{ 'dark': dark }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Agenda de Salas — ESDEP-MT' }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logo-esdep-icone.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen overflow-x-hidden bg-slate-50 text-slate-900 antialiased dark:bg-[#081712] dark:text-[#e6f2ec]">
    <x-toast />

    <div class="min-h-screen">

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full border-r border-slate-200 bg-white transition-transform duration-200 dark:border-[#16352a] dark:bg-[#0c221a] lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen }">
            <div class="flex h-full flex-col">

                {{-- Logo --}}
                <div class="flex h-16 items-center justify-between border-b border-slate-200 px-6 dark:border-[#16352a]">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <img src="{{ asset('assets/images/logo-esdep.png') }}" alt="ESDEP-MT"
                            class="h-10 w-auto dark:brightness-0 dark:invert">
                    </a>
                    <button @click="sidebarOpen = false" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 dark:text-[#8aa39a] dark:hover:bg-[#16352a] lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Navegacao --}}
                <nav class="flex-1 overflow-y-auto px-4 py-6">
                    <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#70847d]">Principal</p>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-[#e6f2ec] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.calendar') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-[#e6f2ec] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Agenda
                        </a>
                        <a href="{{ route('reservations.create') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-[#e6f2ec] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Nova Reserva
                        </a>
                    </div>

                    <p class="mb-3 mt-8 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-[#70847d]">Administracao</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.reservations.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-[#e6f2ec] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            Gerenciar Reservas
                        </a>
                        <a href="{{ route('admin.rooms.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-[#e6f2ec] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Salas & Espaços
                        </a>
                        <a href="{{ route('admin.contacts.index') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-[#e6f2ec] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Contatos & Grupos
                        </a>
                        <a href="{{ route('admin.reports') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-[#e6f2ec] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Relatorios
                        </a>
                        <a href="{{ route('admin.activity') }}" class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 dark:text-[#e6f2ec] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Historico de Atividades
                        </a>
                    </div>
                </nav>

                {{-- Usuario --}}
                <div class="border-t border-slate-200 p-4 dark:border-[#16352a]">
                    <div class="flex items-center gap-3 rounded-xl p-3 bg-green-50 dark:bg-[#16352a] dark:text-[#4fd1a5]">
                        <a href="{{ route('admin.profile') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-green-700 text-sm font-bold text-white transition hover:bg-green-800 dark:bg-[#008b5c] dark:hover:bg-[#006e49]">
                            {{ substr(Auth::user()->name ?? 'A', 0, 2) }}
                        </a>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('admin.profile') }}" class="block truncate text-sm font-semibold text-slate-900 transition hover:text-green-700 dark:text-[#e6f2ec] dark:hover:text-[#4fd1a5]">{{ Auth::user()->name ?? 'Admin' }}</a>
                            <p class="truncate text-xs text-slate-500 dark:text-[#8aa39a]">{{ Auth::user()->role ?? 'admin' }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-slate-400 transition hover:text-red-500 dark:text-[#8aa39a] dark:hover:text-red-400" title="Sair">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </aside>

        {{-- Overlay mobile --}}
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden" x-cloak></div>

        {{-- Area principal --}}
        <div class="lg:pl-72">

            {{-- Header --}}
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur dark:border-[#16352a] dark:bg-[#0c221a]/95">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">

                    <div class="flex items-center gap-3">
                        <button type="button" @click="sidebarOpen = !sidebarOpen" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 dark:text-[#e6f2ec] dark:hover:bg-[#16352a] lg:hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-2 2xsm:gap-3">
                        <!-- Theme Toggle Button -->
                        <button class="flex items-center justify-center text-slate-500 transition-colors bg-slate-100 border border-slate-200 rounded-full h-11 w-11 hover:bg-slate-200 hover:text-slate-700 dark:bg-[#112a21] dark:border-[#16352a] dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]" @click="dark = !dark" title="Alternar tema">
                            <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </button>

                        <livewire:notification-dropdown />

                        <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-center text-slate-500 transition-colors bg-slate-100 border border-slate-200 rounded-full h-11 w-11 hover:bg-slate-200 hover:text-slate-700 dark:bg-[#112a21] dark:border-[#16352a] dark:text-[#8aa39a] dark:hover:bg-[#16352a] dark:hover:text-[#4fd1a5]" title="Abrir site publico">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>

                </div>
            </header>

            {{-- Conteudo --}}
            <main class="p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>

        </div>

    </div>

    @livewireScripts

</body>

</html>
