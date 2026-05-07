<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_exits', function (Blueprint $table) {
            // Añadir tipo de documento después de exit_date
            $table->enum('document_type', [
                'factura',
                'boleta',
                'nota_entrada',
                'nota_salida',
                'guia_remision',
                'otros'
            ])->nullable()->after('exit_date');
            
            // Índice para búsquedas
            $table->index('document_type');
        });
    }

    public function down(): void
    {
        Schema::table('stock_exits', function (Blueprint $table) {
            $table->dropColumn('document_type');
        });
    }
};