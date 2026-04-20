<div class="space-y-6">

    {{-- ===== ENCABEZADO + FILTRO ===== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">

        <h1 class="text-2xl font-bold text-gray-800">Estadísticas</h1>

        <div class="flex items-center gap-2 flex-wrap">

            {{-- Exportar Excel --}}
            <button
                wire:click="exportarExcel"
                class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-green-400 text-gray-600 hover:text-green-700 text-sm font-medium rounded-lg transition"
                title="Exportar a Excel"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                Excel
            </button>

            {{-- Botón Todo --}}
            <button
                wire:click="seleccionarTodo"
                class="px-4 py-2 rounded-lg text-sm font-medium transition
                    {{ $periodo === 'todo'
                        ? 'bg-pink-600 text-white shadow-sm'
                        : 'bg-white text-gray-500 border border-gray-200 hover:border-pink-300 hover:text-pink-600' }}"
            >
                Todo el tiempo
            </button>

            {{-- Selector de año --}}
            <div class="flex items-center rounded-lg border border-gray-200 overflow-hidden bg-white">
                <button
                    wire:click="anioAnterior"
                    class="px-3 py-2 text-gray-400 hover:text-pink-600 hover:bg-pink-50 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </button>

                <button
                    wire:click="seleccionarAnio"
                    class="px-4 py-2 text-sm font-medium min-w-16 text-center transition
                        {{ $periodo === (string)$anioFiltro
                            ? 'bg-pink-600 text-white'
                            : 'text-gray-700 hover:bg-gray-50' }}"
                >
                    {{ $anioFiltro }}
                </button>

                <button
                    wire:click="anioSiguiente"
                    class="px-3 py-2 text-gray-400 hover:text-pink-600 hover:bg-pink-50 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </button>
            </div>

            {{-- Total del período --}}
            @if($totalTurnos > 0)
            <span class="text-sm text-gray-400 bg-white border border-gray-200 rounded-lg px-3 py-2">
                <strong class="text-gray-700">{{ $totalTurnos }}</strong>
                {{ $totalTurnos === 1 ? 'turno' : 'turnos' }}
                {{ $periodo === 'todo' ? 'en total' : 'en '.$periodo }}
            </span>
            @endif

        </div>{{-- fin botones derecha --}}
    </div>

    {{-- ===== DOS COLUMNAS ===== --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- ===== SECCIÓN 1: CLIENTES ===== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-pink-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800 text-sm">Visitas por cliente</h2>
                    <p class="text-xs text-gray-400">{{ $porCliente->count() }} {{ $porCliente->count() === 1 ? 'cliente' : 'clientes' }}</p>
                </div>
            </div>

            @if($porCliente->isEmpty())
                <div class="py-12 text-center text-gray-400 text-sm">Sin datos para este período.</div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($porCliente as $i => $fila)
                    @php $pct = ($fila->visitas / $maxVisitas) * 100; @endphp
                    <div class="px-5 py-3.5">
                        <div class="flex items-center gap-3 mb-2">

                            {{-- Posición --}}
                            <span class="flex-shrink-0 w-6 text-center text-xs font-bold
                                @if($i === 0) text-yellow-500
                                @elseif($i === 1) text-gray-400
                                @elseif($i === 2) text-amber-600
                                @else text-gray-300 @endif">
                                @if($i < 3)
                                    <svg class="w-4 h-4 mx-auto" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                    </svg>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </span>

                            {{-- Avatar --}}
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center">
                                <span class="text-rose-600 font-bold text-xs">{{ mb_strtoupper(mb_substr($fila->nombre_completo, 0, 1)) }}</span>
                            </div>

                            {{-- Nombre + dato --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-semibold text-gray-800 truncate">{{ $fila->nombre_completo }}</span>
                                    <span class="flex-shrink-0 text-sm font-bold text-gray-700">
                                        {{ $fila->visitas }} <span class="text-xs font-normal text-gray-400">{{ $fila->visitas === 1 ? 'visita' : 'visitas' }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Barra de progreso --}}
                        <div class="ml-9 flex items-center gap-3">
                            <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                <div
                                    class="h-1.5 rounded-full transition-all duration-500
                                        @if($i === 0) bg-rose-500
                                        @elseif($i === 1) bg-rose-400
                                        @elseif($i === 2) bg-rose-300
                                        @else bg-rose-200 @endif"
                                    style="width: {{ $pct }}%"
                                ></div>
                            </div>
                            <span class="text-xs text-gray-400 w-20 text-right">
                                ${{ number_format($fila->total_gastado, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

        </div>


        {{-- ===== SECCIÓN 2: TRATAMIENTOS ===== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800 text-sm">Veces por tratamiento</h2>
                    <p class="text-xs text-gray-400">{{ $porTratamiento->count() }} {{ $porTratamiento->count() === 1 ? 'tratamiento' : 'tratamientos' }}</p>
                </div>
            </div>

            @if($porTratamiento->isEmpty())
                <div class="py-12 text-center text-gray-400 text-sm">Sin datos para este período.</div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach($porTratamiento as $i => $fila)
                    @php
                        $pct       = ($fila->veces / $maxVeces) * 100;
                        $pctTotal  = $totalTurnos > 0 ? ($fila->veces / $totalTurnos) * 100 : 0;
                    @endphp
                    <div class="px-5 py-3.5">
                        <div class="flex items-center gap-3 mb-2">

                            {{-- Posición --}}
                            <span class="flex-shrink-0 w-6 text-center text-xs font-bold
                                @if($i === 0) text-yellow-500
                                @elseif($i === 1) text-gray-400
                                @elseif($i === 2) text-amber-600
                                @else text-gray-300 @endif">
                                @if($i < 3)
                                    <svg class="w-4 h-4 mx-auto" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                                    </svg>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </span>

                            {{-- Ícono tratamiento --}}
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                                </svg>
                            </div>

                            {{-- Nombre + conteo --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-semibold text-gray-800 truncate">{{ $fila->nombre }}</span>
                                    <span class="flex-shrink-0 text-sm font-bold text-gray-700">
                                        {{ $fila->veces }} <span class="text-xs font-normal text-gray-400">{{ $fila->veces === 1 ? 'vez' : 'veces' }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Barra de progreso --}}
                        <div class="ml-9 flex items-center gap-3">
                            <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                <div
                                    class="h-1.5 rounded-full transition-all duration-500
                                        @if($i === 0) bg-violet-500
                                        @elseif($i === 1) bg-violet-400
                                        @elseif($i === 2) bg-violet-300
                                        @else bg-violet-200 @endif"
                                    style="width: {{ $pct }}%"
                                ></div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="text-xs text-gray-400">{{ number_format($pctTotal, 0) }}% del total</span>
                                <span class="text-xs font-medium text-gray-500 w-20 text-right">
                                    ${{ number_format($fila->total_generado, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

        </div>

    </div>{{-- fin grid --}}

</div>
