<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Orden importante: primero las tablas sin dependencias
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            ClientSeeder::class, // ⭐ NUEVO SEEDER DE CLIENTES
            // ⭐ NUEVOS SEEDERS DE LOTES
            StockEntryBatchSeeder::class,
            StockExitBatchSeeder::class,
            
            // Ya no usamos estos (comentar o eliminar)
            StockEntrySeeder::class,
            StockExitSeeder::class,
            
            StockAdjustmentSeeder::class,
        ]);
    }
}