<div class="space-y-6">

    {{-- ===== ENCABEZADO + CONTROLES ===== --}}
    <div class="flex flex-wrap items-center justify-between gap-4">

        <h1 class="text-2xl font-bold text-gray-800">Resumen Económico</h1>

        <div class="flex flex-wrap items-center gap-3">

            {{-- Toggle Mensual / Anual --}}
            <div class="flex rounded-lg overflow-hidden border border-gray-200 text-sm font-medium">
                <button
                    wire:click="$set('vista', 'mensual')"
                    class="{{ $vista === 'mensual' ? 'bg-pink-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }} px-4 py-2 transition"
                >
                    Mensual
                </button>
                <button
                    wire:click="$set('vista', 'anual')"
                    class="{{ $vista === 'anual' ? 'bg-pink-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }} px-4 py-2 transition"
                >
                    Anual
                </button>
            </div>

            {{-- Toggle Todos / Cobrados --}}
            <div class="flex rounded-lg overflow-hidden border border-gray-200 text-sm font-medium">
                <button
                    wire:click="$set('filtro', 'todos')"
                    class="{{ $filtro === 'todos' ? 'bg-gray-700 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }} px-4 py-2 transition"
                >
                    Todos
                </button>
                <button
                    wire:click="$set('filtro', 'cobrados')"
                    class="{{ $filtro === 'cobrados' ? 'bg-green-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }} px-4 py-2 transition"
                >
                    Solo cobrados
                </button>
            </div>

        </div>
    </div>

    {{-- ===== NAVEGACIÓN DE PERÍODO ===== --}}
    <div class="flex items-center gap-3">
        @if($vista === 'mensual')
            <button wire:click="mesAnterior"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:border-pink-300 hover:text-pink-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </button>
            <span class="text-base font-bold text-gray-700 min-w-40 text-center">
                {{ $nombresMes[$mes] }} {{ $anio }}
            </span>
            <button wire:click="mesSiguiente"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:border-pink-300 hover:text-pink-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </button>
        @else
            <button wire:click="anioAnterior"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:border-pink-300 hover:text-pink-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </button>
            <span class="text-base font-bold text-gray-700 min-w-40 text-center">
                Año {{ $anio }}
            </span>
            <button wire:click="anioSiguiente"
                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:border-pink-300 hover:text-pink-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </button>
        @endif
    </div>

    {{-- ===== KPI CARDS ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">

        {{-- Turnos --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Turnos</p>
            <p class="text-3xl font-bold text-gray-800">{{ $kpis['turnos'] }}</p>
            <p class="text-xs text-gray-400 mt-1">en el período</p>
        </div>

        {{-- Clientes --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-pink-100 bg-pink-50/20">
            <p class="text-xs font-semibold text-pink-500 uppercase tracking-wide mb-2">Clientes</p>
            <p class="text-3xl font-bold text-pink-700">{{ $kpis['clientes'] }}</p>
            <p class="text-xs text-pink-400 mt-1">atendidas</p>
        </div>

        {{-- Facturado --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Facturado</p>
            <p class="text-3xl font-bold text-gray-800">${{ number_format($kpis['facturado'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">valor total</p>
        </div>

        {{-- Cobrado --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-green-100 bg-green-50/30">
            <p class="text-xs font-semibold text-green-600 uppercase tracking-wide mb-2">Cobrado</p>
            <p class="text-3xl font-bold text-green-700">${{ number_format($kpis['cobrado'], 0, ',', '.') }}</p>
            @if($kpis['facturado'] > 0)
                <p class="text-xs text-green-500 mt-1">
                    {{ number_format(($kpis['cobrado'] / $kpis['facturado']) * 100, 0) }}% del total
                </p>
            @else
                <p class="text-xs text-gray-400 mt-1">—</p>
            @endif
        </div>

        {{-- Pendiente --}}
        <div class="bg-white rounded-xl shadow-sm p-5 border border-amber-100 bg-amber-50/30">
            <p class="text-xs font-semibold text-amber-600 uppercase tracking-wide mb-2">Pendiente</p>
            <p class="text-3xl font-bold text-amber-700">${{ number_format($kpis['pendiente'], 0, ',', '.') }}</p>
            @if($kpis['facturado'] > 0)
                <p class="text-xs text-amber-500 mt-1">
                    {{ number_format(($kpis['pendiente'] / $kpis['facturado']) * 100, 0) }}% del total
                </p>
            @else
                <p class="text-xs text-gray-400 mt-1">—</p>
            @endif
        </div>

    </div>

    {{-- ===== TABLA POR MES (solo anual) ===== --}}
    @if($vista === 'anual' && $porMes)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">

        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-700 text-sm">Desglose mensual — {{ $anio }}</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Mes</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Turnos</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Facturado</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Cobrado</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Pendiente</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($porMes as $fila)
                    <tr class="{{ $fila['cantidad'] > 0 ? 'hover:bg-gray-50/50' : 'opacity-40' }} transition">
                        <td class="px-5 py-3 font-medium text-gray-700">{{ $fila['nombre'] }}</td>
                        <td class="px-5 py-3 text-right text-gray-600">{{ $fila['cantidad'] ?: '—' }}</td>
                        <td class="px-5 py-3 text-right font-medium text-gray-700">
                            {{ $fila['facturado'] > 0 ? '$'.number_format($fila['facturado'], 0, ',', '.') : '—' }}
                        </td>
                        <td class="px-5 py-3 text-right font-medium text-green-600">
                            {{ $fila['cobrado'] > 0 ? '$'.number_format($fila['cobrado'], 0, ',', '.') : '—' }}
                        </td>
                        <td class="px-5 py-3 text-right font-medium text-amber-600">
                            {{ $fila['pendiente'] > 0 ? '$'.number_format($fila['pendiente'], 0, ',', '.') : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                    <tr>
                        <td class="px-5 py-3 font-bold text-gray-800 text-sm">Total</td>
                        <td class="px-5 py-3 text-right font-bold text-gray-800">{{ $kpis['turnos'] }}</td>
                        <td class="px-5 py-3 text-right font-bold text-gray-800">${{ number_format($kpis['facturado'], 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right font-bold text-green-700">${{ number_format($kpis['cobrado'], 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right font-bold text-amber-700">${{ number_format($kpis['pendiente'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    {{-- ===== TABLA POR TRATAMIENTO ===== --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">

        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-700 text-sm">
                Por tratamiento —
                @if($vista === 'mensual')
                    {{ $nombresMes[$mes] }} {{ $anio }}
                @else
                    Año {{ $anio }}
                @endif
            </h2>
        </div>

        @if($porTratamiento->isEmpty())
            <div class="py-12 text-center">
                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z"/>
                    </svg>
                </div>
                <p class="text-gray-400 text-sm">Sin datos para este período.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tratamiento</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Turnos</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Facturado</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Cobrado</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Pendiente</th>
                            <th class="px-5 py-3 w-28"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($porTratamiento as $fila)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-5 py-3 font-medium text-gray-800">{{ $fila['nombre'] }}</td>
                            <td class="px-5 py-3 text-right text-gray-600">{{ $fila['cantidad'] }}</td>
                            <td class="px-5 py-3 text-right font-medium text-gray-700">
                                ${{ number_format($fila['facturado'], 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-right font-medium text-green-600">
                                ${{ number_format($fila['cobrado'], 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-right font-medium text-amber-600">
                                {{ $fila['pendiente'] > 0 ? '$'.number_format($fila['pendiente'], 0, ',', '.') : '—' }}
                            </td>
                            <td class="px-5 py-3">
                                @if($fila['facturado'] > 0)
                                    @php $pct = ($fila['cobrado'] / $fila['facturado']) * 100; @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                            <div
                                                class="bg-green-400 h-1.5 rounded-full transition-all"
                                                style="width: {{ min($pct, 100) }}%"
                                            ></div>
                                        </div>
                                        <span class="text-xs text-gray-400 w-8 text-right">{{ number_format($pct, 0) }}%</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-gray-200 bg-gray-50">
                        <tr>
                            <td class="px-5 py-3 font-bold text-gray-800 text-sm">Total</td>
                            <td class="px-5 py-3 text-right font-bold text-gray-800">{{ $kpis['turnos'] }}</td>
                            <td class="px-5 py-3 text-right font-bold text-gray-800">${{ number_format($kpis['facturado'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-bold text-green-700">${{ number_format($kpis['cobrado'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-bold text-amber-700">
                                {{ $kpis['pendiente'] > 0 ? '$'.number_format($kpis['pendiente'], 0, ',', '.') : '—' }}
                            </td>
                            <td class="px-5 py-3">
                                @if($kpis['facturado'] > 0)
                                    @php $pct = ($kpis['cobrado'] / $kpis['facturado']) * 100; @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                            <div class="bg-green-400 h-1.5 rounded-full" style="width: {{ min($pct, 100) }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 w-8 text-right font-semibold">{{ number_format($pct, 0) }}%</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

</div>
