<?php

namespace App\Livewire;

use App\Exports\ClientesExport;
use App\Models\Cliente;
use App\Models\Configuracion;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.app')]
#[Title('Clientes')]
class Clientes extends Component
{
    use WithPagination;

    public string $buscar = '';

    // Formulario alta/edición
    public bool   $modalFormulario = false;
    public ?int   $clienteId       = null;
    public string $nombre          = '';
    public string $apellido        = '';
    public string $celular         = '';

    // Modal historial
    public bool $modalHistorial     = false;
    public ?int $historialClienteId = null;

    // Modal eliminar
    public bool   $modalEliminar  = false;
    public ?int   $eliminarId     = null;
    public string $eliminarNombre = '';

    protected function rules(): array
    {
        return [
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'celular'  => 'required|string|max:20',
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required'   => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'celular.required'  => 'El celular es obligatorio.',
        ];
    }

    public function updatingBuscar(): void
    {
        $this->resetPage();
    }

    public function updatedNombre(string $valor): void
    {
        $this->nombre = mb_convert_case($valor, MB_CASE_TITLE, 'UTF-8');
    }

    public function updatedApellido(string $valor): void
    {
        $this->apellido = mb_convert_case($valor, MB_CASE_TITLE, 'UTF-8');
    }

    public function abrirModalNuevo(): void
    {
        $this->reset(['clienteId', 'nombre', 'apellido', 'celular']);
        $this->resetErrorBag();
        $this->modalFormulario = true;
    }

    public function editar(int $id): void
    {
        $cliente         = Cliente::findOrFail($id);
        $this->clienteId = $cliente->id;
        $this->nombre    = $cliente->nombre;
        $this->apellido  = $cliente->apellido;
        $this->celular   = $cliente->celular;
        $this->resetErrorBag();
        $this->modalFormulario = true;
    }

    public function guardar(): void
    {
        $this->validate();

        $datos = [
            'nombre'   => $this->nombre,
            'apellido' => $this->apellido,
            'celular'  => $this->celular,
        ];

        if ($this->clienteId) {
            Cliente::findOrFail($this->clienteId)->update($datos);
        } else {
            Cliente::create($datos);
        }

        $this->modalFormulario = false;
        $this->reset(['clienteId', 'nombre', 'apellido', 'celular']);
    }

    public function abrirHistorial(int $id): void
    {
        $this->historialClienteId = $id;
        $this->modalHistorial     = true;
    }

    public function confirmarEliminar(int $id): void
    {
        $cliente              = Cliente::findOrFail($id);
        $this->eliminarId     = $id;
        $this->eliminarNombre = $cliente->nombre . ' ' . $cliente->apellido;
        $this->modalEliminar  = true;
    }

    public function eliminar(): void
    {
        Cliente::findOrFail($this->eliminarId)->delete();
        $this->modalEliminar = false;
        $this->eliminarId    = null;
    }

    public function exportarExcel()
    {
        return Excel::download(new ClientesExport, 'clientes-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function render()
    {
        $clientes = Cliente::with('proximoTurno')
            ->where(function ($q) {
                $q->where('nombre',   'like', "%{$this->buscar}%")
                  ->orWhere('apellido', 'like', "%{$this->buscar}%");
            })
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->paginate(10);

        $clienteHistorial = $this->historialClienteId
            ? Cliente::with([
                'turnos'             => fn ($q) => $q->with('tratamiento')->orderByDesc('fecha')->orderByDesc('hora'),
                'proximoTurno.tratamiento',
              ])->find($this->historialClienteId)
            : null;

        $mensajeWa = Configuracion::obtener(
            'whatsapp_mensaje',
            'Hola {nombre}, te recuerdo tu turno. ¡Hasta pronto! 🌸'
        );

        return view('livewire.clientes', [
            'clientes'         => $clientes,
            'clienteHistorial' => $clienteHistorial,
            'mensajeWa'        => $mensajeWa,
        ]);
    }
}
