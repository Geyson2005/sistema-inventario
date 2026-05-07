<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromCollection, WithHeadings, WithStyles, WithTitle
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
        return collect($this->data['products'])->map(function($product, $index) {
            return [
                'N°' => $index + 1,
                'Código' => $product->code,
                'Nombre' => $product->name,
                'Categoría' => $product->category->name,
                'Stock Actual' => $product->current_stock,
                'Unidad' => $product->unit->abbreviation,
                'Stock Mínimo' => $product->min_stock,
                'Estado Stock' => $product->stock_status_label,
                'Estado' => $product->status === 'active' ? 'Activo' : 'Inactivo',
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
            'Código',
            'Nombre del Producto',
            'Categoría',
            'Stock Actual',
            'Unidad',
            'Stock Mínimo',
            'Estado Stock',
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
        return 'Inventario';
    }
}