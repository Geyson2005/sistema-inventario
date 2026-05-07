<?php

namespace Database\Seeders;

use App\Models\StockEntry;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockEntrySeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            // Entradas de hace 30 días
            [
                'product_id' => 1, // Mouse
                'supplier_id' => 1, // Distribuidora Tech
                'user_id' => 1,
                'quantity' => 50,
                'entry_date' => Carbon::now()->subDays(30),
                'document_type' => 'factura',
                'document_number' => 'F001-00125',
                'notes' => 'Primera compra de inventario',
            ],
            [
                'product_id' => 2, // Teclado
                'supplier_id' => 1, // Distribuidora Tech
                'user_id' => 1,
                'quantity' => 30,
                'entry_date' => Carbon::now()->subDays(30),
                'document_type' => 'factura',
                'document_number' => 'F001-00126',
                'notes' => 'Primera compra de inventario',
            ],
            [
                'product_id' => 4, // Arroz
                'supplier_id' => 2, // Alimentos del Norte
                'user_id' => 1,
                'quantity' => 200,
                'entry_date' => Carbon::now()->subDays(28),
                'document_type' => 'factura',
                'document_number' => 'F002-00089',
                'notes' => 'Compra mensual',
            ],
            [
                'product_id' => 5, // Aceite
                'supplier_id' => 2, // Alimentos del Norte
                'user_id' => 2,
                'quantity' => 80,
                'entry_date' => Carbon::now()->subDays(28),
                'document_type' => 'boleta',
                'document_number' => 'B002-00156',
                'notes' => 'Compra mensual',
            ],
            
            // Entradas de hace 15 días
            [
                'product_id' => 7, // Detergente
                'supplier_id' => 3, // Ferretería Central
                'user_id' => 2,
                'quantity' => 100,
                'entry_date' => Carbon::now()->subDays(15),
                'document_type' => 'guia_remision',
                'document_number' => 'G001-00045',
                'notes' => 'Reposición de stock',
            ],
            [
                'product_id' => 10, // Papel Bond
                'supplier_id' => 4, // Importaciones Global
                'user_id' => 1,
                'quantity' => 120,
                'entry_date' => Carbon::now()->subDays(15),
                'document_type' => 'factura',
                'document_number' => 'F004-00234',
                'notes' => 'Compra trimestral de papelería',
            ],
            
            // Entradas de hace 5 días
            [
                'product_id' => 3, // Audífonos
                'supplier_id' => 1, // Distribuidora Tech
                'user_id' => 2,
                'quantity' => 25,
                'entry_date' => Carbon::now()->subDays(5),
                'document_type' => 'nota_entrada',
                'document_number' => 'NE-00012',
                'notes' => 'Nueva línea de productos',
            ],
            [
                'product_id' => 13, // Martillo
                'supplier_id' => 3, // Ferretería Central
                'user_id' => 1,
                'quantity' => 15,
                'entry_date' => Carbon::now()->subDays(5),
                'document_type' => 'boleta',
                'document_number' => 'B003-00078',
                'notes' => 'Reposición ferretería',
            ],
            
            // Entradas de ayer
            [
                'product_id' => 16, // Camisetas
                'supplier_id' => 5, // Proveedor Local
                'user_id' => 2,
                'quantity' => 100,
                'entry_date' => Carbon::yesterday(),
                'document_type' => 'otros',
                'document_number' => 'COMP-00089',
                'notes' => 'Nueva temporada textiles',
            ],
            [
                'product_id' => 11, // Lapiceros
                'supplier_id' => 4, // Importaciones Global
                'user_id' => 3,
                'quantity' => 150,
                'entry_date' => Carbon::yesterday(),
                'document_type' => 'factura',
                'document_number' => 'F004-00235',
                'notes' => 'Stock para inicio de clases',
            ],
        ];

        foreach ($entries as $entry) {
            StockEntry::create($entry);
        }
    }
}