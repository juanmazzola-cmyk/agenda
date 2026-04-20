<?php

namespace App\Livewire;

use App\Models\Tratamiento;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Tratamientos')]
class Tratamientos extends Component
{
    use WithPagination;

    public string $buscar = '';

    // Formulario alta/edición
    public bool   $modalFormulario  = false;
    public ?int   $tratamientoId    = null;
    public string $nombre           = '';

    // Modal eliminar
    public bool   $modalEliminar  = false;
    public ?int   $eliminarId     = null;
    public string $eliminarNombre = '';

    protected function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
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

    public function abrirModalNuevo(): void
    {
        $this->reset(['tratamientoId', 'nombre']);
        $this->resetErrorBag();
        $this->modalFormulario = true;
    }

    public function editar(int $id): void
    {
        $tratamiento          = Tratamiento::findOrFail($id);
        $this->tratamientoId  = $tratamiento->id;
        $this->nombre         = $tratamiento->nombre;
        $this->resetErrorBag();
        $this->modalFormulario = true;
    }

    public function guardar(): void
    {
        $this->validate();

        if ($this->tratamientoId) {
            Tratamiento::findOrFail($this->tratamientoId)->update(['nombre' => $this->nombre]);
        } else {
            Tratamiento::create(['nombre' => $this->nombre]);
        }

        $this->modalFormulario = false;
        $this->reset(['tratamientoId', 'nombre']);
    }

    public function confirmarEliminar(int $id): void
    {
        $tratamiento          = Tratamiento::findOrFail($id);
        $this->eliminarId     = $id;
        $this->eliminarNombre = $tratamiento->nombre;
        $this->modalEliminar  = true;
    }

    public function eliminar(): void
    {
        Tratamiento::findOrFail($this->eliminarId)->delete();
        $this->modalEliminar = false;
        $this->eliminarId    = null;
    }

    public function render()
    {
        $tratamientos = Tratamiento::query()
            ->where('nombre', 'like', "%{$this->buscar}%")
            ->orderBy('nombre')
            ->paginate(10);

        return view('livewire.tratamientos', compact('tratamientos'));
    }
}
