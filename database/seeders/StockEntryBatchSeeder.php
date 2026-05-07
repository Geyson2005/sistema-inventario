<?php

namespace Database\Seeders;

use App\Models\StockEntryBatch;
use App\Models\StockEntry;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockEntryBatchSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Lote 1: Hace 30 días
            $batch1 = StockEntryBatch::create([
                'supplier_id' => 1, // Distribuidora Tech
                'user_id' => 1,
                'entry_date' => Carbon::now()->subDays(30),
                'document_type' => 'factura',
                'document_number' => 'F001-00125',
                'notes' => 'Compra mensual de productos electrónicos',
            ]);

            // Entradas del lote 1
            StockEntry::create([
                'batch_id' => $batch1->id,
                'product_id' => 1, // Mouse
                'supplier_id' => 1,
                'user_id' => 1,
                'quantity' => 50,
                'entry_date' => $batch1->entry_date,
                'document_type' => $batch1->document_type,
                'document_number' => $batch1->document_number,
            ]);

            StockEntry::create([
                'batch_id' => $batch1->id,
                'product_id' => 2, // Teclado
                'supplier_id' => 1,
                'user_id' => 1,
                'quantity' => 30,
                'entry_date' => $batch1->entry_date,
                'document_type' => $batch1->document_type,
                'document_number' => $batch1->document_number,
            ]);

            StockEntry::create([
                'batch_id' => $batch1->id,
                'product_id' => 3, // Audífonos
                'supplier_id' => 1,
                'user_id' => 1,
                'quantity' => 25,
                'entry_date' => $batch1->entry_date,
                'document_type' => $batch1->document_type,
                'document_number' => $batch1->document_number,
            ]);

            $batch1->calculateTotals();

            // Lote 2: Hace 15 días
            $batch2 = StockEntryBatch::create([
                'supplier_id' => 2, // Alimentos del Norte
                'user_id' => 2,
                'entry_date' => Carbon::now()->subDays(15),
                'document_type' => 'factura',
                'document_number' => 'F002-00089',
                'notes' => 'Compra de alimentos no perecederos',
            ]);

            StockEntry::create([
                'batch_id' => $batch2->id,
                'product_id' => 4, // Arroz
                'supplier_id' => 2,
                'user_id' => 2,
                'quantity' => 200,
                'entry_date' => $batch2->entry_date,
                'document_type' => $batch2->document_type,
                'document_number' => $batch2->document_number,
            ]);

            StockEntry::create([
                'batch_id' => $batch2->id,
                'product_id' => 5, // Aceite
                'supplier_id' => 2,
                'user_id' => 2,
                'quantity' => 80,
                'entry_date' => $batch2->entry_date,
                'document_type' => $batch2->document_type,
                'document_number' => $batch2->document_number,
            ]);

            StockEntry::create([
                'batch_id' => $batch2->id,
                'product_id' => 6, // Azúcar
                'supplier_id' => 2,
                'user_id' => 2,
                'quantity' => 100,
                'entry_date' => $batch2->entry_date,
                'document_type' => $batch2->document_type,
                'document_number' => $batch2->document_number,
            ]);

            $batch2->calculateTotals();

            // Lote 3: Hace 5 días
            $batch3 = StockEntryBatch::create([
                'supplier_id' => 3, // Ferretería Central
                'user_id' => 1,
                'entry_date' => Carbon::now()->subDays(5),
                'document_type' => 'guia_remision',
                'document_number' => 'GR-00045',
                'notes' => 'Reposición de productos de ferretería',
            ]);

            StockEntry::create([
                'batch_id' => $batch3->id,
                'product_id' => 13, // Martillo
                'supplier_id' => 3,
                'user_id' => 1,
                'quantity' => 15,
                'entry_date' => $batch3->entry_date,
                'document_type' => $batch3->document_type,
                'document_number' => $batch3->document_number,
            ]);

            StockEntry::create([
                'batch_id' => $batch3->id,
                'product_id' => 14, // Clavos
                'supplier_id' => 3,
                'user_id' => 1,
                'quantity' => 50,
                'entry_date' => $batch3->entry_date,
                'document_type' => $batch3->document_type,
                'document_number' => $batch3->document_number,
            ]);

            $batch3->calculateTotals();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}