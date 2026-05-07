<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            
            // Tipo de movimiento
            $table->enum('movement_type', ['entry', 'exit', 'adjustment']);
            
            // Control de stock
            $table->integer('quantity');
            $table->integer('previous_stock');
            $table->integer('new_stock');
            
            // Referencia al registro original
            $table->unsignedBigInteger('reference_id');
            $table->string('reference_type', 50); // App\Models\StockEntry, StockExit, StockAdjustment
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Índices para consultas rápidas
            $table->index(['product_id', 'created_at']);
            $table->index('movement_type');
            $table->index(['reference_id', 'reference_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};