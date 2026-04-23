<div>

    {{-- Layout de dos columnas: calendario + lista del día --}}
    <div class="grid grid-cols-1 xl:grid-cols-[360px,1fr] gap-6 items-start">


        {{-- ===== CALENDARIO ===== --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

            {{-- Header: mes y navegación --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <button
                    wire:click="mesAnterior"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-pink-50 hover:text-pink-600 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </button>

                <h2 class="text-base font-bold text-gray-800">
                    {{ $nombreMes }} {{ $anio }}
                </h2>

                <button
                    wire:click="mesSiguiente"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:bg-pink-50 hover:text-pink-600 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </button>
            </div>

            {{-- Días de la semana --}}
            <div class="grid grid-cols-7 px-4 pt-4 pb-1">
                @foreach(['Lu','Ma','Mi','Ju','Vi','Sa','Do'] as $cabecera)
                <div class="text-center text-xs font-semibold text-gray-400">{{ $cabecera }}</div>
                @endforeach
            </div>

            {{-- Grid de días --}}
            <div class="px-3 pb-4">
                @foreach($semanas as $semana)
                <div class="grid grid-cols-7">
                    @foreach($semana as $dia)
                    <div class="p-0.5">
                        @if($dia === null)
                            <div class="aspect-square"></div>
                        @else
                            <button
                                wire:click="seleccionarDia('{{ $dia['fecha'] }}')"
                                class="w-full aspect-square flex flex-col items-center justify-center rounded-lg text-sm relative transition-all
                                    @if($dia['seleccionado'])
                                        bg-pink-600 text-white font-bold shadow-sm
                                    @elseif($dia['esHoy'])
                                        ring-2 ring-pink-400 text-pink-600 font-bold hover:bg-pink-50
                                    @else
                                        text-gray-600 hover:bg-pink-50
                                    @endif"
                            >
                                <span class="leading-none">{{ $dia['numero'] }}</span>
                                @if($dia['tieneTurno'])
                                    <span class="absolute bottom-1 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full
                                        {{ $dia['seleccionado'] ? 'bg-pink-200' : 'bg-pink-500' }}">
                                    </span>
                                @endif
                            </button>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>

            {{-- Leyenda --}}
            <div class="px-5 py-3 border-t border-gray-100 flex items-center gap-4 text-xs text-gray-400">
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-2 h-2 rounded-full bg-pink-500"></span> Tiene turnos
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-4 h-4 rounded ring-2 ring-pink-400"></span> Hoy
                </span>
            </div>

        </div>


        {{-- ===== LISTA DEL DÍA ===== --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Turnos</h2>
                    @if($diaFormateado)
                        <p class="text-sm text-pink-500 font-medium mt-0.5">{{ $diaFormateado }}</p>
                    @else
                        <p class="text-sm text-gray-400 mt-0.5">Seleccioná un día</p>
                    @endif
                </div>
                <button
                    wire:click="abrirModalNuevo"
                    class="bg-pink-600 hover:bg-pink-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Nuevo Turno
                </button>
            </div>

            {{-- Cards de turnos --}}
            @if($turnosDia->isEmpty())
                <div class="py-16 text-center">
                    <div class="w-14 h-14 bg-pink-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-pink-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                        </svg>
                    </div>
                    <p class="text-gray-400 text-sm">
                        {{ $diaSeleccionado ? 'No hay turnos para este día.' : 'Hacé click en un día del calendario.' }}
                    </p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($turnosDia as $turno)
                    <div class="flex items-center gap-4 px-5 py-4 hover:bg-rose-50/30 transition group">

                        {{-- Badge hora --}}
                        <div class="flex-shrink-0 w-14 h-14 bg-rose-500 rounded-2xl flex items-center justify-center shadow-sm">
                            <span class="text-white font-bold text-sm leading-tight text-center">
                                {{ substr($turno->hora, 0, 5) }}
                            </span>
                        </div>

                        {{-- Info del turno --}}
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 text-sm leading-tight whitespace-nowrap">
                                {{ $turno->cliente->nombre }} {{ $turno->cliente->apellido }}
                            </p>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mt-1">
                                {{ $turno->tratamiento->nombre }}
                            </p>
                            <p class="text-sm font-bold text-gray-700 mt-0.5">
                                ${{ number_format($turno->valor, 0, ',', '.') }}
                            </p>
                            @if($turno->notas)
                                <p class="text-xs text-gray-400 italic mt-0.5 truncate">{{ $turno->notas }}</p>
                            @endif
                        </div>

                        {{-- Acciones --}}
                        @php
                            $waMsg = str_replace(
                                ['{nombre}', '{apellido}', '{tratamiento}', '{fecha}', '{hora}'],
                                [
                                    $turno->cliente->nombre,
                                    $turno->cliente->apellido,
                                    $turno->tratamiento->nombre,
                                    \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y'),
                                    substr($turno->hora, 0, 5),
                                ],
                                $mensajeWa
                            );
                            $waUrl = 'https://wa.me/54' . $turno->cliente->celular . '?text=' . rawurlencode($waMsg);
                        @endphp
                        <div class="flex items-center gap-1 flex-shrink-0">

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

                            {{-- Editar --}}
                            <button
                                wire:click="editar({{ $turno->id }})"
                                title="Editar turno"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-50 text-orange-400 hover:bg-orange-100 transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                </svg>
                            </button>

                            {{-- Eliminar --}}
                            <button
                                wire:click="confirmarEliminar({{ $turno->id }})"
                                title="Eliminar turno"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 text-red-400 hover:bg-red-100 transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                </svg>
                            </button>

                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Resumen del día --}}
                <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between text-sm bg-gray-50/50">
                    <span class="text-gray-400">
                        {{ $turnosDia->count() }} {{ $turnosDia->count() === 1 ? 'turno' : 'turnos' }}
                    </span>
                    <div class="flex gap-4">
                        <span class="text-gray-500">
                            Total: <strong class="text-gray-700">${{ number_format($turnosDia->sum('valor'), 0, ',', '.') }}</strong>
                        </span>
                        <span class="text-green-600">
                            Cobrado: <strong>${{ number_format($turnosDia->where('cobrado', true)->sum('valor'), 0, ',', '.') }}</strong>
                        </span>
                    </div>
                </div>
            @endif

        </div>

    </div>{{-- fin grid --}}


    {{-- ========== MODAL: FORMULARIO ========== --}}
    @if($modalFormulario)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">

            <h2 class="text-lg font-semibold text-gray-800 mb-5">
                {{ $turnoId ? 'Editar Turno' : 'Nuevo Turno' }}
            </h2>

            <form wire:submit="guardar" class="space-y-4">

                {{-- Fila: Fecha y Hora --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input
                            wire:model="fecha"
                            type="date"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 @error('fecha') border-red-400 @enderror"
                        >
                        @error('fecha')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                        <input
                            wire:model="hora"
                            type="time"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 @error('hora') border-red-400 @enderror"
                        >
                        @error('hora')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Cliente --}}
                <div
                    x-data="{
                        busqueda: '',
                        abierto: false,
                        clientes: @js($clientes->map(fn($c) => ['id' => $c->id, 'apellido' => $c->apellido, 'nombre' => $c->nombre])),
                        get filtrados() {
                            const q = this.busqueda.trim().toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
                            if (!q) return this.clientes.slice(0, 10);
                            return this.clientes.filter(c => {
                                const t = (c.apellido + ' ' + c.nombre).toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
                                return t.includes(q);
                            }).slice(0, 10);
                        },
                        seleccionar(c) {
                            $wire.set('clienteId', String(c.id));
                            this.busqueda = c.apellido + ', ' + c.nombre;
                            this.abierto = false;
                        },
                        limpiar() {
                            $wire.set('clienteId', '');
                            this.busqueda = '';
                            this.$nextTick(() => this.$refs.input.focus());
                        }
                    }"
                    x-init="
                        $wire.$watch('clienteId', id => {
                            if (!id) { busqueda = ''; abierto = false; }
                            else {
                                const c = clientes.find(c => String(c.id) === String(id));
                                if (c) busqueda = c.apellido + ', ' + c.nombre;
                            }
                        });
                        const id = $wire.get('clienteId');
                        if (id) {
                            const c = clientes.find(c => String(c.id) === String(id));
                            if (c) busqueda = c.apellido + ', ' + c.nombre;
                        }
                    "
                    @click.outside="abierto = false"
                    class="relative"
                >
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                    <div class="relative">
                        <input
                            x-ref="input"
                            type="text"
                            x-model="busqueda"
                            @input="abierto = true"
                            @focus="abierto = true; $event.target.select()"
                            placeholder="Buscar cliente..."
                            autocomplete="off"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 @error('clienteId') border-red-400 @enderror"
                        >
                        <button
                            type="button"
                            x-show="busqueda"
                            @click="limpiar()"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-lg leading-none"
                        >&times;</button>
                    </div>
                    <div
                        x-show="abierto"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                    >
                        <template x-if="filtrados.length > 0">
                            <div>
                                <template x-for="c in filtrados" :key="c.id">
                                    <div
                                        @click="seleccionar(c)"
                                        class="px-3 py-2 text-sm cursor-pointer hover:bg-pink-50 active:bg-pink-100 border-b border-gray-100 last:border-0"
                                        x-text="c.apellido + ', ' + c.nombre"
                                    ></div>
                                </template>
                            </div>
                        </template>
                        <template x-if="filtrados.length === 0">
                            <div class="px-3 py-2 text-sm text-gray-400">Sin resultados</div>
                        </template>
                    </div>
                    @error('clienteId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tratamiento --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tratamiento</label>
                    <select
                        wire:model="tratamientoId"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 @error('tratamientoId') border-red-400 @enderror"
                    >
                        <option value="">— Seleccioná un tratamiento —</option>
                        @foreach($tratamientos as $tratamiento)
                            <option value="{{ $tratamiento->id }}">{{ $tratamiento->nombre }}</option>
                        @endforeach
                    </select>
                    @error('tratamientoId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fila: Valor y Cobrado --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valor ($)</label>
                        <input
                            wire:model="valor"
                            type="number"
                            min="0"
                            step="1"
                            x-on:focus="$el.select()"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 @error('valor') border-red-400 @enderror"
                            placeholder="0"
                        >
                        @error('valor')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cobrado</label>
                        <label class="flex items-center gap-3 h-9 cursor-pointer select-none">
                            <div class="relative">
                                <input wire:model="cobrado" type="checkbox" class="sr-only peer">
                                <div class="w-10 h-6 bg-gray-200 peer-checked:bg-pink-500 rounded-full transition-colors duration-200"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-4"></div>
                            </div>
                            <span class="text-sm text-gray-500 peer-checked:text-pink-600">{{ $cobrado ? 'Sí' : 'No' }}</span>
                        </label>
                    </div>
                </div>

                {{-- Notas --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Notas <span class="text-gray-400 font-normal">(opcional)</span>
                    </label>
                    <textarea
                        wire:model="notas"
                        rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 resize-none"
                        placeholder="Observaciones del turno..."
                    ></textarea>
                </div>

                {{-- Acciones --}}
                <div class="flex justify-end gap-3 pt-1">
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
                        {{ $turnoId ? 'Guardar cambios' : 'Crear turno' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
    @endif


    {{-- ========== MODAL: CONFIRMAR ELIMINAR ========== --}}
    @if($modalEliminar)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6">

            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">¿Eliminar turno?</h3>
                    <p class="text-sm text-gray-500 mt-1">Esta acción no se puede deshacer.</p>
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
