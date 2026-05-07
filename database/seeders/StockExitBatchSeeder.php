<?php

namespace Database\Seeders;

use App\Models\StockExitBatch;
use App\Models\StockExit;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockExitBatchSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Lote 1: Hace 20 días - Venta
            $batch1 = StockExitBatch::create([
                'user_id' => 2,
                'exit_date' => Carbon::now()->subDays(20),
                'reason' => 'sale',
                'document_type' => 'factura',
                'document_number' => 'F001-00125',
                'notes' => 'Venta a cliente corporativo',
            ]);

            StockExit::create([
                'batch_id' => $batch1->id,
                'product_id' => 1, // Mouse
                'user_id' => 2,
                'quantity' => 15,
                'exit_date' => $batch1->exit_date,
                'reason' => $batch1->reason,
                'document_type' => $batch1->document_type,
                'document_number' => $batch1->document_number,
            ]);

            StockExit::create([
                'batch_id' => $batch1->id,
                'product_id' => 2, // Teclado
                'user_id' => 2,
                'quantity' => 8,
                'exit_date' => $batch1->exit_date,
                'reason' => $batch1->reason,
                'document_type' => $batch1->document_type,
                'document_number' => $batch1->document_number,
            ]);

            $batch1->calculateTotals();

            // Lote 2: Hace 10 días - Venta
            $batch2 = StockExitBatch::create([
                'user_id' => 3,
                'exit_date' => Carbon::now()->subDays(10),
                'reason' => 'sale',
                'document_type' => 'boleta',
                'document_number' => 'B001-00089',
                'notes' => 'Venta al por menor',
            ]);

            StockExit::create([
                'batch_id' => $batch2->id,
                'product_id' => 4, // Arroz
                'user_id' => 3,
                'quantity' => 50,
                'exit_date' => $batch2->exit_date,
                'reason' => $batch2->reason,
                'document_type' => $batch2->document_type,
                'document_number' => $batch2->document_number,
            ]);

            StockExit::create([
                'batch_id' => $batch2->id,
                'product_id' => 5, // Aceite
                'user_id' => 3,
                'quantity' => 20,
                'exit_date' => $batch2->exit_date,
                'reason' => $batch2->reason,
                'document_type' => $batch2->document_type,
                'document_number' => $batch2->document_number,
            ]);

            $batch2->calculateTotals();

            // Lote 3: Hace 3 días - Transferencia
            $batch3 = StockExitBatch::create([
                'user_id' => 2,
                'exit_date' => Carbon::now()->subDays(3),
                'reason' => 'transfer',
                'document_type' => 'guia_remision',
                'document_number' => 'GT-00045',
                'notes' => 'Transferencia a sucursal Lima',
            ]);

            StockExit::create([
                'batch_id' => $batch3->id,
                'product_id' => 10, // Papel Bond
                'user_id' => 2,
                'quantity' => 40,
                'exit_date' => $batch3->exit_date,
                'reason' => $batch3->reason,
                'document_type' => $batch3->document_type,
                'document_number' => $batch3->document_number,
            ]);

            StockExit::create([
                'batch_id' => $batch3->id,
                'product_id' => 11, // Lapiceros
                'user_id' => 2,
                'quantity' => 60,
                'exit_date' => $batch3->exit_date,
                'reason' => $batch3->reason,
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