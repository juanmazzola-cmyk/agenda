<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\Tratamiento;
use App\Models\Turno;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Ajustes')]
class Ajustes extends Component
{
    use WithFileUploads;

    // WhatsApp
    public string $mensajeWa      = '';
    public bool   $mensajeGuardado = false;

    // Import
    public        $archivoImport     = null;
    public bool   $confirmandoImport = false;
    public bool   $importExito       = false;
    public string $importError       = '';
    public array  $previewImport     = [];

    public function mount(): void
    {
        $this->mensajeWa = Configuracion::obtener(
            'whatsapp_mensaje',
            'Hola {nombre}, te recuerdo tu turno. ¡Hasta pronto! 🌸'
        );
    }

    protected function rules(): array
    {
        return [
            'mensajeWa'     => 'required|string|max:500',
            'archivoImport' => 'nullable|file|max:20480',
        ];
    }

    public function updatedMensajeWa(): void
    {
        $this->mensajeGuardado = false;
    }

    public function guardarMensaje(): void
    {
        $this->validateOnly('mensajeWa', ['mensajeWa' => 'required|string|max:500']);
        Configuracion::establecer('whatsapp_mensaje', $this->mensajeWa);
        $this->mensajeGuardado = true;
    }

    public function exportar()
    {
        $data = [
            'exportado_en' => now()->toISOString(),
            'version'      => '1.0',
            'clientes'     => Cliente::all()->toArray(),
            'tratamientos' => Tratamiento::all()->toArray(),
            'turnos'       => Turno::all()->toArray(),
            'configuracion'=> Configuracion::all()->toArray(),
        ];

        $json     = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = 'backup-agenda-' . now()->format('Y-m-d') . '.json';

        return response()->streamDownload(
            fn () => print($json),
            $filename,
            ['Content-Type' => 'application/json']
        );
    }

    public function verificarArchivo(): void
    {
        $this->importError       = '';
        $this->confirmandoImport = false;
        $this->previewImport     = [];

        if (!$this->archivoImport) {
            $this->importError = 'Seleccioná un archivo primero.';
            return;
        }

        $contenido = file_get_contents($this->archivoImport->getRealPath());
        $data      = json_decode($contenido, true);

        if (!$data || !isset($data['clientes'], $data['tratamientos'], $data['turnos'])) {
            $this->importError = 'El archivo no tiene el formato correcto de backup.';
            return;
        }

        $this->previewImport = [
            'clientes'     => count($data['clientes']),
            'tratamientos' => count($data['tratamientos']),
            'turnos'       => count($data['turnos']),
            'exportado_en' => $data['exportado_en'] ?? '—',
        ];
        $this->confirmandoImport = true;
    }

    public function ejecutarImport(): void
    {
        if (!$this->archivoImport) {
            $this->confirmandoImport = false;
            return;
        }

        $contenido = file_get_contents($this->archivoImport->getRealPath());
        $data      = json_decode($contenido, true);

        $now = now();

        DB::transaction(function () use ($data, $now) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('turnos')->truncate();
            DB::table('clientes')->truncate();
            DB::table('tratamientos')->truncate();
            DB::table('configuracion')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            if (!empty($data['tratamientos'])) {
                DB::table('tratamientos')->insert(
                    collect($data['tratamientos'])->map(fn ($r) => [
                        'id'         => $r['id'],
                        'nombre'     => $r['nombre'],
                        'created_at' => $r['created_at'] ?? $now,
                        'updated_at' => $r['updated_at'] ?? $now,
                    ])->toArray()
                );
            }

            if (!empty($data['clientes'])) {
                DB::table('clientes')->insert(
                    collect($data['clientes'])->map(fn ($r) => [
                        'id'         => $r['id'],
                        'nombre'     => $r['nombre'],
                        'apellido'   => $r['apellido'],
                        'celular'    => $r['celular'],
                        'created_at' => $r['created_at'] ?? $now,
                        'updated_at' => $r['updated_at'] ?? $now,
                    ])->toArray()
                );
            }

            if (!empty($data['turnos'])) {
                DB::table('turnos')->insert(
                    collect($data['turnos'])->map(fn ($r) => [
                        'id'             => $r['id'],
                        'cliente_id'     => $r['cliente_id'],
                        'tratamiento_id' => $r['tratamiento_id'],
                        'fecha'          => $r['fecha'],
                        'hora'           => $r['hora'],
                        'valor'          => $r['valor'],
                        'cobrado'        => $r['cobrado'],
                        'notas'          => $r['notas'] ?? null,
                        'created_at'     => $r['created_at'] ?? $now,
                        'updated_at'     => $r['updated_at'] ?? $now,
                    ])->toArray()
                );
            }

            if (!empty($data['configuracion'])) {
                DB::table('configuracion')->insert(
                    collect($data['configuracion'])->map(fn ($r) => [
                        'id'         => $r['id'],
                        'clave'      => $r['clave'],
                        'valor'      => $r['valor'],
                        'created_at' => $r['created_at'] ?? $now,
                        'updated_at' => $r['updated_at'] ?? $now,
                    ])->toArray()
                );
            }
        });

        $this->mensajeWa         = Configuracion::obtener('whatsapp_mensaje', $this->mensajeWa);
        $this->confirmandoImport = false;
        $this->archivoImport     = null;
        $this->previewImport     = [];
        $this->importExito       = true;
    }

    public function cancelarImport(): void
    {
        $this->confirmandoImport = false;
        $this->archivoImport     = null;
        $this->previewImport     = [];
        $this->importError       = '';
    }

    public function render()
    {
        return view('livewire.ajustes');
    }
}
