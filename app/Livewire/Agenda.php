<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\Tratamiento;
use App\Models\Turno;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Agenda')]
class Agenda extends Component
{
    public int    $anio;
    public int    $mes;
    public string $diaSeleccionado = '';

    // Formulario
    public bool   $modalFormulario = false;
    public ?int   $turnoId         = null;
    public string $clienteId       = '';
    public string $tratamientoId   = '';
    public string $fecha           = '';
    public string $hora            = '';
    public string $valor           = '';
    public bool   $cobrado         = false;
    public string $notas           = '';

    // Eliminar
    public bool  $modalEliminar = false;
    public ?int  $eliminarId    = null;

    public function mount(): void
    {
        $this->anio            = now()->year;
        $this->mes             = now()->month;
        $this->diaSeleccionado = now()->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'clienteId'     => 'required|exists:clientes,id',
            'tratamientoId' => 'required|exists:tratamientos,id',
            'fecha'         => 'required|date',
            'hora'          => 'required',
            'valor'         => 'nullable|numeric|min:0',
            'notas'         => 'nullable|string|max:500',
        ];
    }

    protected function messages(): array
    {
        return [
            'clienteId.required'     => 'Seleccioná una cliente.',
            'tratamientoId.required' => 'Seleccioná un tratamiento.',
            'fecha.required'         => 'La fecha es obligatoria.',
            'hora.required'          => 'La hora es obligatoria.',
            'valor.numeric'          => 'El valor debe ser un número.',
            'valor.min'              => 'El valor no puede ser negativo.',
        ];
    }

    public function mesAnterior(): void
    {
        if ($this->mes === 1) {
            $this->mes = 12;
            $this->anio--;
        } else {
            $this->mes--;
        }
        $this->diaSeleccionado = '';
    }

    public function mesSiguiente(): void
    {
        if ($this->mes === 12) {
            $this->mes = 1;
            $this->anio++;
        } else {
            $this->mes++;
        }
        $this->diaSeleccionado = '';
    }

    public function seleccionarDia(string $fecha): void
    {
        $this->diaSeleccionado = $fecha;
    }

    public function abrirModalNuevo(): void
    {
        $this->reset(['turnoId', 'clienteId', 'tratamientoId', 'hora', 'valor', 'notas']);
        $this->cobrado = false;
        $this->fecha   = $this->diaSeleccionado ?: now()->format('Y-m-d');
        $this->resetErrorBag();
        $this->modalFormulario = true;
    }

    public function editar(int $id): void
    {
        $turno               = Turno::findOrFail($id);
        $this->turnoId       = $turno->id;
        $this->clienteId     = (string) $turno->cliente_id;
        $this->tratamientoId = (string) $turno->tratamiento_id;
        $this->fecha         = Carbon::parse($turno->fecha)->format('Y-m-d');
        $this->hora          = substr($turno->hora, 0, 5);
        $this->valor         = (string) $turno->valor;
        $this->cobrado       = (bool) $turno->cobrado;
        $this->notas         = $turno->notas ?? '';
        $this->resetErrorBag();
        $this->modalFormulario = true;
    }

    public function guardar(): void
    {
        $this->validate();

        $datos = [
            'cliente_id'     => (int) $this->clienteId,
            'tratamiento_id' => (int) $this->tratamientoId,
            'fecha'          => $this->fecha,
            'hora'           => $this->hora,
            'valor'          => $this->valor !== '' ? (float) str_replace(['.', ','], ['', '.'], $this->valor) : null,
            'cobrado'        => $this->cobrado,
            'notas'          => $this->notas ?: null,
        ];

        if ($this->turnoId) {
            Turno::findOrFail($this->turnoId)->update($datos);
        } else {
            Turno::create($datos);
        }

        $fechaGuardada         = Carbon::parse($this->fecha);
        $this->anio            = $fechaGuardada->year;
        $this->mes             = $fechaGuardada->month;
        $this->diaSeleccionado = $fechaGuardada->format('Y-m-d');

        $this->modalFormulario = false;
        $this->reset(['turnoId', 'clienteId', 'tratamientoId', 'fecha', 'hora', 'valor', 'notas']);
        $this->cobrado = false;
    }

    public function toggleCobrado(int $id): void
    {
        $turno = Turno::findOrFail($id);
        $turno->update(['cobrado' => ! $turno->cobrado]);
    }

    public function confirmarEliminar(int $id): void
    {
        $this->eliminarId    = $id;
        $this->modalEliminar = true;
    }

    public function eliminar(): void
    {
        Turno::findOrFail($this->eliminarId)->delete();
        $this->modalEliminar = false;
        $this->eliminarId    = null;
    }

    public function render()
    {
        $nombresMes = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $mesesEs    = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                       'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

        $primero   = Carbon::create($this->anio, $this->mes, 1);
        $diasEnMes = $primero->daysInMonth;
        $offset    = $primero->dayOfWeek === 0 ? 6 : $primero->dayOfWeek - 1;

        $fechasConTurno = Turno::whereYear('fecha', $this->anio)
            ->whereMonth('fecha', $this->mes)
            ->pluck('fecha')
            ->map(fn ($f) => Carbon::parse($f)->format('Y-m-d'))
            ->unique()
            ->toArray();

        $dias = array_fill(0, $offset, null);

        for ($d = 1; $d <= $diasEnMes; $d++) {
            $fecha  = Carbon::create($this->anio, $this->mes, $d)->format('Y-m-d');
            $dias[] = [
                'numero'      => $d,
                'fecha'       => $fecha,
                'tieneTurno'  => in_array($fecha, $fechasConTurno),
                'esHoy'       => $fecha === now()->format('Y-m-d'),
                'seleccionado'=> $fecha === $this->diaSeleccionado,
            ];
        }

        while (count($dias) % 7 !== 0) {
            $dias[] = null;
        }

        $semanas = array_chunk($dias, 7);

        $turnosDia = $this->diaSeleccionado
            ? Turno::with(['cliente', 'tratamiento'])
                ->whereDate('fecha', $this->diaSeleccionado)
                ->orderBy('hora')
                ->get()
            : collect();

        $diaFormateado = null;
        if ($this->diaSeleccionado) {
            $c             = Carbon::parse($this->diaSeleccionado);
            $diaFormateado = $diasSemana[$c->dayOfWeek] . ' ' . $c->day . ' de ' . $mesesEs[$c->month];
        }

        $clientes     = Cliente::orderBy('apellido')->orderBy('nombre')->get();
        $tratamientos = Tratamiento::orderBy('nombre')->get();
        $mensajeWa    = Configuracion::obtener('whatsapp_mensaje', 'Hola {nombre}, te recuerdo tu turno. ¡Hasta pronto! 🌸');

        return view('livewire.agenda', [
            'semanas'       => $semanas,
            'turnosDia'     => $turnosDia,
            'clientes'      => $clientes,
            'tratamientos'  => $tratamientos,
            'nombreMes'     => $nombresMes[$this->mes],
            'diaFormateado' => $diaFormateado,
            'mensajeWa'     => $mensajeWa,
        ]);
    }
}
