<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovementsExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Retorna la colección de datos
     */
    public function collection()
    {
        return collect($this->data['movements'])->map(function($movement, $index) {
            $typeLabel = match($movement->movement_type) {
                'entry' => 'Entrada',
                'exit' => 'Salida',
                'adjustment' => 'Ajuste',
                default => 'Desconocido',
            };

            return [
                'N°' => $index + 1,
                'Fecha' => $movement->created_at->format('d/m/Y H:i'),
                'Tipo' => $typeLabel,
                'Producto' => $movement->product->name,
                'Cantidad' => $movement->quantity,
                'Unidad' => $movement->product->unit->abbreviation,
                'Stock Anterior' => $movement->previous_stock,
                'Stock Nuevo' => $movement->new_stock,
                'Usuario' => $movement->user->name,
                'Notas' => $movement->notes ?? '-',
            ];
        });
    }

    /**
     * Encabezados de las columnas
     */
    public function headings(): array
    {
        return [
            'N°',
            'Fecha y Hora',
            'Tipo de Movimiento',
            'Producto',
            'Cantidad',
            'Unidad',
            'Stock Anterior',
            'Stock Nuevo',
            'Usuario',
            'Notas',
        ];
    }

    /**
     * Estilos para la hoja
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    /**
     * Título de la hoja
     */
    public function title(): string
    {
        return 'Movimientos';
    }
}