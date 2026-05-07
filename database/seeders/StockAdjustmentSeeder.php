<?php

namespace Database\Seeders;

use App\Models\StockAdjustment;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockAdjustmentSeeder extends Seeder
{
    public function run(): void
    {
        $adjustments = [
            // Ajuste por diferencia de inventario
            [
                'product_id' => 1, // Mouse
                'user_id' => 1,
                'quantity' => 3,
                'type' => 'decrease',
                'reason' => 'Diferencia encontrada en inventario físico',
                'adjustment_date' => Carbon::now()->subDays(7),
                'notes' => 'Auditoría semanal - faltante',
            ],
            
            // Ajuste por productos dañados
            [
                'product_id' => 7, // Detergente
                'user_id' => 1,
                'quantity' => 5,
                'type' => 'decrease',
                'reason' => 'Productos dañados durante manipulación',
                'adjustment_date' => Carbon::now()->subDays(5),
                'notes' => 'Envases rotos',
            ],
            
            // Ajuste por corrección de entrada
            [
                'product_id' => 11, // Lapiceros
                'user_id' => 1,
                'quantity' => 10,
                'type' => 'increase',
                'reason' => 'Corrección por error de registro',
                'adjustment_date' => Carbon::now()->subDays(2),
                'notes' => 'Se registró 150 en lugar de 160',
            ],
        ];

        foreach ($adjustments as $adjustment) {
            StockAdjustment::create($adjustment);
        }
    }
}