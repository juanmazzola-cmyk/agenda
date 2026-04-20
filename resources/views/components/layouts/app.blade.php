<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Agenda Esteticista' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-pink-50/40 min-h-screen antialiased" x-data="{ menuAbierto: false }">

    {{-- Overlay mobile --}}
    <div
        x-show="menuAbierto"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="menuAbierto = false"
        class="fixed inset-0 z-20 bg-black/30 lg:hidden"
        style="display:none"
    ></div>

    {{-- ===== SIDEBAR ===== --}}
    <aside
        :class="menuAbierto ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-60 bg-white border-r border-pink-100 flex flex-col
               transition-transform duration-300 ease-in-out lg:translate-x-0"
    >
        {{-- Marca --}}
        <div class="px-5 py-5 border-b border-pink-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-pink-400 to-rose-500
                            flex items-center justify-center shadow-sm flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                    </svg>
                </div>
                <div class="leading-tight">
                    <p class="font-bold text-gray-800 text-sm">Agenda de</p>
                    <p class="text-xs text-pink-400 font-medium">Andrea Caprio</p>
                </div>
            </div>
        </div>

        {{-- Navegación --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            @php
                $seccion = request()->segment(1) ?? '';
            @endphp

            {{-- Agenda --}}
            <a href="/agenda" @click="menuAbierto=false"
               class="nav-link {{ $seccion === 'agenda' ? 'nav-activo' : 'nav-inactivo' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
                Agenda
            </a>

            {{-- Clientes --}}
            <a href="/clientes" @click="menuAbierto=false"
               class="nav-link {{ $seccion === 'clientes' ? 'nav-activo' : 'nav-inactivo' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
                Clientes
            </a>

            {{-- Tratamientos --}}
            <a href="/tratamientos" @click="menuAbierto=false"
               class="nav-link {{ $seccion === 'tratamientos' ? 'nav-activo' : 'nav-inactivo' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                </svg>
                Tratamientos
            </a>

            {{-- Separador --}}
            <div class="pt-3 pb-1 px-3">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Reportes</p>
            </div>

            {{-- Resumen --}}
            <a href="/resumen" @click="menuAbierto=false"
               class="nav-link {{ $seccion === 'resumen' ? 'nav-activo' : 'nav-inactivo' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
                Resumen
            </a>

            {{-- Estadísticas --}}
            <a href="/estadisticas" @click="menuAbierto=false"
               class="nav-link {{ $seccion === 'estadisticas' ? 'nav-activo' : 'nav-inactivo' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                </svg>
                Estadísticas
            </a>

            {{-- Separador --}}
            <div class="pt-3 pb-1 px-3">
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Sistema</p>
            </div>

            {{-- Ajustes --}}
            <a href="/ajustes" @click="menuAbierto=false"
               class="nav-link {{ $seccion === 'ajustes' ? 'nav-activo' : 'nav-inactivo' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Ajustes
            </a>

        </nav>

        {{-- Versión --}}
        <div class="px-5 py-3 border-t border-pink-100">
            <p class="text-xs text-gray-300 font-medium">versión 1.0</p>
        </div>
    </aside>

    {{-- ===== CONTENIDO PRINCIPAL ===== --}}
    <div class="lg:pl-60 min-h-screen flex flex-col">

        {{-- Top bar (solo mobile) --}}
        <header class="lg:hidden sticky top-0 z-10 bg-white border-b border-pink-100 shadow-sm">
            <div class="flex items-center gap-3 px-4 py-3">
                <button @click="menuAbierto = true"
                        class="text-gray-500 hover:text-pink-600 transition p-1 rounded-lg hover:bg-pink-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-pink-400 to-rose-500 flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-gray-800 text-sm">Agenda de Andrea Caprio</span>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>

    </div>

    {{-- Estilos utilitarios para la nav --}}
    <style>
        .nav-link  { display:flex; align-items:center; gap:0.625rem; padding:0.5rem 0.75rem; border-radius:0.625rem; font-size:0.875rem; font-weight:500; transition:all 150ms; }
        .nav-icon  { width:1.125rem; height:1.125rem; flex-shrink:0; }
        .nav-activo  { background:#fdf2f8; color:#db2777; }
        .nav-inactivo { color:#6b7280; }
        .nav-inactivo:hover { background:#f9fafb; color:#1f2937; }
    </style>

    @livewireScripts
</body>
</html>
