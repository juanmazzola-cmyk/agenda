<?php

namespace App\Livewire;

use App\Models\Turno;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Resumen Económico')]
class Resumen extends Component
{
    public string $vista  = 'mensual'; // 'mensual' | 'anual'
    public string $filtro = 'todos';   // 'todos'   | 'cobrados'
    public int    $anio;
    public int    $mes;

    public function mount(): void
    {
        $this->anio = now()->year;
        $this->mes  = now()->month;
    }

    public function mesAnterior(): void
    {
        if ($this->mes === 1) { $this->mes = 12; $this->anio--; }
        else                  { $this->mes--; }
    }

    public function mesSiguiente(): void
    {
        if ($this->mes === 12) { $this->mes = 1; $this->anio++; }
        else                   { $this->mes++; }
    }

    public function anioAnterior(): void { $this->anio--; }
    public function anioSiguiente(): void { $this->anio++; }

    public function render()
    {
        $nombresMes = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        if ($this->vista === 'mensual') {
            $turnos = Turno::with('tratamiento')
                ->whereYear('fecha', $this->anio)
                ->whereMonth('fecha', $this->mes)
                ->get();
        } else {
            $turnos = Turno::with('tratamiento')
                ->whereYear('fecha', $this->anio)
                ->get();
        }

        $filtrados = $this->filtro === 'cobrados'
            ? $turnos->where('cobrado', true)
            : $turnos;

        // KPIs
        $kpis = [
            'turnos'    => $filtrados->count(),
            'facturado' => $filtrados->sum('valor'),
            'cobrado'   => $filtrados->where('cobrado', true)->sum('valor'),
            'pendiente' => $filtrados->where('cobrado', false)->sum('valor'),
            'clientes'  => $filtrados->pluck('cliente_id')->unique()->count(),
        ];

        // Por tratamiento
        $porTratamiento = $filtrados
            ->groupBy('tratamiento_id')
            ->map(fn ($g) => [
                'nombre'    => $g->first()->tratamiento->nombre,
                'cantidad'  => $g->count(),
                'facturado' => $g->sum('valor'),
                'cobrado'   => $g->where('cobrado', true)->sum('valor'),
                'pendiente' => $g->where('cobrado', false)->sum('valor'),
            ])
            ->sortByDesc('facturado')
            ->values();

        // Por mes (solo vista anual)
        $porMes = null;
        if ($this->vista === 'anual') {
            $porMes = collect(range(1, 12))->map(function ($m) use ($filtrados, $nombresMes) {
                $del = $filtrados->filter(fn ($t) => $t->fecha->month === $m);
                return [
                    'nombre'    => $nombresMes[$m],
                    'cantidad'  => $del->count(),
                    'facturado' => $del->sum('valor'),
                    'cobrado'   => $del->where('cobrado', true)->sum('valor'),
                    'pendiente' => $del->where('cobrado', false)->sum('valor'),
                ];
            });
        }

        return view('livewire.resumen', [
            'kpis'            => $kpis,
            'porTratamiento'  => $porTratamiento,
            'porMes'          => $porMes,
            'nombresMes'      => $nombresMes,
        ]);
    }
}
