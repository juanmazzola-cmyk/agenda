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
    public string $vista         = 'mensual';
    public string $filtro        = 'todos';
    public bool   $ocultarMontos = false;
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
            $todosTurnos = Turno::with('tratamiento')
                ->whereYear('fecha', $this->anio)
                ->whereMonth('fecha', $this->mes)
                ->get();
            $turnosPasados = $todosTurnos->filter(fn ($t) => $t->fecha->lte(now()->startOfDay()));
        } else {
            $todosTurnos = Turno::with('tratamiento')
                ->whereYear('fecha', $this->anio)
                ->get();
            $turnosPasados = $todosTurnos->filter(fn ($t) => $t->fecha->lte(now()->startOfDay()));
        }

        $filtrados = $this->filtro === 'cobrados'
            ? $turnosPasados->where('cobrado', true)
            : $turnosPasados;

        $todosFiltrados = $this->filtro === 'cobrados'
            ? $todosTurnos->where('cobrado', true)
            : $todosTurnos;

        // KPIs
        $kpis = [
            'turnos'    => $todosFiltrados->count(),
            'atendidas' => $filtrados->count(),
            'facturado' => $filtrados->sum('valor'),
            'cobrado'   => $filtrados->where('cobrado', true)->sum('valor'),
            'pendiente' => $filtrados->where('cobrado', false)->sum('valor'),
        ];

        // Por tratamiento (solo turnos pasados)
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

        // Por mes (solo vista anual, solo pasados)
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
