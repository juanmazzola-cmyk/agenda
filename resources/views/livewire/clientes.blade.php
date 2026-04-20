<div>

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Clientes</h1>
        <div class="flex items-center gap-2">
            <button
                wire:click="exportarExcel"
                class="flex items-center gap-1.5 border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg transition"
            >
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                Excel
            </button>
            <button
                wire:click="abrirModalNuevo"
                class="bg-pink-600 hover:bg-pink-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition"
            >
                + Nueva Cliente
            </button>
        </div>
    </div>

    {{-- Buscador --}}
    <div class="mb-4">
        <input
            wire:model.live.debounce.300ms="buscar"
            type="text"
            placeholder="Buscar por nombre o apellido..."
            class="w-full sm:w-80 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400"
        >
    </div>

    {{-- Cards de clientes --}}
    @php
        $meses = ['','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    @endphp

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        @forelse($clientes as $cliente)
        @php
            $waMsg = str_replace(['{nombre}','{apellido}'], [$cliente->nombre, $cliente->apellido], $mensajeWa);
            $waUrl = 'https://wa.me/54' . $cliente->celular . '?text=' . rawurlencode($waMsg);
        @endphp
        <div class="flex items-center gap-4 px-5 py-4 hover:bg-rose-50/30 transition border-b border-gray-100 last:border-0">

            {{-- Avatar --}}
            <div class="flex-shrink-0 w-11 h-11 rounded-full bg-rose-500 flex items-center justify-center shadow-sm">
                <span class="text-white font-bold text-base">{{ mb_strtoupper(mb_substr($cliente->nombre, 0, 1)) }}</span>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm leading-tight">
                    {{ $cliente->nombre }} {{ $cliente->apellido }}
                </p>

                @if($cliente->proximoTurno)
                    @php $t = $cliente->proximoTurno; @endphp
                    <p class="text-xs text-rose-500 font-medium mt-0.5 flex items-start gap-1">
                        <svg class="w-3 h-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                        <span>
                            <span class="block">Próximo turno</span>
                            <span class="block text-[10px]">{{ $t->fecha->format('d/m') }} — {{ substr($t->hora, 0, 5) }}</span>
                        </span>
                    </p>
                @else
                    <p class="text-xs text-gray-400 mt-0.5">Sin turnos próximos</p>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-1.5 flex-shrink-0">

                {{-- WhatsApp --}}
                <a
                    href="{{ $waUrl }}"
                    target="_blank"
                    title="WhatsApp"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-green-50 text-green-500 hover:bg-green-100 transition"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </a>

                {{-- Historial --}}
                <button
                    wire:click="abrirHistorial({{ $cliente->id }})"
                    title="Historial de turnos"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-50 text-indigo-400 hover:bg-indigo-100 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/>
                    </svg>
                </button>

                {{-- Editar --}}
                <button
                    wire:click="editar({{ $cliente->id }})"
                    title="Editar"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-50 text-orange-400 hover:bg-orange-100 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                    </svg>
                </button>

                {{-- Eliminar --}}
                <button
                    wire:click="confirmarEliminar({{ $cliente->id }})"
                    title="Eliminar"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 text-red-400 hover:bg-red-100 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                </button>

            </div>
        </div>
        @empty
        <div class="py-12 text-center text-gray-400 text-sm">
            No hay clientes registradas.
        </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if($clientes->hasPages())
    <div class="mt-4">
        {{ $clientes->links() }}
    </div>
    @endif


    {{-- ========== MODAL: FORMULARIO ========== --}}
    @if($modalFormulario)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">

            <h2 class="text-lg font-semibold text-gray-800 mb-5">
                {{ $clienteId ? 'Editar Cliente' : 'Nueva Cliente' }}
            </h2>

            <form wire:submit="guardar" class="space-y-4">

                {{-- Nombre --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input
                        wire:model="nombre"
                        type="text"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 @error('nombre') border-red-400 @enderror"
                        placeholder="Ej: María"
                        autofocus
                    >
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Apellido --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                    <input
                        wire:model="apellido"
                        type="text"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 @error('apellido') border-red-400 @enderror"
                        placeholder="Ej: García"
                    >
                    @error('apellido')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Celular --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 py-2 border border-r-0 border-gray-300 rounded-l-lg bg-gray-100 text-sm text-gray-500 select-none">
                            +54
                        </span>
                        <input
                            wire:model="celular"
                            type="tel"
                            class="flex-1 border border-gray-300 rounded-r-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 @error('celular') border-red-400 @enderror"
                            placeholder="1123456789"
                        >
                    </div>
                    @error('celular')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Acciones --}}
                <div class="flex justify-end gap-3 pt-2">
                    <button
                        type="button"
                        wire:click="$set('modalFormulario', false)"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 bg-pink-600 hover:bg-pink-700 text-white text-sm font-medium rounded-lg transition"
                    >
                        {{ $clienteId ? 'Guardar cambios' : 'Crear cliente' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
    @endif


    {{-- ========== MODAL: HISTORIAL ========== --}}
    @if($modalHistorial && $clienteHistorial)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-6 max-h-[80vh] flex flex-col">

            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    Historial — {{ $clienteHistorial->nombre }} {{ $clienteHistorial->apellido }}
                </h2>
                <button
                    wire:click="$set('modalHistorial', false)"
                    class="text-gray-400 hover:text-gray-600 transition"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Próximo turno --}}
            @if($clienteHistorial->proximoTurno)
                @php $pt = $clienteHistorial->proximoTurno; @endphp
                <div class="flex items-center gap-3 bg-rose-50 border border-rose-100 rounded-lg px-4 py-3 mb-4">
                    <svg class="w-4 h-4 text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                    <div class="text-sm">
                        <span class="font-semibold text-rose-600">Próximo turno:</span>
                        <span class="text-gray-700 ml-1">
                            {{ $pt->fecha->format('d/m/Y') }} — {{ substr($pt->hora, 0, 5) }} hs
                        </span>
                        <span class="text-gray-500 ml-1">· {{ $pt->tratamiento->nombre }}</span>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-lg px-4 py-3 mb-4 text-sm text-gray-400">
                    Sin turnos próximos
                </div>
            @endif

            <div class="overflow-y-auto flex-1">
                @if($clienteHistorial->turnos->isEmpty())
                    <p class="text-gray-400 text-sm text-center py-6">Sin turnos registrados.</p>
                @else
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-3 py-2 text-gray-500 font-medium">Fecha</th>
                                <th class="text-left px-3 py-2 text-gray-500 font-medium">Tratamiento</th>
                                <th class="text-right px-3 py-2 text-gray-500 font-medium">Valor</th>
                                <th class="text-center px-3 py-2 text-gray-500 font-medium">Cobrado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($clienteHistorial->turnos as $turno)
                            <tr>
                                <td class="px-3 py-2 text-gray-700">
                                    {{ $turno->fecha->format('d/m/Y') }}
                                </td>
                                <td class="px-3 py-2 text-gray-700">
                                    {{ $turno->tratamiento->nombre }}
                                </td>
                                <td class="px-3 py-2 text-right text-gray-700">
                                    ${{ number_format($turno->valor, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if($turno->cobrado)
                                        <span class="text-green-600 font-medium">✓</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="mt-4 flex justify-end">
                <button
                    wire:click="$set('modalHistorial', false)"
                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition"
                >
                    Cerrar
                </button>
            </div>

        </div>
    </div>
    @endif


    {{-- ========== MODAL: CONFIRMAR ELIMINAR ========== --}}
    @if($modalEliminar)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6">

            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">¿Eliminar cliente?</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Se eliminará <strong>{{ $eliminarNombre }}</strong> y todos sus turnos. Esta acción no se puede deshacer.
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button
                    wire:click="$set('modalEliminar', false)"
                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition"
                >
                    Cancelar
                </button>
                <button
                    wire:click="eliminar"
                    class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition"
                >
                    Sí, eliminar
                </button>
            </div>

        </div>
    </div>
    @endif

</div>
