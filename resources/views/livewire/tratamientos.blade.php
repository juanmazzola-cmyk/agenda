<div>

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Tratamientos</h1>
        <button
            wire:click="abrirModalNuevo"
            class="bg-pink-600 hover:bg-pink-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition"
        >
            + Nuevo Tratamiento
        </button>
    </div>

    {{-- Buscador --}}
    <div class="mb-4">
        <input
            wire:model.live.debounce.300ms="buscar"
            type="text"
            placeholder="Buscar por nombre..."
            class="w-full sm:w-80 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400"
        >
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Nombre</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tratamientos as $tratamiento)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">{{ $tratamiento->nombre }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">

                            {{-- Editar --}}
                            <button
                                wire:click="editar({{ $tratamiento->id }})"
                                title="Editar"
                                class="text-gray-400 hover:text-blue-600 transition"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                </svg>
                            </button>

                            {{-- Eliminar --}}
                            <button
                                wire:click="confirmarEliminar({{ $tratamiento->id }})"
                                title="Eliminar"
                                class="text-gray-400 hover:text-red-600 transition"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                            </button>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="px-4 py-8 text-center text-gray-400">
                        No hay tratamientos registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($tratamientos->hasPages())
    <div class="mt-4">
        {{ $tratamientos->links() }}
    </div>
    @endif


    {{-- ========== MODAL: FORMULARIO ========== --}}
    @if($modalFormulario)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">

            <h2 class="text-lg font-semibold text-gray-800 mb-5">
                {{ $tratamientoId ? 'Editar Tratamiento' : 'Nuevo Tratamiento' }}
            </h2>

            <form wire:submit="guardar" class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input
                        wire:model="nombre"
                        type="text"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 @error('nombre') border-red-400 @enderror"
                        placeholder="Ej: Lifting de pestañas"
                        autofocus
                    >
                    @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

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
                        {{ $tratamientoId ? 'Guardar cambios' : 'Crear tratamiento' }}
                    </button>
                </div>

            </form>
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
                    <h3 class="text-base font-semibold text-gray-800">¿Eliminar tratamiento?</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Se eliminará <strong>{{ $eliminarNombre }}</strong> y todos los turnos asociados. Esta acción no se puede deshacer.
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
