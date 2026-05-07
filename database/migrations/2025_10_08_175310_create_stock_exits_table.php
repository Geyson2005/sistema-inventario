<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_exits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            
            $table->integer('quantity');
            $table->date('exit_date');
            $table->enum('reason', ['sale', 'transfer', 'damage', 'other'])->default('sale');
            $table->string('document_number', 50)->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Índices para reportes y búsquedas
            $table->index('exit_date');
            $table->index('product_id');
            $table->index(['product_id', 'exit_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_exits');
    }
};