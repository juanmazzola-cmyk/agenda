<?php

namespace App\Livewire;

use App\Exports\EstadisticasExport;
use App\Models\Turno;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.app')]
#[Title('Estadísticas')]
class Estadisticas extends Component
{
    public string $periodo = 'todo'; // 'todo' | '2025' | '2026' ...
    public int    $anioFiltro;

    public function mount(): void
    {
        $this->anioFiltro = now()->year;
    }

    public function anioAnterior(): void
    {
        $this->anioFiltro--;
        $this->periodo = (string) $this->anioFiltro;
    }

    public function anioSiguiente(): void
    {
        $this->anioFiltro++;
        $this->periodo = (string) $this->anioFiltro;
    }

    public function seleccionarTodo(): void
    {
        $this->periodo = 'todo';
    }

    public function seleccionarAnio(): void
    {
        $this->periodo = (string) $this->anioFiltro;
    }

    public function exportarExcel()
    {
        $nombre = 'estadisticas-' . ($this->periodo === 'todo' ? 'todo' : $this->periodo) . '-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new EstadisticasExport($this->periodo), $nombre);
    }

    public function render()
    {
        $base = Turno::query()
            ->whereDate('fecha', '<=', now())
            ->when($this->periodo !== 'todo', fn ($q) => $q->whereYear('fecha', $this->periodo));

        // Clientes: agregar en DB para eficiencia
        $porCliente = (clone $base)
            ->join('clientes', 'turnos.cliente_id', '=', 'clientes.id')
            ->select(
                'clientes.id',
                DB::raw("CONCAT(clientes.nombre, ' ', clientes.apellido) as nombre_completo"),
                DB::raw('COUNT(*) as visitas'),
                DB::raw('SUM(turnos.valor) as total_gastado'),
                DB::raw('MAX(turnos.fecha) as ultimo_turno')
            )
            ->groupBy('clientes.id', 'clientes.nombre', 'clientes.apellido')
            ->orderByDesc('visitas')
            ->get();

        // Tratamientos: agregar en DB
        $porTratamiento = (clone $base)
            ->join('tratamientos', 'turnos.tratamiento_id', '=', 'tratamientos.id')
            ->select(
                'tratamientos.id',
                'tratamientos.nombre',
                DB::raw('COUNT(*) as veces'),
                DB::raw('SUM(turnos.valor) as total_generado')
            )
            ->groupBy('tratamientos.id', 'tratamientos.nombre')
            ->orderByDesc('veces')
            ->get();

        $maxVisitas    = $porCliente->max('visitas') ?: 1;
        $maxVeces      = $porTratamiento->max('veces') ?: 1;
        $totalTurnos   = (clone $base)->count();

        // Años disponibles para el selector
        $aniosDisponibles = Turno::selectRaw('YEAR(fecha) as anio')
            ->distinct()
            ->orderByDesc('anio')
            ->pluck('anio');

        return view('livewire.estadisticas', [
            'porCliente'       => $porCliente,
            'porTratamiento'   => $porTratamiento,
            'maxVisitas'       => $maxVisitas,
            'maxVeces'         => $maxVeces,
            'totalTurnos'      => $totalTurnos,
            'aniosDisponibles' => $aniosDisponibles,
        ]);
    }
}
