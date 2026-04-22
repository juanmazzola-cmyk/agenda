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
    public string $vista = 'mensual'; // 'mensual' | 'anual' | 'todo'
    public int    $anioFiltro;
    public int    $mesFiltro;

    public function mount(): void
    {
        $this->anioFiltro = now()->year;
        $this->mesFiltro  = now()->month;
    }

    public function mesAnterior(): void
    {
        if ($this->mesFiltro === 1) { $this->mesFiltro = 12; $this->anioFiltro--; }
        else { $this->mesFiltro--; }
    }

    public function mesSiguiente(): void
    {
        if ($this->mesFiltro === 12) { $this->mesFiltro = 1; $this->anioFiltro++; }
        else { $this->mesFiltro++; }
    }

    public function anioAnterior(): void { $this->anioFiltro--; }
    public function anioSiguiente(): void { $this->anioFiltro++; }

    public function seleccionarVista(string $vista): void
    {
        $this->vista = $vista;
        $this->anioFiltro = now()->year;
        $this->mesFiltro  = now()->month;
    }

    public function exportarExcel()
    {
        $nombre = 'estadisticas-' . $this->vista . '-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new EstadisticasExport($this->vista === 'todo' ? 'todo' : (string) $this->anioFiltro), $nombre);
    }

    public function render()
    {
        $nombresMes = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $base = Turno::query()->whereDate('fecha', '<=', now());

        if ($this->vista === 'mensual') {
            $base->whereYear('fecha', $this->anioFiltro)->whereMonth('fecha', $this->mesFiltro);
        } elseif ($this->vista === 'anual') {
            $base->whereYear('fecha', $this->anioFiltro);
        }

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
            'nombresMes'       => $nombresMes,
        ]);
    }
}
