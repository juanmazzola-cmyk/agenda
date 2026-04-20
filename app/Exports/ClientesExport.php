<?php

namespace App\Exports;

use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function title(): string
    {
        return 'Clientes';
    }

    public function collection()
    {
        return Cliente::with('proximoTurno')
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();
    }

    public function headings(): array
    {
        return ['Nombre', 'Apellido', 'Celular', 'Próximo turno'];
    }

    public function map($cliente): array
    {
        $proximo = $cliente->proximoTurno
            ? \Carbon\Carbon::parse($cliente->proximoTurno->fecha)->format('d/m/Y') . ' ' . substr($cliente->proximoTurno->hora, 0, 5)
            : '';

        return [
            $cliente->nombre,
            $cliente->apellido,
            '+54 ' . $cliente->celular,
            $proximo,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
