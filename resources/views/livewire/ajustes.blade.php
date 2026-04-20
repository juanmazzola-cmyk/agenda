<div class="space-y-6 max-w-2xl">

    <h1 class="text-2xl font-bold text-gray-800">Ajustes</h1>

    {{-- ===== SECCIÓN 1: MENSAJE DE WHATSAPP ===== --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a9.837 9.837 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-gray-800 text-sm">Mensaje de WhatsApp predeterminado</h2>
                <p class="text-xs text-gray-400">Se usa al tocar el ícono de WhatsApp en clientes y turnos</p>
            </div>
        </div>

        <div class="px-5 py-5 space-y-4">

            {{-- Variables disponibles --}}
            <div class="flex flex-wrap gap-2">
                <span class="text-xs text-gray-500 font-medium self-center">Variables:</span>
                @foreach(['{nombre}', '{apellido}', '{fecha}', '{hora}', '{tratamiento}'] as $var)
                    <span class="text-xs bg-gray-100 text-gray-600 font-mono px-2 py-0.5 rounded">{{ $var }}</span>
                @endforeach
            </div>

            {{-- Textarea --}}
            <div>
                <textarea
                    wire:model="mensajeWa"
                    rows="3"
                    maxlength="500"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 resize-none @error('mensajeWa') border-red-400 @enderror"
                    placeholder="Ej: Hola {nombre}, te recuerdo tu turno el {fecha} a las {hora} hs. ¡Saludos! 🌸"
                ></textarea>
                @error('mensajeWa')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-400 mt-1 text-right">{{ strlen($mensajeWa) }}/500</p>
            </div>

            {{-- Botón guardar --}}
            <div class="flex items-center gap-3">
                <button
                    wire:click="guardarMensaje"
                    class="px-5 py-2 bg-pink-600 hover:bg-pink-700 text-white text-sm font-medium rounded-lg transition"
                >
                    Guardar mensaje
                </button>
                @if($mensajeGuardado)
                    <span class="text-green-600 text-sm font-medium flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                        Guardado
                    </span>
                @endif
            </div>

        </div>
    </div>


    {{-- ===== SECCIÓN 2: EXPORTAR BACKUP ===== --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-gray-800 text-sm">Exportar backup</h2>
                <p class="text-xs text-gray-400">Descarga un archivo JSON con toda la información</p>
            </div>
        </div>

        <div class="px-5 py-5">
            <button
                wire:click="exportar"
                class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                Descargar backup JSON
            </button>
        </div>

    </div>


    {{-- ===== SECCIÓN 3: IMPORTAR BACKUP ===== --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-gray-800 text-sm">Importar backup</h2>
                <p class="text-xs text-gray-400">Restaura todos los datos desde un archivo JSON</p>
            </div>
        </div>

        <div class="px-5 py-5 space-y-4">

            {{-- Éxito --}}
            @if($importExito)
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-lg px-4 py-3 text-sm text-green-700">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Backup importado correctamente. Todos los datos fueron restaurados.</span>
            </div>
            @endif

            {{-- Advertencia --}}
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-sm text-amber-700">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
                <span><strong>Atención:</strong> importar reemplazará <strong>todos</strong> los datos actuales (clientes, tratamientos, turnos y configuración). Esta acción no se puede deshacer.</span>
            </div>

            {{-- Input de archivo --}}
            @if(!$confirmandoImport)
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Archivo de backup (.json)</label>
                    <input
                        wire:model="archivoImport"
                        type="file"
                        accept=".json"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 cursor-pointer border border-gray-300 rounded-lg"
                    >
                    @if($importError)
                        <p class="text-red-500 text-xs mt-1">{{ $importError }}</p>
                    @endif
                </div>

                <button
                    wire:click="verificarArchivo"
                    class="flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    Verificar archivo
                </button>
            </div>
            @endif

            {{-- Preview de confirmación --}}
            @if($confirmandoImport && !empty($previewImport))
            <div class="border border-red-200 rounded-xl overflow-hidden">
                <div class="bg-red-50 px-4 py-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <span class="text-sm font-semibold text-red-700">¿Confirmar importación?</span>
                </div>
                <div class="px-4 py-4 space-y-3">
                    <p class="text-sm text-gray-600">
                        El archivo contiene:
                    </p>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-gray-50 rounded-lg py-2">
                            <p class="text-xl font-bold text-gray-800">{{ $previewImport['clientes'] }}</p>
                            <p class="text-xs text-gray-500">clientes</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg py-2">
                            <p class="text-xl font-bold text-gray-800">{{ $previewImport['tratamientos'] }}</p>
                            <p class="text-xs text-gray-500">tratamientos</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg py-2">
                            <p class="text-xl font-bold text-gray-800">{{ $previewImport['turnos'] }}</p>
                            <p class="text-xs text-gray-500">turnos</p>
                        </div>
                    </div>
                    @if($previewImport['exportado_en'] !== '—')
                    <p class="text-xs text-gray-400">Exportado el {{ \Carbon\Carbon::parse($previewImport['exportado_en'])->format('d/m/Y H:i') }}</p>
                    @endif
                    <div class="flex gap-3 pt-1">
                        <button
                            wire:click="ejecutarImport"
                            class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition"
                        >
                            Sí, reemplazar todo e importar
                        </button>
                        <button
                            wire:click="cancelarImport"
                            class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-200 rounded-lg transition"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
