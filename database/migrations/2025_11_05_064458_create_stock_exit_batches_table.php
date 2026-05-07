<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_exit_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            
            $table->date('exit_date');
            $table->enum('reason', ['sale', 'transfer', 'damage', 'other'])->default('sale');
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
            $table->integer('total_items')->default(0);
            $table->integer('total_quantity')->default(0);
            
            $table->timestamps();
            
            // Índices
            $table->index('exit_date');
            $table->index('document_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_exit_batches');
    }
};