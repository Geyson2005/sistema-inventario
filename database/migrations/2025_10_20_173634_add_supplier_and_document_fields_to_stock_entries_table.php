<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            // Añadir relación con proveedor
            $table->foreignId('supplier_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained()
                  ->onDelete('restrict');
            
            // Añadir tipo de documento
            $table->enum('document_type', [
                'factura',
                'boleta',
                'nota_entrada',
                'nota_salida',
                'guia_remision',
                'otros'
            ])->nullable()->after('entry_date');
            
            // Modificar document_number (ya existe pero lo hacemos nullable)
            // Si necesitas añadirlo de nuevo:
            // $table->string('document_number', 50)->nullable()->after('document_type');
            
            // Índices
            $table->index('supplier_id');
            $table->index('document_type');
        });
    }

    public function down(): void
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['supplier_id', 'document_type']);
        });
    }
};