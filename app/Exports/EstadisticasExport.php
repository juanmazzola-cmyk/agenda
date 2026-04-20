<?php

namespace App\Exports;

use App\Models\Turno;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EstadisticasExport implements WithMultipleSheets
{
    public function __construct(
        public readonly string $periodo = 'todo'
    ) {}

    public function sheets(): array
    {
        return [
            new EstadisticasClientesSheet($this->periodo),
            new EstadisticasTratamientosSheet($this->periodo),
        ];
    }
}


class EstadisticasClientesSheet implements
    \Maatwebsite\Excel\Concerns\FromCollection,
    \Maatwebsite\Excel\Concerns\WithHeadings,
    \Maatwebsite\Excel\Concerns\WithTitle,
    \Maatwebsite\Excel\Concerns\WithStyles
{
    public function __construct(private string $periodo) {}

    public function title(): string { return 'Por Cliente'; }

    public function collection()
    {
        return Turno::join('clientes', 'turnos.cliente_id', '=', 'clientes.id')
            ->when($this->periodo !== 'todo', fn ($q) => $q->whereYear('turnos.fecha', $this->periodo))
            ->select(
                DB::raw("CONCAT(clientes.nombre, ' ', clientes.apellido) as cliente"),
                DB::raw('COUNT(*) as visitas'),
                DB::raw('SUM(turnos.valor) as total_gastado')
            )
            ->groupBy('clientes.id', 'clientes.nombre', 'clientes.apellido')
            ->orderByDesc('visitas')
            ->get();
    }

    public function headings(): array
    {
        return ['Cliente', 'Visitas', 'Total gastado ($)'];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}


class EstadisticasTratamientosSheet implements
    \Maatwebsite\Excel\Concerns\FromCollection,
    \Maatwebsite\Excel\Concerns\WithHeadings,
    \Maatwebsite\Excel\Concerns\WithTitle,
    \Maatwebsite\Excel\Concerns\WithStyles
{
    public function __construct(private string $periodo) {}

    public function title(): string { return 'Por Tratamiento'; }

    public function collection()
    {
        return Turno::join('tratamientos', 'turnos.tratamiento_id', '=', 'tratamientos.id')
            ->when($this->periodo !== 'todo', fn ($q) => $q->whereYear('turnos.fecha', $this->periodo))
            ->select(
                'tratamientos.nombre',
                DB::raw('COUNT(*) as veces'),
                DB::raw('SUM(turnos.valor) as total_generado')
            )
            ->groupBy('tratamientos.id', 'tratamientos.nombre')
            ->orderByDesc('veces')
            ->get();
    }

    public function headings(): array
    {
        return ['Tratamiento', 'Veces realizado', 'Total generado ($)'];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
