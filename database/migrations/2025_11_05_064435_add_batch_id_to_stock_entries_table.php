<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            // Añadir relación con el lote
            $table->foreignId('batch_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('stock_entry_batches')
                  ->onDelete('cascade');
            
            // Índice
            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
    }
};