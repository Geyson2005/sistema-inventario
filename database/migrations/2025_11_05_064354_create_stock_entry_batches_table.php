<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_entry_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            
            $table->date('entry_date');
            $table->enum('document_type', [
                'factura',
                'boleta',
                'nota_entrada',
                'nota_salida',
                'guia_remision',
                'otros'
            ])->nullable();
            $table->string('document_number', 50)->nullable();
            $table->text('notes')->nullable();
            
            // Campos calculados
            $table->integer('total_items')->default(0); // Total de productos diferentes
            $table->integer('total_quantity')->default(0); // Suma de todas las cantidades
            
            $table->timestamps();
            
            // Índices
            $table->index('entry_date');
            $table->index('supplier_id');
            $table->index('document_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_entry_batches');
    }
};