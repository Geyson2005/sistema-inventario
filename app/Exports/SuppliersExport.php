<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuppliersExport implements FromCollection, WithHeadings, WithStyles, WithTitle
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
        return collect($this->data['suppliers'])->map(function($item, $index) {
            $supplier = $item['supplier'];
            
            return [
                'N°' => $index + 1,
                'Nombre' => $supplier->name,
                'RUC' => $supplier->ruc ?? 'Sin RUC',
                'Contacto' => $supplier->contact_person ?? '-',
                'Teléfono' => $supplier->phone ?? '-',
                'Email' => $supplier->email ?? '-',
                'Dirección' => $supplier->address ?? '-',
                'Total Entradas' => $item['total_entries'],
                'Cantidad Total' => $item['total_quantity'],
                'Estado' => $supplier->status === 'active' ? 'Activo' : 'Inactivo',
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
            'Nombre',
            'RUC',
            'Persona de Contacto',
            'Teléfono',
            'Email',
            'Dirección',
            'Total Entradas',
            'Cantidad Total Suministrada',
            'Estado',
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
        return 'Proveedores';
    }
}