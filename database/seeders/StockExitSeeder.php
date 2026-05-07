<?php

namespace Database\Seeders;

use App\Models\StockExit;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockExitSeeder extends Seeder
{
    public function run(): void
    {
        $exits = [
            // Salidas de hace 20 días
            [
                'product_id' => 1, // Mouse
                'user_id' => 2,
                'quantity' => 15,
                'exit_date' => Carbon::now()->subDays(20),
                'reason' => 'sale',
                'document_type' => 'factura',           // ⭐ AÑADIDO
                'document_number' => 'F001-00125',
                'notes' => 'Venta a cliente corporativo',
            ],
            [
                'product_id' => 4, // Arroz
                'user_id' => 2,
                'quantity' => 50,
                'exit_date' => Carbon::now()->subDays(18),
                'reason' => 'sale',
                'document_type' => 'boleta',            // ⭐ AÑADIDO
                'document_number' => 'B001-00089',
                'notes' => 'Venta mayorista',
            ],
            
            // Salidas de hace 10 días
            [
                'product_id' => 2, // Teclado
                'user_id' => 3,
                'quantity' => 8,
                'exit_date' => Carbon::now()->subDays(10),
                'reason' => 'sale',
                'document_type' => 'boleta',            // ⭐ AÑADIDO
                'document_number' => 'B001-00090',
                'notes' => 'Venta retail',
            ],
            [
                'product_id' => 7, // Detergente
                'user_id' => 2,
                'quantity' => 30,
                'exit_date' => Carbon::now()->subDays(10),
                'reason' => 'sale',
                'document_type' => 'factura',           // ⭐ AÑADIDO
                'document_number' => 'F001-00126',
                'notes' => 'Venta al por menor',
            ],
            
            // Salidas de hace 3 días
            [
                'product_id' => 10, // Papel Bond
                'user_id' => 3,
                'quantity' => 40,
                'exit_date' => Carbon::now()->subDays(3),
                'reason' => 'transfer',
                'document_type' => 'guia_remision',     // ⭐ AÑADIDO
                'document_number' => 'GR-00045',
                'notes' => 'Transferencia a sucursal',
            ],
            [
                'product_id' => 5, // Aceite
                'user_id' => 2,
                'quantity' => 20,
                'exit_date' => Carbon::now()->subDays(3),
                'reason' => 'sale',
                'document_type' => 'boleta',            // ⭐ AÑADIDO
                'document_number' => 'B001-00091',
                'notes' => 'Venta local',
            ],
            
            // Salidas de hoy
            [
                'product_id' => 16, // Camisetas
                'user_id' => 3,
                'quantity' => 25,
                'exit_date' => Carbon::today(),
                'reason' => 'sale',
                'document_type' => 'nota_salida',       // ⭐ AÑADIDO
                'document_number' => 'NS-00012',
                'notes' => 'Venta promocional',
            ],
            [
                'product_id' => 3, // Audífonos
                'user_id' => 2,
                'quantity' => 5,
                'exit_date' => Carbon::today(),
                'reason' => 'sale',
                'document_type' => 'factura',           // ⭐ AÑADIDO
                'document_number' => 'F001-00127',
                'notes' => 'Venta retail',
            ],
        ];

        foreach ($exits as $exit) {
            StockExit::create($exit);
        }
    }
}